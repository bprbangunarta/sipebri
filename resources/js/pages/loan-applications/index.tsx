import { Head, router, useForm, useHttp } from '@inertiajs/react';
import { FileText, Loader2, Plus, Search, ShieldAlert, X } from 'lucide-react';
import { useState } from 'react';
import { lookup as lookupRoute } from '@/actions/App/Http/Controllers/LoanApplicationController';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/choice';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { Combobox } from '@/components/ui/combobox';
import { DialogBody, DialogFooter, Modal } from '@/components/ui/dialog';
import { Field } from '@/components/ui/field';
import { InputGroup } from '@/components/ui/input';
import { MaskedInput } from '@/components/ui/masked-input';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Alert, Badge, PageHeader } from '@/components/ui/misc';
import type { BadgeTone } from '@/components/ui/misc';
import type { PageMeta } from '@/components/ui/pagination';
import { nextSort } from '@/components/ui/sort-head';
import { Tip } from '@/components/ui/tooltip';
import { useListQuery } from '@/hooks/use-list-query';
import { formatDate, formatNumber, rupiah } from '@/lib/format';
import { MASKS } from '@/lib/mask';

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
    committeeMembers: Option[];
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

type Member = { id: number; name: string; role: string | null };

type Lookup = { found: boolean; customer: Customer | null; committee_member: Member | null; message: string | null };

/** The lookup answers 503 with a message when the customer system cannot be reached; anything else gets a general sentence. */
function failureMessage(body: string): string {
    try {
        const parsed: unknown = JSON.parse(body);

        if (parsed && typeof parsed === 'object' && 'message' in parsed && typeof parsed.message === 'string') {
            return parsed.message;
        }
    } catch {
        // Not JSON (a proxy error page): fall through to the general sentence.
    }

    return 'Server tidak dapat dihubungi. Coba lagi.';
}

function NewApplication({ open, onOpenChange, source, members }: { open: boolean; onOpenChange: (open: boolean) => void; source: string; members: Option[] }) {
    const form = useForm({ nik: '', committee_conflict_user_id: '' });
    const http = useHttp<Record<string, never>, Lookup>();
    const [customer, setCustomer] = useState<Customer | null>(null);
    // A committee member recognised by national ID, or one the officer flags by hand.
    const [member, setMember] = useState<Member | null>(null);
    const [flagged, setFlagged] = useState(false);
    const [message, setMessage] = useState<string | null>(null);

    const forget = () => {
        setCustomer(null);
        setMember(null);
        setFlagged(false);
        setMessage(null);
    };

    const lookup = () => {
        forget();
        form.setData('committee_conflict_user_id', '');
        http.get(lookupRoute.url({ query: { nik: form.data.nik } }), {
            onSuccess: (body) => {
                setCustomer(body.customer);
                setMember(body.committee_member ?? null);
                setMessage(body.message);
            },
            onHttpException: (response) => {
                setMessage(failureMessage(response.data));
            },
            onNetworkError: () => {
                setMessage('Server tidak dapat dihubungi. Coba lagi.');
            },
        }).catch(() => undefined);
    };

    const close = (next: boolean) => {
        if (!next) {
            form.reset();
            form.clearErrors();
            forget();
        }
        onOpenChange(next);
    };

    return (
        <Modal persistent open={open} onOpenChange={close} title="Pengajuan kredit baru" description={`Pemohon dicari di ${source} berdasarkan NIK.`}>
            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    form.post('/loan-applications');
                }}
                noValidate
            >
                <DialogBody className="flex flex-col gap-3">
                    <Field label="NIK (KTP)" required error={form.errors.nik ?? undefined} hint="16 digit">
                        <InputGroup>
                            <MaskedInput
                                autoFocus
                                className="min-w-0 flex-1"
                                mask={MASKS.nik}
                                value={form.data.nik}
                                onValueChange={(nik) => {
                                    form.setData({ nik, committee_conflict_user_id: '' });
                                    forget();
                                }}
                                aria-invalid={!!form.errors.nik}
                            />
                            <Tip label="Cari di sistem nasabah">
                                <Button
                                    variant="outline"
                                    size="fieldIcon"
                                    aria-label="Cari NIK"
                                    disabled={form.data.nik.length !== 16 || http.processing}
                                    onClick={lookup}
                                >
                                    {http.processing ? <Loader2 className="animate-spin" /> : <Search />}
                                </Button>
                            </Tip>
                        </InputGroup>
                    </Field>
                    {message && <Alert tone="danger">{message}</Alert>}
                    {customer && (
                        <dl className="grid grid-cols-2 gap-2 rounded-md border border-line bg-canvas p-2.5 text-sm">
                            <div className="col-span-2">
                                <dt className="text-xs text-muted">Nama</dt>
                                <dd className="font-medium">{customer.full_name}</dd>
                            </div>
                            <div>
                                <dt className="text-xs text-muted">Lahir</dt>
                                <dd>{[customer.birth_place, customer.birth_date && formatDate(customer.birth_date)].filter(Boolean).join(', ') || '–'}</dd>
                            </div>
                            <div>
                                <dt className="text-xs text-muted">Sumber</dt>
                                <dd>{customer.source}</dd>
                            </div>
                            <div className="col-span-2">
                                <dt className="text-xs text-muted">Alamat</dt>
                                <dd>{customer.address ?? '–'}</dd>
                            </div>
                        </dl>
                    )}
                    {customer && member && (
                        <Alert tone="warning" icon={<ShieldAlert />}>
                            <strong>
                                Pemohon adalah anggota komite: {member.name}
                                {member.role ? ` (${member.role})` : ''}.
                            </strong>{' '}
                            Berkas ditandai sejak awal. Ia tidak bisa memutus maupun menjadi Kasi Analisnya, dan persetujuan melewatinya.
                        </Alert>
                    )}
                    {customer && !member && (
                        <>
                            <Checkbox
                                label="Pemohon adalah anggota komite"
                                checked={flagged}
                                onCheckedChange={(checked) => {
                                    setFlagged(checked === true);
                                    form.setData('committee_conflict_user_id', '');
                                }}
                            />
                            {flagged && (
                                <Field
                                    label="Anggota komite"
                                    required
                                    error={form.errors.committee_conflict_user_id}
                                    hint="Tidak dikenali dari NIK (belum ada NIK tercatat), jadi pilih orangnya."
                                >
                                    <Combobox
                                        options={members}
                                        value={form.data.committee_conflict_user_id}
                                        onChange={(v) => form.setData('committee_conflict_user_id', v ?? '')}
                                        invalid={!!form.errors.committee_conflict_user_id}
                                    />
                                </Field>
                            )}
                        </>
                    )}
                </DialogBody>
                <DialogFooter>
                    <Button variant="outline" onClick={() => close(false)}>
                        Batal
                    </Button>
                    <Button type="submit" loading={form.processing} disabled={!customer || (flagged && !form.data.committee_conflict_user_id)}>
                        Buka pengajuan
                    </Button>
                </DialogFooter>
            </form>
        </Modal>
    );
}

