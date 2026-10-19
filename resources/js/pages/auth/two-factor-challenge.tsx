import { router, useForm } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { useEffect, useState } from 'react';
import { AuthLayout } from '@/components/auth-layout';
import { Button } from '@/components/ui/button';
import { CodeInput } from '@/components/ui/code-input';
import { Field } from '@/components/ui/field';
import { Card } from '@/components/ui/misc';

type Props = {
    method: 'totp' | 'email';
    email: string | null;
    resendIn: number;
    name: string;
};

export default function TwoFactorChallenge({ method, email, resendIn, name }: Props) {
    const form = useForm({ code: '' });
    const [recovery, setRecovery] = useState(false);
    const [wait, setWait] = useState(resendIn);

    useEffect(() => {
        setWait(resendIn);
    }, [resendIn]);

    useEffect(() => {
        if (wait <= 0) {
            return;
        }
        const timer = setTimeout(() => setWait((seconds) => seconds - 1), 1000);

        return () => clearTimeout(timer);
    }, [wait]);

    return (
        <AuthLayout
            title="Verifikasi dua langkah"
            illustration="verify"
            description={
                method === 'email'
                    ? `Halo ${name}, masukkan kode yang kami kirim ke ${email}.`
                    : recovery
                      ? `Halo ${name}, masukkan salah satu kode pemulihan Anda.`
                      : `Halo ${name}, masukkan kode dari aplikasi authenticator Anda.`
            }
        >
            <Card className="p-4">
                <form
                    className="flex flex-col gap-3"
                    noValidate
                    onSubmit={(e) => {
                        e.preventDefault();
                        form.post('/two-factor-challenge', {
                            onError: () => form.reset('code'),
                        });
                    }}
                >
                    <Field label={recovery ? 'Kode pemulihan' : 'Kode verifikasi'} error={form.errors.code}>
                        <CodeInput
                            autoFocus
                            recovery={recovery}
                            value={form.data.code}
                            onValueChange={(value) => form.setData('code', value)}
                            aria-invalid={!!form.errors.code}
                            placeholder={recovery ? 'xxxxx-xxxxx' : '••••••'}
                        />
                    </Field>
                    <Button type="submit" loading={form.processing} disabled={form.data.code.length < (recovery ? 11 : 6)}>
                        Verifikasi
                    </Button>
                    <div className="flex flex-col items-center gap-1.5 text-xs">
                        {method === 'email' && (
                            <button
                                type="button"
                                disabled={wait > 0}
                                onClick={() => router.post('/two-factor-challenge/resend', {}, { preserveScroll: true })}
                                className="cursor-pointer text-primary hover:underline disabled:cursor-not-allowed disabled:text-muted disabled:no-underline"
                            >
                                {wait > 0 ? `Kirim kode baru dalam ${wait} dtk` : 'Kirim kode baru'}
                            </button>
                        )}
                        {method === 'totp' && (
                            <button
                                type="button"
                                onClick={() => {
                                    setRecovery((current) => !current);
                                    form.reset('code');
                                    form.clearErrors();
                                }}
                                className="cursor-pointer text-primary hover:underline"
                            >
                                {recovery ? 'Pakai aplikasi authenticator' : 'Pakai kode pemulihan'}
                            </button>
                        )}
                        <button
                            type="button"
                            onClick={() => router.post('/two-factor-challenge/cancel')}
                            className="flex cursor-pointer items-center gap-1 text-muted hover:text-ink"
                        >
                            <ArrowLeft className="size-3" /> Kembali ke halaman masuk
                        </button>
                    </div>
                </form>
            </Card>
        </AuthLayout>
    );
}
