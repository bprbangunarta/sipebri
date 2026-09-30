import { Head, router, useForm } from '@inertiajs/react';
import { ArrowLeft, Save } from 'lucide-react';
import type { ReactNode } from 'react';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Card, PageHeader } from '@/components/ui/misc';
import { rupiah } from '@/lib/format';

type Option = { id: number; name: string };
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
            <h2 className="border-b border-line px-3 py-2 text-sm font-semibold">
                {title}
            </h2>
            <div className="grid gap-3 p-3 sm:grid-cols-2 lg:grid-cols-4">
                {children}
            </div>
        </Card>
    );
}

function CheckList({
    options,
    value,
    onChange,
    disabled,
}: {
    options: Option[];
    value: number[];
    onChange: (v: number[]) => void;
    disabled: boolean;
}) {
    return (
        <div className="flex flex-wrap gap-x-4 gap-y-1.5 rounded-md border border-line p-2">
            {options.map((o) => (
                <label key={o.id} className="flex items-center gap-1.5 text-xs">
                    <input
                        type="checkbox"
                        className="accent-primary"
                        disabled={disabled}
                        checked={value.includes(o.id)}
                        onChange={(e) =>
                            onChange(
                                e.target.checked
                                    ? [...value, o.id]
                                    : value.filter((v) => v !== o.id),
                            )
                        }
                    />
                    {o.name}
                </label>
            ))}
        </div>
    );
}

