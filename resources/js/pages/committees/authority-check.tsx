import { AlertTriangle, Scale } from 'lucide-react';
import { useHttp } from '@inertiajs/react';
import { useState } from 'react';
import { authority } from '@/actions/App/Http/Controllers/CommitteeController';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { DialogBody, DialogFooter, Modal } from '@/components/ui/dialog';
import { Field } from '@/components/ui/field';
import { CurrencyInput } from '@/components/ui/currency-input';
import { Alert, Badge } from '@/components/ui/misc';
import type { BadgeTone } from '@/components/ui/misc';

type ChainRow = {
    id: number;
    label: string | null;
    role: string;
    min_amount: number | null;
    max_amount: number | null;
    decisions: string[];
    status: 'decider' | 'escalate' | 'blocked' | 'not_needed' | 'skipped';
    status_label: string;
    user_count: number;
};

type Result = {
    found: boolean;
    message?: string;
    path?: {
        title: string;
        product_label: string;
        condition_label: string;
        mechanism_label: string;
        matched_globally: boolean;
    };
    chain: ChainRow[];
    applicant?: { id: number; name: string; role: string | null } | null;
    exception?: string | null;
    warnings: string[];
};

const tones: Record<ChainRow['status'], BadgeTone> = {
    decider: 'success',
    escalate: 'info',
    blocked: 'danger',
    not_needed: 'neutral',
    skipped: 'warning',
};

/** Read-only check of which tier decides a given product, condition and amount. */
const chainColumns: Column<ChainRow>[] = [
    { key: 'tier', header: 'Jenjang', cell: (row) => row.label ?? '–' },
    { key: 'role', header: 'Peran', cell: (row) => row.role },
    {
        key: 'result',
        header: 'Hasil',
        cell: (row) => <Badge tone={tones[row.status]}>{row.status_label}</Badge>,
    },
    {
        key: 'users',
        header: 'Pengguna',
        align: 'right',
        className: 'tabular-nums',
        cell: (row) => row.user_count,
    },
];

type Option = { value: number | string; label: string };

/** What the dialog offers to choose from; every tab of Data Komite passes the same. */
export type AuthorityOptions = {
    productOptions: Option[];
    conditionMap: Record<string, string[]>;
    committeeMembers: Option[];
};

/** The button of the page header together with its dialog, so every tab of Data Komite carries the check the same way. */
export function AuthorityCheckButton({ options }: { options: AuthorityOptions }) {
    const [open, setOpen] = useState(false);

    return (
        <>
            <Button variant="outline" onClick={() => setOpen(true)}>
                <Scale /> Cek wewenang
            </Button>
            <AuthorityCheck
                open={open}
                onOpenChange={setOpen}
                productOptions={options.productOptions}
                conditionMap={options.conditionMap}
                memberOptions={options.committeeMembers}
            />
        </>
    );
}

