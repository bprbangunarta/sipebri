import { Head, router, useForm } from '@inertiajs/react';
import {
    ArrowLeft,
    Camera,
    Check,
    CircleAlert,
    ExternalLink,
    Loader2,
    Lock,
    LocateFixed,
    MapPin,
    Pencil,
    Trash2,
    X,
} from 'lucide-react';
import type { ReactNode } from 'react';
import { useMemo, useRef, useState } from 'react';
import { toast } from 'sonner';
import { LocationMap } from '@/components/location-map';
import type { MapPin as Pin } from '@/components/location-map';
import { Button } from '@/components/ui/button';
import { ConfirmDialog, DialogFooter, Modal } from '@/components/ui/dialog';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Badge, Card, PageHeader } from '@/components/ui/misc';
import { Tip } from '@/components/ui/tooltip';
import { formatDate, rupiah } from '@/lib/format';

type Photo = {
    id: number;
    url: string;
    collateral_id: number | null;
    latitude: number | null;
    longitude: number | null;
    taken_at: string | null;
    saved: boolean;
    created_at: string;
};

type Place = {
    key: string;
    type: 'survey' | 'collateral';
    collateral_id: number | null;
    label: string;
    detail: string;
    location: {
        latitude: number;
        longitude: number;
        source: string | null;
        located_at: string | null;
        located_by: string | null;
        maps_url: string;
    } | null;
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
    locations: Place[];
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

/** A fix worse than this still gets saved, with a warning to retry in the open. */
const WEAK_GPS_METERS = 100;

const SOURCES: Record<string, string> = {
    gps: 'GPS',
    paste: 'Pasted',
    map: 'Map pin',
    photo: 'Photo GPS',
};

function Section({
    title,
    children,
    action,
}: {
    title: string;
    children: ReactNode;
    action?: ReactNode;
}) {
    return (
        <Card>
            <div className="flex items-center justify-between gap-2 border-b border-line px-3 py-2">
                <h2 className="text-sm font-semibold">{title}</h2>
                {action}
            </div>
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

/** Reads the phone's position; the browser only allows this on HTTPS or localhost. */
function currentPosition(): Promise<GeolocationPosition> {
    return new Promise((resolve, reject) => {
        if (!window.isSecureContext) {
            reject(
                new Error(
                    'Location needs a secure connection. Open the app through https:// (or localhost), not a plain http:// address.',
                ),
            );

            return;
        }

        if (!navigator.geolocation) {
            reject(new Error('This device cannot provide a location.'));

            return;
        }

        navigator.geolocation.getCurrentPosition(
            resolve,
            (error) =>
                reject(
                    new Error(
                        error.code === error.PERMISSION_DENIED
                            ? 'Location access is blocked. Allow it for this site in the browser settings and try again.'
                            : error.code === error.TIMEOUT
                              ? 'Getting your location took too long. Move to an open area and try again.'
                              : 'Your location is unavailable. Turn on location services (GPS) and try again.',
                    ),
                ),
            { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 },
        );
    });
}

export default function SurveyShow({
    loan,
    customer,
    locations,
    photos,
    survey,
    maxPhotos,
}: Props) {
    const form = useForm({ note: survey?.note ?? '' });
    const [marking, setMarking] = useState<string | null>(null);
    const [replace, setReplace] = useState<Place | null>(null);
    const [editing, setEditing] = useState<Place | null>(null);
    const [clearing, setClearing] = useState<Place | null>(null);
    const [uploading, setUploading] = useState<string | null>(null);

    const surveyPlace = locations[0];
    const pendingSurveyPhotos = photos.filter(
        (p) => !p.saved && p.collateral_id === null,
    ).length;
    const missing = [
        !surveyPlace.location && 'mark the survey location',
        pendingSurveyPhotos === 0 && 'add a photo of the survey location',
    ].filter(Boolean) as string[];

    const pins: Pin[] = useMemo(
        () =>
            locations.flatMap((p) =>
                p.location
                    ? [
                          {
                              key: p.key,
                              label: p.label,
                              latitude: p.location.latitude,
                              longitude: p.location.longitude,
                              tone: p.type,
                          },
                      ]
                    : [],
            ),
        [locations],
    );

    const payload = (place: Place) => ({
        target: place.type,
        collateral_id: place.collateral_id,
    });

    /** On site: store the phone's GPS position for a place with one tap. */
    const mark = async (place: Place) => {
        setMarking(place.key);

        try {
            const position = await currentPosition();
            const accuracy = Math.round(position.coords.accuracy);

            if (accuracy > WEAK_GPS_METERS) {
                toast.warning(
                    `Weak GPS signal (about ±${accuracy} m). The position is saved; mark it again in the open for a better fix.`,
                );
            }

            router.post(
                `/surveys/${loan.id}/locations`,
                {
                    ...payload(place),
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    source: 'gps',
                    accuracy,
                },
                {
                    preserveScroll: true,
                    onFinish: () => setMarking(null),
                },
            );
        } catch (e) {
            toast.error(
                e instanceof Error
                    ? e.message
                    : 'Unable to read your location.',
            );
            setMarking(null);
        }
    };

    const ask = (place: Place) =>
        place.location ? setReplace(place) : void mark(place);

    /** Photos are sent one by one, so a failure stops at that file and the rest are not lost silently. */
    const upload = async (place: Place, files: FileList | null) => {
        if (!files?.length) {
            return;
        }

        setUploading(place.key);

        for (const file of Array.from(files)) {
            const ok = await new Promise<boolean>((resolve) =>
                router.post(
                    `/surveys/${loan.id}/photos`,
                    { photo: file, collateral_id: place.collateral_id },
                    {
                        forceFormData: true,
                        preserveScroll: true,
                        onSuccess: () => resolve(true),
                        onError: () => resolve(false),
                        onCancel: () => resolve(false),
                    },
                ),
            );

            if (!ok) {
                break;
            }
        }

        setUploading(null);
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
                        {!loan.locked && (
                            <Button
                                onClick={() => ask(surveyPlace)}
                                disabled={marking !== null}
                            >
                                {marking === 'survey' ? (
                                    <Loader2 className="animate-spin" />
                                ) : (
                                    <LocateFixed />
                                )}{' '}
                                Mark location
                            </Button>
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

                    <Section
                        title="Locations and photos"
                        action={
                            !loan.locked && (
                                <span className="text-xs text-muted">
                                    Required: survey location and its photo
                                </span>
                            )
                        }
                    >
                        <div className="flex flex-col gap-3 p-3">
                            {pins.length > 0 && <LocationMap pins={pins} />}

                            {locations.map((place) => {
                                const mine = photos.filter(
                                    (p) =>
                                        p.collateral_id === place.collateral_id,
                                );
                                const pendingHere = mine.filter(
                                    (p) => !p.saved,
                                ).length;
                                const required = place.type === 'survey';

                                return (
                                    <PlaceBlock
                                        key={place.key}
                                        place={place}
                                        required={required}
                                        photos={mine}
                                        locked={loan.locked}
                                        maxPhotos={maxPhotos}
                                        marking={marking === place.key}
                                        uploading={uploading === place.key}
                                        canAddPhotos={pendingHere < maxPhotos}
                                        onMark={() => ask(place)}
                                        onEdit={() => setEditing(place)}
                                        onClear={() => setClearing(place)}
                                        onUpload={(files) =>
                                            upload(place, files)
                                        }
                                        onDeletePhoto={(id) =>
                                            router.delete(
                                                `/surveys/${loan.id}/photos/${id}`,
                                                { preserveScroll: true },
                                            )
                                        }
                                    />
                                );
                            })}
                        </div>
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
                                        disabled={missing.length > 0}
                                    >
                                        <Check /> Save survey result
                                    </Button>
                                    {missing.length > 0 && (
                                        <span className="ml-2 text-xs text-muted">
                                            First {missing.join(' and ')}.
                                        </span>
                                    )}
                                </div>
                            )}
                        </form>
                    </Section>
                </div>
            </div>

            <ConfirmDialog
                open={replace !== null}
                onOpenChange={(open) => !open && setReplace(null)}
                title="Replace the saved position?"
                description={
                    <>
                        <strong>{replace?.label}</strong> already has a position
                        from {replace?.location?.located_at}. Marking again
                        replaces it with your current position.
                    </>
                }
                confirmLabel="Replace"
                onConfirm={() => {
                    const place = replace;
                    setReplace(null);

                    if (place) {
                        void mark(place);
                    }
                }}
            />

            <ConfirmDialog
                open={clearing !== null}
                onOpenChange={(open) => !open && setClearing(null)}
                title="Remove the position?"
                description={
                    <>
                        The saved position of <strong>{clearing?.label}</strong>{' '}
                        is removed.
                    </>
                }
                confirmLabel="Remove"
                onConfirm={() => {
                    const place = clearing;
                    setClearing(null);

                    if (place) {
                        router.delete(`/surveys/${loan.id}/locations`, {
                            data: payload(place),
                            preserveScroll: true,
                        });
                    }
                }}
            />

            <EditLocation
                key={editing?.key ?? 'none'}
                place={editing}
                loanId={loan.id}
                photos={photos}
                onClose={() => setEditing(null)}
            />
        </>
    );
}

function PlaceBlock({
    place,
    required,
    photos,
    locked,
    maxPhotos,
    marking,
    uploading,
    canAddPhotos,
    onMark,
    onEdit,
    onClear,
    onUpload,
    onDeletePhoto,
}: {
    place: Place;
    required: boolean;
    photos: Photo[];
    locked: boolean;
    maxPhotos: number;
    marking: boolean;
    uploading: boolean;
    canAddPhotos: boolean;
    onMark: () => void;
    onEdit: () => void;
    onClear: () => void;
    onUpload: (files: FileList | null) => void;
    onDeletePhoto: (id: number) => void;
}) {
    const input = useRef<HTMLInputElement>(null);
    const loc = place.location;
    const pending = photos.filter((p) => !p.saved).length;

    return (
        <div className="rounded-md border border-line">
            <div className="flex flex-wrap items-start justify-between gap-2 border-b border-line px-3 py-2">
                <div className="min-w-0">
                    <p className="flex flex-wrap items-center gap-1.5 text-sm font-medium">
                        {place.label}
                        {required ? (
                            <Badge tone="info">Required</Badge>
                        ) : (
                            <Badge>Optional</Badge>
                        )}
                    </p>
                    {place.detail && (
                        <p className="text-xs text-muted">{place.detail}</p>
                    )}
                </div>
                {!locked && (
                    <div className="flex flex-wrap items-center gap-1.5">
                        <Button
                            size="sm"
                            variant="outline"
                            onClick={onMark}
                            disabled={marking}
                        >
                            {marking ? (
                                <Loader2 className="animate-spin" />
                            ) : (
                                <LocateFixed />
                            )}{' '}
                            {loc ? 'Mark again' : 'Mark here'}
                        </Button>
                        <Tip label="Enter or fix the position">
                            <Button
                                size="icon"
                                variant="ghost"
                                aria-label={`Edit position of ${place.label}`}
                                onClick={onEdit}
                            >
                                <Pencil />
                            </Button>
                        </Tip>
                        {loc && (
                            <Tip label="Remove the position">
                                <Button
                                    size="icon"
                                    variant="ghost"
                                    aria-label={`Remove position of ${place.label}`}
                                    onClick={onClear}
                                >
                                    <X />
                                </Button>
                            </Tip>
                        )}
                    </div>
                )}
            </div>

            <div className="flex flex-col gap-2 p-3">
                {loc ? (
                    <p className="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs">
                        <MapPin className="size-3.5 shrink-0 text-primary" />
                        <span className="font-mono tabular-nums">
                            {loc.latitude.toFixed(6)},{' '}
                            {loc.longitude.toFixed(6)}
                        </span>
                        {loc.source && (
                            <Badge>{SOURCES[loc.source] ?? loc.source}</Badge>
                        )}
                        <span className="text-muted">
                            {[loc.located_by, loc.located_at]
                                .filter(Boolean)
                                .join(' · ')}
                        </span>
                        <a
                            href={loc.maps_url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-1 text-primary hover:underline"
                        >
                            Google Maps <ExternalLink className="size-3" />
                        </a>
                    </p>
                ) : (
                    <p
                        className={
                            required
                                ? 'flex items-center gap-1.5 text-xs text-amber-700'
                                : 'text-xs text-muted'
                        }
                    >
                        {required && <CircleAlert className="size-3.5" />} No
                        position yet
                    </p>
                )}

                {photos.length > 0 && (
                    <div className="grid grid-cols-3 gap-2 sm:grid-cols-5">
                        {photos.map((p) => (
                            <figure
                                key={p.id}
                                className="overflow-hidden rounded-md border border-line"
                            >
                                <a
                                    href={p.url}
                                    target="_blank"
                                    rel="noreferrer"
                                >
                                    <img
                                        src={p.url}
                                        alt={`Photo of ${place.label}`}
                                        className="aspect-square w-full object-cover"
                                    />
                                </a>
                                {!locked && !p.saved && (
                                    <figcaption className="flex justify-end p-1">
                                        <button
                                            type="button"
                                            aria-label="Delete photo"
                                            className="cursor-pointer text-danger"
                                            onClick={() => onDeletePhoto(p.id)}
                                        >
                                            <Trash2 className="size-3.5" />
                                        </button>
                                    </figcaption>
                                )}
                            </figure>
                        ))}
                    </div>
                )}

                {!locked && (
                    <div className="flex flex-wrap items-center gap-2">
                        <input
                            ref={input}
                            type="file"
                            accept="image/*"
                            multiple
                            className="sr-only"
                            onChange={(e) => {
                                onUpload(e.target.files);
                                e.target.value = '';
                            }}
                        />
                        <Button
                            size="sm"
                            variant="outline"
                            disabled={!canAddPhotos || uploading}
                            onClick={() => input.current?.click()}
                        >
                            {uploading ? (
                                <Loader2 className="animate-spin" />
                            ) : (
                                <Camera />
                            )}{' '}
                            Add photos
                        </Button>
                        <span className="text-xs text-muted">
                            {pending}/{maxPhotos} photos
                            {required &&
                                pending === 0 &&
                                ' · at least 1 needed'}
                        </span>
                    </div>
                )}
            </div>
        </div>
    );
}

/** Office-side editing: paste coordinates or a map link, drop a pin on the map, or use a photo that still has GPS data. */
function EditLocation({
    place,
    loanId,
    photos,
    onClose,
}: {
    place: Place | null;
    loanId: number;
    photos: Photo[];
    onClose: () => void;
}) {
    const form = useForm({ text: '' });
    const [picked, setPicked] = useState<{ lat: number; lng: number } | null>(
        null,
    );
    const [busy, setBusy] = useState(false);

    const pins: Pin[] = [
        ...(place?.location
            ? [
                  {
                      key: 'saved',
                      label: place.label,
                      latitude: place.location.latitude,
                      longitude: place.location.longitude,
                      tone: place.type,
                  } satisfies Pin,
              ]
            : []),
        ...(picked
            ? [
                  {
                      key: 'draft',
                      label: 'New position',
                      latitude: picked.lat,
                      longitude: picked.lng,
                      tone: 'draft',
                  } satisfies Pin,
              ]
            : []),
    ];
    const withGps = photos.filter(
        (p) =>
            p.latitude !== null &&
            p.collateral_id === (place?.collateral_id ?? null),
    );

    const send = (data: Record<string, unknown>) => {
        if (!place) {
            return;
        }

        setBusy(true);
        router.post(
            `/surveys/${loanId}/locations`,
            {
                target: place.type,
                collateral_id: place.collateral_id,
                ...data,
            },
            {
                preserveScroll: true,
                onSuccess: onClose,
                onError: (errors) =>
                    form.setError('text', errors.coordinates ?? 'Not saved.'),
                onFinish: () => setBusy(false),
            },
        );
    };

    return (
        <Modal
            wide
            open={place !== null}
            onOpenChange={(open) => !open && onClose()}
            title={`Position of ${place?.label ?? ''}`}
            description="Paste coordinates or a Google Maps link, or click the map to place a pin."
        >
            <form
                noValidate
                onSubmit={(e) => {
                    e.preventDefault();
                    send(
                        picked &&
                            form.data.text === `${picked.lat}, ${picked.lng}`
                            ? {
                                  latitude: picked.lat,
                                  longitude: picked.lng,
                                  source: 'map',
                              }
                            : { coordinates: form.data.text },
                    );
                }}
            >
                <div className="flex max-h-[70vh] flex-col gap-3 overflow-y-auto p-4">
                    <Field
                        label="Coordinates or map link"
                        error={form.errors.text}
                        hint="For example -6.4643, 107.8083, or the link of a location shared over WhatsApp."
                    >
                        <Input
                            autoFocus
                            value={form.data.text}
                            onChange={(e) => {
                                form.setData('text', e.target.value);
                                form.clearErrors();
                            }}
                            aria-invalid={!!form.errors.text}
                        />
                    </Field>

                    <LocationMap
                        pins={pins}
                        height={240}
                        onPick={(lat, lng) => {
                            const point = {
                                lat: Number(lat.toFixed(7)),
                                lng: Number(lng.toFixed(7)),
                            };
                            setPicked(point);
                            form.setData('text', `${point.lat}, ${point.lng}`);
                            form.clearErrors();
                        }}
                    />

                    {withGps.length > 0 && (
                        <div className="flex flex-col gap-1.5">
                            <p className="text-xs font-medium">
                                Photos that still carry GPS data
                            </p>
                            <div className="flex flex-wrap gap-2">
                                {withGps.map((p, i) => (
                                    <Button
                                        key={p.id}
                                        type="button"
                                        size="sm"
                                        variant="outline"
                                        disabled={busy}
                                        onClick={() => send({ photo_id: p.id })}
                                    >
                                        <MapPin /> Use photo {i + 1} (
                                        {p.latitude?.toFixed(5)},{' '}
                                        {p.longitude?.toFixed(5)})
                                    </Button>
                                ))}
                            </div>
                        </div>
                    )}
                </div>
                <DialogFooter>
                    <Button variant="outline" onClick={onClose}>
                        Cancel
                    </Button>
                    <Button
                        type="submit"
                        loading={busy}
                        disabled={form.data.text.trim() === ''}
                    >
                        Save position
                    </Button>
                </DialogFooter>
            </form>
        </Modal>
    );
}
