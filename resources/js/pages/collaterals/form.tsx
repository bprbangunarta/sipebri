import { Head, router, useForm } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { DatePicker } from '@/components/ui/date-picker';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Card, PageHeader } from '@/components/ui/misc';
import { rupiah } from '@/lib/format';

type Option = { value: string; label: string };
type Collateral = {
    id: number;
    cbs_id: string | null;
    credit_account: string | null;
    collateral_type_code: string;
    binding_type_code: string | null;
    document_number: string | null;
    description: string | null;
    owner_name: string | null;
    owner_address: string | null;
    region_code: string | null;
    guarantee_value: number;
    adjustment_value: number;
    fair_value: number;
    njop_value: number;
    appraisal_value: number;
    independent_value: number;
    independent_name: string | null;
    independent_at: string | null;
    condition_code: string | null;
    condition_date: string | null;
    insurance_code: string;
    insurance_date: string | null;
    ppap_code: string;
};

type Props = {
    collateral: Collateral | null;
    options: {
        types: Option[];
        bindings: Option[];
        conditions: Option[];
        methods: Option[];
        regions: Option[];
    };
};

const VALUES = [
    ['guarantee_value', 'Nilai jaminan'],
    ['adjustment_value', 'Nilai penyesuaian'],
    ['fair_value', 'Nilai wajar'],
    ['njop_value', 'Nilai NJOP'],
    ['appraisal_value', 'Nilai taksasi'],
    ['independent_value', 'Nilai independen'],
] as const;

function Section({
    title,
    hint,
    children,
}: {
    title: string;
    hint?: string;
    children: ReactNode;
}) {
    return (
        <Card>
            <div className="border-b border-line px-3 py-2">
                <h2 className="text-sm font-semibold">{title}</h2>
                {hint && <p className="text-xs text-muted">{hint}</p>}
            </div>
            <div className="grid gap-3 p-3 sm:grid-cols-2 lg:grid-cols-4">
                {children}
            </div>
        </Card>
    );
}

