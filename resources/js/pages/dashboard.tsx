import { Head } from '@inertiajs/react';
import { FileText } from 'lucide-react';
import type { ReactNode } from 'react';
import { BarChart, ColumnChart, DonutChart } from '@/components/ui/chart';
import type { ChartItem } from '@/components/ui/chart';
import { Badge, Card, CardBody, CardHeader, EmptyState, PageHeader } from '@/components/ui/misc';
import type { BadgeTone } from '@/components/ui/misc';
import { formatDate, formatNumber, rupiah } from '@/lib/format';

type Slice = { name: string; total: number };

type Props = {
    scope: 'all' | 'mine';
    stats: {
        total: number;
        in_process: number;
        drafts: number;
        this_month: number;
        amount_in_process: number;
    };
    byStatus: Slice[];
    byProduct: Slice[];
    byOffice: Slice[];
    byMonth: { month: string; total: number }[];
    upcomingSurveys: {
        id: number;
        code: string;
        name: string;
        date: string | null;
        surveyor: string | null;
    }[];
    recent: {
        id: number;
        code: string;
        name: string;
        amount: number;
        date: string;
        status: string;
        tone: BadgeTone;
    }[];
};

const items = (slices: Slice[]): ChartItem[] => slices.map((slice) => ({ label: slice.name, count: slice.total }));

function Kpi({ label, value, note }: { label: string; value: ReactNode; note?: string }) {
    return (
        <Card className="px-3 py-2.5">
            <p className="text-xs text-muted">{label}</p>
            <p className="text-base leading-tight font-semibold tabular-nums">{value}</p>
            {note && <p className="text-xs text-muted">{note}</p>}
        </Card>
    );
}

function ChartCard({ title, description, className, children }: { title: string; description?: string; className?: string; children: ReactNode }) {
    return (
        <Card className={className}>
            <CardHeader title={title} description={description} />
            <CardBody>{children}</CardBody>
        </Card>
    );
}

export default function Dashboard({ scope, stats, byStatus, byProduct, byOffice, byMonth, upcomingSurveys, recent }: Props) {
    const description = scope === 'all' ? 'Berkas kredit di semua kantor' : 'Berkas kredit yang Anda buka';

    if (stats.total === 0) {
        return (
            <>
                <Head title="Dashboard" />
                <PageHeader title="Dashboard" description={description} />
                <Card>
                    <EmptyState icon={<FileText />} title="Belum ada berkas kredit" description="Ringkasan tampil di sini setelah ada pengajuan kredit." />
                </Card>
            </>
        );
    }

    return (
        <>
            <Head title="Dashboard" />
            <PageHeader title="Dashboard" description={description} />

            <div className="grid grid-cols-2 gap-3 lg:grid-cols-4">
                <Kpi label="Dalam proses" value={formatNumber(stats.in_process)} note={`dari ${formatNumber(stats.total)} berkas`} />
                <Kpi label="Draf" value={formatNumber(stats.drafts)} />
                <Kpi label="Baru bulan ini" value={formatNumber(stats.this_month)} />
                <Kpi label="Plafon diajukan, dalam proses" value={rupiah(stats.amount_in_process)} />
            </div>

            <div className="mt-3 grid gap-3 lg:grid-cols-3">
                <ChartCard title="Berkas baru" description="12 bulan terakhir, menurut tanggal pengajuan" className="lg:col-span-2">
                    <ColumnChart label="Berkas baru per bulan" items={byMonth.map((month) => ({ label: month.month, count: month.total }))} />
                </ChartCard>
                <ChartCard title="Menurut status">
                    <DonutChart label="Berkas menurut status" items={items(byStatus.filter((slice) => slice.total > 0))} centerLabel="berkas" />
                </ChartCard>
                <ChartCard title="Menurut produk" className="lg:col-span-2">
                    <BarChart label="Berkas menurut produk" items={items(byProduct)} />
                </ChartCard>
                <ChartCard title="Menurut kantor">
                    <BarChart label="Berkas menurut kantor" items={items(byOffice)} />
                </ChartCard>
                <Card className="lg:col-span-2">
                    <CardHeader title="Pengajuan terbaru" />
                    <ul className="divide-y divide-line">
                        {recent.map((loan) => (
                            <li key={loan.id} className="flex items-center justify-between gap-2 px-3 py-1.5 text-sm">
                                <div className="min-w-0">
                                    <p className="truncate font-medium">{loan.name}</p>
                                    <p className="truncate text-xs text-muted">
                                        {loan.code} · {formatDate(loan.date)} · {rupiah(loan.amount)}
                                    </p>
                                </div>
                                <Badge tone={loan.tone}>{loan.status}</Badge>
                            </li>
                        ))}
                    </ul>
                </Card>
                <Card>
                    <CardHeader title="Survei mendatang" />
                    {upcomingSurveys.length === 0 ? (
                        <p className="px-3 py-4 text-sm text-muted">Belum ada survei terjadwal.</p>
                    ) : (
                        <ul className="divide-y divide-line">
                            {upcomingSurveys.map((loan) => (
                                <li key={loan.id} className="flex items-center justify-between gap-2 px-3 py-1.5 text-sm">
                                    <div className="min-w-0">
                                        <p className="truncate font-medium">{loan.name}</p>
                                        <p className="truncate text-xs text-muted">
                                            {loan.code}
                                            {loan.surveyor ? ` · ${loan.surveyor}` : ''}
                                        </p>
                                    </div>
                                    <span className="shrink-0 text-xs text-muted">{formatDate(loan.date)}</span>
                                </li>
                            ))}
                        </ul>
                    )}
                </Card>
            </div>
        </>
    );
}
