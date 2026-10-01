import { Head, router, useForm } from '@inertiajs/react';
import { ArrowLeft, FileText, Gavel } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { ConfirmDialog } from '@/components/ui/dialog';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Badge, Card, PageHeader } from '@/components/ui/misc';
import type { BadgeTone } from '@/components/ui/misc';
import { rupiah } from '@/lib/format';
import {
    MoneyField,
    Panel,
    Stat,
    TextField,
} from '@/pages/credit-analysis/parts';

type Step = {
    id: number;
    sort: number;
    label: string | null;
    role: string;
    individual: boolean;
    decision: string | null;
    decision_label: string | null;
    decided_by: string | null;
    decided_at: string | null;
    method_label: string | null;
    amount: number;
    tenor: number;
    interest_rate: string;
    provision_rate: string;
    admin_rate: string;
    max_amount: number;
    rc_ratio: string;
    note: string | null;
    pending: boolean;
};

type Props = {
    record: {
        id: number;
        application_code: string;
        full_name: string;
        nik: string;
        requested_amount: number;
        requested_tenor: number;
        usage_type: string | null;
        application_date: string;
        status: string;
        status_label: string;
        product_label: string | null;
        office_label: string | null;
        supervisor_name: string | null;
        surveyor_name: string | null;
        submitted_at: string | null;
        submitted_by: string | null;
        exception: string | null;
        decision_note: string | null;
        approved_amount: number;
        approved_tenor: number;
        approved_rate: string;
    };
    basis: {
        capacity: number;
        rc_threshold: number;
        max_amount: number;
        rc_ratio: number;
        amount: number;
        tenor: number;
        interest_rate: number;
        provision_rate: number;
        admin_rate: number;
        method_label: string | null;
        appraisal_total: number;
    };
    steps: Step[];
    flow: {
        pending_label: string | null;
        my_turn: boolean;
        allowed: string[];
        blocked_reason: string | null;
    };
    form: {
        method_id: number | null;
        amount: number;
        tenor: number;
        interest_rate: string | number;
        provision_rate: string | number;
        admin_rate: string | number;
    };
    methodOptions: { value: number; label: string }[];
    decisionLabels: Record<string, string>;
};

const TONES: Record<string, BadgeTone> = {
    forward: 'info',
    approve: 'success',
    reject: 'danger',
    cancel: 'neutral',
};
const STATUS_TONES: Record<string, BadgeTone> = {
    committee: 'info',
    approved: 'success',
    rejected: 'danger',
    cancelled: 'neutral',
};

