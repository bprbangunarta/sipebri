import { Head, useForm } from '@inertiajs/react';
import { Save } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Card, PageHeader } from '@/components/ui/misc';
import { rupiah } from '@/lib/format';

export default function Bmpk({ amount }: { amount: number | null }) {
    const form = useForm({ amount: amount === null ? '' : String(amount) });

    return (
        <>
            <Head title="BMPK" />
            <PageHeader
                title="BMPK"
                description="Legal lending limit. No loan application may exceed it, whatever the product allows."
                actions={
                    <Button
                        loading={form.processing}
                        onClick={() =>
                            form.put('/master-data/bmpk', {
                                preserveScroll: true,
                            })
                        }
                    >
                        <Save /> Save
                    </Button>
                }
            />
            <Card>
                <div className="grid gap-3 p-3 sm:max-w-sm">
                    <Field
                        label="Maximum per loan (IDR)"
                        error={form.errors.amount}
                        hint={rupiah(
                            form.data.amount === ''
                                ? null
                                : Number(form.data.amount),
                        )}
                    >
                        <Input
                            id="amount"
                            type="number"
                            min={1}
                            value={form.data.amount}
                            onChange={(e) =>
                                form.setData('amount', e.target.value)
                            }
                            aria-invalid={!!form.errors.amount}
                        />
                    </Field>
                </div>
            </Card>
        </>
    );
}
