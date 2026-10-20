import { describe, expect, it } from 'vitest';
import { formatDate, formatDateTime, formatNumber, formatPercent, formatRelative, rupiah } from '@/lib/format';

describe('format', () => {
    it('formats whole rupiah with Indonesian separators', () => {
        expect(rupiah(1500000).replace(/\s/g, ' ')).toBe('Rp 1.500.000');
        expect(rupiah('2500').replace(/\s/g, ' ')).toBe('Rp 2.500');
    });

    it('shows a dash for empty values', () => {
        expect(rupiah(null)).toBe('–');
        expect(rupiah('')).toBe('–');
        expect(formatDate(null)).toBe('–');
    });

    it('formats an ISO date in Indonesian', () => {
        expect(formatDate('2026-10-04T10:00:00.000Z')).toBe('04 Okt 2026');
    });

    it('writes date and time with seconds, and accepts a space as the separator the server sends', () => {
        expect(formatDateTime('2026-10-07 08:45:03')).toBe('07 Okt 2026 08:45:03');
        expect(formatDateTime(new Date(2026, 9, 7, 8, 45, 3))).toBe('07 Okt 2026 08:45:03');
        expect(formatDateTime(undefined)).toBe('–');
    });

    it('writes a whole number with Indonesian thousands separators', () => {
        expect(formatNumber(1500000)).toBe('1.500.000');
    });

    it('writes relative time in Indonesian', () => {
        expect(formatRelative(new Date(Date.now() - 3 * 60 * 1000))).toBe('3 menit yang lalu');
    });

    it('shows a dash, not an error, for a value that is not a date', () => {
        expect(formatDate('salah')).toBe('–');
        expect(formatDateTime('salah')).toBe('–');
        expect(formatDate(new Date('tidak jelas'))).toBe('–');
        expect(formatRelative('salah')).toBe('–');
    });
});

describe('format with values that cannot be read as a number', () => {
    it('shows a dash for a number that is not one, instead of "NaN"', () => {
        expect(rupiah('abc')).toBe('–');
        expect(rupiah(Number.NaN)).toBe('–');
        expect(rupiah(Number.POSITIVE_INFINITY)).toBe('–');
        expect(formatNumber(Number.NaN)).toBe('–');
        expect(formatNumber(Number.NEGATIVE_INFINITY)).toBe('–');
    });

    it('writes zero, a negative amount, a decimal string from the server, and a very large amount', () => {
        const plain = (text: string) => text.replace(/\s/g, ' ');

        expect(plain(rupiah(0))).toBe('Rp 0');
        expect(plain(rupiah(-1500))).toBe('-Rp 1.500');
        expect(plain(rupiah('1500000.00'))).toBe('Rp 1.500.000');
        expect(plain(rupiah(999999999999999))).toBe('Rp 999.999.999.999.999');
        expect(formatNumber(0)).toBe('0');
        expect(formatNumber(-2500)).toBe('-2.500');
    });
});

