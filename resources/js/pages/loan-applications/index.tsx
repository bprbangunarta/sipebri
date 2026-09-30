import { Head, router, useForm } from '@inertiajs/react';
import { FileText, Loader2, Plus, Search, X } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { Combobox } from '@/components/ui/combobox';
import { DialogFooter, Modal } from '@/components/ui/dialog';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Badge, PageHeader } from '@/components/ui/misc';
import type { BadgeTone } from '@/components/ui/misc';
import type { PageMeta } from '@/components/ui/pagination';
import { nextSort } from '@/components/ui/sort-head';
import { useListQuery } from '@/hooks/use-list-query';
import { formatDate, rupiah } from '@/lib/format';

export type LoanRow = {
    id: number;
    application_code: string;
    application_date: string;
    nik: string;
    full_name: string;
    status: string;
    status_label: string;
    status_tone: BadgeTone;
    product_label: string | null;
    office_label: string | null;
    requested_amount: number;
    requested_tenor: number;
};

type Filters = {
    search: string;
    status: string | null;
    product: number | null;
    sort: string;
    direction: 'asc' | 'desc';
    per_page: number;
};
type Option = { value: string | number; label: string };

type Props = {
    loans: { data: LoanRow[] } & PageMeta;
    filters: Filters;
    perPageOptions: number[];
    statuses: Option[];
    products: Option[];
    canManage: boolean;
    customerSource: string;
};

const DEFAULTS = { sort: 'application_code', direction: 'desc', per_page: 10 };

type Customer = {
    full_name: string;
    nik: string;
    birth_place?: string;
    birth_date?: string;
    address?: string;
    source: string;
};

function NewApplication({
    open,
    onOpenChange,
    source,
}: {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    source: string;
}) {
    const form = useForm({ nik: '' });
    const [customer, setCustomer] = useState<Customer | null>(null);
    const [message, setMessage] = useState<string | null>(null);
    const [looking, setLooking] = useState(false);

    const lookup = async () => {
        setLooking(true);
        setCustomer(null);
        setMessage(null);

        try {
            const response = await fetch(
                `/loan-applications/lookup?nik=${encodeURIComponent(form.data.nik)}`,
                { headers: { Accept: 'application/json' } },
            );
            const body = await response.json();
            setCustomer(body.customer);
            setMessage(body.message);
        } catch {
            setMessage('Unable to reach the server. Please try again.');
        } finally {
            setLooking(false);
        }
    };

    const close = (next: boolean) => {
        if (!next) {
            form.reset();
            form.clearErrors();
            setCustomer(null);
            setMessage(null);
        }
        onOpenChange(next);
    };

    return (
        <Modal
            open={open}
            onOpenChange={close}
            title="New loan application"
            description={`The applicant is looked up in ${source} by national ID.`}
        >
            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    form.post('/loan-applications');
                }}
                noValidate
            >
                <div className="flex flex-col gap-3 p-4">
                    <Field
                        label="National ID (NIK)"
                        required
                        error={form.errors.nik ?? undefined}
                        hint="16 digits"
                    >
                        <div className="flex gap-2">
                            <Input
                                autoFocus
                                inputMode="numeric"
                                maxLength={16}
                                value={form.data.nik}
                                onChange={(e) => {
                                    form.setData(
                                        'nik',
                                        e.target.value.replace(/\D/g, ''),
                                    );
                                    setCustomer(null);
                                    setMessage(null);
                                }}
                                aria-invalid={!!form.errors.nik}
                            />
                            <Button
                                variant="outline"
                                disabled={
                                    form.data.nik.length !== 16 || looking
                                }
                                onClick={lookup}
                            >
                                {looking ? (
                                    <Loader2 className="animate-spin" />
                                ) : (
                                    <Search />
                                )}{' '}
                                Look up
                            </Button>
                        </div>
                    </Field>
                    {message && (
                        <p className="text-xs text-danger">{message}</p>
                    )}
                    {customer && (
                        <dl className="grid grid-cols-2 gap-2 rounded-md border border-line bg-canvas p-2.5 text-sm">
                            <div className="col-span-2">
                                <dt className="text-xs text-muted">Name</dt>
                                <dd className="font-medium">
                                    {customer.full_name}
                                </dd>
                            </div>
                            <div>
                                <dt className="text-xs text-muted">Birth</dt>
                                <dd>
                                    {[
                                        customer.birth_place,
                                        customer.birth_date &&
                                            formatDate(customer.birth_date),
                                    ]
                                        .filter(Boolean)
                                        .join(', ') || '–'}
                                </dd>
                            </div>
                            <div>
                                <dt className="text-xs text-muted">Source</dt>
                                <dd>{customer.source}</dd>
                            </div>
                            <div className="col-span-2">
                                <dt className="text-xs text-muted">Address</dt>
                                <dd>{customer.address ?? '–'}</dd>
                            </div>
                        </dl>
                    )}
                </div>
                <DialogFooter>
                    <Button variant="outline" onClick={() => close(false)}>
                        Cancel
                    </Button>
                    <Button
                        type="submit"
                        loading={form.processing}
                        disabled={!customer}
                    >
                        Open application
                    </Button>
                </DialogFooter>
            </form>
        </Modal>
    );
}

