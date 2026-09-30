import { Head, router, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    Check,
    CircleAlert,
    Plus,
    Save,
    Send,
    Trash2,
    Unlink,
} from 'lucide-react';
import type { ReactNode } from 'react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { DatePicker } from '@/components/ui/date-picker';
import { ConfirmDialog, DialogFooter, Modal } from '@/components/ui/dialog';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Badge, Card, PageHeader } from '@/components/ui/misc';
import type { BadgeTone } from '@/components/ui/misc';
import { Tip } from '@/components/ui/tooltip';
import { formatDate, rupiah } from '@/lib/format';

type Option = {
    value: string | number;
    label: string;
    description?: string;
};
type Parameter = {
    method_ids: number[];
    installment_ids: number[];
    default_method_id: number | null;
    default_installment_id: number | null;
    interest_rate: string | null;
    min_amount: number;
    max_amount: number;
    min_tenor: number;
    max_tenor: number;
    collateral_required: boolean;
};

type Loan = {
    id: number;
    application_code: string;
    application_date: string;
    status: string;
    status_label: string;
    status_tone: BadgeTone;
    nik: string;
    full_name: string;
    cif_number: string | null;
    product_id: number | null;
    office_id: number | null;
    institution_id: number | null;
    committee_path_id: number | null;
    method_id: number | null;
    installment_id: number | null;
    supervisor_id: number | null;
    usage_type: string | null;
    marketing: string | null;
    note: string | null;
    interest_rate: string | null;
    requested_amount: number;
    requested_tenor: number;
    checklist: { customer: boolean; application: boolean; collateral: boolean };
    collateral_required: boolean;
};

type CollateralRow = {
    id: number;
    cbs_id: string | null;
    collateral_type_code: string;
    owner_name: string | null;
    document_number: string | null;
    description: string | null;
    appraisal_value: number;
};

type Props = {
    loan: Loan;
    collaterals: CollateralRow[];
    collateralOptions: Option[];
    editable: boolean;
    references: {
        usageTypes: Option[];
        offices: Option[];
        products: Option[];
        institutions: Option[];
        methods: Option[];
        installments: (Option & { period_months: number })[];
        supervisors: Option[];
        collateralTypes: Option[];
        bindingTypes: Option[];
        regions: Option[];
        parameters: Record<string, Parameter>;
        categories: Record<string, Option[]>;
    };
};

function Step({
    number,
    title,
    done,
    children,
    action,
}: {
    number: number;
    title: string;
    done?: boolean;
    children: ReactNode;
    action?: ReactNode;
}) {
    return (
        <Card>
            <div className="flex items-center justify-between gap-2 border-b border-line px-3 py-2">
                <h2 className="flex items-center gap-2 text-sm font-semibold">
                    <span
                        className={`flex size-5 items-center justify-center rounded-full text-[11px] ${done ? 'bg-emerald-600 text-white' : 'bg-canvas text-muted ring-1 ring-line'}`}
                    >
                        {done ? <Check className="size-3" /> : number}
                    </span>
                    {title}
                </h2>
                {action}
            </div>
            {children}
        </Card>
    );
}

