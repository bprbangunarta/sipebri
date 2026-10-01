import { Head, router, usePage } from '@inertiajs/react';
import { ArrowLeft, Check, SendHorizontal } from 'lucide-react';
import { useState } from 'react';
import { navIcon } from '@/components/nav-icons';
import { Button } from '@/components/ui/button';
import { ConfirmDialog } from '@/components/ui/dialog';
import { Badge, Card, PageHeader } from '@/components/ui/misc';
import { rupiah } from '@/lib/format';
import { CorrectionPanel } from '@/pages/credit-analysis/correction-panel';
import type { Corrections } from '@/pages/credit-analysis/correction-panel';
import { Stat } from '@/pages/credit-analysis/parts';
import { AdministrationSection } from '@/pages/credit-analysis/sections/administration';
import { BusinessesSection } from '@/pages/credit-analysis/sections/businesses';
import { CollateralSection } from '@/pages/credit-analysis/sections/collateral';
import { FinanceSection } from '@/pages/credit-analysis/sections/finance';
import { FiveCSection } from '@/pages/credit-analysis/sections/five-c';
import { MemorandumSection } from '@/pages/credit-analysis/sections/memorandum';
import { OwnershipSection } from '@/pages/credit-analysis/sections/ownership';
import { QualitativeSection } from '@/pages/credit-analysis/sections/qualitative';
import type {
    Administration,
    AnalysisRecord,
    BusinessRow,
    CollateralRow,
    Finance,
    FiveC,
    Memorandum,
    Options,
    Section,
} from '@/pages/credit-analysis/types';

type Props = {
    record: AnalysisRecord;
    sections: Section[];
    canEdit: boolean;
    corrections: Corrections;
    businesses: BusinessRow[];
    finance: Finance;
    fiveC: FiveC;
    qualitative: Record<string, string | number | null> & {
        updated_at: string | null;
    };
    collaterals: CollateralRow[];
    memorandum: Memorandum;
    administration: Administration;
    submission: {
        gaps: string[];
        submitted_at: string | null;
        submitted_by: string | null;
    };
    options: Options;
};