export default function CollateralForm({ collateral, options }: Props) {
    const isEdit = collateral !== null;
    const form = useForm({
        cbs_id: collateral?.cbs_id ?? '',
        credit_account: collateral?.credit_account ?? '',
        collateral_type_code: collateral?.collateral_type_code ?? '',
        binding_type_code: collateral?.binding_type_code ?? '',
        document_number: collateral?.document_number ?? '',
        description: collateral?.description ?? '',
        owner_name: collateral?.owner_name ?? '',
        owner_address: collateral?.owner_address ?? '',
        region_code: collateral?.region_code ?? '',
        guarantee_value: collateral?.guarantee_value ?? 0,
        adjustment_value: collateral?.adjustment_value ?? 0,
        fair_value: collateral?.fair_value ?? 0,
        njop_value: collateral?.njop_value ?? 0,
        appraisal_value: collateral?.appraisal_value ?? 0,
        independent_value: collateral?.independent_value ?? 0,
        independent_name: collateral?.independent_name ?? '',
        independent_at: collateral?.independent_at ?? '',
        condition_code: collateral?.condition_code ?? '',
        condition_date: collateral?.condition_date ?? '',
        insurance_code: collateral?.insurance_code ?? 'T',
        insurance_date: collateral?.insurance_date ?? '',
        ppap_code: collateral?.ppap_code ?? '1',
    });
    const { data, setData, errors } = form;
    const text = (name: keyof typeof data, upper = false) => ({
        id: name,
        className: upper ? 'uppercase' : undefined,
        value: data[name] as string,
        onChange: (e: React.ChangeEvent<HTMLInputElement>) =>
            setData(name, e.target.value as never),
        'aria-invalid': !!errors[name],
    });
    const today = new Date();

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (isEdit) {
            form.put(`/collaterals/${collateral.id}`);
        } else {
            form.post('/collaterals');
        }
    };

    return (
        <>
            <Head title={isEdit ? 'Ubah jaminan' : 'Tambah jaminan'} />
            <PageHeader
                title={
                    isEdit
                        ? `Ubah ${collateral.cbs_id ?? `jaminan #${collateral.id}`}`
                        : 'Tambah jaminan'
                }
                description={
                    isEdit
                        ? 'Kondisi, asuransi, dan taksasi wajib diisi pada tahap ini. Penilai tercatat atas nama Anda.'
                        : 'Kolom bertanda * wajib diisi'
                }
            />

            <form onSubmit={submit} className="flex flex-col gap-3" noValidate>
                <Section title="Jaminan">
                    <Field
                        label="ID agunan"
                        error={errors.cbs_id}
                        hint="ID core banking, bila sudah ada"
                    >
                        <Input {...text('cbs_id', true)} maxLength={50} />
                    </Field>
                    <Field
                        label="Rekening kredit"
                        error={errors.credit_account}
                    >
                        <Input {...text('credit_account')} maxLength={30} />
                    </Field>
                    <Field
                        label="Jenis agunan"
                        required
                        error={errors.collateral_type_code}
                    >
                        <Combobox
                            id="collateral_type_code"
                            options={options.types}
                            value={data.collateral_type_code}
                            onChange={(v) =>
                                setData('collateral_type_code', v ?? '')
                            }
                            invalid={!!errors.collateral_type_code}
                        />
                    </Field>
                    <Field
                        label="Jenis pengikatan"
                        error={errors.binding_type_code}
                    >
                        <Combobox
                            id="binding_type_code"
                            clearable
                            options={options.bindings}
                            value={data.binding_type_code}
                            onChange={(v) =>
                                setData('binding_type_code', v ?? '')
                            }
                        />
                    </Field>
                    <Field
                        label="Nomor dokumen"
                        required
                        error={errors.document_number}
                        className="lg:col-span-2"
                    >
                        <Input
                            {...text('document_number', true)}
                            maxLength={100}
                        />
                    </Field>
                    <Field
                        label="Keterangan"
                        required
                        error={errors.description}
                        className="lg:col-span-2"
                    >
                        <Input {...text('description', true)} maxLength={255} />
                    </Field>
                </Section>

                <Section title="Pemilik & lokasi">
                    <Field
                        label="Nama pemilik"
                        required
                        error={errors.owner_name}
                        className="lg:col-span-2"
                    >
                        <Input {...text('owner_name', true)} maxLength={100} />
                    </Field>
                    <Field
                        label="Lokasi (kabupaten/kota)"
                        required
                        error={errors.region_code}
                        className="lg:col-span-2"
                    >
                        <Combobox
                            id="region_code"
                            options={options.regions}
                            value={data.region_code}
                            onChange={(v) => setData('region_code', v ?? '')}
                            invalid={!!errors.region_code}
                        />
                    </Field>
                    <Field
                        label="Alamat agunan"
                        required
                        error={errors.owner_address}
                        className="sm:col-span-2 lg:col-span-4"
                    >
                        <Input
                            {...text('owner_address', true)}
                            maxLength={255}
                        />
                    </Field>
                </Section>

                <Section title="Nilai (Rp)">
                    {VALUES.map(([name, label]) => (
                        <Field
                            key={name}
                            label={label}
                            error={errors[name]}
                            hint={rupiah(data[name])}
                        >
                            <Input
                                id={name}
                                type="number"
                                min={0}
                                value={data[name]}
                                onChange={(e) =>
                                    setData(
                                        name,
                                        e.target.value as unknown as number,
                                    )
                                }
                                aria-invalid={!!errors[name]}
                            />
                        </Field>
                    ))}
                    <Field
                        label="Penilai independen"
                        error={errors.independent_name}
                    >
                        <Input
                            {...text('independent_name', true)}
                            maxLength={100}
                        />
                    </Field>
                    <Field
                        label="Tanggal penilaian independen"
                        error={errors.independent_at}
                    >
                        <DatePicker
                            id="independent_at"
                            min={new Date(2000, 0, 1)}
                            max={today}
                            value={data.independent_at}
                            onChange={(v) => setData('independent_at', v)}
                            invalid={!!errors.independent_at}
                        />
                    </Field>
                </Section>

                <Section title="Kondisi & asuransi">
                    <Field
                        label="Kondisi"
                        required={isEdit}
                        error={errors.condition_code}
                    >
                        <Combobox
                            id="condition_code"
                            clearable={!isEdit}
                            options={options.conditions}
                            value={data.condition_code}
                            onChange={(v) => setData('condition_code', v ?? '')}
                            invalid={!!errors.condition_code}
                        />
                    </Field>
                    <Field
                        label="Tanggal kondisi"
                        required={isEdit}
                        error={errors.condition_date}
                    >
                        <DatePicker
                            id="condition_date"
                            min={new Date(2000, 0, 1)}
                            max={today}
                            value={data.condition_date}
                            onChange={(v) => setData('condition_date', v)}
                            invalid={!!errors.condition_date}
                        />
                    </Field>
                    <Field
                        label="Diasuransikan"
                        required={isEdit}
                        error={errors.insurance_code}
                    >
                        <Combobox
                            id="insurance_code"
                            searchable={false}
                            options={[
                                { value: 'Y', label: 'Ya' },
                                { value: 'T', label: 'Tidak' },
                            ]}
                            value={data.insurance_code}
                            onChange={(v) =>
                                setData('insurance_code', v ?? 'T')
                            }
                        />
                    </Field>
                    <Field
                        label="Tanggal mulai asuransi"
                        required={isEdit}
                        error={errors.insurance_date}
                    >
                        <DatePicker
                            id="insurance_date"
                            min={new Date(2000, 0, 1)}
                            max={new Date(today.getFullYear() + 5, 11, 31)}
                            value={data.insurance_date}
                            onChange={(v) => setData('insurance_date', v)}
                            invalid={!!errors.insurance_date}
                        />
                    </Field>
                    <Field label="Metode penilaian" error={errors.ppap_code}>
                        <Combobox
                            id="ppap_code"
                            searchable={false}
                            options={options.methods}
                            value={data.ppap_code}
                            onChange={(v) => setData('ppap_code', v ?? '1')}
                        />
                    </Field>
                </Section>

                <div className="flex items-center justify-between gap-2">
                    <Button
                        variant="outline"
                        onClick={() => router.visit('/collaterals')}
                    >
                        Batal
                    </Button>
                    <Button type="submit" loading={form.processing}>
                        {isEdit ? 'Simpan perubahan' : 'Buat jaminan'}
                    </Button>
                </div>
            </form>
        </>
    );
}
