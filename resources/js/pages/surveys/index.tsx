import { Head, router } from '@inertiajs/react';
import { Check, ClipboardPen, LocateFixed, Loader2, MapPin } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { ConfirmDialog } from '@/components/ui/dialog';
import { FilterBar, SearchInput } from '@/components/ui/filter-bar';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';
import { Badge, PageHeader } from '@/components/ui/misc';
import { Tip } from '@/components/ui/tooltip';
import { useListQuery } from '@/hooks/use-list-query';
import { formatDate, formatDateTime, formatNumber, rupiah } from '@/lib/format';
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

export default function SurveysIndex({ loans, filters, today }: { loans: Row[]; filters: Filters; today: string }) {
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
        await markPosition(l.id, { target: 'survey', collateral_id: null }, () => setMarking(null));
    };
    const ask = (l: Row) => (l.has_location ? setReplace(l) : void mark(l));

    // Lokasi and Foto show what the survey still needs, so the surveyor sees what is left without opening each file.
    const columns: Column<Row>[] = [
        { key: 'code', header: 'Kode', className: 'font-medium', cell: (l) => l.application_code },
        { key: 'product', header: 'Produk', cell: (l) => l.product_label ?? '–' },
        { key: 'applicant', header: 'Pemohon', cell: (l) => l.full_name },
        { key: 'nik', header: 'NIK', className: 'font-mono', cell: (l) => l.nik },
        { key: 'amount', header: 'Plafon', align: 'right', className: 'tabular-nums', cell: (l) => rupiah(l.requested_amount) },
        { key: 'tenor', header: 'Tenor', align: 'right', className: 'tabular-nums', cell: (l) => `${formatNumber(l.requested_tenor)} bulan` },
        {
            key: 'location',
            header: 'Lokasi',
            cell: (l) => (
                <Badge tone={l.has_location ? 'success' : 'warning'}>
                    {l.has_location && <Check className="mr-0.5 size-3" />}
                    {l.has_location ? 'Ditandai' : 'Belum'}
                </Badge>
            ),
        },
        {
            key: 'photos',
            header: 'Foto',
            cell: (l) => (
                <Badge tone={l.survey_photos > 0 ? 'success' : 'warning'}>
                    {l.survey_photos > 0 && <Check className="mr-0.5 size-3" />}
                    {formatNumber(l.survey_photos)} foto
                </Badge>
            ),
        },
        {
            key: 'actions',
            header: 'Aksi',
            srOnly: true,
            narrow: true,
            align: 'right',
            cell: (l) => (
                <span className="inline-flex items-center gap-1">
                    <Tip label={l.has_location ? 'Tandai ulang lokasi dengan GPS Anda' : 'Tandai lokasi survei dengan GPS Anda'}>
                        <Button
                            size="icon"
                            variant="ghost"
                            aria-label={`Tandai lokasi ${l.application_code}`}
                            disabled={marking !== null}
                            onClick={() => ask(l)}
                        >
                            {marking === l.id ? <Loader2 className="animate-spin" /> : <LocateFixed />}
                        </Button>
                    </Tip>
                    <Tip label="Buka survei">
                        <Button size="icon" variant="ghost" aria-label={`Buka survei ${l.application_code}`} onClick={() => router.visit(`/surveys/${l.id}`)}>
                            <ClipboardPen />
                        </Button>
                    </Tip>
                </span>
            ),
        },
    ];

    return (
        <>
            <Head title="Proses Survey" />
            <PageHeader title="Proses Survey" description={`Berkas yang dijadwalkan untuk Anda hari ini, ${formatDate(today)}`} />

            <DataTable
                rows={loans}
                rowKey={(l) => l.id}
                loading={loading}
                error={error}
                onRetry={() => visit({})}
                toolbar={<FilterBar search={<SearchInput value={search} onChange={onSearch} placeholder="Cari kode, nama, NIK…" label="Cari berkas" />} />}
                columns={columns}
                onRowClick={(l) => router.visit(`/surveys/${l.id}`)}
                empty={{
                    icon: <MapPin />,
                    title: 'Tidak ada survei hari ini',
                    description: 'Berkas tampil di sini pada tanggal surveinya.',
                }}
            />

            <ConfirmDialog
                open={replace !== null}
                onOpenChange={(open) => !open && setReplace(null)}
                title="Ganti posisi yang tersimpan?"
                description={
                    <>
                        Berkas <strong>{replace?.application_code}</strong> sudah punya lokasi survei dari {formatDateTime(replace?.located_at)}. Posisi itu
                        akan diganti dengan posisi Anda saat ini.
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
