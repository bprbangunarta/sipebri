import { Head } from '@inertiajs/react';
import { FileText } from 'lucide-react';
import type { ReactNode } from 'react';
import {
    Bar,
    BarChart,
    CartesianGrid,
    Cell,
    Pie,
    PieChart,
    ResponsiveContainer,
    Tooltip,
    XAxis,
    YAxis,
} from 'recharts';
import { Badge, Card, EmptyState, PageHeader } from '@/components/ui/misc';
import type { BadgeTone } from '@/components/ui/misc';
import { formatDate, rupiah } from '@/lib/format';

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

const PRIMARY = 'oklch(0.5 0.16 265)';
const PALETTE = [
    'oklch(0.5 0.16 265)',
    'oklch(0.68 0.14 200)',
    'oklch(0.75 0.15 75)',
    'oklch(0.62 0.17 340)',
    'oklch(0.65 0.15 150)',
    'oklch(0.6 0.02 265)',
];
const AXIS = { fontSize: 11, fill: 'oklch(0.55 0.02 265)' };

function Kpi({
    label,
    value,
    note,
}: {
    label: string;
    value: ReactNode;
    note?: string;
}) {
    return (
        <Card className="px-3 py-2.5">
            <p className="text-xs text-muted">{label}</p>
            <p className="text-xl leading-tight font-semibold tabular-nums">
                {value}
            </p>
            {note && <p className="text-xs text-muted">{note}</p>}
        </Card>
    );
}

function ChartCard({
    title,
    subtitle,
    className,
    children,
}: {
    title: string;
    subtitle?: string;
    className?: string;
    children: ReactNode;
}) {
    return (
        <Card className={className}>
            <div className="border-b border-line px-3 py-2">
                <h2 className="text-sm font-semibold">{title}</h2>
                {subtitle && <p className="text-xs text-muted">{subtitle}</p>}
            </div>
            <div className="p-3">{children}</div>
        </Card>
    );
}

function HorizontalBars({
    data,
    rowHeight = 24,
}: {
    data: Slice[];
    rowHeight?: number;
}) {
    return (
        <ResponsiveContainer
            width="100%"
            height={Math.max(data.length * rowHeight, 60) + 8}
        >
            <BarChart
                data={data}
                layout="vertical"
                margin={{ left: 0, right: 16, top: 0, bottom: 0 }}
            >
                <XAxis type="number" hide allowDecimals={false} />
                <YAxis
                    type="category"
                    dataKey="name"
                    width={90}
                    tick={AXIS}
                    tickLine={false}
                    axisLine={false}
                    interval={0}
                />
                <Tooltip
                    cursor={{ fill: 'oklch(0.96 0.005 260)' }}
                    formatter={(v) => [v, 'Berkas']}
                />
                <Bar
                    dataKey="total"
                    fill={PRIMARY}
                    radius={[0, 3, 3, 0]}
                    barSize={12}
                    label={{
                        position: 'right',
                        fontSize: 11,
                        fill: 'oklch(0.4 0.02 265)',
                    }}
                />
            </BarChart>
        </ResponsiveContainer>
    );
}

function VerticalBars({
    data,
    xKey = 'name',
}: {
    data: Record<string, string | number>[];
    xKey?: string;
}) {
    return (
        <ResponsiveContainer width="100%" height={180}>
            <BarChart
                data={data}
                margin={{ left: -24, right: 4, top: 8, bottom: 0 }}
            >
                <CartesianGrid
                    vertical={false}
                    stroke="oklch(0.92 0.006 260)"
                />
                <XAxis
                    dataKey={xKey}
                    tick={AXIS}
                    tickLine={false}
                    axisLine={false}
                    interval={0}
                />
                <YAxis
                    tick={AXIS}
                    tickLine={false}
                    axisLine={false}
                    allowDecimals={false}
                />
                <Tooltip
                    cursor={{ fill: 'oklch(0.96 0.005 260)' }}
                    formatter={(v) => [v, 'Berkas']}
                />
                <Bar
                    dataKey="total"
                    fill={PRIMARY}
                    radius={[3, 3, 0, 0]}
                    maxBarSize={28}
                />
            </BarChart>
        </ResponsiveContainer>
    );
}

