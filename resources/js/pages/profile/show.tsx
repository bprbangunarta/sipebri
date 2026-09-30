import { Head, router, useForm } from '@inertiajs/react';
import { QRCodeSVG } from 'qrcode.react';
import {
    Copy,
    KeyRound,
    Mail,
    Save,
    ShieldCheck,
    ShieldOff,
    Smartphone,
} from 'lucide-react';
import type { ReactNode } from 'react';
import { useState } from 'react';
import { toast } from 'sonner';
import { Button } from '@/components/ui/button';
import { CodeInput } from '@/components/ui/code-input';
import { DialogFooter, Modal } from '@/components/ui/dialog';
import { Field } from '@/components/ui/field';
import { Badge, Card, PageHeader } from '@/components/ui/misc';
import { PasswordInput } from '@/components/ui/password-input';

type Props = {
    account: {
        name: string;
        username: string | null;
        email: string;
        office: string | null;
        role: string | null;
    };
    twoFactor: {
        enabled: boolean;
        method: 'totp' | 'email' | null;
        emailAvailable: boolean;
        recoveryRemaining: number;
        setup: { secret: string; uri: string } | null;
        emailCodeSent: boolean;
        resendIn: number;
        recoveryCodes: string[] | null;
    };
};

function Detail({ label, children }: { label: string; children: ReactNode }) {
    return (
        <div className="min-w-0">
            <dt className="text-xs text-muted">{label}</dt>
            <dd className="truncate text-sm font-medium">{children || '–'}</dd>
        </div>
    );
}

/** Confirm a code with the server (`method` + `url`), showing errors under the field. */
function CodeForm({
    url,
    method = 'post',
    submitLabel,
    onDone,
    recovery = false,
    children,
}: {
    url: string;
    method?: 'post' | 'delete';
    submitLabel: string;
    onDone: () => void;
    recovery?: boolean;
    children?: ReactNode;
}) {
    const form = useForm({ code: '' });

    return (
        <form
            noValidate
            onSubmit={(e) => {
                e.preventDefault();
                form[method](url, {
                    preserveScroll: true,
                    onSuccess: () => {
                        form.reset();
                        onDone();
                    },
                });
            }}
        >
            <div className="flex flex-col gap-3 p-4">
                {children}
                <Field
                    label={
                        recovery ? 'Code or recovery code' : 'Verification code'
                    }
                    error={form.errors.code}
                >
                    <CodeInput
                        recovery={recovery}
                        value={form.data.code}
                        onValueChange={(value) => form.setData('code', value)}
                        aria-invalid={!!form.errors.code}
                        placeholder={
                            recovery ? 'code or xxxxx-xxxxx' : '••••••'
                        }
                    />
                </Field>
            </div>
            <DialogFooter>
                <Button variant="outline" onClick={onDone}>
                    Cancel
                </Button>
                <Button
                    type="submit"
                    loading={form.processing}
                    variant={method === 'delete' ? 'danger' : 'primary'}
                    disabled={form.data.code.length < 6}
                >
                    {submitLabel}
                </Button>
            </DialogFooter>
        </form>
    );
}

function SendCode({ resendIn, sent }: { resendIn: number; sent: boolean }) {
    return (
        <div className="flex items-center gap-2">
            <Button
                variant="outline"
                size="sm"
                disabled={resendIn > 0}
                onClick={() =>
                    router.post(
                        '/profile/two-factor/email/send',
                        {},
                        { preserveScroll: true },
                    )
                }
            >
                <Mail /> {sent ? 'Send a new code' : 'Send code'}
            </Button>
            {resendIn > 0 && (
                <span className="text-xs text-muted">
                    Available again in {resendIn}s
                </span>
            )}
        </div>
    );
}

function RecoveryCodes({
    codes,
    onClose,
}: {
    codes: string[] | null;
    onClose: () => void;
}) {
    return (
        <Modal
            open={codes !== null}
            onOpenChange={(open) => !open && onClose()}
            title="Save your recovery codes"
            description="Each code works once if you lose your phone. They are shown only now."
        >
            <div className="p-4">
                <ul className="grid grid-cols-2 gap-1.5 rounded-md border border-line bg-canvas p-3 font-mono text-sm">
                    {codes?.map((code) => (
                        <li key={code}>{code}</li>
                    ))}
                </ul>
            </div>
            <DialogFooter>
                <Button
                    variant="outline"
                    onClick={() => {
                        void navigator.clipboard.writeText(
                            (codes ?? []).join('\n'),
                        );
                        toast.success('Recovery codes copied.');
                    }}
                >
                    <Copy /> Copy
                </Button>
                <Button onClick={onClose}>I saved them</Button>
            </DialogFooter>
        </Modal>
    );
}

