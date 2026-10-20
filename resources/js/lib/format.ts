import { formatDistanceToNow, parseISO } from 'date-fns';
import { id } from 'date-fns/locale';

/**
 * The only place a date, a time, or a number is turned into text for a person (Indonesian, e.g. "04 Okt 2026"). Screens call these and
 * never format on their own; what goes over the wire stays ISO and plain numbers.
 */

type Moment = string | Date | null | undefined;

/**
 * Every time on screen is the company's clock, WIB (`config/app.php`, `timezone`), whatever zone the device is in: a person on a
 * phone in Bali or abroad reads the same hour as the audit log. Indonesia's WIB has no summer time, so it is a fixed +07:00.
 */
const COMPANY_ZONE = 'Asia/Jakarta';
const COMPANY_OFFSET = '+07:00';
const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];

type Clock = { year: number; month: number; day: number; hour: number; minute: number; second: number };

const companyClock = new Intl.DateTimeFormat('en-GB', {
    timeZone: COMPANY_ZONE,
    hourCycle: 'h23',
    year: 'numeric',
    month: 'numeric',
    day: 'numeric',
    hour: 'numeric',
    minute: 'numeric',
    second: 'numeric',
});

/** A date alone ("2026-10-19"), and a date with a time and no zone ("2026-10-07 08:45:03", as the audit log writes it: already on the company's clock). */
const DATE_ONLY = /^(\d{4})-(\d{2})-(\d{2})$/;
const NAIVE = /^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})(?::(\d{2}))?(?:\.\d+)?$/;

function real(clock: Clock): Clock | null {
    const check = new Date(Date.UTC(clock.year, clock.month - 1, clock.day, clock.hour, clock.minute, clock.second));
    const same =
        check.getUTCFullYear() === clock.year &&
        check.getUTCMonth() === clock.month - 1 &&
        check.getUTCDate() === clock.day &&
        check.getUTCHours() === clock.hour &&
        check.getUTCMinutes() === clock.minute &&
        check.getUTCSeconds() === clock.second;

    return same ? clock : null;
}

function atCompanyClock(instant: Date): Clock | null {
    if (Number.isNaN(instant.getTime())) {
        return null;
    }

    const parts = Object.fromEntries(companyClock.formatToParts(instant).map((part) => [part.type, Number(part.value)]));

    return { year: parts.year, month: parts.month, day: parts.day, hour: parts.hour, minute: parts.minute, second: parts.second };
}

/**
 * The company's wall clock for a value from the server, or null when it cannot be read (a value that is not a date shows a dash, it must
 * not break the screen). A `Date` is a moment, except for a date alone (`asDay`): a day picked on a calendar is the day on the device.
 */
function clockOf(value: string | Date, asDay: boolean): Clock | null {
    if (value instanceof Date) {
        return asDay && !Number.isNaN(value.getTime())
            ? { year: value.getFullYear(), month: value.getMonth() + 1, day: value.getDate(), hour: 0, minute: 0, second: 0 }
            : atCompanyClock(value);
    }

    const text = value.trim();
    const day = DATE_ONLY.exec(text);

    if (day) {
        return real({ year: Number(day[1]), month: Number(day[2]), day: Number(day[3]), hour: 0, minute: 0, second: 0 });
    }

    const naive = NAIVE.exec(text);

    if (naive) {
        return real({
            year: Number(naive[1]),
            month: Number(naive[2]),
            day: Number(naive[3]),
            hour: Number(naive[4]),
            minute: Number(naive[5]),
            second: Number(naive[6] ?? 0),
        });
    }

    return atCompanyClock(parseISO(text));
}

const two = (n: number) => String(n).padStart(2, '0');
const dayText = (clock: Clock) => `${two(clock.day)} ${MONTHS[clock.month - 1]} ${clock.year}`;

/** "04 Okt 2026", or a dash when empty. */
export function formatDate(value: Moment): string {
    const clock = value ? clockOf(value, true) : null;

    return clock ? dayText(clock) : '–';
}

/** "04 Okt 2026 08:45:03" on the company's clock, or a dash when empty. A space instead of the "T" between date and time is accepted. */
export function formatDateTime(value: Moment): string {
    const clock = value ? clockOf(value, false) : null;

    return clock ? `${dayText(clock)} ${two(clock.hour)}:${two(clock.minute)}:${two(clock.second)}` : '–';
}

/** "3 menit yang lalu". A time without a zone is on the company's clock. */
export function formatRelative(value: string | Date): string {
    const date =
        typeof value === 'string' && NAIVE.test(value.trim())
            ? parseISO(`${value.trim().replace(' ', 'T')}${COMPANY_OFFSET}`)
            : typeof value === 'string'
              ? parseISO(value)
              : value;

    return Number.isNaN(date.getTime()) ? '–' : formatDistanceToNow(date, { addSuffix: true, locale: id });
}

const numberFormat = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 });

/** A whole number with Indonesian thousands separators: "1.500.000". */
export function formatNumber(value: number): string {
    return Number.isFinite(value) ? numberFormat.format(value) : '–';
}

const percentFormat = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 });

/** A percentage with up to two decimals and no more: "65,79%", "152%". */
export function formatPercent(value: number): string {
    return Number.isFinite(value) ? `${percentFormat.format(value)}%` : '–';
}

const rupiahFormat = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
});

/** Whole-rupiah amount, e.g. "Rp 1.500.000". */
export function rupiah(value: number | string | null | undefined): string {
    if (value === null || value === undefined || (typeof value === 'string' && value.trim() === '')) {
        return '–';
    }

    const amount = Number(value);

    return Number.isFinite(amount) ? rupiahFormat.format(amount) : '–';
}