export default function LoanApplicationsIndex({
    loans,
    filters,
    perPageOptions,
    statuses,
    products,
    canManage,
    customerSource,
}: Props) {
    const [creating, setCreating] = useState(false);
    const { visit, search, onSearch, clear, loading, error } =
        useListQuery<Filters>({
            url: '/loan-applications',
            filters,
            defaults: DEFAULTS,
            only: ['loans', 'filters'],
        });
    const hasFilters = Boolean(
        filters.search || filters.status || filters.product,
    );
    const sortBy = (column: string) => visit(nextSort(filters, column));

    const columns: Column<LoanRow>[] = [
        {
            key: 'file',
            header: 'File',
            sort: 'application_code',
            cell: (l) => (
                <>
                    <p className="font-medium">{l.application_code}</p>
                    <p className="text-xs text-muted">
                        {formatDate(l.application_date)}
                    </p>
                </>
            ),
        },
        {
            key: 'applicant',
            header: 'Applicant',
            sort: 'full_name',
            cell: (l) => (
                <>
                    {l.full_name}
                    <p className="font-mono text-xs text-muted">{l.nik}</p>
                </>
            ),
        },
        {
            key: 'product',
            header: 'Product',
            hideBelow: 'md',
            cell: (l) => l.product_label ?? '–',
        },
        {
            key: 'amount',
            header: 'Amount',
            sort: 'requested_amount',
            align: 'right',
            hideBelow: 'sm',
            className: 'whitespace-nowrap tabular-nums',
            cell: (l) => (
                <>
                    {l.requested_amount ? rupiah(l.requested_amount) : '–'}
                    {l.requested_tenor > 0 && (
                        <p className="text-xs text-muted">
                            {l.requested_tenor} months
                        </p>
                    )}
                </>
            ),
        },
        {
            key: 'status',
            header: 'Status',
            sort: 'status',
            cell: (l) => <Badge tone={l.status_tone}>{l.status_label}</Badge>,
        },
    ];

    return (
        <>
            <Head title="Loan applications" />
            <PageHeader
                title="Loan applications"
                description="Your loan files, from registration to the committee decision"
                actions={
                    canManage && (
                        <Button onClick={() => setCreating(true)}>
                            <Plus /> New application
                        </Button>
                    )
                }
            />

            <DataTable
                rows={loans.data}
                rowKey={(r) => r.id}
                loading={loading}
                error={error}
                onRetry={() => visit({})}
                toolbar={
                    <FilterBar
                        search={
                            <SearchInput
                                value={search}
                                onChange={onSearch}
                                placeholder="Search code, name, NIK…"
                                label="Search applications"
                            />
                        }
                    >
                        <Combobox
                            className="w-full sm:w-40"
                            clearable
                            searchable={false}
                            placeholder="Status"
                            options={statuses}
                            value={filters.status}
                            onChange={(v) => visit({ status: v })}
                        />
                        <Combobox
                            className="w-full sm:w-56"
                            clearable
                            placeholder="Product"
                            options={products}
                            value={filters.product}
                            onChange={(v) =>
                                visit({ product: v ? Number(v) : null })
                            }
                        />
                        {hasFilters && (
                            <Button
                                variant="ghost"
                                size="sm"
                                onClick={() =>
                                    clear({ status: null, product: null })
                                }
                            >
                                <X /> Reset
                            </Button>
                        )}
                    </FilterBar>
                }
                columns={columns}
                sort={{
                    sort: filters.sort,
                    direction: filters.direction,
                    onSort: sortBy,
                }}
                onRowClick={(l) => router.visit(`/loan-applications/${l.id}`)}
                empty={{
                    icon: <FileText />,
                    title: hasFilters
                        ? 'No applications match your filters'
                        : 'No applications yet',
                    description: hasFilters
                        ? 'Try a different search or clear the filters.'
                        : 'Open a new application with the applicant’s national ID.',
                    action: hasFilters ? (
                        <Button
                            variant="outline"
                            size="sm"
                            onClick={() =>
                                clear({ status: null, product: null })
                            }
                        >
                            Clear filters
                        </Button>
                    ) : canManage ? (
                        <Button size="sm" onClick={() => setCreating(true)}>
                            <Plus /> New application
                        </Button>
                    ) : undefined,
                }}
                pagination={{
                    meta: loans,
                    perPage: filters.per_page,
                    options: perPageOptions,
                    onPage: (page) => visit({ page }),
                    onPerPage: (per_page) => visit({ per_page }),
                }}
            />

            <NewApplication
                open={creating}
                onOpenChange={setCreating}
                source={customerSource}
            />
        </>
    );
}
