import { useForm } from '@inertiajs/react';
import { rupiah } from '@/lib/format';
import {
    MoneyField,
    Panel,
    SaveBar,
    Stat,
} from '@/pages/credit-analysis/parts';
import type { Administration } from '@/pages/credit-analysis/types';

const GROUPS = [
    {
        title: 'Biaya Kredit',
        fields: [
            ['Administrasi', 'administration'],
            ['Provisi', 'provision'],
            ['Materai', 'stamp_duty'],
            ['Transaksi Kredit', 'credit_transaction'],
        ],
    },
    {
        title: 'Asuransi Jiwa',
        fields: [
            ['Asr. Jiwa Menurun 1', 'declining_life_insurance_1'],
            ['Asr. Jiwa Menurun 2', 'declining_life_insurance_2'],
            ['Asr. Jiwa Menurun 3', 'declining_life_insurance_3'],
            ['Asr. Jiwa Tetap 1', 'flat_life_insurance_1'],
            ['Asr. Jiwa Tetap 2', 'flat_life_insurance_2'],
            ['Pertanggungan Asr. Jiwa', 'life_insurance'],
        ],
    },
    {
        title: 'Agunan & Pengikatan',
        fields: [
            ['Asr. Kendaraan Bermotor', 'motorcycle_insurance'],
            ['Pajak STNK', 'vehicle_tax'],
            ['Proses SHM', 'shm_processing'],
            ['Polis dan Materai', 'policy_stamp'],
            ['Proses APHT', 'apht_processing'],
            ['Biaya Fiducia', 'fiducia_fee'],
        ],
    },
] as const;

export function AdministrationSection({
    loanId,
    administration,
    canEdit,
}: {
    loanId: number;
    administration: Administration;
    canEdit: boolean;
}) {
    const keys = GROUPS.flatMap((g) => g.fields.map(([, k]) => k));
    const form = useForm<Record<string, string | number>>(
        Object.fromEntries(keys.map((k) => [k, administration[k] ?? 0])),
    );
    const total = keys.reduce((t, k) => t + Number(form.data[k] || 0), 0);

    return (
        <div className="flex flex-col gap-3">
            {GROUPS.map((group) => (
                <Panel key={group.title} title={group.title}>
                    {group.fields.map(([label, key]) => (
                        <MoneyField
                            key={key}
                            label={label}
                            value={form.data[key]}
                            onChange={(v) => form.setData(key, v)}
                            error={form.errors[key]}
                            disabled={!canEdit}
                        />
                    ))}
                </Panel>
            ))}
            <Panel title="Ringkasan Biaya" columns="">
                <Stat
                    label="Total Biaya Administrasi"
                    value={rupiah(total)}
                    strong
                />
            </Panel>
            {canEdit && (
                <SaveBar
                    processing={form.processing}
                    dirty={form.isDirty}
                    note={administration.updated_at}
                    onReset={() => form.reset()}
                    onSave={() =>
                        form.put(`/credit-analysis/${loanId}/administration`, {
                            preserveScroll: true,
                            onSuccess: () => form.setDefaults(),
                        })
                    }
                />
            )}
        </div>
    );
}
