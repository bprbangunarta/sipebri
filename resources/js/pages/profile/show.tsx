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
                        recovery
                            ? 'Kode atau kode pemulihan'
                            : 'Kode verifikasi'
                    }
                    error={form.errors.code}
                >
                    <CodeInput
                        recovery={recovery}
                        value={form.data.code}
                        onValueChange={(value) => form.setData('code', value)}
                        aria-invalid={!!form.errors.code}
                        placeholder={
                            recovery ? 'kode atau xxxxx-xxxxx' : '••••••'
                        }
                    />
                </Field>
            </div>
            <DialogFooter>
                <Button variant="outline" onClick={onDone}>
                    Batal
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
                <Mail /> {sent ? 'Kirim kode baru' : 'Kirim kode'}
            </Button>
            {resendIn > 0 && (
                <span className="text-xs text-muted">
                    Bisa lagi dalam {resendIn} dtk
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
            title="Simpan kode pemulihan Anda"
            description="Tiap kode hanya berlaku sekali bila ponsel Anda hilang. Kode ini hanya ditampilkan sekarang."
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
                        toast.success('Kode pemulihan disalin.');
                    }}
                >
                    <Copy /> Salin
                </Button>
                <Button onClick={onClose}>Sudah saya simpan</Button>
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
                Kata sandi
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
                    label="Kata sandi saat ini"
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
                        label="Kata sandi baru"
                        required
                        error={form.errors.password}
                        hint="Minimal 8 karakter"
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
                        label="Konfirmasi kata sandi baru"
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
                        Kata sandi Anda disimpan di Codex dan berlaku untuk
                        semua sistem yang memakainya.
                    </p>
                    <Button
                        type="submit"
                        loading={form.processing}
                        disabled={
                            !form.data.current_password || !form.data.password
                        }
                    >
                        <Save /> Ubah kata sandi
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
            <Head title="Profil" />
            <PageHeader
                title="Profil"
                description="Akun Anda dan cara Anda masuk"
            />

            <div className="grid gap-3 lg:grid-cols-2">
                <div className="flex flex-col gap-3">
                    <Card>
                        <h2 className="border-b border-line px-3 py-2 text-sm font-semibold">
                            Akun
                        </h2>
                        <dl className="grid gap-3 p-3 sm:grid-cols-2">
                            <Detail label="Nama">{account.name}</Detail>
                            <Detail label="Nama pengguna">
                                {account.username}
                            </Detail>
                            <Detail label="Email">{account.email}</Detail>
                            <Detail label="Kantor">{account.office}</Detail>
                            <Detail label="Peran">{account.role}</Detail>
                        </dl>
                        <p className="border-t border-line px-3 py-2 text-xs text-muted">
                            Data ini berasal dari Codex dan diperbarui setiap
                            kali Anda masuk.
                        </p>
                    </Card>
                    <PasswordCard />
                </div>

                <Card className="self-start">
                    <div className="flex items-center justify-between border-b border-line px-3 py-2">
                        <h2 className="text-sm font-semibold">
                            Verifikasi dua langkah
                        </h2>
                        {enabled && (
                            <Badge tone={method ? 'success' : 'warning'}>
                                {method ? 'Aktif' : 'Mati'}
                            </Badge>
                        )}
                    </div>

                    {!enabled ? (
                        <p className="p-3 text-sm text-muted">
                            Verifikasi dua langkah dimatikan oleh administrator.
                        </p>
                    ) : (
                        <div className="flex flex-col divide-y divide-line">
                            <div className="flex items-center justify-between gap-3 p-3">
                                <div className="flex gap-2.5">
                                    <Smartphone className="mt-0.5 size-4 shrink-0 text-muted" />
                                    <div>
                                        <p className="text-sm font-medium">
                                            Aplikasi authenticator
                                        </p>
                                        <p className="text-xs text-muted">
                                            Kode dari aplikasi seperti Google
                                            Authenticator atau Microsoft
                                            Authenticator. Disarankan.
                                        </p>
                                        {method === 'totp' && (
                                            <p className="mt-1 text-xs text-muted">
                                                {twoFactor.recoveryRemaining}{' '}
                                                kode pemulihan tersisa
                                            </p>
                                        )}
                                    </div>
                                </div>
                                {method === 'totp' ? (
                                    <Badge tone="success">Dipakai</Badge>
                                ) : (
                                    <Button
                                        size="sm"
                                        variant={method ? 'outline' : 'primary'}
                                        onClick={startTotp}
                                    >
                                        <ShieldCheck />{' '}
                                        {method ? 'Ganti' : 'Atur'}
                                    </Button>
                                )}
                            </div>
                            <div className="flex items-center justify-between gap-3 p-3">
                                <div className="flex gap-2.5">
                                    <Mail className="mt-0.5 size-4 shrink-0 text-muted" />
                                    <div>
                                        <p className="text-sm font-medium">
                                            Kode email
                                        </p>
                                        <p className="text-xs text-muted">
                                            {twoFactor.emailAvailable
                                                ? `Kode dikirim ke ${account.email} setiap kali Anda masuk.`
                                                : 'Akun Anda tidak punya alamat email yang bisa menerima kode.'}
                                        </p>
                                    </div>
                                </div>
                                {method === 'email' ? (
                                    <Badge tone="success">Dipakai</Badge>
                                ) : (
                                    <Button
                                        size="sm"
                                        variant={method ? 'outline' : 'primary'}
                                        disabled={!twoFactor.emailAvailable}
                                        onClick={() => setEmailOpen(true)}
                                    >
                                        <KeyRound /> {method ? 'Ganti' : 'Atur'}
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
                                        <ShieldOff /> Matikan
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
                title="Atur aplikasi authenticator"
                description="Pindai kode QR, lalu masukkan kode 6 digit yang tampil di aplikasi."
            >
                {twoFactor.setup && (
                    <CodeForm
                        url="/profile/two-factor/totp"
                        submitLabel="Aktifkan"
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
                title="Atur kode email"
                description={`Kami akan mengirim kode ke ${account.email} untuk memastikan berfungsi.`}
            >
                <CodeForm
                    url="/profile/two-factor/email"
                    submitLabel="Aktifkan"
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
                title="Matikan verifikasi dua langkah"
                description="Masukkan kode untuk memastikan ini Anda."
            >
                <CodeForm
                    url="/profile/two-factor"
                    method="delete"
                    submitLabel="Matikan"
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