function Donut({ data, total }: { data: Slice[]; total: number }) {
    const visible = data.filter((d) => d.total > 0);

    return (
        <div className="flex items-center gap-4">
            <div className="relative size-32 shrink-0">
                <ResponsiveContainer width="100%" height="100%">
                    <PieChart>
                        <Pie
                            data={visible}
                            dataKey="total"
                            nameKey="name"
                            innerRadius={40}
                            outerRadius={60}
                            paddingAngle={2}
                            strokeWidth={0}
                        >
                            {visible.map((_, i) => (
                                <Cell
                                    key={i}
                                    fill={PALETTE[i % PALETTE.length]}
                                />
                            ))}
                        </Pie>
                        <Tooltip formatter={(v) => [v, 'Berkas']} />
                    </PieChart>
                </ResponsiveContainer>
                <div className="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                    <span className="text-base font-semibold tabular-nums">
                        {total}
                    </span>
                    <span className="text-[10px] text-muted">total</span>
                </div>
            </div>
            <ul className="flex-1 space-y-1 text-sm">
                {visible.map((d, i) => (
                    <li
                        key={d.name}
                        className="flex items-center justify-between gap-2"
                    >
                        <span className="flex items-center gap-2">
                            <span
                                className="size-2.5 rounded-sm"
                                style={{
                                    background: PALETTE[i % PALETTE.length],
                                }}
                            />
                            {d.name}
                        </span>
                        <span className="text-muted tabular-nums">
                            {d.total} ·{' '}
                            {total ? Math.round((d.total / total) * 100) : 0}%
                        </span>
                    </li>
                ))}
            </ul>
        </div>
    );
}

export default function Dashboard({
    scope,
    stats,
    byStatus,
    byProduct,
    byOffice,
    byMonth,
    upcomingSurveys,
    recent,
}: Props) {
    const description =
        scope === 'all'
            ? 'Berkas kredit di semua kantor'
            : 'Berkas kredit yang Anda buka';

    if (stats.total === 0) {
        return (
            <>
                <Head title="Dashboard" />
                <PageHeader title="Dashboard" description={description} />
                <Card>
                    <EmptyState
                        icon={<FileText />}
                        title="Belum ada berkas kredit"
                        description="Ringkasan tampil di sini setelah ada pengajuan kredit."
                    />
                </Card>
            </>
        );
    }

    return (
        <>
            <Head title="Dashboard" />
            <PageHeader title="Dashboard" description={description} />

            <div className="grid grid-cols-2 gap-3 lg:grid-cols-4">
                <Kpi
                    label="Dalam proses"
                    value={stats.in_process}
                    note={`dari ${stats.total} berkas`}
                />
                <Kpi label="Draf" value={stats.drafts} />
                <Kpi label="Baru bulan ini" value={stats.this_month} />
                <Kpi
                    label="Plafon diajukan, dalam proses"
                    value={rupiah(stats.amount_in_process)}
                />
            </div>

            <div className="mt-3 grid gap-3 lg:grid-cols-3">
                <ChartCard
                    title="Berkas baru"
                    subtitle="12 bulan terakhir, menurut tanggal pengajuan"
                    className="lg:col-span-2"
                >
                    <VerticalBars data={byMonth} xKey="month" />
                </ChartCard>
                <ChartCard title="Menurut status">
                    <Donut data={byStatus} total={stats.total} />
                </ChartCard>
                <ChartCard title="Menurut produk" className="lg:col-span-2">
                    <HorizontalBars data={byProduct} />
                </ChartCard>
                <ChartCard title="Menurut kantor">
                    <HorizontalBars data={byOffice} />
                </ChartCard>
                <Card className="lg:col-span-2">
                    <div className="border-b border-line px-3 py-2">
                        <h2 className="text-sm font-semibold">
                            Pengajuan terbaru
                        </h2>
                    </div>
                    <ul className="divide-y divide-line">
                        {recent.map((l) => (
                            <li
                                key={l.id}
                                className="flex items-center justify-between gap-2 px-3 py-1.5 text-sm"
                            >
                                <div className="min-w-0">
                                    <p className="truncate font-medium">
                                        {l.name}
                                    </p>
                                    <p className="truncate text-xs text-muted">
                                        {l.code} · {formatDate(l.date)} ·{' '}
                                        {rupiah(l.amount)}
                                    </p>
                                </div>
                                <Badge tone={l.tone}>{l.status}</Badge>
                            </li>
                        ))}
                    </ul>
                </Card>
                <Card>
                    <div className="border-b border-line px-3 py-2">
                        <h2 className="text-sm font-semibold">
                            Survei mendatang
                        </h2>
                    </div>
                    {upcomingSurveys.length === 0 ? (
                        <p className="px-3 py-4 text-sm text-muted">
                            Belum ada survei terjadwal.
                        </p>
                    ) : (
                        <ul className="divide-y divide-line">
                            {upcomingSurveys.map((l) => (
                                <li
                                    key={l.id}
                                    className="flex items-center justify-between gap-2 px-3 py-1.5 text-sm"
                                >
                                    <div className="min-w-0">
                                        <p className="truncate font-medium">
                                            {l.name}
                                        </p>
                                        <p className="truncate text-xs text-muted">
                                            {l.code}
                                            {l.surveyor
                                                ? ` · ${l.surveyor}`
                                                : ''}
                                        </p>
                                    </div>
                                    <span className="shrink-0 text-xs text-muted">
                                        {formatDate(l.date)}
                                    </span>
                                </li>
                            ))}
                        </ul>
                    )}
                </Card>
            </div>
        </>
    );
}