export default function CreditAnalysisShow(props: Props) {
    const {
        record,
        sections,
        canEdit,
        corrections,
        businesses,
        finance,
        fiveC,
        qualitative,
        collaterals,
        memorandum,
        administration,
        submission,
        options,
    } = props;
    const query = new URLSearchParams(usePage().url.split('?')[1] ?? '');
    const [active, setActive] = useState(
        sections.some((s) => s.key === query.get('section'))
            ? (query.get('section') as string)
            : sections[0].key,
    );
    const [type, setType] = useState<BusinessRow['type']>(
        (['trade', 'farm', 'service', 'other'].includes(query.get('type') ?? '')
            ? query.get('type')
            : 'trade') as BusinessRow['type'],
    );
    const [confirming, setConfirming] = useState(false);
    const [sending, setSending] = useState(false);

    const filled: Record<string, boolean> = {
        business: businesses.length > 0,
        finance:
            finance.metrics.household_cost > 0 ||
            finance.obligations.length > 0,
        ownership: !!finance.asset_house || finance.assets.length > 0,
        collateral: collaterals.some(
            (c) => c.appraisal_value > 0 || c.location !== '',
        ),
        'five-c': fiveC.metrics.grade !== null,
        qualitative: !!qualitative.slik_check || !!qualitative.notes,
        memorandum: Number(memorandum.proposed_amount) > 0,
        administration: administration.total > 0,
    };
    const section = sections.find((s) => s.key === active) ?? sections[0];
    const ready = submission.gaps.length === 0;
    const atCommittee = record.status === 'committee';

    return (
        <>
            <Head title={`Analisa ${record.application_code}`} />
            <PageHeader
                title={`Analisa ${record.application_code}`}
                description={`${record.full_name} · ${record.nik}`}
                actions={
                    <>
                        <Badge tone={atCommittee ? 'success' : 'info'}>
                            {record.status_label}
                        </Badge>
                        <Button
                            variant="outline"
                            onClick={() => router.visit('/credit-analysis')}
                        >
                            <ArrowLeft /> Kembali
                        </Button>
                        {canEdit && record.status !== 'approved' && (
                            <Button
                                disabled={!ready}
                                onClick={() => setConfirming(true)}
                            >
                                <SendHorizontal /> Ajukan ke Komite
                            </Button>
                        )}
                    </>
                }
            />

            <Card className="mb-3">
                <div className="border-b border-line px-3 py-2">
                    <h2 className="text-sm font-semibold">Data Pengajuan</h2>
                </div>
                <div className="grid gap-3 p-3 sm:grid-cols-2 lg:grid-cols-4">
                    <Stat label="Produk" value={record.product_label ?? '–'} />
                    <Stat
                        label="Plafon / Jangka"
                        value={`${rupiah(record.requested_amount)} · ${record.requested_tenor ? `${record.requested_tenor} bln` : '–'}`}
                    />
                    <Stat
                        label="Suku Bunga"
                        value={
                            record.interest_rate
                                ? `${record.interest_rate}%`
                                : '–'
                        }
                    />
                    <Stat label="Penggunaan" value={record.usage_type ?? '–'} />
                    <Stat label="Kantor" value={record.office_label ?? '–'} />
                    <Stat
                        label="Kasi Analis"
                        value={record.supervisor_name ?? '–'}
                    />
                    <div className="sm:col-span-2">
                        <Stat
                            label="Hasil Survei"
                            value={
                                record.survey_note ?? 'Tanpa survei lapangan.'
                            }
                        />
                        {record.survey_at && (
                            <p className="text-xs text-muted">
                                {record.survey_by} · {record.survey_at}
                            </p>
                        )}
                    </div>
                </div>
            </Card>

            {atCommittee ? (
                <p className="mb-3 rounded-md border border-line bg-surface px-3 py-2 text-xs text-muted">
                    Diajukan ke komite {submission.submitted_at} oleh{' '}
                    {submission.submitted_by}. Lembar analisa tidak bisa diubah
                    lagi.
                </p>
            ) : (
                canEdit &&
                record.status !== 'approved' &&
                !ready && (
                    <p
                        role="status"
                        className="mb-3 rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800"
                    >
                        Belum bisa diajukan ke komite — lengkapi:{' '}
                        {submission.gaps.join(', ')}.
                    </p>
                )
            )}

            <CorrectionPanel loanId={record.id} corrections={corrections} />

            <div className="grid gap-3 lg:grid-cols-[14rem_minmax(0,1fr)]">
                <nav
                    aria-label="Bagian analisa"
                    className="flex flex-row gap-1 overflow-x-auto lg:sticky lg:top-3 lg:flex-col lg:self-start"
                >
                    {sections.map((s) => {
                        const Icon = navIcon(s.icon);

                        return (
                            <button
                                key={s.key}
                                type="button"
                                aria-current={
                                    s.key === active ? 'true' : undefined
                                }
                                onClick={() => setActive(s.key)}
                                className={`flex h-8 shrink-0 cursor-pointer items-center gap-2 rounded-md px-2.5 text-left text-sm font-medium whitespace-nowrap transition-colors ${s.key === active ? 'bg-primary-soft text-primary' : 'text-muted hover:bg-canvas hover:text-ink'}`}
                            >
                                <Icon className="size-4" />
                                <span className="flex-1">{s.label}</span>
                                {filled[s.key] && (
                                    <Check
                                        className="size-3.5 text-emerald-600"
                                        aria-label="Sudah diisi"
                                    />
                                )}
                            </button>
                        );
                    })}
                </nav>

                <div className="min-w-0">
                    <div className="mb-2 flex items-center justify-between gap-2">
                        <h2 className="text-base font-semibold">
                            {section.label}
                        </h2>
                        <Badge tone={filled[active] ? 'success' : 'neutral'}>
                            {filled[active] ? 'Sudah diisi' : 'Belum diisi'}
                        </Badge>
                    </div>
                    {active === 'business' && (
                        <BusinessesSection
                            loanId={record.id}
                            businesses={businesses}
                            type={type}
                            onType={setType}
                            canEdit={canEdit}
                        />
                    )}
                    {active === 'finance' && (
                        <FinanceSection
                            loanId={record.id}
                            finance={finance}
                            canEdit={canEdit}
                        />
                    )}
                    {active === 'ownership' && (
                        <OwnershipSection
                            loanId={record.id}
                            finance={finance}
                            assets={options.assets}
                            canEdit={canEdit}
                        />
                    )}
                    {active === 'collateral' && (
                        <CollateralSection
                            loanId={record.id}
                            rows={collaterals}
                            kinds={options.collateralKinds}
                            canEdit={canEdit}
                        />
                    )}
                    {active === 'five-c' && (
                        <FiveCSection
                            loanId={record.id}
                            fiveC={fiveC}
                            appraisalTotal={memorandum.appraisal_total}
                            canEdit={canEdit}
                        />
                    )}
                    {active === 'qualitative' && (
                        <QualitativeSection
                            loanId={record.id}
                            qualitative={qualitative}
                            choices={options.qualitativeChoices}
                            canEdit={canEdit}
                        />
                    )}
                    {active === 'memorandum' && (
                        <MemorandumSection
                            loanId={record.id}
                            memorandum={memorandum}
                            bindings={options.bindings}
                            canEdit={canEdit}
                        />
                    )}
                    {active === 'administration' && (
                        <AdministrationSection
                            loanId={record.id}
                            administration={administration}
                            canEdit={canEdit}
                        />
                    )}
                </div>
            </div>

            <ConfirmDialog
                open={confirming}
                onOpenChange={(open) => !sending && setConfirming(open)}
                title="Ajukan ke komite?"
                description={
                    <>
                        Berkas <strong>{record.application_code}</strong>{' '}
                        diteruskan ke komite kredit dan lembar analisanya tidak
                        bisa diubah lagi.
                    </>
                }
                confirmLabel="Ajukan"
                loading={sending}
                onConfirm={() =>
                    router.post(
                        `/credit-analysis/${record.id}/submit`,
                        {},
                        {
                            onStart: () => setSending(true),
                            onFinish: () => {
                                setSending(false);
                                setConfirming(false);
                            },
                        },
                    )
                }
            />
        </>
    );
}