function PasswordCard() {
    const form = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    return (
        <Card>
            <h2 className="border-b border-line px-3 py-2 text-sm font-semibold">
                Password
            </h2>
            <form
                noValidate
                className="flex flex-col gap-3 p-3"
                onSubmit={(e) => {
                    e.preventDefault();
                    form.put('/profile/password', {
                        preserveScroll: true,
                        onSuccess: () => form.reset(),
                    });
                }}
            >
                <Field
                    label="Current password"
                    required
                    error={form.errors.current_password}
                >
                    <PasswordInput
                        autoComplete="current-password"
                        value={form.data.current_password}
                        onChange={(e) =>
                            form.setData('current_password', e.target.value)
                        }
                        aria-invalid={!!form.errors.current_password}
                    />
                </Field>
                <div className="grid gap-3 sm:grid-cols-2">
                    <Field
                        label="New password"
                        required
                        error={form.errors.password}
                        hint="At least 8 characters"
                    >
                        <PasswordInput
                            autoComplete="new-password"
                            value={form.data.password}
                            onChange={(e) =>
                                form.setData('password', e.target.value)
                            }
                            aria-invalid={!!form.errors.password}
                        />
                    </Field>
                    <Field
                        label="Confirm new password"
                        required
                        error={form.errors.password_confirmation}
                    >
                        <PasswordInput
                            autoComplete="new-password"
                            value={form.data.password_confirmation}
                            onChange={(e) =>
                                form.setData(
                                    'password_confirmation',
                                    e.target.value,
                                )
                            }
                            aria-invalid={!!form.errors.password_confirmation}
                        />
                    </Field>
                </div>
                <div className="flex items-center justify-between gap-2">
                    <p className="text-xs text-muted">
                        Your password is kept in Codex and applies to every
                        system that uses it.
                    </p>
                    <Button
                        type="submit"
                        loading={form.processing}
                        disabled={
                            !form.data.current_password || !form.data.password
                        }
                    >
                        <Save /> Change password
                    </Button>
                </div>
            </form>
        </Card>
    );
}

