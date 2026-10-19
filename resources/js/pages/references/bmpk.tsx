import { Head, useForm } from '@inertiajs/react';
import { Save } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { CurrencyInput } from '@/components/ui/currency-input';
import { Field } from '@/components/ui/field';
import { Card, CardBody, PageHeader } from '@/components/ui/misc';

export default function Bmpk({ amount }: { amount: number | null }) {
    const form = useForm<{ amount: number | null }>({ amount });

    return (
        <>
            <Head title="BMPK" />
            <PageHeader
                title="BMPK"
                description="Batas Maksimum Pemberian Kredit. Tidak ada pengajuan yang boleh melebihinya, berapa pun batas produknya."
                actions={
                    <Button
                        loading={form.processing}
                        onClick={() =>
                            form.put('/references/bmpk', {
                                preserveScroll: true,
                            })
                        }
                    >
                        <Save /> Simpan
                    </Button>
                }
            />
            <Card>
                <CardBody className="grid gap-3 sm:max-w-sm">
                    <Field label="Maksimum per kredit" error={form.errors.amount}>
                        <CurrencyInput id="amount" maxDigits={12} value={form.data.amount} onValueChange={(value) => form.setData('amount', value)} />
                    </Field>
                </CardBody>
            </Card>
        </>
    );
}
