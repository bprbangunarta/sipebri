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
import { useEffect, useMemo, useRef, useState } from 'react';
import { LocationMap } from '@/components/location-map';
import { ReasonDialog } from '@/components/reason-dialog';
import type { MapPin as Pin } from '@/components/location-map';
import { Button } from '@/components/ui/button';
import { ConfirmDialog } from '@/components/ui/dialog';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Badge, Card, PageHeader } from '@/components/ui/misc';
import { Tip } from '@/components/ui/tooltip';
import { formatDate, rupiah } from '@/lib/format';
import { markPosition } from '@/lib/geolocation';

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
        address: string | null;
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

const SOURCES: Record<string, string> = {
    gps: 'GPS',
    paste: 'Ditempel',
    map: 'Pin peta',
    photo: 'GPS foto',
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
    const [active, setActive] = useState(locations[0].key);
    const [clearing, setClearing] = useState<Place | null>(null);
    const [cancelling, setCancelling] = useState(false);
    const [uploading, setUploading] = useState<string | null>(null);

    const surveyPlace = locations[0];
    const pendingSurveyPhotos = photos.filter(
        (p) => !p.saved && p.collateral_id === null,
    ).length;
    const missing = [
        !surveyPlace.location && 'tandai lokasi survei',
        pendingSurveyPhotos === 0 && 'tambahkan foto lokasi survei',
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
        await markPosition(loan.id, payload(place), () => setMarking(null));
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
            <Head title={`Survei ${loan.application_code}`} />
            <PageHeader
                title={`Survei ${loan.application_code}`}
                description={`Dijadwalkan ${formatDate(loan.survey_date)}`}
                actions={
                    <>
                        {loan.locked && (
                            <Badge tone="success">
                                <Lock className="mr-1 size-3" /> Tersimpan
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
                                Tandai lokasi
                            </Button>
                        )}
                        <Button
                            variant="outline"
                            onClick={() => router.visit('/surveys')}
                        >
                            <ArrowLeft /> Kembali
                        </Button>
                    </>
                }
            />

            <div className="grid gap-3 lg:grid-cols-3">
                <div className="flex flex-col gap-3 lg:col-span-2">
                    <Section title="Pemohon">
                        <dl className="grid gap-3 p-3 sm:grid-cols-2">
                            <Detail label="Nama">{loan.full_name}</Detail>
                            <Detail label="NIK (KTP)">
                                <span className="font-mono">{loan.nik}</span>
                            </Detail>
                            <Detail label="Alamat">{customer?.address}</Detail>
                            <Detail label="Telepon">{customer?.phone}</Detail>
                            <Detail label="Tempat kerja">
                                {customer?.employer_name}
                            </Detail>
                            <Detail label="Nomor CIF">{loan.cif_number}</Detail>
                        </dl>
                    </Section>

                    <Section
                        title="Lokasi dan foto"
                        action={
                            !loan.locked && (
                                <span className="text-xs text-muted">
                                    Wajib: lokasi survei dan fotonya
                                </span>
                            )
                        }
                    >
                        <div className="flex flex-col gap-3 p-3">
                            {loan.locked ? (
                                pins.length > 0 && <LocationMap pins={pins} />
                            ) : (
                                <PositionPicker
                                    places={locations}
                                    activeKey={active}
                                    onActive={setActive}
                                    photos={photos}
                                    loanId={loan.id}
                                    pins={pins}
                                />
                            )}

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
                                        onEdit={() => {
                                            setActive(place.key);
                                            document
                                                .getElementById(
                                                    'position-picker',
                                                )
                                                ?.scrollIntoView({
                                                    behavior: 'smooth',
                                                    block: 'center',
                                                });
                                        }}
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
                    <Section title="Permohonan kredit">
                        <dl className="grid gap-3 p-3">
                            <Detail label="Produk">{loan.product_label}</Detail>
                            <Detail label="Plafon">
                                {rupiah(loan.requested_amount)}
                            </Detail>
                            <Detail label="Tenor">
                                {loan.requested_tenor} months
                            </Detail>
                            <Detail label="Penggunaan">
                                {loan.usage_type}
                            </Detail>
                            <Detail label="Kasi Analis">
                                {loan.supervisor_name}
                            </Detail>
                            <Detail label="Catatan jadwal">
                                {loan.schedule_note}
                            </Detail>
                        </dl>
                    </Section>

                    <Section title="Hasil">
                        <form
                            className="flex flex-col gap-3 p-3"
                            onSubmit={(e) => {
                                e.preventDefault();
                                form.post(`/surveys/${loan.id}`, {
                                    preserveScroll: true,
                                });
                            }}
                        >
                            <Field
                                label="Catatan survei"
                                error={form.errors.note}
                            >
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
                                        Disimpan oleh {survey.created_by} pada{' '}
                                        {survey.created_at}. Hasilnya terkunci.
                                    </p>
                                )
                            ) : (
                                <>
                                    <div className="flex flex-wrap items-center justify-between gap-2">
                                        <Button
                                            type="button"
                                            variant="outline"
                                            onClick={() => setCancelling(true)}
                                        >
                                            <X /> Batalkan dan minta jadwal baru
                                        </Button>
                                        <Button
                                            type="submit"
                                            loading={form.processing}
                                            disabled={missing.length > 0}
                                        >
                                            <Check /> Simpan hasil survei
                                        </Button>
                                    </div>
                                    {missing.length > 0 && (
                                        <p className="text-xs text-muted">
                                            Dahulu {missing.join(' dan ')}.
                                        </p>
                                    )}
                                </>
                            )}
                        </form>
                    </Section>
                </div>
            </div>

            <ReasonDialog
                title="Batalkan survei"
                description="Beri tahu Kasi Analis mengapa survei tidak bisa dilaksanakan sesuai jadwal."
                action={cancelling ? `/scheduling/${loan.id}/cancel` : null}
                confirmLabel="Kirim permintaan"
                fieldLabel="Alasan pembatalan"
                hint="Berkas kembali ke Kasi Analis untuk dijadwalkan ulang. Riwayat jadwal sebelumnya tetap ada; posisi dan foto yang dimasukkan untuk kunjungan ini dibuang."
                tone="primary"
                extra={{ return: 'surveys' }}
                onClose={() => setCancelling(false)}
            />

            <ConfirmDialog
                open={replace !== null}
                onOpenChange={(open) => !open && setReplace(null)}
                title="Ganti posisi yang tersimpan?"
                description={
                    <>
                        <strong>{replace?.label}</strong> sudah punya posisi
                        dari {replace?.location?.located_at}. Menandai ulang
                        akan menggantinya dengan posisi Anda saat ini.
                    </>
                }
                confirmLabel="Ganti"
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
                title="Hapus posisi?"
                description={
                    <>
                        Posisi tersimpan <strong>{clearing?.label}</strong> akan
                        dihapus.
                    </>
                }
                confirmLabel="Hapus"
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
            <div className="flex flex-wrap items-center justify-between gap-2 border-b border-line px-3 py-2">
                <div className="min-w-0">
                    <p className="flex flex-wrap items-center gap-1.5 text-sm font-medium">
                        {place.label}
                        {required ? (
                            <Badge tone="info">Wajib</Badge>
                        ) : (
                            <Badge>Opsional</Badge>
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
                            {loc ? 'Tandai ulang' : 'Tandai di sini'}
                        </Button>
                        <Tip label="Isi atau perbaiki posisi">
                            <Button
                                size="icon"
                                variant="ghost"
                                aria-label={`Ubah posisi ${place.label}`}
                                onClick={onEdit}
                            >
                                <Pencil />
                            </Button>
                        </Tip>
                        {loc && (
                            <Tip label="Hapus posisi">
                                <Button
                                    size="icon"
                                    variant="ghost"
                                    aria-label={`Hapus posisi ${place.label}`}
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
                        {required && <CircleAlert className="size-3.5" />} Belum
                        ada posisi
                    </p>
                )}

                {loc?.address && (
                    <p
                        className="text-xs text-muted"
                        title="Perkiraan alamat dari OpenStreetMap, berdasarkan koordinat. Cek di lokasi."
                    >
                        ≈ {loc.address}
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
                                        alt={`Foto ${place.label}`}
                                        className="aspect-square w-full object-cover"
                                    />
                                </a>
                                {!locked && !p.saved && (
                                    <figcaption className="flex justify-end p-1">
                                        <button
                                            type="button"
                                            aria-label="Hapus foto"
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
                            Tambah foto
                        </Button>
                        <span className="text-xs text-muted">
                            {pending}/{maxPhotos} foto
                            {required && pending === 0 && ' · minimal 1'}
                        </span>
                    </div>
                )}
            </div>
        </div>
    );
}

/**
 * Set the position of a place from the page itself: drag and zoom the map and click to drop a pin, or paste coordinates or a
 * map link, or reuse a photo that still carries GPS data. Nothing is saved until the button is pressed.
 */
function PositionPicker({
    places,
    activeKey,
    onActive,
    photos,
    loanId,
    pins,
}: {
    places: Place[];
    activeKey: string;
    onActive: (key: string) => void;
    photos: Photo[];
    loanId: number;
    pins: Pin[];
}) {
    const place = places.find((p) => p.key === activeKey) ?? places[0];
    const form = useForm({ text: '' });
    const [picked, setPicked] = useState<{ lat: number; lng: number } | null>(
        null,
    );
    const [busy, setBusy] = useState(false);

    // Switching to another place starts clean: a pin or text typed for one place must not be saved on another.
    useEffect(() => {
        setPicked(null);
        form.setData('text', '');
        form.clearErrors();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [place.key]);

    const shown: Pin[] = [
        ...pins,
        ...(picked
            ? [
                  {
                      key: 'draft',
                      label: 'Posisi baru',
                      latitude: picked.lat,
                      longitude: picked.lng,
                      tone: 'draft',
                  } satisfies Pin,
              ]
            : []),
    ];
    const withGps = photos.filter(
        (p) => p.latitude !== null && p.collateral_id === place.collateral_id,
    );

    const send = (data: Record<string, unknown>) => {
        setBusy(true);
        router.post(
            `/surveys/${loanId}/locations`,
            { target: place.type, collateral_id: place.collateral_id, ...data },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setPicked(null);
                    form.setData('text', '');
                },
                onError: (errors) =>
                    form.setError(
                        'text',
                        errors.coordinates ?? 'Tidak tersimpan.',
                    ),
                onFinish: () => setBusy(false),
            },
        );
    };

    return (
        <div
            id="position-picker"
            className="flex flex-col gap-2.5 rounded-md border border-line p-2.5"
        >
            <div className="flex flex-wrap items-center gap-1.5">
                <span className="text-xs font-medium">Tentukan posisi:</span>
                {places.map((p) => (
                    <Button
                        key={p.key}
                        type="button"
                        size="sm"
                        variant={p.key === place.key ? undefined : 'outline'}
                        onClick={() => onActive(p.key)}
                    >
                        {p.location && <Check />} {p.label}
                    </Button>
                ))}
            </div>

            <LocationMap
                pins={shown}
                height={320}
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
            <p className="text-xs text-muted">
                Geser peta untuk mencari lokasi, klik untuk memasang pin. Klik
                peta dulu bila ingin memperbesar dengan roda mouse.
            </p>

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
                <div className="min-w-0 flex-1">
                    <Field
                        label={`Koordinat atau tautan peta untuk ${place.label}`}
                        error={form.errors.text}
                        hint="Contoh -6.4643, 107.8083, atau tautan lokasi yang dibagikan lewat WhatsApp."
                    >
                        <div className="flex gap-2">
                            <Input
                                className="min-w-0 flex-1"
                                value={form.data.text}
                                onChange={(e) => {
                                    form.setData('text', e.target.value);
                                    form.clearErrors();
                                }}
                                aria-invalid={!!form.errors.text}
                            />
                            <Button
                                type="submit"
                                loading={busy}
                                disabled={form.data.text.trim() === ''}
                            >
                                Simpan posisi
                            </Button>
                        </div>
                    </Field>
                </div>
            </form>

            {withGps.length > 0 && (
                <div className="flex flex-col gap-1.5">
                    <p className="text-xs font-medium">
                        Foto yang masih membawa data GPS
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
                                <MapPin /> Pakai foto {i + 1} (
                                {p.latitude?.toFixed(5)},{' '}
                                {p.longitude?.toFixed(5)})
                            </Button>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}