function AuthorityCheck({
    open,
    onOpenChange,
    productOptions,
    conditionMap,
    memberOptions,
}: {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    productOptions: Option[];
    conditionMap: Record<string, string[]>;
    memberOptions: Option[];
}) {
    const http = useHttp<{ product_id: string; condition: string; amount: number | null; applicant_id: string }, Result>({
        product_id: '',
        condition: '',
        amount: null,
        applicant_id: '',
    });
    const { product_id: product, condition, amount, applicant_id: applicant } = http.data;
    const [result, setResult] = useState<Result | null>(null);
    const [error, setError] = useState<string | null>(null);

    const conditions = [...new Set([...(conditionMap[product] ?? []), ...(conditionMap.global ?? [])])].sort();
    const options = conditions.map((c) => ({
        value: c,
        label: c === '' ? 'Normal' : c,
    }));
    const validAmount = amount !== null;

    const run = () => {
        setError(null);
        http.get(authority.url(), {
            onSuccess: (response) => setResult(response),
            onError: () => setError('Periksa kembali plafonnya.'),
        }).catch(() => setError('Pengecekan gagal dijalankan.'));
    };

    return (
        <Modal
            open={open}
            onOpenChange={onOpenChange}
            wide
            persistent
            title="Cek wewenang"
            description="Cari tahu siapa yang memutus. Tidak ada yang disimpan."
        >
            <form
                noValidate
                onSubmit={(e) => {
                    e.preventDefault();

                    if (validAmount) {
                        run();
                    }
                }}
            >
                <DialogBody className="flex flex-col gap-3">
                    <div className="grid gap-3 sm:grid-cols-3">
                        <Field label="Produk">
                            <Combobox
                                clearable
                                placeholder="Semua produk"
                                options={productOptions}
                                value={product}
                                onChange={(v) => {
                                    http.setData({ ...http.data, product_id: v ?? '', condition: '' });
                                    setResult(null);
                                }}
                            />
                        </Field>
                        <Field label="Kondisi">
                            <Combobox
                                searchable={false}
                                placeholder="Normal"
                                options={options}
                                value={condition}
                                onChange={(v) => {
                                    http.setData('condition', v ?? '');
                                    setResult(null);
                                }}
                            />
                        </Field>
                        <Field label="Plafon">
                            <CurrencyInput
                                value={amount}
                                onValueChange={(v) => {
                                    http.setData('amount', v);
                                    setResult(null);
                                }}
                            />
                        </Field>
                    </div>
                    <Field
                        label="Pemohon adalah anggota komite"
                        hint="Mensimulasikan anggota yang mengajukan kredit: ia tidak boleh memutus berkasnya sendiri, jadi persetujuan melewatinya."
                    >
                        <Combobox
                            clearable
                            placeholder="Tidak"
                            options={memberOptions}
                            value={applicant}
                            onChange={(v) => {
                                http.setData('applicant_id', v ?? '');
                                setResult(null);
                            }}
                        />
                    </Field>
                    {error && <Alert tone="danger">{error}</Alert>}

                    {result && !result.found && <Alert tone="neutral">{result.message}</Alert>}
                    {result?.found && result.path && (
                        <div className="flex flex-col gap-2">
                            <p className="text-sm">
                                <strong>{result.path.title}</strong> · {result.path.mechanism_label}
                                {result.path.matched_globally && <span className="text-muted"> (jalur lintas produk)</span>}
                            </p>
                            <div className="rounded-md border border-line">
                                <DataTable
                                    bare
                                    dense
                                    rows={result.chain}
                                    rowKey={(row) => row.id}
                                    columns={chainColumns}
                                    empty={{
                                        icon: <AlertTriangle />,
                                        title: 'Tidak ada jenjang',
                                    }}
                                />
                            </div>
                            {result.applicant && (
                                <Alert tone="warning">
                                    Pemohon:{' '}
                                    <strong>
                                        {result.applicant.name}
                                        {result.applicant.role ? ` (${result.applicant.role})` : ''}
                                    </strong>
                                    {result.chain.some((r) => r.status === 'skipped')
                                        ? '. Jenjang bertanda dilewati tidak ikut memutus.'
                                        : '. Jenjangnya tetap: pemegang peran lain yang bertindak.'}
                                </Alert>
                            )}
                            {result.exception && (
                                <Alert tone="danger" icon={<AlertTriangle />}>
                                    <strong>Pengecualian.</strong> {result.exception}
                                </Alert>
                            )}
                            {result.warnings.map((w) => (
                                <Alert key={w} tone="warning" icon={<AlertTriangle />}>
                                    {w}
                                </Alert>
                            ))}
                        </div>
                    )}
                </DialogBody>
                <DialogFooter>
                    <Button variant="outline" onClick={() => onOpenChange(false)}>
                        Batal
                    </Button>
                    <Button type="submit" loading={http.processing} disabled={!validAmount}>
                        Cek
                    </Button>
                </DialogFooter>
            </form>
        </Modal>
    );
}
