import { Head, router, useForm } from '@inertiajs/react';
import { ArrowLeft, Check, CircleAlert, Plus, ShieldAlert, Unlink } from 'lucide-react';
import type { ReactNode } from 'react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { CurrencyInput } from '@/components/ui/currency-input';
import { DatePicker } from '@/components/ui/date-picker';
import { ConfirmDialog, DialogBody, DialogFooter, Modal } from '@/components/ui/dialog';
import { Field } from '@/components/ui/field';
import { FilteredInput } from '@/components/ui/filtered-input';
import { Input } from '@/components/ui/input';
import { MaskedInput } from '@/components/ui/masked-input';
import { LocationsPanel } from '@/components/locations-panel';
import type { LocationPlace } from '@/components/locations-panel';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Alert, Badge, Card, CardBody, CardFooter, CardHeader, PageHeader } from '@/components/ui/misc';
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
    committee_conflict: {
        user_id: number;
        name: string;
        role: string | null;
        source: 'nik' | 'manual';
    } | null;
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
    locations: LocationPlace[];
    editable: boolean;
    references: {
        usageTypes: Option[];
        offices: Option[];
        products: Option[];
        institutions: Option[];
        methods: Option[];
        installments: (Option & { period_months: number })[];
        supervisors: Option[];
        committeeMembers: Option[];
        collateralTypes: Option[];
        bindingTypes: Option[];
        regions: Option[];
        parameters: Record<string, Parameter>;
        categories: Record<string, Option[]>;
    };
};

/** Digits and the decimal point: the rate the server accepts as a number between 0 and 100. */
const RATE_CHARACTER = /^[0-9.]$/;

