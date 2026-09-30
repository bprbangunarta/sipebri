import { Head, router, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    Camera,
    Check,
    Loader2,
    Lock,
    MapPin,
    Trash2,
} from 'lucide-react';
import type { ReactNode } from 'react';
import { useRef, useState } from 'react';
import { toast } from 'sonner';
import { Button } from '@/components/ui/button';
import { Field } from '@/components/ui/field';
import { Badge, Card, PageHeader } from '@/components/ui/misc';
import { formatDate, rupiah } from '@/lib/format';

type Photo = {
    id: number;
    url: string;
    latitude: number;
    longitude: number;
    source: string;
    saved: boolean;
    created_at: string;
};

type Props = {
    loan: {
        id: number;
        application_code: string;
        application_date: string;
        survey_date: string | null;
        status: string;
        full_name: string;
        nik: string;
        cif_number: string | null;
        usage_type: string | null;
        requested_amount: number;
        requested_tenor: number;
        product_label: string | null;
        supervisor_name: string | null;
        schedule_note: string | null;
        locked: boolean;
    };
    customer: {
        full_name: string;
        address: string | null;
        phone: string | null;
        employer_name: string | null;
        source: string;
    } | null;
    collaterals: {
        id: number;
        cbs_id: string | null;
        owner_name: string | null;
        description: string | null;
        appraisal_value: number;
    }[];
    photos: Photo[];
    survey: {
        note: string | null;
        latitude: string;
        longitude: string;
        created_by: string;
        created_at: string;
    } | null;
    maxPhotos: number;
};

function Section({ title, children }: { title: string; children: ReactNode }) {
    return (
        <Card>
            <h2 className="border-b border-line px-3 py-2 text-sm font-semibold">
                {title}
            </h2>
            {children}
        </Card>
    );
}

function Detail({ label, children }: { label: string; children: ReactNode }) {
    return (
        <div>
            <dt className="text-xs text-muted">{label}</dt>
            <dd className="text-sm font-medium">{children || '–'}</dd>
        </div>
    );
}

/** Reads the device position; the browser only allows this on HTTPS or localhost. */
function currentPosition(): Promise<GeolocationPosition> {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject(new Error('This device cannot provide a location.'));

            return;
        }
        navigator.geolocation.getCurrentPosition(
            resolve,
            () =>
                reject(
                    new Error(
                        'Location is required. Allow location access and try again.',
                    ),
                ),
            { enableHighAccuracy: true, timeout: 15000 },
        );
    });
}

