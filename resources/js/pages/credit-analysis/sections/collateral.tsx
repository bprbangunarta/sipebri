import { useForm } from '@inertiajs/react';
import { Landmark } from 'lucide-react';
import { Combobox } from '@/components/ui/combobox';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { EmptyState, Card } from '@/components/ui/misc';
import { rupiah } from '@/lib/format';
import {
    MoneyField,
    Panel,
    SaveBar,
    Stat,
    TextField,
} from '@/pages/credit-analysis/parts';
import type { CollateralRow } from '@/pages/credit-analysis/types';

const KIND_LABELS: Record<string, string> = {
    vehicle: 'Kendaraan',
    land: 'Tanah',
    other: 'Lainnya',
};

const VEHICLE_FIELDS = [
    ['Merek Kendaraan', 'brand'],
    ['Tipe Kendaraan', 'vehicle_type'],
    ['Tahun Kendaraan', 'year'],
    ['Nomor Rangka', 'chassis_number'],
    ['Nomor Mesin', 'engine_number'],
    ['Nomor Polisi', 'plate_number'],
    ['Warna Kendaraan', 'color'],
] as const;

export function CollateralSection({
    loanId,
    rows,
    kinds,
    canEdit,
}: {
    loanId: number;
    rows: CollateralRow[];
    kinds: string[];
    canEdit: boolean;
}) {
    const form = useForm<{ rows: CollateralRow[] }>({ rows });
    const set = (index: number, key: keyof CollateralRow, value: string) =>
        form.setData(
            'rows',
            form.data.rows.map((r, i) =>
                i === index ? { ...r, [key]: value } : r,
            ),
        );
    const error = (index: number, key: string) =>
        (form.errors as Record<string, string>)[`rows.${index}.${key}`];
    const totalMarket = form.data.rows.reduce(
        (t, r) => t + Number(r.market_value || 0),
        0,
    );
    const totalAppraisal = form.data.rows.reduce(
        (t, r) => t + Number(r.appraisal_value || 0),
        0,
    );

    if (rows.length === 0) {
        return (
            <Card>
                <EmptyState
                    icon={<Landmark />}
                    title="Berkas ini belum punya jaminan"
                    description="Tambahkan jaminan lewat halaman pengajuan bila produk ini membutuhkannya."
                />
            </Card>
        );
    }

    return (
        <div className="flex flex-col gap-3">
            {form.data.rows.map((row, index) => (
                <Panel
                    key={row.collateral_id}
                    title={`${row.owner_name ?? '–'} · ${row.document_number ?? '–'}`}
                    hint={row.label ?? undefined}
                >
                    <Field label="Jenis Pemeriksaan">
                        <Combobox
                            searchable={false}
                            options={kinds.map((k) => ({
                                value: k,
                                label: KIND_LABELS[k] ?? k,
                            }))}
                            value={row.kind}
                            onChange={(v) =>
                                canEdit && set(index, 'kind', v ?? 'other')
                            }
                        />
                    </Field>
                    <Stat
                        label="Taksasi CBS"
                        value={rupiah(row.cbs_appraisal)}
                    />
                    {row.kind === 'vehicle' &&
                        VEHICLE_FIELDS.map(([label, key]) => (
                            <TextField
                                key={key}
                                label={label}
                                value={row[key]}
                                onChange={(v) => set(index, key, v)}
                                error={error(index, key)}
                                disabled={!canEdit}
                                maxLength={key === 'year' ? 4 : 100}
                            />
                        ))}
                    {row.kind === 'land' && (
                        <>
                            <Field
                                label="Luas Tanah (m²)"
                                error={error(index, 'land_area')}
                            >
                                <Input
                                    type="number"
                                    min={0}
                                    disabled={!canEdit}
                                    value={row.land_area}
                                    onChange={(e) =>
                                        set(index, 'land_area', e.target.value)
                                    }
                                    className="text-right tabular-nums"
                                />
                            </Field>
                            <TextField
                                label="Lokasi Agunan"
                                value={row.location}
                                onChange={(v) => set(index, 'location', v)}
                                error={error(index, 'location')}
                                disabled={!canEdit}
                                className="sm:col-span-2"
                            />
                        </>
                    )}
                    <MoneyField
                        label="Nilai Pasar"
                        value={row.market_value}
                        onChange={(v) => set(index, 'market_value', v)}
                        error={error(index, 'market_value')}
                        disabled={!canEdit}
                    />
                    <MoneyField
                        label="Nilai Taksasi"
                        value={row.appraisal_value}
                        onChange={(v) => set(index, 'appraisal_value', v)}
                        error={error(index, 'appraisal_value')}
                        disabled={!canEdit}
                    />
                    <TextField
                        label="Catatan Pemeriksaan"
                        value={row.notes}
                        onChange={(v) => set(index, 'notes', v)}
                        error={error(index, 'notes')}
                        disabled={!canEdit}
                        maxLength={1000}
                        className="sm:col-span-2 lg:col-span-3"
                    />
                </Panel>
            ))}
            <Panel title="Ringkasan Agunan">
                <Stat
                    label="Jumlah Agunan"
                    value={String(form.data.rows.length)}
                />
                <Stat label="Total Nilai Pasar" value={rupiah(totalMarket)} />
                <Stat
                    label="Total Nilai Taksasi"
                    value={rupiah(totalAppraisal)}
                    strong
                />
            </Panel>
            {canEdit && (
                <SaveBar
                    processing={form.processing}
                    dirty={form.isDirty}
                    onReset={() => form.reset()}
                    onSave={() =>
                        form.put(`/credit-analysis/${loanId}/collaterals`, {
                            preserveScroll: true,
                            onSuccess: () => form.setDefaults(),
                        })
                    }
                />
            )}
        </div>
    );
}
