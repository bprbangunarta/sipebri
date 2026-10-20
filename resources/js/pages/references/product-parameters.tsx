import { Head, router, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import type { ReactNode } from 'react';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/choice';
import { Combobox } from '@/components/ui/combobox';
import { CurrencyInput } from '@/components/ui/currency-input';
import { Field } from '@/components/ui/field';
import { FilteredInput } from '@/components/ui/filtered-input';
import { Input } from '@/components/ui/input';
import { MaskedInput } from '@/components/ui/masked-input';
import { Card, CardBody, CardHeader, PageHeader } from '@/components/ui/misc';

type Option = { id: number; name: string };

// The server takes a plain decimal with a dot (`numeric`).
const DECIMAL_CHARACTER = /^[0-9.]$/;
type Parameter = {
    min_amount: number | null;
    max_amount: number | null;
    min_tenor: number | null;
    max_tenor: number | null;
    interest_rate: string | null;
    provision_rate: string | null;
    admin_rate: string | null;
    rc_threshold: string | null;
    default_method_id: number | null;
    default_installment_id: number | null;
    allowed_method_ids: number[] | null;
    allowed_installment_ids: number[] | null;
    collateral_required: boolean;
    decree: string | null;
    note: string | null;
};

type Props = {
    product: { id: number; code: string; alias: string; name: string };
    parameter: Parameter | null;
    methods: Option[];
    installments: Option[];
    canManage: boolean;
};

function Section({ title, children }: { title: string; children: ReactNode }) {
    return (
        <Card>
            <CardHeader title={title} />
            <CardBody className="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">{children}</CardBody>
        </Card>
    );
}

function CheckList({ options, value, onChange, disabled }: { options: Option[]; value: number[]; onChange: (v: number[]) => void; disabled: boolean }) {
    return (
        <div className="flex flex-wrap gap-x-4 gap-y-1.5 rounded-md border border-line p-2">
            {options.map((o) => (
                <Checkbox
                    key={o.id}
                    label={o.name}
                    disabled={disabled}
                    checked={value.includes(o.id)}
                    onCheckedChange={(checked) => onChange(checked === true ? [...value, o.id] : value.filter((v) => v !== o.id))}
                />
            ))}
        </div>
    );
}

export default function ProductParameters({ product, parameter, methods, installments, canManage }: Props) {
    const form = useForm({
        min_amount: parameter?.min_amount ?? null,
        max_amount: parameter?.max_amount ?? null,
        min_tenor: parameter?.min_tenor == null ? '' : String(parameter.min_tenor),
        max_tenor: parameter?.max_tenor == null ? '' : String(parameter.max_tenor),
        interest_rate: parameter?.interest_rate ?? '',
        provision_rate: parameter?.provision_rate ?? '',
        admin_rate: parameter?.admin_rate ?? '',
        rc_threshold: parameter?.rc_threshold ?? '',
        default_method_id: parameter?.default_method_id ? String(parameter.default_method_id) : '',
        default_installment_id: parameter?.default_installment_id ? String(parameter.default_installment_id) : '',
        allowed_method_ids: parameter?.allowed_method_ids ?? ([] as number[]),
        allowed_installment_ids: parameter?.allowed_installment_ids ?? ([] as number[]),
        collateral_required: parameter?.collateral_required ?? false,
        decree: parameter?.decree ?? '',
        note: parameter?.note ?? '',
        reason: '',
    });
    const { data, setData, errors } = form;
    const rate = (name: 'interest_rate' | 'provision_rate' | 'admin_rate' | 'rc_threshold') => (
        <FilteredInput
            id={name}
            inputMode="decimal"
            maxLength={7}
            allowed={DECIMAL_CHARACTER}
            disabled={!canManage}
            value={data[name]}
            onValueChange={(v) => setData(name, v)}
        />
    );
    const tenor = (name: 'min_tenor' | 'max_tenor') => (
        <MaskedInput id={name} mask={'9'.repeat(3)} disabled={!canManage} value={data[name]} onValueChange={(v) => setData(name, v)} />
    );
    const amount = (name: 'min_amount' | 'max_amount') => (
        <CurrencyInput id={name} disabled={!canManage} value={data[name]} onValueChange={(v) => setData(name, v)} />
    );
    const choices = (all: Option[], allowed: number[]) =>
        (allowed.length ? all.filter((o) => allowed.includes(o.id)) : all).map((o) => ({ value: o.id, label: o.name }));

    return (
        <>
            <Head title={`Parameter – ${product.alias}`} />
            <PageHeader
                title={`${product.alias} – ${product.name}`}
                description="Batas sesuai SK Direksi. Pengajuan kredit diperiksa terhadap batas ini."
                actions={
                    <>
                        <Button variant="outline" onClick={() => router.visit('/references/products')}>
                            <ArrowLeft /> Kembali
                        </Button>
                        {canManage && (
                            <Button
                                loading={form.processing}
                                onClick={() =>
                                    form.put(`/references/products/${product.id}/parameters`, { preserveScroll: true, onSuccess: () => setData('reason', '') })
                                }
                            >
                                <Save /> Simpan parameter
                            </Button>
                        )}
                    </>
                }
            />

            <div className="flex flex-col gap-3">
                <Section title="Batas">
                    <Field label="Plafon minimum" error={errors.min_amount}>
                        {amount('min_amount')}
                    </Field>
                    <Field label="Plafon maksimum" error={errors.max_amount} hint="Kosongkan bila tidak dibatasi. Nol bukan batas.">
                        {amount('max_amount')}
                    </Field>
                    <Field label="Tenor minimum (bulan)" error={errors.min_tenor}>
                        {tenor('min_tenor')}
                    </Field>
                    <Field label="Tenor maksimum (bulan)" error={errors.max_tenor}>
                        {tenor('max_tenor')}
                    </Field>
                </Section>

                <Section title="Bunga & biaya (% dari plafon)">
                    <Field label="Suku bunga (%)" error={errors.interest_rate}>
                        {rate('interest_rate')}
                    </Field>
                    <Field label="Provisi (%)" error={errors.provision_rate}>
                        {rate('provision_rate')}
                    </Field>
                    <Field label="Biaya administrasi (%)" error={errors.admin_rate}>
                        {rate('admin_rate')}
                    </Field>
                    <Field label="RC maksimum (%)" error={errors.rc_threshold} hint="Batas kemampuan bayar (repayment capacity), antara 1 dan 100.">
                        {rate('rc_threshold')}
                    </Field>
                </Section>

                <Section title="Metode bunga & sistem angsuran">
                    <Field label="Metode bunga yang diizinkan" className="sm:col-span-2" hint="Tidak ada yang dicentang = semua diizinkan.">
                        <CheckList options={methods} value={data.allowed_method_ids} onChange={(v) => setData('allowed_method_ids', v)} disabled={!canManage} />
                    </Field>
                    <Field label="Metode bunga bawaan" error={errors.default_method_id} className="sm:col-span-2">
                        <Combobox
                            clearable
                            options={choices(methods, data.allowed_method_ids)}
                            value={data.default_method_id}
                            onChange={(v) => setData('default_method_id', v ?? '')}
                            invalid={!!errors.default_method_id}
                        />
                    </Field>
                    <Field label="Sistem angsuran yang diizinkan" className="sm:col-span-2" hint="Tidak ada yang dicentang = semua diizinkan.">
                        <CheckList
                            options={installments}
                            value={data.allowed_installment_ids}
                            onChange={(v) => setData('allowed_installment_ids', v)}
                            disabled={!canManage}
                        />
                    </Field>
                    <Field label="Sistem angsuran bawaan" error={errors.default_installment_id} className="sm:col-span-2">
                        <Combobox
                            clearable
                            options={choices(installments, data.allowed_installment_ids)}
                            value={data.default_installment_id}
                            onChange={(v) => setData('default_installment_id', v ?? '')}
                            invalid={!!errors.default_installment_id}
                        />
                    </Field>
                </Section>

                <Section title="Ketentuan lain">
                    <Field label="Nomor SK Direksi" error={errors.decree}>
                        <Input id="decree" disabled={!canManage} value={data.decree} maxLength={100} onChange={(e) => setData('decree', e.target.value)} />
                    </Field>
                    <Field label="Catatan" error={errors.note} className="sm:col-span-2">
                        <Input id="note" disabled={!canManage} value={data.note} maxLength={255} onChange={(e) => setData('note', e.target.value)} />
                    </Field>
                    {canManage && (
                        <Field
                            label="Alasan perubahan"
                            required
                            error={errors.reason}
                            className="sm:col-span-2 lg:col-span-4"
                            hint="Wajib setiap parameter diubah. Tersimpan di audit log bersama nilai sebelum dan sesudahnya."
                        >
                            <Input
                                id="reason"
                                maxLength={255}
                                value={data.reason}
                                onChange={(e) => setData('reason', e.target.value)}
                                aria-invalid={Boolean(errors.reason)}
                            />
                        </Field>
                    )}
                    <Checkbox
                        className="h-8 items-center self-end"
                        label="Agunan wajib"
                        disabled={!canManage}
                        checked={data.collateral_required}
                        onCheckedChange={(checked) => setData('collateral_required', checked === true)}
                    />
                </Section>
            </div>
        </>
    );
}