export default function ApprovalShow({
    record,
    basis,
    steps,
    flow,
    form: initial,
    methodOptions,
    decisionLabels,
}: Props) {
    const form = useForm({
        decision: flow.allowed[0] ?? '',
        method_id: initial.method_id === null ? '' : String(initial.method_id),
        amount: String(initial.amount),
        tenor: String(initial.tenor),
        interest_rate: String(initial.interest_rate),
        provision_rate: String(initial.provision_rate),
        admin_rate: String(initial.admin_rate),
        note: '',
    });
    const [confirming, setConfirming] = useState(false);
    const decided = record.status !== 'committee';
    const final = form.data.decision !== '' && form.data.decision !== 'forward';

    const columns: Column<Step>[] = [
        {
            key: 'order',
            header: '#',
            narrow: true,
            className: 'text-muted tabular-nums',
            cell: (s) => s.sort,
        },
        {
            key: 'tier',
            header: 'Jenjang',
            cell: (s) => (
                <>
                    <p className="font-medium">{s.label ?? '–'}</p>
                    <p className="text-xs text-muted">
                        {s.role}
                        {s.individual ? ' · perorangan' : ''}
                    </p>
                </>
            ),
        },
        {
            key: 'decision',
            header: 'Keputusan',
            cell: (s) =>
                s.decision ? (
                    <>
                        <Badge tone={TONES[s.decision] ?? 'neutral'}>
                            {s.decision_label}
                        </Badge>
                        <p className="text-xs text-muted">
                            {s.decided_by} · {s.decided_at}
                        </p>
                    </>
                ) : (
                    <Badge tone="warning">
                        {s.pending &&
                        s.label === flow.pending_label?.split(' · ')[0]
                            ? 'Menunggu'
                            : 'Belum giliran'}
                    </Badge>
                ),
        },
        {
            key: 'terms',
            header: 'Syarat yang diputus',
            hideBelow: 'md',
            cell: (s) =>
                s.decision ? (
                    <span className="text-xs tabular-nums">
                        {rupiah(s.amount)} · {s.tenor} bln · bunga{' '}
                        {s.interest_rate}% · provisi {s.provision_rate}% · admin{' '}
                        {s.admin_rate}%
                        {s.method_label ? ` · ${s.method_label}` : ''}
                        <span className="block text-muted">
                            RC {s.rc_ratio}% dari maks. {rupiah(s.max_amount)}
                        </span>
                    </span>
                ) : (
                    '–'
                ),
        },
        {
            key: 'note',
            header: 'Catatan',
            hideBelow: 'lg',
            cell: (s) => s.note ?? '–',
        },
    ];

    return (
        <>
            <Head title={`Persetujuan ${record.application_code}`} />
            <PageHeader
                title={`Persetujuan ${record.application_code}`}
                description={`${record.full_name} · ${record.nik}`}
                actions={
                    <>
                        <Badge tone={STATUS_TONES[record.status] ?? 'neutral'}>
                            {record.status_label}
                        </Badge>
                        <Button
                            variant="outline"
                            onClick={() => router.visit('/approvals')}
                        >
                            <ArrowLeft /> Kembali
                        </Button>
                        <Button
                            variant="outline"
                            onClick={() =>
                                router.visit(`/credit-analysis/${record.id}`)
                            }
                        >
                            <FileText /> Lembar analisa
                        </Button>
                    </>
                }
            />

            {record.exception && (
                <p
                    role="status"
                    className="mb-3 rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800"
                >
                    <strong>Pengecualian.</strong> {record.exception}
                </p>
            )}

            <div className="flex flex-col gap-3">
                <Panel
                    title="Pengajuan dan Usulan Analis"
                    hint={
                        record.submitted_at
                            ? `Diajukan ${record.submitted_at} oleh ${record.submitted_by}`
                            : undefined
                    }
                    columns="sm:grid-cols-2 lg:grid-cols-4"
                >
                    <Stat label="Produk" value={record.product_label ?? '–'} />
                    <Stat label="Kantor" value={record.office_label ?? '–'} />
                    <Stat
                        label="Kasi Analis"
                        value={record.supervisor_name ?? '–'}
                    />
                    <Stat
                        label="Surveyor / Analis"
                        value={record.surveyor_name ?? '–'}
                    />
                    <Stat
                        label="Plafon Diajukan"
                        value={`${rupiah(record.requested_amount)} · ${record.requested_tenor} bln`}
                    />
                    <Stat
                        label="Usulan Analis"
                        value={`${rupiah(basis.amount)} · ${basis.tenor} bln`}
                        strong
                    />
                    <Stat
                        label="Bunga / Provisi / Admin"
                        value={`${basis.interest_rate}% / ${basis.provision_rate}% / ${basis.admin_rate}%`}
                    />
                    <Stat
                        label="Metode Bunga"
                        value={basis.method_label ?? '–'}
                    />
                    <Stat
                        label="Taksasi Agunan"
                        value={rupiah(basis.appraisal_total)}
                    />
                    <Stat
                        label="Kemampuan per Bulan"
                        value={rupiah(basis.capacity)}
                    />
                    <Stat
                        label={`Plafon Maksimum (RC ${basis.rc_threshold}%)`}
                        value={rupiah(basis.max_amount)}
                    />
                    <Stat
                        label="Usulan terhadap Maksimum"
                        value={`${basis.rc_ratio}%`}
                        strong
                    />
                </Panel>

                <div>
                    <h2 className="mb-2 text-sm font-semibold">Jalur Komite</h2>
                    <DataTable
                        rows={steps}
                        rowKey={(s) => s.id}
                        columns={columns}
                        empty={{
                            icon: <Gavel />,
                            title: 'Belum ada jalur komite',
                        }}
                    />
                </div>

                {decided && (
                    <Panel
                        title="Hasil Keputusan"
                        columns="sm:grid-cols-2 lg:grid-cols-4"
                    >
                        <Stat
                            label="Keputusan"
                            value={record.status_label}
                            strong
                        />
                        {record.status === 'approved' && (
                            <>
                                <Stat
                                    label="Plafon Disetujui"
                                    value={rupiah(record.approved_amount)}
                                />
                                <Stat
                                    label="Jangka Waktu"
                                    value={`${record.approved_tenor} bln`}
                                />
                                <Stat
                                    label="Suku Bunga"
                                    value={`${record.approved_rate}%`}
                                />
                            </>
                        )}
                        {record.decision_note && (
                            <Stat
                                label="Catatan"
                                value={record.decision_note}
                            />
                        )}
                    </Panel>
                )}

                {!decided && flow.my_turn && flow.allowed.length > 0 && (
                    <Panel
                        title="Keputusan Anda"
                        hint={`Jenjang ${flow.pending_label}`}
                        columns="sm:grid-cols-2 lg:grid-cols-4"
                    >
                        <Field
                            label="Keputusan"
                            required
                            error={form.errors.decision}
                        >
                            <Combobox
                                searchable={false}
                                options={flow.allowed.map((d) => ({
                                    value: d,
                                    label: decisionLabels[d] ?? d,
                                }))}
                                value={form.data.decision}
                                onChange={(v) =>
                                    form.setData('decision', v ?? '')
                                }
                                invalid={!!form.errors.decision}
                            />
                        </Field>
                        <Field
                            label="Metode Bunga"
                            required
                            error={form.errors.method_id}
                        >
                            <Combobox
                                options={methodOptions}
                                value={form.data.method_id}
                                onChange={(v) =>
                                    form.setData('method_id', v ?? '')
                                }
                                invalid={!!form.errors.method_id}
                            />
                        </Field>
                        <MoneyField
                            label="Plafon"
                            value={form.data.amount}
                            onChange={(v) => form.setData('amount', v)}
                            error={form.errors.amount}
                        />
                        <Field
                            label="Jangka Waktu (Bulan)"
                            error={form.errors.tenor}
                        >
                            <Input
                                type="number"
                                min={1}
                                value={form.data.tenor}
                                onChange={(e) =>
                                    form.setData('tenor', e.target.value)
                                }
                                className="text-right tabular-nums"
                            />
                        </Field>
                        {(
                            [
                                'interest_rate',
                                'provision_rate',
                                'admin_rate',
                            ] as const
                        ).map((key) => (
                            <Field
                                key={key}
                                label={
                                    key === 'interest_rate'
                                        ? 'Suku Bunga (%)'
                                        : key === 'provision_rate'
                                          ? 'Biaya Provisi (%)'
                                          : 'Biaya Admin (%)'
                                }
                                error={form.errors[key]}
                            >
                                <Input
                                    type="number"
                                    min={0}
                                    max={100}
                                    step="0.01"
                                    value={form.data[key]}
                                    onChange={(e) =>
                                        form.setData(key, e.target.value)
                                    }
                                    className="text-right tabular-nums"
                                />
                            </Field>
                        ))}
                        <TextField
                            label="Catatan"
                            value={form.data.note}
                            onChange={(v) => form.setData('note', v)}
                            error={form.errors.note}
                            className="sm:col-span-2 lg:col-span-4"
                        />
                        <div className="sm:col-span-2 lg:col-span-4">
                            <Button
                                disabled={
                                    form.data.decision === '' || form.processing
                                }
                                loading={form.processing}
                                onClick={() => setConfirming(true)}
                            >
                                Simpan keputusan
                            </Button>
                        </div>
                    </Panel>
                )}

                {!decided &&
                    !(flow.my_turn && flow.allowed.length > 0) &&
                    flow.blocked_reason && (
                        <Card>
                            <p className="p-3 text-sm text-muted">
                                {flow.blocked_reason}
                            </p>
                        </Card>
                    )}
            </div>

            <ConfirmDialog
                open={confirming}
                onOpenChange={setConfirming}
                title={`${decisionLabels[form.data.decision] ?? 'Simpan keputusan'}?`}
                description={
                    final ? (
                        <>
                            Berkas <strong>{record.application_code}</strong>{' '}
                            diputus dan tidak bisa diubah lagi.
                        </>
                    ) : (
                        <>
                            Berkas <strong>{record.application_code}</strong>{' '}
                            diteruskan ke jenjang berikutnya.
                        </>
                    )
                }
                confirmLabel="Ya, simpan"
                onConfirm={() =>
                    form.post(`/approvals/${record.id}/decide`, {
                        preserveScroll: true,
                        onFinish: () => setConfirming(false),
                    })
                }
            />
        </>
    );
}