export default function ProfileShow({ account, twoFactor }: Props) {
    const [emailOpen, setEmailOpen] = useState(false);
    const [disableOpen, setDisableOpen] = useState(false);
    const [codesOpen, setCodesOpen] = useState(
        twoFactor.recoveryCodes !== null,
    );
    const { method, enabled } = twoFactor;
    const startTotp = () =>
        router.post(
            '/profile/two-factor/totp/start',
            {},
            { preserveScroll: true },
        );
    const cancelTotp = () =>
        router.post(
            '/profile/two-factor/totp/cancel',
            {},
            { preserveScroll: true },
        );

    return (
        <>
            <Head title="Profile" />
            <PageHeader
                title="Profile"
                description="Your account and how you sign in"
            />

            <div className="grid gap-3 lg:grid-cols-2">
                <div className="flex flex-col gap-3">
                    <Card>
                        <h2 className="border-b border-line px-3 py-2 text-sm font-semibold">
                            Account
                        </h2>
                        <dl className="grid gap-3 p-3 sm:grid-cols-2">
                            <Detail label="Name">{account.name}</Detail>
                            <Detail label="Username">{account.username}</Detail>
                            <Detail label="Email">{account.email}</Detail>
                            <Detail label="Office">{account.office}</Detail>
                            <Detail label="Role">{account.role}</Detail>
                        </dl>
                        <p className="border-t border-line px-3 py-2 text-xs text-muted">
                            These details come from Codex and are updated each
                            time you sign in.
                        </p>
                    </Card>
                    <PasswordCard />
                </div>

                <Card className="self-start">
                    <div className="flex items-center justify-between border-b border-line px-3 py-2">
                        <h2 className="text-sm font-semibold">
                            Two-factor authentication
                        </h2>
                        {enabled && (
                            <Badge tone={method ? 'success' : 'warning'}>
                                {method ? 'On' : 'Off'}
                            </Badge>
                        )}
                    </div>

                    {!enabled ? (
                        <p className="p-3 text-sm text-muted">
                            Two-factor authentication is switched off by the
                            administrator.
                        </p>
                    ) : (
                        <div className="flex flex-col divide-y divide-line">
                            <div className="flex items-start justify-between gap-3 p-3">
                                <div className="flex gap-2.5">
                                    <Smartphone className="mt-0.5 size-4 shrink-0 text-muted" />
                                    <div>
                                        <p className="text-sm font-medium">
                                            Authenticator app
                                        </p>
                                        <p className="text-xs text-muted">
                                            Codes from an app such as Google
                                            Authenticator or Microsoft
                                            Authenticator. Recommended.
                                        </p>
                                        {method === 'totp' && (
                                            <p className="mt-1 text-xs text-muted">
                                                {twoFactor.recoveryRemaining}{' '}
                                                recovery codes left
                                            </p>
                                        )}
                                    </div>
                                </div>
                                {method === 'totp' ? (
                                    <Badge tone="success">In use</Badge>
                                ) : (
                                    <Button
                                        size="sm"
                                        variant={method ? 'outline' : 'primary'}
                                        onClick={startTotp}
                                    >
                                        <ShieldCheck />{' '}
                                        {method ? 'Switch' : 'Set up'}
                                    </Button>
                                )}
                            </div>
                            <div className="flex items-start justify-between gap-3 p-3">
                                <div className="flex gap-2.5">
                                    <Mail className="mt-0.5 size-4 shrink-0 text-muted" />
                                    <div>
                                        <p className="text-sm font-medium">
                                            Email code
                                        </p>
                                        <p className="text-xs text-muted">
                                            {twoFactor.emailAvailable
                                                ? `A code is sent to ${account.email} each time you sign in.`
                                                : 'Your account has no email address that can receive codes.'}
                                        </p>
                                    </div>
                                </div>
                                {method === 'email' ? (
                                    <Badge tone="success">In use</Badge>
                                ) : (
                                    <Button
                                        size="sm"
                                        variant={method ? 'outline' : 'primary'}
                                        disabled={!twoFactor.emailAvailable}
                                        onClick={() => setEmailOpen(true)}
                                    >
                                        <KeyRound />{' '}
                                        {method ? 'Switch' : 'Set up'}
                                    </Button>
                                )}
                            </div>
                            {method && (
                                <div className="flex justify-end p-3">
                                    <Button
                                        size="sm"
                                        variant="outline"
                                        onClick={() => setDisableOpen(true)}
                                    >
                                        <ShieldOff /> Turn off
                                    </Button>
                                </div>
                            )}
                        </div>
                    )}
                </Card>
            </div>

            <Modal
                open={twoFactor.setup !== null}
                onOpenChange={(open) => !open && cancelTotp()}
                title="Set up an authenticator app"
                description="Scan the QR code, then enter the 6-digit code the app shows."
            >
                {twoFactor.setup && (
                    <CodeForm
                        url="/profile/two-factor/totp"
                        submitLabel="Turn on"
                        onDone={cancelTotp}
                    >
                        <div className="flex flex-col items-center gap-2">
                            <div className="rounded-md border border-line bg-white p-2">
                                <QRCodeSVG
                                    value={twoFactor.setup.uri}
                                    size={148}
                                />
                            </div>
                            <p className="text-xs text-muted">
                                Cannot scan? Enter this key in the app:
                            </p>
                            <code className="rounded bg-canvas px-2 py-1 font-mono text-xs break-all select-all">
                                {twoFactor.setup.secret}
                            </code>
                        </div>
                    </CodeForm>
                )}
            </Modal>

            <Modal
                open={emailOpen}
                onOpenChange={setEmailOpen}
                title="Set up email codes"
                description={`We will send a code to ${account.email} to confirm it works.`}
            >
                <CodeForm
                    url="/profile/two-factor/email"
                    submitLabel="Turn on"
                    onDone={() => setEmailOpen(false)}
                >
                    <SendCode
                        resendIn={twoFactor.resendIn}
                        sent={twoFactor.emailCodeSent}
                    />
                </CodeForm>
            </Modal>

            <Modal
                open={disableOpen}
                onOpenChange={setDisableOpen}
                title="Turn off two-factor authentication"
                description="Enter a code to confirm it is you."
            >
                <CodeForm
                    url="/profile/two-factor"
                    method="delete"
                    submitLabel="Turn off"
                    recovery={method === 'totp'}
                    onDone={() => setDisableOpen(false)}
                >
                    {method === 'email' && (
                        <SendCode
                            resendIn={twoFactor.resendIn}
                            sent={twoFactor.emailCodeSent}
                        />
                    )}
                </CodeForm>
            </Modal>

            <RecoveryCodes
                codes={codesOpen ? twoFactor.recoveryCodes : null}
                onClose={() => setCodesOpen(false)}
            />
        </>
    );
}