export default function SurveyShow({
    loan,
    customer,
    collaterals,
    photos,
    survey,
    maxPhotos,
}: Props) {
    const input = useRef<HTMLInputElement>(null);
    const [uploading, setUploading] = useState(false);
    const form = useForm({ note: survey?.note ?? '' });
    const pending = photos.filter((p) => !p.saved);
    const full = pending.length >= maxPhotos;

    const upload = async (file: File | undefined) => {
        if (!file) {
            return;
        }
        setUploading(true);

        try {
            const position = await currentPosition();
            router.post(
                `/surveys/${loan.id}/photos`,
                {
                    photo: file,
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    source: 'camera',
                },
                {
                    forceFormData: true,
                    preserveScroll: true,
                    onFinish: () => setUploading(false),
                },
            );
        } catch (e) {
            toast.error(
                e instanceof Error
                    ? e.message
                    : 'Unable to read your location.',
            );
            setUploading(false);
        } finally {
            if (input.current) {
                input.current.value = '';
            }
        }
    };

    return (
        <>
            <Head title={`Survey ${loan.application_code}`} />
            <PageHeader
                title={`Survey ${loan.application_code}`}
                description={`${loan.full_name} · scheduled ${formatDate(loan.survey_date)}`}
                actions={
                    <>
                        {loan.locked && (
                            <Badge tone="success">
                                <Lock className="mr-1 size-3" /> Saved
                            </Badge>
                        )}
                        <Button
                            variant="outline"
                            onClick={() => router.visit('/surveys')}
                        >
                            <ArrowLeft /> Back
                        </Button>
                    </>
                }
            />

            <div className="grid gap-3 lg:grid-cols-3">
                <div className="flex flex-col gap-3 lg:col-span-2">
                    <Section title="Applicant">
                        <dl className="grid gap-3 p-3 sm:grid-cols-2">
                            <Detail label="Name">{loan.full_name}</Detail>
                            <Detail label="National ID (NIK)">
                                <span className="font-mono">{loan.nik}</span>
                            </Detail>
                            <Detail label="Address">{customer?.address}</Detail>
                            <Detail label="Phone">{customer?.phone}</Detail>
                            <Detail label="Employer">
                                {customer?.employer_name}
                            </Detail>
                            <Detail label="CIF number">
                                {loan.cif_number}
                            </Detail>
                        </dl>
                    </Section>

                    <Section title="Location photos">
                        <div className="flex flex-col gap-3 p-3">
                            <div className="grid grid-cols-2 gap-2 sm:grid-cols-3">
                                {photos.map((p) => (
                                    <figure
                                        key={p.id}
                                        className="overflow-hidden rounded-md border border-line"
                                    >
                                        <img
                                            src={p.url}
                                            alt={`Location photo ${p.id}`}
                                            className="aspect-video w-full object-cover"
                                        />
                                        <figcaption className="flex items-start justify-between gap-1 p-1.5 text-[11px] text-muted">
                                            <span className="flex items-center gap-1">
                                                <MapPin className="size-3 shrink-0" />
                                                {p.latitude.toFixed(5)},{' '}
                                                {p.longitude.toFixed(5)}
                                            </span>
                                            {!loan.locked && !p.saved && (
                                                <button
                                                    type="button"
                                                    aria-label="Delete photo"
                                                    className="cursor-pointer text-danger"
                                                    onClick={() =>
                                                        router.delete(
                                                            `/surveys/${loan.id}/photos/${p.id}`,
                                                            {
                                                                preserveScroll: true,
                                                            },
                                                        )
                                                    }
                                                >
                                                    <Trash2 className="size-3.5" />
                                                </button>
                                            )}
                                        </figcaption>
                                    </figure>
                                ))}
                            </div>
                            {!loan.locked && (
                                <div className="flex flex-wrap items-center gap-2">
                                    <input
                                        ref={input}
                                        type="file"
                                        accept="image/*"
                                        capture="environment"
                                        className="sr-only"
                                        onChange={(e) =>
                                            upload(e.target.files?.[0])
                                        }
                                    />
                                    <Button
                                        variant="outline"
                                        disabled={full || uploading}
                                        onClick={() => input.current?.click()}
                                    >
                                        {uploading ? (
                                            <Loader2 className="animate-spin" />
                                        ) : (
                                            <Camera />
                                        )}{' '}
                                        Take or choose a photo
                                    </Button>
                                    <span className="text-xs text-muted">
                                        {pending.length}/{maxPhotos} photos ·
                                        location is captured with each photo
                                        (needs HTTPS or localhost)
                                    </span>
                                </div>
                            )}
                        </div>
                    </Section>

                    <Section title="Result">
                        <form
                            className="flex flex-col gap-3 p-3"
                            onSubmit={(e) => {
                                e.preventDefault();
                                form.post(`/surveys/${loan.id}`, {
                                    preserveScroll: true,
                                });
                            }}
                        >
                            <Field label="Survey note" error={form.errors.note}>
                                <textarea
                                    className="min-h-20 w-full rounded-md border border-line bg-surface px-2.5 py-1.5 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none disabled:opacity-60"
                                    maxLength={500}
                                    disabled={loan.locked}
                                    value={form.data.note}
                                    onChange={(e) =>
                                        form.setData('note', e.target.value)
                                    }
                                />
                            </Field>
                            {loan.locked ? (
                                survey && (
                                    <p className="text-xs text-muted">
                                        Saved by {survey.created_by} on{' '}
                                        {survey.created_at}. The result is
                                        locked.
                                    </p>
                                )
                            ) : (
                                <div>
                                    <Button
                                        type="submit"
                                        loading={form.processing}
                                        disabled={pending.length === 0}
                                    >
                                        <Check /> Save survey result
                                    </Button>
                                    {pending.length === 0 && (
                                        <span className="ml-2 text-xs text-muted">
                                            Add at least one photo first.
                                        </span>
                                    )}
                                </div>
                            )}
                        </form>
                    </Section>
                </div>

                <div className="flex flex-col gap-3">
                    <Section title="Loan request">
                        <dl className="grid gap-3 p-3">
                            <Detail label="Product">
                                {loan.product_label}
                            </Detail>
                            <Detail label="Amount">
                                {rupiah(loan.requested_amount)}
                            </Detail>
                            <Detail label="Tenor">
                                {loan.requested_tenor} months
                            </Detail>
                            <Detail label="Usage">{loan.usage_type}</Detail>
                            <Detail label="Section head">
                                {loan.supervisor_name}
                            </Detail>
                            <Detail label="Schedule note">
                                {loan.schedule_note}
                            </Detail>
                        </dl>
                    </Section>
                    <Section title="Collateral">
                        {collaterals.length === 0 ? (
                            <p className="p-3 text-xs text-muted">
                                No collateral attached.
                            </p>
                        ) : (
                            <ul className="divide-y divide-line">
                                {collaterals.map((c) => (
                                    <li
                                        key={c.id}
                                        className="px-3 py-2 text-sm"
                                    >
                                        <p className="font-medium">
                                            {c.cbs_id ?? `#${c.id}`} ·{' '}
                                            {c.owner_name}
                                        </p>
                                        <p className="text-xs text-muted">
                                            {c.description}
                                        </p>
                                        <p className="text-xs tabular-nums">
                                            {rupiah(c.appraisal_value)}
                                        </p>
                                    </li>
                                ))}
                            </ul>
                        )}
                    </Section>
                </div>
            </div>
        </>
    );
}
