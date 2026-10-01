import { Head, router } from '@inertiajs/react';
import { Check, LocateFixed, Loader2, MapPin } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { ConfirmDialog } from '@/components/ui/dialog';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Badge, PageHeader } from '@/components/ui/misc';
import { Tip } from '@/components/ui/tooltip';
import { useListQuery } from '@/hooks/use-list-query';
import { formatDate, rupiah } from '@/lib/format';
import { markPosition } from '@/lib/geolocation';

type Row = {
    id: number;
    application_code: string;
    full_name: string;
    nik: string;
    requested_amount: number;
    requested_tenor: number;
    product_label: string | null;
    supervisor_name: string | null;
    has_location: boolean;
    located_at: string | null;
    survey_photos: number;
};
type Filters = { search: string };

/** What the survey still needs: its location and at least one photo of it. */
function Progress({ row }: { row: Row }) {
    return (
        <span className="flex flex-wrap gap-1">
            <Badge tone={row.has_location ? 'success' : 'warning'}>
                {row.has_location && <Check className="mr-0.5 size-3" />}
                Lokasi
            </Badge>
            <Badge tone={row.survey_photos > 0 ? 'success' : 'warning'}>
                {row.survey_photos > 0 && <Check className="mr-0.5 size-3" />}
                Foto {row.survey_photos}
            </Badge>
        </span>
    );
}

export default function SurveysIndex({
    loans,
    filters,
    today,
}: {
    loans: Row[];
    filters: Filters;
    today: string;
}) {
    const [marking, setMarking] = useState<number | null>(null);
    const [replace, setReplace] = useState<Row | null>(null);
    const { visit, search, onSearch, loading, error } = useListQuery<Filters>({
        url: '/surveys',
        filters,
        defaults: {},
        only: ['loans', 'filters'],
    });

    /** On site: mark the survey location with the phone's GPS without opening the file. */
    const mark = async (l: Row) => {
        setMarking(l.id);
        await markPosition(
            l.id,
            { target: 'survey', collateral_id: null },
            () => setMarking(null),
        );
    };
    const ask = (l: Row) => (l.has_location ? setReplace(l) : void mark(l));

    const columns: Column<Row>[] = [
        {
            key: 'file',
            header: 'Berkas',
            cell: (l) => (
                <>
                    <p className="font-medium">{l.application_code}</p>
                    <p className="text-xs text-muted">{l.product_label}</p>
                    {/* On phones the Progress column is hidden to keep the actions in view. */}
                    <span className="mt-1 block sm:hidden">
                        <Progress row={l} />
                    </span>
                </>
            ),
        },
        {
            key: 'applicant',
            header: 'Pemohon',
            cell: (l) => (
                <>
                    {l.full_name}
                    <p className="font-mono text-xs text-muted">{l.nik}</p>
                </>
            ),
        },
        {
            key: 'amount',
            header: 'Plafon',
            align: 'right',
            hideBelow: 'md',
            className: 'whitespace-nowrap tabular-nums',
            cell: (l) => (
                <>
                    {rupiah(l.requested_amount)}
                    <p className="text-xs text-muted">
                        {l.requested_tenor} months
                    </p>
                </>
            ),
        },
        {
            key: 'progress',
            header: 'Kemajuan',
            hideBelow: 'sm',
            cell: (l) => <Progress row={l} />,
        },
        {
            key: 'actions',
            header: 'Aksi',
            srOnly: true,
            align: 'right',
            className: 'whitespace-nowrap',
            cell: (l) => (
                <span className="inline-flex items-center gap-1.5">
                    <Tip
                        label={
                            l.has_location
                                ? 'Tandai ulang lokasi dengan GPS Anda'
                                : 'Tandai lokasi survei dengan GPS Anda'
                        }
                    >
                        <Button
                            size="sm"
                            variant="outline"
                            aria-label={`Tandai lokasi ${l.application_code}`}
                            disabled={marking !== null}
                            onClick={() => ask(l)}
                        >
                            {marking === l.id ? (
                                <Loader2 className="animate-spin" />
                            ) : (
                                <LocateFixed />
                            )}
                            <span className="hidden sm:inline">
                                {l.has_location
                                    ? 'Tandai ulang'
                                    : 'Tandai lokasi'}
                            </span>
                        </Button>
                    </Tip>
                    <Button
                        size="sm"
                        onClick={() => router.visit(`/surveys/${l.id}`)}
                    >
                        Open
                    </Button>
                </span>
            ),
        },
    ];

    return (
        <>
            <Head title="Proses Survey" />
            <PageHeader
                title="Proses Survey"
                description={`Berkas yang dijadwalkan untuk Anda hari ini, ${formatDate(today)}`}
            />

            <DataTable
                rows={loans}
                rowKey={(l) => l.id}
                loading={loading}
                error={error}
                onRetry={() => visit({})}
                toolbar={
                    <FilterBar
                        search={
                            <SearchInput
                                value={search}
                                onChange={onSearch}
                                placeholder="Cari kode, nama, NIK…"
                                label="Cari berkas"
                            />
                        }
                    />
                }
                columns={columns}
                empty={{
                    icon: <MapPin />,
                    title: 'Tidak ada survei hari ini',
                    description:
                        'Berkas tampil di sini pada tanggal surveinya.',
                }}
            />

            <ConfirmDialog
                open={replace !== null}
                onOpenChange={(open) => !open && setReplace(null)}
                title="Ganti posisi yang tersimpan?"
                description={
                    <>
                        Berkas <strong>{replace?.application_code}</strong>{' '}
                        sudah punya lokasi survei dari {replace?.located_at}.
                        Menandai ulang akan menggantinya dengan posisi Anda saat
                        ini.
                    </>
                }
                confirmLabel="Ganti"
                onConfirm={() => {
                    const l = replace;
                    setReplace(null);

                    if (l) {
                        void mark(l);
                    }
                }}
            />
        </>
    );
}
