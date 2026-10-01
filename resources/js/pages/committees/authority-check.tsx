import { AlertTriangle } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Modal } from '@/components/ui/dialog';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/misc';
import type { BadgeTone } from '@/components/ui/misc';
import { rupiah } from '@/lib/format';

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
    { key: 'tier', header: 'Tier', cell: (row) => row.label ?? '–' },
    { key: 'role', header: 'Role', cell: (row) => row.role },
    {
        key: 'result',
        header: 'Result',
        cell: (row) => (
            <Badge tone={tones[row.status]}>{row.status_label}</Badge>
        ),
    },
    {
        key: 'users',
        header: 'Users',
        align: 'right',
        className: 'tabular-nums',
        cell: (row) => row.user_count,
    },
];

export function AuthorityCheck({
    open,
    onOpenChange,
    productOptions,
    conditionMap,
    memberOptions,
}: {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    productOptions: { value: number | string; label: string }[];
    conditionMap: Record<string, string[]>;
    memberOptions: { value: number | string; label: string }[];
}) {
    const [product, setProduct] = useState('');
    const [condition, setCondition] = useState('');
    const [amount, setAmount] = useState('');
    const [applicant, setApplicant] = useState('');
    const [result, setResult] = useState<Result | null>(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const conditions = [
        ...new Set([
            ...(conditionMap[product] ?? []),
            ...(conditionMap.global ?? []),
        ]),
    ].sort();
    const options = conditions.map((c) => ({
        value: c,
        label: c === '' ? 'Normal' : c,
    }));
    const validAmount = amount !== '' && Number(amount) >= 0;

    const run = async () => {
        setLoading(true);
        setError(null);

        try {
            const query = new URLSearchParams({ amount, condition });
            if (product) {
                query.set('product_id', product);
            }
            if (applicant) {
                query.set('applicant_id', applicant);
            }
            const response = await fetch(`/committees/authority?${query}`, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error(
                    response.status === 422
                        ? 'Please check the amount.'
                        : 'Unable to run the check.',
                );
            }
            setResult(await response.json());
        } catch (e) {
            setError(
                e instanceof Error ? e.message : 'Unable to run the check.',
            );
        } finally {
            setLoading(false);
        }
    };

    return (
        <Modal
            open={open}
            onOpenChange={onOpenChange}
            title="Check authority"
            description="Find who decides. Nothing is saved."
        >
            <div className="flex max-h-[70vh] flex-col gap-3 overflow-auto p-4">
                <div className="grid gap-3 sm:grid-cols-3">
                    <Field label="Product">
                        <Combobox
                            clearable
                            placeholder="All products"
                            options={productOptions}
                            value={product}
                            onChange={(v) => {
                                setProduct(v ?? '');
                                setCondition('');
                                setResult(null);
                            }}
                        />
                    </Field>
                    <Field label="Condition">
                        <Combobox
                            searchable={false}
                            placeholder="Normal"
                            options={options}
                            value={condition}
                            onChange={(v) => {
                                setCondition(v ?? '');
                                setResult(null);
                            }}
                        />
                    </Field>
                    <Field
                        label="Amount (IDR)"
                        hint={amount ? rupiah(Number(amount)) : undefined}
                    >
                        <Input
                            type="number"
                            min={0}
                            value={amount}
                            onChange={(e) => {
                                setAmount(e.target.value);
                                setResult(null);
                            }}
                        />
                    </Field>
                </div>
                <Field
                    label="Applicant is a committee member"
                    hint="Simulates a member applying for a credit: they cannot decide their own file, so the approval skips them."
                >
                    <Combobox
                        clearable
                        placeholder="No"
                        options={memberOptions}
                        value={applicant}
                        onChange={(v) => {
                            setApplicant(v ?? '');
                            setResult(null);
                        }}
                    />
                </Field>
                <div>
                    <Button
                        loading={loading}
                        disabled={!validAmount}
                        onClick={run}
                    >
                        Check
                    </Button>
                </div>
                {error && <p className="text-xs text-danger">{error}</p>}

                {result && !result.found && (
                    <p className="rounded-md border border-line bg-canvas p-2 text-sm">
                        {result.message}
                    </p>
                )}
                {result?.found && result.path && (
                    <div className="flex flex-col gap-2">
                        <p className="text-sm">
                            <strong>{result.path.title}</strong> ·{' '}
                            {result.path.mechanism_label}
                            {result.path.matched_globally && (
                                <span className="text-muted">
                                    {' '}
                                    (cross-product path)
                                </span>
                            )}
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
                                    title: 'No tiers',
                                }}
                            />
                        </div>
                        {result.applicant && (
                            <p className="rounded-md border border-amber-200 bg-amber-50 px-2.5 py-1.5 text-xs text-amber-800">
                                Applicant:{' '}
                                <strong>
                                    {result.applicant.name}
                                    {result.applicant.role
                                        ? ` (${result.applicant.role})`
                                        : ''}
                                </strong>
                                {result.chain.some(
                                    (r) => r.status === 'skipped',
                                )
                                    ? '. The tiers marked skipped are left out.'
                                    : '. Their tier stays: another holder of the role acts.'}
                            </p>
                        )}
                        {result.exception && (
                            <p className="flex items-start gap-1.5 rounded-md border border-danger/30 bg-red-50 px-2.5 py-1.5 text-xs text-danger">
                                <AlertTriangle className="mt-0.5 size-3.5 shrink-0" />
                                <span>
                                    <strong>Exception.</strong>{' '}
                                    {result.exception}
                                </span>
                            </p>
                        )}
                        {result.warnings.map((w) => (
                            <p
                                key={w}
                                className="flex items-start gap-1.5 text-xs text-amber-700"
                            >
                                <AlertTriangle className="mt-0.5 size-3.5 shrink-0" />{' '}
                                {w}
                            </p>
                        ))}
                    </div>
                )}
            </div>
        </Modal>
    );
}