describe('format and the company clock (Asia/Jakarta)', () => {
    it('takes the day from the clock the tests run on (set in vite.config.ts), also across midnight', () => {
        expect(process.env.TZ).toBe('Asia/Jakarta');
        // 17:30 UTC on 31 October is 00:30 on 1 November in Jakarta.
        expect(formatDate('2026-10-31T17:30:00Z')).toBe('01 Nov 2026');
        expect(formatDateTime('2026-10-31T17:30:00Z')).toBe('01 Nov 2026 00:30:00');
        expect(formatDateTime('2026-10-31T17:30:00+00:00')).toBe('01 Nov 2026 00:30:00');
    });

    it('keeps a date without a time on the same day, whatever the zone', () => {
        expect(formatDate('2026-10-19')).toBe('19 Okt 2026');
        expect(formatDate('2026-01-01')).toBe('01 Jan 2026');
        expect(formatDate('2026-12-31')).toBe('31 Des 2026');
    });

    it('writes every month abbreviation in Indonesian', () => {
        const months = Array.from({ length: 12 }, (_, month) => formatDate(new Date(2026, month, 5)).split(' ')[1]);

        expect(months).toEqual(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des']);
    });

    it('treats an empty string, zero-like and date-only space values the way the screen expects', () => {
        expect(formatDate('')).toBe('–');
        expect(formatDateTime('')).toBe('–');
        expect(formatDateTime('2026-10-07 08:45')).toBe('07 Okt 2026 08:45:00');
    });
});

describe('format shows the company clock (WIB) on a device in any other zone', () => {
    const zones = ['UTC', 'America/New_York', 'Asia/Tokyo', 'Pacific/Auckland'];

    async function inZone<T>(zone: string, run: () => T): Promise<T> {
        const before = process.env.TZ;
        process.env.TZ = zone;

        try {
            return run();
        } finally {
            process.env.TZ = before;
        }
    }

    it.each(zones)('reads a moment with a zone as the same WIB time, on a device in %s', async (zone) => {
        const text = await inZone(zone, () => [
            formatDateTime('2026-10-31T17:30:00Z'),
            formatDate('2026-10-31T17:30:00Z'),
            formatDateTime('2026-10-20T00:15:00+07:00'),
        ]);

        expect(text).toEqual(['01 Nov 2026 00:30:00', '01 Nov 2026', '20 Okt 2026 00:15:00']);
    });

    it.each(zones)('reads a time without a zone (the audit log) as already WIB, on a device in %s', async (zone) => {
        const text = await inZone(zone, () => [
            formatDateTime('2026-10-07 08:45:03'),
            formatDateTime('2026-10-07T23:59:59.123456'),
            formatDateTime('2026-10-07 08:45'),
        ]);

        expect(text).toEqual(['07 Okt 2026 08:45:03', '07 Okt 2026 23:59:59', '07 Okt 2026 08:45:00']);
    });

    it.each(zones)('keeps a date alone and a picked day on the same day, on a device in %s', async (zone) => {
        const text = await inZone(zone, () => [formatDate('2026-10-19'), formatDate('2026-01-01'), formatDate(new Date(2026, 9, 19))]);

        expect(text).toEqual(['19 Okt 2026', '01 Jan 2026', '19 Okt 2026']);
    });

    it('counts a time without a zone as WIB when it says how long ago', async () => {
        const now = new Date();
        const wib = new Date(now.getTime() + 7 * 3600_000);
        const naive = `${wib.getUTCFullYear()}-${String(wib.getUTCMonth() + 1).padStart(2, '0')}-${String(wib.getUTCDate()).padStart(2, '0')} ${String(wib.getUTCHours()).padStart(2, '0')}:${String(wib.getUTCMinutes()).padStart(2, '0')}:00`;

        for (const zone of zones) {
            expect(await inZone(zone, () => formatRelative(naive))).toMatch(
                /^(kurang dari 1 menit|1 menit) yang lalu$|^kurang dari semenit yang lalu$|^1 menit yang lalu$/,
            );
        }
    });

    it('refuses what is not a real date or time', () => {
        for (const bad of ['2026-02-30', '2026-13-01', '2026-10-07 25:00:00', '2026-10-07 08:61', 'kemarin']) {
            expect(formatDate(bad)).toBe('–');
            expect(formatDateTime(bad)).toBe('–');
        }
    });
});

describe('formatPercent', () => {
    it('keeps up to two decimals with a decimal comma, and writes a dash for what is not a number', () => {
        expect(formatPercent(65.79)).toBe('65,79%');
        expect(formatPercent(152)).toBe('152%');
        expect(formatPercent(73.1)).toBe('73,1%');
        expect(formatPercent(-3.5)).toBe('-3,5%');
        expect(formatPercent(Number.NaN)).toBe('–');
    });
});
