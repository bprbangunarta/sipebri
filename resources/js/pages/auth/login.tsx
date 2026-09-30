import { Form, Head } from '@inertiajs/react';
import { Field } from '@/components/ui/field';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/misc';
import { Input } from '@/components/ui/input';
import { PasswordInput } from '@/components/ui/password-input';
import LoginController from '@/actions/App/Http/Controllers/Auth/LoginController';

export default function Login() {
    return (
        <div className="flex min-h-screen items-center justify-center p-4">
            <Head title="Log in" />
            <div className="w-full max-w-xs">
                <div className="mb-4 flex flex-col items-center gap-1">
                    <span className="flex size-9 items-center justify-center rounded-lg bg-primary text-base font-bold text-white">
                        S
                    </span>
                    <h1 className="text-base font-semibold">SIPEBRI</h1>
                    <p className="text-xs text-muted">
                        Sign in to your account
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
                                <Field label="Username" error={errors.username}>
                                    <Input
                                        name="username"
                                        type="text"
                                        autoComplete="username"
                                        autoFocus
                                        required
                                        aria-invalid={!!errors.username}
                                        placeholder="Your Codex username"
                                    />
                                </Field>
                                <Field label="Password" error={errors.password}>
                                    <PasswordInput
                                        name="password"
                                        autoComplete="current-password"
                                        required
                                        aria-invalid={!!errors.password}
                                    />
                                </Field>
                                <label className="flex items-center gap-2 text-xs text-muted">
                                    <input
                                        type="checkbox"
                                        name="remember"
                                        className="accent-primary"
                                    />
                                    Remember me
                                </label>
                                <Button
                                    type="submit"
                                    loading={processing}
                                    className="w-full"
                                >
                                    Log in
                                </Button>
                            </>
                        )}
                    </Form>
                </Card>
            </div>
        </div>
    );
}