/** A step of the file: numbered title, whether it is complete, and its own actions. */
function Step({ number, title, done, children, action }: { number: number; title: string; done?: boolean; children: ReactNode; action?: ReactNode }) {
    return (
        <Card>
            <CardHeader
                title={`${number}. ${title}`}
                actions={
                    <>
                        {done ? (
                            <Badge tone="success">
                                <Check className="mr-0.5 size-3" /> Lengkap
                            </Badge>
                        ) : (
                            <Badge>Belum lengkap</Badge>
                        )}
                        {action}
                    </>
                }
            />
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
    const upper = (name: 'document_number' | 'owner_name' | 'owner_address' | 'description', label: string, maxLength: number, span = '') => (
        <Field label={label} required error={form.errors[name]} className={span}>
            <Input
                className="uppercase"
                maxLength={maxLength}
                value={form.data[name]}
                onChange={(e) => form.setData(name, e.target.value)}
                aria-invalid={!!form.errors[name]}
            />
        </Field>
    );

    return (
        <Modal persistent open={open} onOpenChange={onOpenChange} wide title="Jaminan baru" description="Langsung dilekatkan ke berkas ini.">
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
                <DialogBody className="grid gap-3 sm:grid-cols-2">
                    <Field label="Jenis agunan" required error={form.errors.collateral_type_code} className="sm:col-span-2">
                        <Combobox
                            options={refs.collateralTypes}
                            value={form.data.collateral_type_code}
                            onChange={(v) => form.setData('collateral_type_code', v ?? '')}
                            invalid={!!form.errors.collateral_type_code}
                        />
                    </Field>
                    <Field label="Jenis pengikatan" error={form.errors.binding_type_code} className="sm:col-span-2">
                        <Combobox
                            clearable
                            options={refs.bindingTypes}
                            value={form.data.binding_type_code}
                            onChange={(v) => form.setData('binding_type_code', v ?? '')}
                        />
                    </Field>
                    {upper('document_number', 'Nomor dokumen', 100)}
                    {upper('owner_name', 'Nama pemilik', 100)}
                    {upper('owner_address', 'Alamat agunan', 255, 'sm:col-span-2')}
                    <Field label="Lokasi (kabupaten/kota)" required error={form.errors.region_code} className="sm:col-span-2">
                        <Combobox
                            options={refs.regions}
                            value={form.data.region_code}
                            onChange={(v) => form.setData('region_code', v ?? '')}
                            invalid={!!form.errors.region_code}
                        />
                    </Field>
                    {upper('description', 'Keterangan', 255, 'sm:col-span-2')}
                </DialogBody>
                <DialogFooter>
                    <Button variant="outline" onClick={() => onOpenChange(false)}>
                        Batal
                    </Button>
                    <Button type="submit" loading={form.processing}>
                        Tambah jaminan
                    </Button>
                </DialogFooter>
            </form>
        </Modal>
    );
}

export default function LoanApplicationShow({ loan, collaterals, collateralOptions, locations, editable, references: refs }: Props) {
    const [newCollateral, setNewCollateral] = useState(false);
    const [confirmDelete, setConfirmDelete] = useState(false);
    const [attach, setAttach] = useState('');
    const form = useForm({
        application_date: loan.application_date,
        product_id: loan.product_id ? String(loan.product_id) : '',
        committee_path_id: loan.committee_path_id ? String(loan.committee_path_id) : '',
        requested_amount: loan.requested_amount || (null as number | null),
        requested_tenor: loan.requested_tenor ? String(loan.requested_tenor) : '',
        method_id: loan.method_id ? String(loan.method_id) : '',
        installment_id: loan.installment_id ? String(loan.installment_id) : '',
        interest_rate: loan.interest_rate ?? '',
        usage_type: loan.usage_type ?? '',
        office_id: loan.office_id ? String(loan.office_id) : '',
        supervisor_id: loan.supervisor_id ? String(loan.supervisor_id) : '',
        committee_conflict_user_id: loan.committee_conflict ? String(loan.committee_conflict.user_id) : '',
        institution_id: loan.institution_id ? String(loan.institution_id) : '',
        marketing: loan.marketing ?? '',
        note: loan.note ?? '',
    });
    const { data, setData, errors } = form;
    const parameter = refs.parameters[data.product_id];
    const categories = [...(refs.categories[data.product_id] ?? []), ...(refs.categories.global ?? [])];
    const methods = parameter?.method_ids.length ? refs.methods.filter((m) => parameter.method_ids.includes(Number(m.value))) : refs.methods;
    const installments = parameter?.installment_ids.length
        ? refs.installments.filter((i) => parameter.installment_ids.includes(Number(i.value)))
        : refs.installments;
    const period = refs.installments.find((i) => String(i.value) === data.installment_id)?.period_months ?? 0;
    const attachable = collateralOptions.filter((o) => !collaterals.some((c) => c.id === o.value));
    const ready = !Object.values(loan.checklist).includes(false);

    const chooseProduct = (id: string | null) => {
        const next = refs.parameters[id ?? ''];
        setData((current) => ({
            ...current,
            product_id: id ?? '',
            committee_path_id: '',
            method_id: next?.default_method_id ? String(next.default_method_id) : '',
            installment_id: next?.default_installment_id ? String(next.default_installment_id) : '',
            interest_rate: next?.interest_rate ?? '',
        }));
    };

    const save = (e: React.FormEvent) => {
        e.preventDefault();
        form.put(`/loan-applications/${loan.id}`, { preserveScroll: true });
    };

    const checklist: [string, boolean][] = [
        ['Nasabah ditemukan di sistem nasabah', loan.checklist.customer],
        ['Data pengajuan lengkap', loan.checklist.application],
        [loan.collateral_required ? 'Jaminan terlampir (wajib untuk produk ini)' : 'Jaminan (opsional untuk produk ini)', loan.checklist.collateral],
    ];

    const collateralColumns: Column<CollateralRow>[] = [
        { key: 'id', header: 'ID agunan', className: 'font-medium', cell: (c) => c.cbs_id ?? `#${c.id}` },
        { key: 'owner', header: 'Pemilik', cell: (c) => c.owner_name ?? '–' },
        { key: 'document', header: 'Dokumen', cell: (c) => c.document_number ?? '–' },
        { key: 'description', header: 'Keterangan', cell: (c) => c.description ?? '–' },
        { key: 'appraisal', header: 'Taksasi', align: 'right', className: 'tabular-nums', cell: (c) => rupiah(c.appraisal_value) },
        ...(editable
            ? [
                  {
                      key: 'actions',
                      header: 'Aksi',
                      srOnly: true,
                      narrow: true,
                      align: 'right',
                      cell: (c: CollateralRow) => (
                          <Tip label="Lepas">
                              <Button
                                  variant="ghost"
                                  size="icon"
                                  aria-label="Lepas jaminan"
                                  onClick={() => router.delete(`/loan-applications/${loan.id}/collaterals/${c.id}`, { preserveScroll: true })}
                              >
                                  <Unlink />
                              </Button>
                          </Tip>
                      ),
                  } satisfies Column<CollateralRow>,
              ]
            : []),
    ];

    return (
        <>
            <Head title={`Pengajuan ${loan.application_code}`} />
            <PageHeader
                title={`Pengajuan ${loan.application_code}`}
                description={`${loan.full_name} · ${formatDate(loan.application_date)}`}
                badge={<Badge tone={loan.status_tone}>{loan.status_label}</Badge>}
                actions={
                    <Button variant="outline" onClick={() => router.visit('/loan-applications')}>
                        <ArrowLeft /> Kembali
                    </Button>
                }
            />

            {!editable && (
                <Alert tone="neutral" className="mb-3">
                    {loan.status === 'draft' ? 'Hanya yang membuka berkas ini yang bisa mengubahnya.' : 'Berkas ini sudah diajukan dan terkunci.'}
                </Alert>
            )}

            <div className="flex flex-col gap-3">
                <Step number={1} title="Pemohon" done={loan.checklist.customer}>
                    <CardBody className="flex flex-col gap-3">
                        <dl className="grid gap-3 sm:grid-cols-3">
                            <div>
                                <dt className="text-xs text-muted">Nama</dt>
                                <dd className="text-sm font-medium">{loan.full_name}</dd>
                            </div>
                            <div>
                                <dt className="text-xs text-muted">NIK (KTP)</dt>
                                <dd className="font-mono text-sm">{loan.nik}</dd>
                            </div>
                            <div>
                                <dt className="text-xs text-muted">Nomor CIF</dt>
                                <dd className="text-sm">{loan.cif_number ?? '–'}</dd>
                            </div>
                        </dl>
                        {loan.committee_conflict && (
                            <Alert tone="warning" icon={<ShieldAlert />}>
                                <strong>
                                    Pemohon adalah anggota komite: {loan.committee_conflict.name}
                                    {loan.committee_conflict.role ? ` (${loan.committee_conflict.role})` : ''}.
                                </strong>{' '}
                                {loan.committee_conflict.source === 'nik' ? 'Dikenali dari NIK. ' : 'Ditandai manual. '}
                                Ia tidak bisa memutus berkas ini, menjadi Kasi Analis atau mensurveinya, dan persetujuan melewatinya.
                            </Alert>
                        )}
                    </CardBody>
                </Step>

                <form onSubmit={save} noValidate>
                    <Step number={2} title="Data pengajuan" done={loan.checklist.application}>
                        <CardBody>
                            <fieldset disabled={!editable} className="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                <Field label="Tanggal pengajuan" required error={errors.application_date}>
                                    <DatePicker
                                        id="application_date"
                                        min={new Date(2020, 0, 1)}
                                        max={new Date()}
                                        value={data.application_date}
                                        onChange={(v) => setData('application_date', v)}
                                        invalid={!!errors.application_date}
                                    />
                                </Field>
                                <Field label="Produk" required error={errors.product_id} className="lg:col-span-2">
                                    <Combobox
                                        id="product_id"
                                        options={refs.products}
                                        value={data.product_id}
                                        onChange={chooseProduct}
                                        invalid={!!errors.product_id}
                                    />
                                </Field>
                                <Field label="Kategori" required error={errors.committee_path_id} hint="Menentukan jalur komite">
                                    <Combobox
                                        id="committee_path_id"
                                        searchable={false}
                                        placeholder={data.product_id ? 'Pilih…' : 'Pilih produk dulu'}
                                        options={categories}
                                        value={data.committee_path_id}
                                        onChange={(v) => setData('committee_path_id', v ?? '')}
                                        invalid={!!errors.committee_path_id}
                                    />
                                </Field>
                                <Field
                                    label="Plafon kredit"
                                    required
                                    error={errors.requested_amount}
                                    hint={parameter ? `${rupiah(parameter.min_amount)} sampai ${rupiah(parameter.max_amount)}` : undefined}
                                >
                                    <CurrencyInput
                                        id="requested_amount"
                                        value={data.requested_amount}
                                        onValueChange={(v) => setData('requested_amount', v)}
                                        aria-invalid={!!errors.requested_amount}
                                    />
                                </Field>
                                <Field
                                    label="Tenor (bulan)"
                                    required
                                    error={errors.requested_tenor}
                                    hint={
                                        parameter
                                            ? `${parameter.min_tenor} sampai ${parameter.max_tenor} bulan${period > 1 ? `, kelipatan ${period}` : ''}`
                                            : undefined
                                    }
                                >
                                    <MaskedInput
                                        id="requested_tenor"
                                        mask="999"
                                        className="text-right tabular-nums"
                                        value={data.requested_tenor}
                                        onValueChange={(v) => setData('requested_tenor', v)}
                                        aria-invalid={!!errors.requested_tenor}
                                    />
                                </Field>
                                <Field label="Metode bunga" required error={errors.method_id}>
                                    <Combobox
                                        id="method_id"
                                        options={methods}
                                        value={data.method_id}
                                        onChange={(v) => setData('method_id', v ?? '')}
                                        invalid={!!errors.method_id}
                                    />
                                </Field>
                                <Field label="Sistem angsuran" required error={errors.installment_id}>
                                    <Combobox
                                        id="installment_id"
                                        options={installments}
                                        value={data.installment_id}
                                        onChange={(v) => setData('installment_id', v ?? '')}
                                        invalid={!!errors.installment_id}
                                    />
                                </Field>
                                <Field label="Suku bunga (%)" required error={errors.interest_rate} hint="0 sampai 100, desimal memakai titik">
                                    <FilteredInput
                                        id="interest_rate"
                                        allowed={RATE_CHARACTER}
                                        inputMode="decimal"
                                        maxLength={6}
                                        className="text-right tabular-nums"
                                        value={data.interest_rate}
                                        onValueChange={(v) => setData('interest_rate', v)}
                                        aria-invalid={!!errors.interest_rate}
                                    />
                                </Field>
                                <Field label="Penggunaan" required error={errors.usage_type}>
                                    <Combobox
                                        id="usage_type"
                                        searchable={false}
                                        options={refs.usageTypes}
                                        value={data.usage_type}
                                        onChange={(v) => setData('usage_type', v ?? '')}
                                        invalid={!!errors.usage_type}
                                    />
                                </Field>
                                <Field label="Kantor" required error={errors.office_id}>
                                    <Combobox
                                        id="office_id"
                                        options={refs.offices}
                                        value={data.office_id}
                                        onChange={(v) => setData('office_id', v ?? '')}
                                        invalid={!!errors.office_id}
                                    />
                                </Field>
                                <Field label="Kasi Analis" required error={errors.supervisor_id} hint="Menjadwalkan survei">
                                    <Combobox
                                        id="supervisor_id"
                                        options={refs.supervisors.filter((s) => String(s.value) !== data.committee_conflict_user_id)}
                                        value={data.supervisor_id}
                                        onChange={(v) => setData('supervisor_id', v ?? '')}
                                        invalid={!!errors.supervisor_id}
                                    />
                                </Field>
                                <Field
                                    label="Pemohon adalah anggota komite"
                                    error={errors.committee_conflict_user_id}
                                    hint={
                                        loan.committee_conflict?.source === 'nik'
                                            ? 'Dikenali dari NIK, tidak bisa diubah.'
                                            : 'Hanya bila sistem tidak mengenalinya.'
                                    }
                                >
                                    <fieldset disabled={loan.committee_conflict?.source === 'nik'}>
                                        <Combobox
                                            id="committee_conflict_user_id"
                                            clearable
                                            placeholder="Tidak"
                                            options={refs.committeeMembers}
                                            value={data.committee_conflict_user_id}
                                            onChange={(v) =>
                                                setData((current) => ({
                                                    ...current,
                                                    committee_conflict_user_id: v ?? '',
                                                    // The applicant cannot be the section head of their own file.
                                                    supervisor_id: v && current.supervisor_id === v ? '' : current.supervisor_id,
                                                }))
                                            }
                                        />
                                    </fieldset>
                                </Field>
                                <Field label="Resort" error={errors.institution_id}>
                                    <Combobox
                                        id="institution_id"
                                        clearable
                                        options={refs.institutions}
                                        value={data.institution_id}
                                        onChange={(v) => setData('institution_id', v ?? '')}
                                    />
                                </Field>
                                <Field label="Pemasar" error={errors.marketing}>
                                    <Input
                                        className="uppercase"
                                        value={data.marketing}
                                        maxLength={100}
                                        onChange={(e) => setData('marketing', e.target.value)}
                                    />
                                </Field>
                                <Field label="Catatan" error={errors.note} className="sm:col-span-2">
                                    <Input value={data.note} maxLength={255} onChange={(e) => setData('note', e.target.value)} />
                                </Field>
                            </fieldset>
                        </CardBody>
                        {editable && (
                            <CardFooter>
                                <p className="text-xs text-muted">Kolom bertanda * wajib diisi.</p>
                                <Button type="submit" loading={form.processing}>
                                    Simpan
                                </Button>
                            </CardFooter>
                        )}
                    </Step>
                </form>

                <Step
                    number={3}
                    title="Jaminan"
                    done={loan.checklist.collateral && collaterals.length > 0}
                    action={
                        editable && (
                            <Button size="sm" variant="outline" onClick={() => setNewCollateral(true)}>
                                <Plus /> Jaminan baru
                            </Button>
                        )
                    }
                >
                    {editable && (
                        <div className="flex flex-wrap items-center gap-2 border-b border-line p-3">
                            <Combobox
                                className="min-w-64 flex-1"
                                aria-label="Jaminan yang sudah ada"
                                placeholder="Lekatkan jaminan yang sudah ada…"
                                options={attachable}
                                value={attach}
                                onChange={(v) => setAttach(v ?? '')}
                            />
                            <Button
                                disabled={!attach}
                                onClick={() =>
                                    router.post(
                                        `/loan-applications/${loan.id}/collaterals`,
                                        { collateral_id: attach },
                                        { preserveScroll: true, onSuccess: () => setAttach('') },
                                    )
                                }
                            >
                                Lekatkan
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
                            title: 'Belum ada jaminan',
                            description: loan.collateral_required ? 'Produk ini mewajibkan jaminan.' : 'Jaminan opsional untuk produk ini.',
                        }}
                    />
                </Step>

                {locations.some((p) => p.location) && (
                    <Card>
                        <CardHeader title="Lokasi" />
                        <LocationsPanel places={locations} />
                    </Card>
                )}

                {loan.status === 'draft' && (
                    <Card>
                        <CardHeader title="Ajukan" description="Berkas bisa diajukan setelah ketiga langkah lengkap." />
                        <CardBody>
                            <ul className="flex flex-col gap-1">
                                {checklist.map(([label, ok]) => (
                                    <li key={label} className="flex items-center gap-2 text-sm">
                                        {ok ? <Check className="size-3.5 text-success" /> : <CircleAlert className="size-3.5 text-warning" />}
                                        <span className={ok ? '' : 'text-muted'}>{label}</span>
                                    </li>
                                ))}
                            </ul>
                        </CardBody>
                        {editable && (
                            <CardFooter>
                                <Button variant="outline" onClick={() => setConfirmDelete(true)}>
                                    Hapus draf
                                </Button>
                                <Button disabled={!ready} onClick={() => router.post(`/loan-applications/${loan.id}/confirm`, {}, { preserveScroll: true })}>
                                    Ajukan pengajuan
                                </Button>
                            </CardFooter>
                        )}
                    </Card>
                )}
            </div>

            <NewCollateral open={newCollateral} onOpenChange={setNewCollateral} loanId={loan.id} refs={refs} />
            <ConfirmDialog
                open={confirmDelete}
                onOpenChange={setConfirmDelete}
                title="Hapus draf?"
                description={
                    <>
                        Pengajuan <strong>{loan.application_code}</strong> atas nama {loan.full_name} akan dihapus beserta data yang sudah diisi.
                    </>
                }
                onConfirm={() => router.delete(`/loan-applications/${loan.id}`)}
            />
        </>
    );
}
