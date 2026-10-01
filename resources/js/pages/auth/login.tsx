import { Form, Head } from '@inertiajs/react';
import { Field } from '@/components/ui/field';
import { NetworkStatus } from '@/components/network-status';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/misc';
import { Input } from '@/components/ui/input';
import { PasswordInput } from '@/components/ui/password-input';
import LoginController from '@/actions/App/Http/Controllers/Auth/LoginController';

export default function Login() {
    return (
        <div className="flex min-h-screen items-center justify-center p-4">
            <NetworkStatus />
            <Head title="Masuk" />
            <div className="w-full max-w-xs">
                <div className="mb-4 flex flex-col items-center gap-1">
                    <span className="flex size-9 items-center justify-center rounded-lg bg-primary text-base font-bold text-white">
                        S
                    </span>
                    <h1 className="text-base font-semibold">SIPEBRI</h1>
                    <p className="text-center text-xs text-muted">
                        Silakan masuk menggunakan akun Anda dengan email atau
                        username dan kata sandi yang telah terdaftar.
                    </p>
                </div>
                <Card className="p-4">
                    <Form
                        action={LoginController.store()}
                        resetOnSuccess={['password']}
                        className="flex flex-col gap-3"
                    >
                        {({ errors, processing }) => (
                            <>
                                <Field
                                    label="Kredensial"
                                    error={errors.username}
                                >
                                    <Input
                                        name="username"
                                        type="text"
                                        autoComplete="username"
                                        autoFocus
                                        required
                                        aria-invalid={!!errors.username}
                                        placeholder="Email atau Username"
                                    />
                                </Field>
                                <Field
                                    label="Kata sandi"
                                    error={errors.password}
                                >
                                    <PasswordInput
                                        name="password"
                                        autoComplete="current-password"
                                        required
                                        aria-invalid={!!errors.password}
                                        placeholder="************************"
                                    />
                                </Field>
                                <label className="flex items-center gap-2 text-xs text-muted">
                                    <input
                                        type="checkbox"
                                        name="remember"
                                        className="accent-primary"
                                    />
                                    Ingat saya
                                </label>
                                <Button
                                    type="submit"
                                    loading={processing}
                                    className="w-full"
                                >
                                    Masuk
                                </Button>
                            </>
                        )}
                    </Form>
                </Card>
            </div>
        </div>
    );
}