function NewCollateral({
    open,
    onOpenChange,
    loanId,
    refs,
}: {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    loanId: number;
    refs: Props['references'];
}) {
    const form = useForm({
        collateral_type_code: '',
        binding_type_code: '',
        document_number: '',
        owner_name: '',
        owner_address: '',
        region_code: '',
        description: '',
    });
    const upper = (
        name:
            | 'document_number'
            | 'owner_name'
            | 'owner_address'
            | 'description',
        label: string,
        span = '',
    ) => (
        <Field
            label={label}
            required
            error={form.errors[name]}
            className={span}
        >
            <Input
                className="uppercase"
                value={form.data[name]}
                onChange={(e) => form.setData(name, e.target.value)}
                aria-invalid={!!form.errors[name]}
            />
        </Field>
    );

    return (
        <Modal
            open={open}
            onOpenChange={onOpenChange}
            wide
            title="New collateral"
            description="It is attached to this file straight away."
        >
            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    form.post(`/loan-applications/${loanId}/collaterals/new`, {
                        preserveScroll: true,
                        onSuccess: () => {
                            form.reset();
                            onOpenChange(false);
                        },
                    });
                }}
                noValidate
            >
                <div className="grid max-h-[65vh] gap-3 overflow-auto p-4 sm:grid-cols-2">
                    <Field
                        label="Collateral type"
                        required
                        error={form.errors.collateral_type_code}
                        className="sm:col-span-2"
                    >
                        <Combobox
                            options={refs.collateralTypes}
                            value={form.data.collateral_type_code}
                            onChange={(v) =>
                                form.setData('collateral_type_code', v ?? '')
                            }
                            invalid={!!form.errors.collateral_type_code}
                        />
                    </Field>
                    <Field
                        label="Binding type"
                        error={form.errors.binding_type_code}
                        className="sm:col-span-2"
                    >
                        <Combobox
                            clearable
                            options={refs.bindingTypes}
                            value={form.data.binding_type_code}
                            onChange={(v) =>
                                form.setData('binding_type_code', v ?? '')
                            }
                        />
                    </Field>
                    {upper('document_number', 'Document number')}
                    {upper('owner_name', 'Owner name')}
                    {upper(
                        'owner_address',
                        'Collateral address',
                        'sm:col-span-2',
                    )}
                    <Field
                        label="Location (regency)"
                        required
                        error={form.errors.region_code}
                        className="sm:col-span-2"
                    >
                        <Combobox
                            options={refs.regions}
                            value={form.data.region_code}
                            onChange={(v) =>
                                form.setData('region_code', v ?? '')
                            }
                            invalid={!!form.errors.region_code}
                        />
                    </Field>
                    {upper('description', 'Description', 'sm:col-span-2')}
                </div>
                <DialogFooter>
                    <Button
                        variant="outline"
                        onClick={() => onOpenChange(false)}
                    >
                        Cancel
                    </Button>
                    <Button type="submit" loading={form.processing}>
                        Add collateral
                    </Button>
                </DialogFooter>
            </form>
        </Modal>
    );
}

