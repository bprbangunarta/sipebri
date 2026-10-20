import { Head, router, useForm } from '@inertiajs/react';
import { ArrowLeft, ClipboardCheck, Plus } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { Checkbox } from '@/components/ui/choice';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { DialogBody, DialogFooter, Modal } from '@/components/ui/dialog';
import { Field } from '@/components/ui/field';
import { Alert, Badge, Card, CardBody, CardHeader, PageHeader } from '@/components/ui/misc';
import { formatDate, formatNumber, formatPercent, rupiah } from '@/lib/format';
import { Stat } from '@/pages/credit-analysis/parts';
import type { CollateralSummary, Summary } from '@/pages/collaterals/appraisals/types';

type Props = {
    collateral: CollateralSummary;
    appraisals: Summary[];
    purposes: Record<string, string>;
    loans: { value: string; label: string }[];
    hasDraft: boolean;
    template: string;
    canManage: boolean;
};

const TEMPLATES: Record<string, string> = { land_building: 'Tanah dan bangunan', vehicle: 'Kendaraan', general: 'Umum' };

export default function CollateralAppraisalsIndex({ collateral, appraisals, purposes, loans, hasDraft, template, canManage }: Props) {
    const [starting, setStarting] = useState(false);
    const latest = appraisals[0];
    const form = useForm<{ purpose: string; loan_application_id: string | null; copy: boolean }>({ purpose: 'new', loan_application_id: null, copy: false });

    const start = (event: React.FormEvent) => {
        event.preventDefault();
        form.transform((data) => ({ purpose: data.purpose, loan_application_id: data.loan_application_id, copy_from: data.copy && latest ? latest.id : null }));
        form.post(`/collaterals/${collateral.id}/appraisals`, { onSuccess: () => setStarting(false) });
    };

    const columns: Column<Summary>[] = [
        { key: 'sequence', header: 'Penilaian', className: 'font-medium', cell: (a) => `#${a.sequence}` },
        {
            key: 'status',
            header: 'Status',
            cell: (a) => <Badge tone={a.status === 'final' ? 'success' : 'warning'}>{a.status === 'final' ? 'Final' : 'Draf'}</Badge>,
        },
        { key: 'purpose', header: 'Tujuan', cell: (a) => a.purpose_label },
        { key: 'effective', header: 'Tanggal efektif', cell: (a) => (a.effective_date ? formatDate(a.effective_date) : '–') },
        { key: 'valid', header: 'Berlaku sampai', cell: (a) => (a.valid_until ? formatDate(a.valid_until) : '–') },
        { key: 'market', header: 'Nilai pasar', align: 'right', className: 'tabular-nums', cell: (a) => (a.market_value > 0 ? rupiah(a.market_value) : '–') },
        {
            key: 'change',
            header: 'Perubahan nilai pasar',
            align: 'right',
            className: 'tabular-nums',
            cell: (a) =>
                a.market_change === null || a.market_change === undefined ? (
                    '–'
                ) : (
                    <span className={a.market_change < 0 ? 'text-danger' : undefined} title={`Dari penilaian ke-${a.previous_sequence}`}>
                        {a.market_change > 0 ? '+' : ''}
                        {rupiah(a.market_change)} ({formatPercent(a.market_change_percent ?? 0)})
                    </span>
                ),
        },
        {
            key: 'liquidation',
            header: 'Nilai likuidasi',
            align: 'right',
            className: 'tabular-nums',
            cell: (a) => (a.liquidation_value > 0 ? rupiah(a.liquidation_value) : '–'),
        },
        { key: 'decision', header: 'Kesimpulan', cell: (a) => a.decision_label ?? '–' },
        { key: 'appraiser', header: 'Penilai', cell: (a) => a.appraiser_name ?? '–' },
    ];

    return (
        <>
            <Head title={`Riwayat penilaian ${collateral.label}`} />
            <PageHeader
                title={`Riwayat penilaian ${collateral.label}`}
                description={`Riwayat penilaian, formulir ${TEMPLATES[template] ?? template}`}
                actions={
                    <>
                        <Button variant="outline" onClick={() => router.visit('/collaterals')}>
                            <ArrowLeft /> Jaminan
                        </Button>
                        {canManage && (
                            <Button disabled={hasDraft} onClick={() => setStarting(true)}>
                                <Plus /> Penilaian baru
                            </Button>
                        )}
                    </>
                }
            />

            {hasDraft && canManage && (
                <Alert tone="info" className="mb-3">
                    Jaminan ini masih punya draf penilaian. Finalkan atau hapus draf itu sebelum memulai yang baru.
                </Alert>
            )}

            <Card className="mb-3">
                <CardHeader title="Data tetap agunan" description="Berlaku untuk semua penilaian." />
                <CardBody className="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    {collateral.facts.map((fact) => (
                        <Stat
                            key={fact.label}
                            label={fact.label}
                            value={
                                fact.type === 'date' ? formatDate(String(fact.value)) : fact.type === 'integer' ? formatNumber(Number(fact.value)) : fact.value
                            }
                        />
                    ))}
                </CardBody>
            </Card>

            <DataTable
                rows={appraisals}
                rowKey={(a) => a.id}
                columns={columns}
                onRowClick={(a) => router.visit(`/collateral-appraisals/${a.id}`)}
                empty={{
                    icon: <ClipboardCheck />,
                    title: 'Belum ada penilaian',
                    description: 'Setiap penilaian jaminan, termasuk penilaian ulang untuk kredit baru atau top-up, tercatat di sini sebagai riwayat.',
                    action: canManage ? (
                        <Button size="sm" onClick={() => setStarting(true)}>
                            <Plus /> Penilaian baru
                        </Button>
                    ) : undefined,
                }}
            />

            <Modal
                open={starting}
                onOpenChange={(open) => !form.processing && setStarting(open)}
                persistent
                title="Penilaian baru"
                description="Penilaian lama tetap tersimpan sebagai riwayat."
            >
                <form onSubmit={start} noValidate>
                    <DialogBody className="flex flex-col gap-3">
                        <Field label="Tujuan penilaian" required error={form.errors.purpose}>
                            <Combobox
                                searchable={false}
                                options={Object.entries(purposes).map(([value, label]) => ({ value, label }))}
                                value={form.data.purpose}
                                onChange={(v) => form.setData('purpose', v ?? 'new')}
                            />
                        </Field>
                        <Field
                            label="Berkas pengajuan"
                            error={form.errors.loan_application_id}
                            hint="Debitur, produk, dan plafon diambil dari berkas ini. Kosongkan bila penilaian tidak terkait pengajuan."
                        >
                            <Combobox
                                clearable
                                placeholder="Tanpa berkas"
                                options={loans}
                                value={form.data.loan_application_id}
                                onChange={(v) => form.setData('loan_application_id', v)}
                            />
                        </Field>
                        {latest && (
                            <Checkbox
                                checked={form.data.copy}
                                onCheckedChange={(checked) => form.setData('copy', checked === true)}
                                label={`Salin jawaban dari penilaian #${latest.sequence}`}
                                description="Pemeriksaan legal dan pernyataan independensi tidak disalin: keduanya harus diperiksa lagi."
                            />
                        )}
                    </DialogBody>
                    <DialogFooter>
                        <Button variant="outline" onClick={() => setStarting(false)}>
                            Batal
                        </Button>
                        <Button type="submit" loading={form.processing}>
                            Mulai
                        </Button>
                    </DialogFooter>
                </form>
            </Modal>
        </>
    );
}