export default function LoanApplicationsIndex({ loans, filters, perPageOptions, statuses, products, canManage, customerSource, committeeMembers }: Props) {
    const [creating, setCreating] = useState(false);
    const { visit, search, onSearch, clear, sorted, loading, error } = useListQuery<Filters>({
        url: '/loan-applications',
        filters,
        defaults: DEFAULTS,
        only: ['loans', 'filters'],
    });
    const hasFilters = Boolean(filters.search || filters.status || filters.product);
    const sortBy = (column: string) => visit(nextSort(filters, column));
    const reset = () => clear({ status: null, product: null });

    const columns: Column<LoanRow>[] = [
        { key: 'code', header: 'Kode', sort: 'application_code', className: 'font-medium', cell: (l) => l.application_code },
        { key: 'date', header: 'Tanggal', sort: 'application_date', cell: (l) => formatDate(l.application_date) },
        { key: 'applicant', header: 'Pemohon', sort: 'full_name', cell: (l) => l.full_name },
        { key: 'nik', header: 'NIK', sort: 'nik', className: 'font-mono', cell: (l) => l.nik },
        { key: 'product', header: 'Produk', sort: 'product', cell: (l) => l.product_label ?? '–' },
        {
            key: 'amount',
            header: 'Plafon',
            sort: 'requested_amount',
            align: 'right',
            className: 'tabular-nums',
            cell: (l) => (l.requested_amount ? rupiah(l.requested_amount) : '–'),
        },
        {
            key: 'tenor',
            header: 'Tenor',
            sort: 'requested_tenor',
            align: 'right',
            className: 'tabular-nums',
            cell: (l) => (l.requested_tenor > 0 ? `${formatNumber(l.requested_tenor)} bulan` : '–'),
        },
        { key: 'status', header: 'Status', sort: 'status', cell: (l) => <Badge tone={l.status_tone}>{l.status_label}</Badge> },
    ];

    return (
        <>
            <Head title="Pengajuan" />
            <PageHeader
                title="Pengajuan"
                description="Berkas kredit Anda, dari pendaftaran sampai keputusan komite"
                actions={
                    canManage && (
                        <Button onClick={() => setCreating(true)}>
                            <Plus /> Pengajuan baru
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
                    <FilterBar search={<SearchInput value={search} onChange={onSearch} placeholder="Cari kode, nama, NIK…" label="Cari pengajuan" />}>
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
                            placeholder="Produk"
                            options={products}
                            value={filters.product}
                            onChange={(v) => visit({ product: v ? Number(v) : null })}
                        />
                        {(hasFilters || sorted) && (
                            <Button variant="ghost" size="sm" onClick={reset}>
                                <X /> Atur ulang
                            </Button>
                        )}
                    </FilterBar>
                }
                columns={columns}
                sort={{ sort: filters.sort, direction: filters.direction, onSort: sortBy }}
                onRowClick={(l) => router.visit(`/loan-applications/${l.id}`)}
                empty={{
                    icon: <FileText />,
                    title: hasFilters ? 'Tidak ada pengajuan yang cocok dengan filter' : 'Belum ada pengajuan',
                    description: hasFilters ? 'Coba pencarian lain atau hapus filter.' : 'Buka pengajuan baru dengan NIK pemohon.',
                    action: hasFilters ? (
                        <Button variant="outline" size="sm" onClick={reset}>
                            Hapus filter
                        </Button>
                    ) : canManage ? (
                        <Button size="sm" onClick={() => setCreating(true)}>
                            <Plus /> Pengajuan baru
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

            <NewApplication open={creating} onOpenChange={setCreating} source={customerSource} members={committeeMembers} />
        </>
    );
}
