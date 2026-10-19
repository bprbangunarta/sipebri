import { Form } from '@inertiajs/react';
import { Field } from '@/components/ui/field';
import { AuthLayout } from '@/components/auth-layout';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/choice';
import { Card } from '@/components/ui/misc';
import { Input } from '@/components/ui/input';
import { PasswordInput } from '@/components/ui/password-input';
import LoginController from '@/actions/App/Http/Controllers/Auth/LoginController';

export default function Login() {
    return (
        <AuthLayout title="Masuk" description="Gunakan email atau username dan kata sandi Codex Anda." illustration="login">
            <Card className="p-4">
                <Form action={LoginController.store()} resetOnSuccess={['password']} className="flex flex-col gap-3">
                    {({ errors, processing }) => (
                        <>
                            <Field label="Kredensial" error={errors.username}>
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
                            <Field label="Kata sandi" error={errors.password}>
                                <PasswordInput
                                    name="password"
                                    autoComplete="current-password"
                                    required
                                    aria-invalid={!!errors.password}
                                    placeholder="************************"
                                />
                            </Field>
                            <Checkbox name="remember" value="1" label="Ingat saya" />
                            <Button type="submit" loading={processing} className="w-full">
                                Masuk
                            </Button>
                        </>
                    )}
                </Form>
            </Card>
        </AuthLayout>
    );
}