export default function ProductParameters({
    product,
    parameter,
    methods,
    installments,
    canManage,
}: Props) {
    const form = useForm({
        min_amount: parameter?.min_amount ?? '',
        max_amount: parameter?.max_amount ?? '',
        min_tenor: parameter?.min_tenor ?? '',
        max_tenor: parameter?.max_tenor ?? '',
        interest_rate: parameter?.interest_rate ?? '',
        provision_rate: parameter?.provision_rate ?? '',
        admin_rate: parameter?.admin_rate ?? '',
        rc_threshold: parameter?.rc_threshold ?? '',
        default_method_id: parameter?.default_method_id
            ? String(parameter.default_method_id)
            : '',
        default_installment_id: parameter?.default_installment_id
            ? String(parameter.default_installment_id)
            : '',
        allowed_method_ids: parameter?.allowed_method_ids ?? ([] as number[]),
        allowed_installment_ids:
            parameter?.allowed_installment_ids ?? ([] as number[]),
        collateral_required: parameter?.collateral_required ?? false,
        decree: parameter?.decree ?? '',
        note: parameter?.note ?? '',
    });
    const { data, setData, errors } = form;
    const num = (
        name:
            | 'min_amount'
            | 'max_amount'
            | 'min_tenor'
            | 'max_tenor'
            | 'interest_rate'
            | 'provision_rate'
            | 'admin_rate'
            | 'rc_threshold',
        step?: string,
    ) => ({
        id: name,
        type: 'number',
        step,
        min: 0,
        disabled: !canManage,
        value: data[name] as string | number,
        onChange: (e: React.ChangeEvent<HTMLInputElement>) =>
            setData(name, e.target.value),
        'aria-invalid': !!errors[name],
    });
    const choices = (all: Option[], allowed: number[]) =>
        (allowed.length ? all.filter((o) => allowed.includes(o.id)) : all).map(
            (o) => ({ value: o.id, label: o.name }),
        );

    return (
        <>
            <Head title={`Parameters – ${product.alias}`} />
            <PageHeader
                title={`${product.alias} – ${product.name}`}
                description="Board-decree limits. They are references: loan applications are checked against them."
                actions={
                    <>
                        <Button
                            variant="outline"
                            onClick={() =>
                                router.visit('/master-data/products')
                            }
                        >
                            <ArrowLeft /> Back
                        </Button>
                        {canManage && (
                            <Button
                                loading={form.processing}
                                onClick={() =>
                                    form.put(
                                        `/master-data/products/${product.id}/parameters`,
                                        { preserveScroll: true },
                                    )
                                }
                            >
                                <Save /> Save parameters
                            </Button>
                        )}
                    </>
                }
            />

            <div className="flex flex-col gap-3">
                <Section title="Limits">
                    <Field
                        label="Minimum amount (IDR)"
                        error={errors.min_amount}
                        hint={rupiah(
                            data.min_amount === ''
                                ? null
                                : Number(data.min_amount),
                        )}
                    >
                        <Input {...num('min_amount')} />
                    </Field>
                    <Field
                        label="Maximum amount (IDR)"
                        error={errors.max_amount}
                        hint={rupiah(
                            data.max_amount === ''
                                ? null
                                : Number(data.max_amount),
                        )}
                    >
                        <Input {...num('max_amount')} />
                    </Field>
                    <Field
                        label="Minimum tenor (months)"
                        error={errors.min_tenor}
                    >
                        <Input {...num('min_tenor')} />
                    </Field>
                    <Field
                        label="Maximum tenor (months)"
                        error={errors.max_tenor}
                    >
                        <Input {...num('max_tenor')} />
                    </Field>
                </Section>

                <Section title="Rates (% of the loan amount)">
                    <Field
                        label="Interest rate (%)"
                        error={errors.interest_rate}
                    >
                        <Input {...num('interest_rate', '0.001')} />
                    </Field>
                    <Field label="Provision (%)" error={errors.provision_rate}>
                        <Input {...num('provision_rate', '0.001')} />
                    </Field>
                    <Field label="Admin fee (%)" error={errors.admin_rate}>
                        <Input {...num('admin_rate', '0.001')} />
                    </Field>
                    <Field
                        label="Maximum RC (%)"
                        error={errors.rc_threshold}
                        hint="Repayment capacity threshold."
                    >
                        <Input {...num('rc_threshold', '0.01')} />
                    </Field>
                </Section>

                <Section title="Interest method & installment system">
                    <Field
                        label="Allowed interest methods"
                        className="sm:col-span-2"
                        hint="None ticked = all allowed."
                    >
                        <CheckList
                            options={methods}
                            value={data.allowed_method_ids}
                            onChange={(v) => setData('allowed_method_ids', v)}
                            disabled={!canManage}
                        />
                    </Field>
                    <Field
                        label="Default interest method"
                        error={errors.default_method_id}
                        className="sm:col-span-2"
                    >
                        <Combobox
                            clearable
                            options={choices(methods, data.allowed_method_ids)}
                            value={data.default_method_id}
                            onChange={(v) =>
                                setData('default_method_id', v ?? '')
                            }
                            invalid={!!errors.default_method_id}
                        />
                    </Field>
                    <Field
                        label="Allowed installment systems"
                        className="sm:col-span-2"
                        hint="None ticked = all allowed."
                    >
                        <CheckList
                            options={installments}
                            value={data.allowed_installment_ids}
                            onChange={(v) =>
                                setData('allowed_installment_ids', v)
                            }
                            disabled={!canManage}
                        />
                    </Field>
                    <Field
                        label="Default installment system"
                        error={errors.default_installment_id}
                        className="sm:col-span-2"
                    >
                        <Combobox
                            clearable
                            options={choices(
                                installments,
                                data.allowed_installment_ids,
                            )}
                            value={data.default_installment_id}
                            onChange={(v) =>
                                setData('default_installment_id', v ?? '')
                            }
                            invalid={!!errors.default_installment_id}
                        />
                    </Field>
                </Section>

                <Section title="Other terms">
                    <Field
                        label="Decree number (SK Direksi)"
                        error={errors.decree}
                    >
                        <Input
                            id="decree"
                            disabled={!canManage}
                            value={data.decree}
                            maxLength={100}
                            onChange={(e) => setData('decree', e.target.value)}
                        />
                    </Field>
                    <Field
                        label="Note"
                        error={errors.note}
                        className="sm:col-span-2"
                    >
                        <Input
                            id="note"
                            disabled={!canManage}
                            value={data.note}
                            maxLength={255}
                            onChange={(e) => setData('note', e.target.value)}
                        />
                    </Field>
                    <Field label="Collateral">
                        <label className="flex h-8 items-center gap-2 text-sm">
                            <input
                                type="checkbox"
                                className="accent-primary"
                                disabled={!canManage}
                                checked={data.collateral_required}
                                onChange={(e) =>
                                    setData(
                                        'collateral_required',
                                        e.target.checked,
                                    )
                                }
                            />
                            Collateral required
                        </label>
                    </Field>
                </Section>
            </div>
        </>
    );
}