export default function LoanApplicationShow({
    loan,
    collaterals,
    collateralOptions,
    editable,
    references: refs,
}: Props) {
    const [newCollateral, setNewCollateral] = useState(false);
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [attach, setAttach] = useState('');
    const form = useForm({
        application_date: loan.application_date,
        product_id: loan.product_id ? String(loan.product_id) : '',
        committee_path_id: loan.committee_path_id
            ? String(loan.committee_path_id)
            : '',
        requested_amount: loan.requested_amount || ('' as string | number),
        requested_tenor: loan.requested_tenor || ('' as string | number),
        method_id: loan.method_id ? String(loan.method_id) : '',
        installment_id: loan.installment_id ? String(loan.installment_id) : '',
        interest_rate: loan.interest_rate ?? '',
        usage_type: loan.usage_type ?? '',
        office_id: loan.office_id ? String(loan.office_id) : '',
        supervisor_id: loan.supervisor_id ? String(loan.supervisor_id) : '',
        institution_id: loan.institution_id ? String(loan.institution_id) : '',
        marketing: loan.marketing ?? '',
        note: loan.note ?? '',
    });
    const { data, setData, errors } = form;
    const parameter = refs.parameters[data.product_id];
    const categories = [
        ...(refs.categories[data.product_id] ?? []),
        ...(refs.categories.global ?? []),
    ];
    const methods = parameter?.method_ids.length
        ? refs.methods.filter((m) =>
              parameter.method_ids.includes(Number(m.value)),
          )
        : refs.methods;
    const installments = parameter?.installment_ids.length
        ? refs.installments.filter((i) =>
              parameter.installment_ids.includes(Number(i.value)),
          )
        : refs.installments;
    const period =
        refs.installments.find((i) => String(i.value) === data.installment_id)
            ?.period_months ?? 0;
    const attachable = collateralOptions.filter(
        (o) => !collaterals.some((c) => c.id === o.value),
    );
    const ready = !Object.values(loan.checklist).includes(false);

    const chooseProduct = (id: string | null) => {
        const next = refs.parameters[id ?? ''];
        setData((current) => ({
            ...current,
            product_id: id ?? '',
            committee_path_id: '',
            method_id: next?.default_method_id
                ? String(next.default_method_id)
                : '',
            installment_id: next?.default_installment_id
                ? String(next.default_installment_id)
                : '',
            interest_rate: next?.interest_rate ?? '',
        }));
    };

    const save = (e: React.FormEvent) => {
        e.preventDefault();
        form.put(`/loan-applications/${loan.id}`, { preserveScroll: true });
    };

    const checklist: [string, boolean][] = [
        ['Customer found in the customer system', loan.checklist.customer],
        ['Application data complete', loan.checklist.application],
        [
            loan.collateral_required
                ? 'Collateral attached (required by the product)'
                : 'Collateral (optional for this product)',
            loan.checklist.collateral,
        ],
    ];

    const collateralColumns: Column<(typeof collaterals)[number]>[] = [
        {
            key: 'collateral',
            header: 'Collateral',
            cell: (c) => (
                <>
                    <p className="font-medium">
                        {c.cbs_id ?? `#${c.id}`} · {c.owner_name}
                    </p>
                    <p className="max-w-md truncate text-xs text-muted">
                        {c.description}
                    </p>
                </>
            ),
        },
        {
            key: 'document',
            header: 'Document',
            hideBelow: 'sm',
            cell: (c) => c.document_number,
        },
        {
            key: 'appraisal',
            header: 'Appraisal',
            align: 'right',
            className: 'whitespace-nowrap tabular-nums',
            cell: (c) => rupiah(c.appraisal_value),
        },
        ...(editable
            ? [
                  {
                      key: 'actions',
                      header: 'Actions',
                      srOnly: true,
                      narrow: true,
                      align: 'right',
                      cell: (c: (typeof collaterals)[number]) => (
                          <Tip label="Detach">
                              <Button
                                  variant="ghost"
                                  size="icon"
                                  aria-label="Detach collateral"
                                  onClick={() =>
                                      router.delete(
                                          `/loan-applications/${loan.id}/collaterals/${c.id}`,
                                          { preserveScroll: true },
                                      )
                                  }
                              >
                                  <Unlink />
                              </Button>
                          </Tip>
                      ),
                  } satisfies Column<(typeof collaterals)[number]>,
              ]
            : []),
    ];

    return (
        <>
            <Head title={`Application ${loan.application_code}`} />
            <PageHeader
                title={`Application ${loan.application_code}`}
                description={`${loan.full_name} · ${formatDate(loan.application_date)}`}
                actions={
                    <>
                        <Badge tone={loan.status_tone}>
                            {loan.status_label}
                        </Badge>
                        <Button
                            variant="outline"
                            onClick={() => router.visit('/loan-applications')}
                        >
                            <ArrowLeft /> Back
                        </Button>
                    </>
                }
            />

            {!editable && (
                <p className="mb-3 rounded-md border border-line bg-surface px-3 py-2 text-xs text-muted">
                    {loan.status === 'draft'
                        ? 'Only the person who opened this file can change it.'
                        : 'This file has been submitted and is locked.'}
                </p>
            )}

            <div className="flex flex-col gap-3">
                <Step
                    number={1}
                    title="Applicant"
                    done={loan.checklist.customer}
                >
                    <dl className="grid gap-3 p-3 sm:grid-cols-3">
                        <div>
                            <dt className="text-xs text-muted">Name</dt>
                            <dd className="text-sm font-medium">
                                {loan.full_name}
                            </dd>
                        </div>
                        <div>
                            <dt className="text-xs text-muted">
                                National ID (NIK)
                            </dt>
                            <dd className="font-mono text-sm">{loan.nik}</dd>
                        </div>
                        <div>
                            <dt className="text-xs text-muted">CIF number</dt>
                            <dd className="text-sm">
                                {loan.cif_number ?? '–'}
                            </dd>
                        </div>
                    </dl>
                </Step>

                <form onSubmit={save} noValidate>
                    <Step
                        number={2}
                        title="Application data"
                        done={loan.checklist.application}
                        action={
                            editable && (
                                <Button
                                    type="submit"
                                    size="sm"
                                    loading={form.processing}
                                >
                                    <Save /> Save
                                </Button>
                            )
                        }
                    >
                        <fieldset
                            disabled={!editable}
                            className="grid gap-3 p-3 sm:grid-cols-2 lg:grid-cols-4"
                        >
                            <Field
                                label="Application date"
                                required
                                error={errors.application_date}
                            >
                                <DatePicker
                                    id="application_date"
                                    min={new Date(2020, 0, 1)}
                                    max={new Date()}
                                    value={data.application_date}
                                    onChange={(v) =>
                                        setData('application_date', v)
                                    }
                                    invalid={!!errors.application_date}
                                />
                            </Field>
                            <Field
                                label="Product"
                                required
                                error={errors.product_id}
                                className="lg:col-span-2"
                            >
                                <Combobox
                                    id="product_id"
                                    options={refs.products}
                                    value={data.product_id}
                                    onChange={chooseProduct}
                                    invalid={!!errors.product_id}
                                />
                            </Field>
                            <Field
                                label="Category"
                                required
                                error={errors.committee_path_id}
                                hint="Decides the committee path"
                            >
                                <Combobox
                                    id="committee_path_id"
                                    searchable={false}
                                    placeholder={
                                        data.product_id
                                            ? 'Select…'
                                            : 'Choose a product first'
                                    }
                                    options={categories}
                                    value={data.committee_path_id}
                                    onChange={(v) =>
                                        setData('committee_path_id', v ?? '')
                                    }
                                    invalid={!!errors.committee_path_id}
                                />
                            </Field>
                            <Field
                                label="Loan amount (IDR)"
                                required
                                error={errors.requested_amount}
                                hint={
                                    parameter
                                        ? `${rupiah(parameter.min_amount)} – ${rupiah(parameter.max_amount)}`
                                        : rupiah(
                                              data.requested_amount === ''
                                                  ? null
                                                  : Number(
                                                        data.requested_amount,
                                                    ),
                                          )
                                }
                            >
                                <Input
                                    id="requested_amount"
                                    type="number"
                                    min={0}
                                    value={data.requested_amount}
                                    onChange={(e) =>
                                        setData(
                                            'requested_amount',
                                            e.target.value,
                                        )
                                    }
                                    aria-invalid={!!errors.requested_amount}
                                />
                            </Field>
                            <Field
                                label="Tenor (months)"
                                required
                                error={errors.requested_tenor}
                                hint={
                                    parameter
                                        ? `${parameter.min_tenor} – ${parameter.max_tenor} months${period > 1 ? `, multiples of ${period}` : ''}`
                                        : undefined
                                }
                            >
                                <Input
                                    id="requested_tenor"
                                    type="number"
                                    min={0}
                                    value={data.requested_tenor}
                                    onChange={(e) =>
                                        setData(
                                            'requested_tenor',
                                            e.target.value,
                                        )
                                    }
                                    aria-invalid={!!errors.requested_tenor}
                                />
                            </Field>
                            <Field
                                label="Interest method"
                                required
                                error={errors.method_id}
                            >
                                <Combobox
                                    id="method_id"
                                    options={methods}
                                    value={data.method_id}
                                    onChange={(v) =>
                                        setData('method_id', v ?? '')
                                    }
                                    invalid={!!errors.method_id}
                                />
                            </Field>
                            <Field
                                label="Installment system"
                                required
                                error={errors.installment_id}
                            >
                                <Combobox
                                    id="installment_id"
                                    options={installments}
                                    value={data.installment_id}
                                    onChange={(v) =>
                                        setData('installment_id', v ?? '')
                                    }
                                    invalid={!!errors.installment_id}
                                />
                            </Field>
                            <Field
                                label="Interest rate (%)"
                                required
                                error={errors.interest_rate}
                            >
                                <Input
                                    id="interest_rate"
                                    type="number"
                                    step="0.01"
                                    min={0}
                                    value={data.interest_rate}
                                    onChange={(e) =>
                                        setData('interest_rate', e.target.value)
                                    }
                                    aria-invalid={!!errors.interest_rate}
                                />
                            </Field>
                            <Field
                                label="Usage"
                                required
                                error={errors.usage_type}
                            >
                                <Combobox
                                    id="usage_type"
                                    searchable={false}
                                    options={refs.usageTypes}
                                    value={data.usage_type}
                                    onChange={(v) =>
                                        setData('usage_type', v ?? '')
                                    }
                                    invalid={!!errors.usage_type}
                                />
                            </Field>
                            <Field
                                label="Office"
                                required
                                error={errors.office_id}
                            >
                                <Combobox
                                    id="office_id"
                                    options={refs.offices}
                                    value={data.office_id}
                                    onChange={(v) =>
                                        setData('office_id', v ?? '')
                                    }
                                    invalid={!!errors.office_id}
                                />
                            </Field>
                            <Field
                                label="Section head"
                                required
                                error={errors.supervisor_id}
                                hint="Schedules the survey"
                            >
                                <Combobox
                                    id="supervisor_id"
                                    options={refs.supervisors}
                                    value={data.supervisor_id}
                                    onChange={(v) =>
                                        setData('supervisor_id', v ?? '')
                                    }
                                    invalid={!!errors.supervisor_id}
                                />
                            </Field>
                            <Field
                                label="Institution"
                                error={errors.institution_id}
                            >
                                <Combobox
                                    id="institution_id"
                                    clearable
                                    options={refs.institutions}
                                    value={data.institution_id}
                                    onChange={(v) =>
                                        setData('institution_id', v ?? '')
                                    }
                                />
                            </Field>
                            <Field label="Marketing" error={errors.marketing}>
                                <Input
                                    className="uppercase"
                                    value={data.marketing}
                                    maxLength={100}
                                    onChange={(e) =>
                                        setData('marketing', e.target.value)
                                    }
                                />
                            </Field>
                            <Field
                                label="Note"
                                error={errors.note}
                                className="sm:col-span-2"
                            >
                                <Input
                                    value={data.note}
                                    maxLength={255}
                                    onChange={(e) =>
                                        setData('note', e.target.value)
                                    }
                                />
                            </Field>
                        </fieldset>
                    </Step>
                </form>

                <Step
                    number={3}
                    title="Collateral"
                    done={loan.checklist.collateral && collaterals.length > 0}
                    action={
                        editable && (
                            <Button
                                size="sm"
                                variant="outline"
                                onClick={() => setNewCollateral(true)}
                            >
                                <Plus /> New collateral
                            </Button>
                        )
                    }
                >
                    {editable && (
                        <div className="flex flex-wrap items-center gap-2 border-b border-line p-2.5">
                            <Combobox
                                className="min-w-64 flex-1"
                                placeholder="Attach an existing collateral…"
                                options={attachable}
                                value={attach}
                                onChange={(v) => setAttach(v ?? '')}
                            />
                            <Button
                                size="sm"
                                disabled={!attach}
                                onClick={() =>
                                    router.post(
                                        `/loan-applications/${loan.id}/collaterals`,
                                        { collateral_id: attach },
                                        {
                                            preserveScroll: true,
                                            onSuccess: () => setAttach(''),
                                        },
                                    )
                                }
                            >
                                Attach
                            </Button>
                        </div>
                    )}
                    <DataTable
                        bare
                        rows={collaterals}
                        rowKey={(c) => c.id}
                        columns={collateralColumns}
                        empty={{
                            icon: <CircleAlert />,
                            title: 'No collateral attached',
                            description: loan.collateral_required
                                ? 'This product requires collateral.'
                                : 'Collateral is optional for this product.',
                        }}
                    />
                </Step>

                {loan.status === 'draft' && (
                    <Card className="p-3">
                        <h2 className="mb-2 text-sm font-semibold">Submit</h2>
                        <ul className="mb-3 flex flex-col gap-1">
                            {checklist.map(([label, ok]) => (
                                <li
                                    key={label}
                                    className="flex items-center gap-2 text-sm"
                                >
                                    {ok ? (
                                        <Check className="size-3.5 text-emerald-600" />
                                    ) : (
                                        <CircleAlert className="size-3.5 text-amber-600" />
                                    )}
                                    <span className={ok ? '' : 'text-muted'}>
                                        {label}
                                    </span>
                                </li>
                            ))}
                        </ul>
                        {editable && (
                            <div className="flex flex-wrap gap-2">
                                <Button
                                    disabled={!ready}
                                    onClick={() =>
                                        router.post(
                                            `/loan-applications/${loan.id}/confirm`,
                                            {},
                                            { preserveScroll: true },
                                        )
                                    }
                                >
                                    <Send /> Submit application
                                </Button>
                                <Button
                                    variant="outline"
                                    onClick={() => setConfirmDelete(true)}
                                >
                                    <Trash2 /> Delete draft
                                </Button>
                            </div>
                        )}
                    </Card>
                )}
            </div>

            <NewCollateral
                open={newCollateral}
                onOpenChange={setNewCollateral}
                loanId={loan.id}
                refs={refs}
            />
            <ConfirmDialog
                open={confirmDelete}
                onOpenChange={setConfirmDelete}
                title="Delete draft?"
                description={
                    <>
                        This removes application{' '}
                        <strong>{loan.application_code}</strong> for{' '}
                        {loan.full_name}.
                    </>
                }
                onConfirm={() => router.delete(`/loan-applications/${loan.id}`)}
            />
        </>
    );
}
