import { Head, router, useForm } from '@inertiajs/react';
import { ArrowLeft, ShieldCheck } from 'lucide-react';
import { useEffect, useState } from 'react';
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

export default function TwoFactorChallenge({
    method,
    email,
    resendIn,
    name,
}: Props) {
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
        <div className="flex min-h-screen items-center justify-center p-4">
            <Head title="Verification" />
            <div className="w-full max-w-xs">
                <div className="mb-4 flex flex-col items-center gap-1">
                    <span className="flex size-9 items-center justify-center rounded-lg bg-primary text-white">
                        <ShieldCheck className="size-5" />
                    </span>
                    <h1 className="text-base font-semibold">
                        Two-factor verification
                    </h1>
                    <p className="text-center text-xs text-muted">
                        {method === 'email'
                            ? `Hi ${name}, enter the code we sent to ${email}.`
                            : recovery
                              ? `Hi ${name}, enter one of your recovery codes.`
                              : `Hi ${name}, enter the code from your authenticator app.`}
                    </p>
                </div>
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
                        <Field
                            label={
                                recovery ? 'Recovery code' : 'Verification code'
                            }
                            error={form.errors.code}
                        >
                            <CodeInput
                                autoFocus
                                recovery={recovery}
                                value={form.data.code}
                                onValueChange={(value) =>
                                    form.setData('code', value)
                                }
                                aria-invalid={!!form.errors.code}
                                placeholder={
                                    recovery ? 'xxxxx-xxxxx' : '••••••'
                                }
                            />
                        </Field>
                        <Button
                            type="submit"
                            loading={form.processing}
                            disabled={
                                form.data.code.length < (recovery ? 11 : 6)
                            }
                        >
                            Verify
                        </Button>
                        <div className="flex flex-col items-center gap-1.5 text-xs">
                            {method === 'email' && (
                                <button
                                    type="button"
                                    disabled={wait > 0}
                                    onClick={() =>
                                        router.post(
                                            '/two-factor-challenge/resend',
                                            {},
                                            { preserveScroll: true },
                                        )
                                    }
                                    className="cursor-pointer text-primary hover:underline disabled:cursor-not-allowed disabled:text-muted disabled:no-underline"
                                >
                                    {wait > 0
                                        ? `Send a new code in ${wait}s`
                                        : 'Send a new code'}
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
                                    {recovery
                                        ? 'Use the authenticator app instead'
                                        : 'Use a recovery code'}
                                </button>
                            )}
                            <button
                                type="button"
                                onClick={() =>
                                    router.post('/two-factor-challenge/cancel')
                                }
                                className="flex cursor-pointer items-center gap-1 text-muted hover:text-ink"
                            >
                                <ArrowLeft className="size-3" /> Back to sign in
                            </button>
                        </div>
                    </form>
                </Card>
            </div>
        </div>
    );
}
