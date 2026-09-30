import { AlertTriangle } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
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
    status: 'decider' | 'escalate' | 'blocked' | 'not_needed';
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
    warnings: string[];
};

const tones: Record<ChainRow['status'], BadgeTone> = {
    decider: 'success',
    escalate: 'info',
    blocked: 'danger',
    not_needed: 'neutral',
};

/** Read-only check of which tier decides a given product, condition and amount. */
export function AuthorityCheck({
    open,
    onOpenChange,
    productOptions,
    conditionMap,
}: {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    productOptions: { value: number | string; label: string }[];
    conditionMap: Record<string, string[]>;
}) {
    const [product, setProduct] = useState('');
    const [condition, setCondition] = useState('');
    const [amount, setAmount] = useState('');
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
                        <div className="relative overflow-x-auto rounded-md border border-line">
                            <table className="w-full text-sm">
                                <thead className="border-b border-line bg-canvas text-xs text-muted">
                                    <tr>
                                        <th className="px-2 py-1.5 text-left font-medium">
                                            Tier
                                        </th>
                                        <th className="px-2 py-1.5 text-left font-medium">
                                            Role
                                        </th>
                                        <th className="px-2 py-1.5 text-left font-medium">
                                            Result
                                        </th>
                                        <th className="px-2 py-1.5 text-right font-medium">
                                            Users
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-line">
                                    {result.chain.map((row) => (
                                        <tr key={row.id}>
                                            <td className="px-2 py-1.5">
                                                {row.label ?? '–'}
                                            </td>
                                            <td className="px-2 py-1.5">
                                                {row.role}
                                            </td>
                                            <td className="px-2 py-1.5">
                                                <Badge tone={tones[row.status]}>
                                                    {row.status_label}
                                                </Badge>
                                            </td>
                                            <td className="px-2 py-1.5 text-right tabular-nums">
                                                {row.user_count}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
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
