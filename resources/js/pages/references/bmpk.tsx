import { Head, useForm } from '@inertiajs/react';
import { Save } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { CurrencyInput } from '@/components/ui/currency-input';
import { Field } from '@/components/ui/field';
import { Textarea } from '@/components/ui/textarea';
import { Card, CardBody, PageHeader } from '@/components/ui/misc';

export default function Bmpk({ amount }: { amount: number | null }) {
    const form = useForm<{ amount: number | null; reason: string }>({ amount, reason: '' });

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
                                onSuccess: () => form.setData('reason', ''),
                            })
                        }
                    >
                        <Save /> Simpan
                    </Button>
                }
            />
            <Card>
                <CardBody className="grid gap-3 sm:max-w-sm">
                    <Field label="Maksimum per kredit" required error={form.errors.amount}>
                        <CurrencyInput id="amount" maxDigits={12} value={form.data.amount} onValueChange={(value) => form.setData('amount', value)} />
                    </Field>
                    <Field
                        label="Alasan perubahan"
                        required
                        error={form.errors.reason}
                        hint="Wajib setiap BMPK diubah. Tersimpan di audit log bersama nilai sebelum dan sesudahnya."
                    >
                        <Textarea
                            id="reason"
                            rows={2}
                            maxLength={255}
                            value={form.data.reason}
                            onChange={(event) => form.setData('reason', event.target.value)}
                            aria-invalid={Boolean(form.errors.reason)}
                        />
                    </Field>
                </CardBody>
            </Card>
        </>
    );
}
