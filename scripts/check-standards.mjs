// Mechanical checks of the UI standards in .ai/rules/js.md (taken from react-starter). Exit code 1 when something is broken.
//   npm run standards
import { readdirSync, readFileSync, statSync } from 'node:fs';
import { join, relative } from 'node:path';
import { fileURLToPath } from 'node:url';

// `fileURLToPath`, not `.pathname`: a folder name with a space or a non-ASCII letter is percent-encoded in a URL and `.pathname` would not open.
const root = fileURLToPath(new URL('../resources/js', import.meta.url));
const navigation = fileURLToPath(new URL('../app/Support/Navigation.php', import.meta.url));
// Wayfinder writes these folders on every build; they are not ours to check.
const generated = new Set(['actions', 'routes', 'wayfinder'].map((name) => join(root, name)));
const files = [];

/** A file or folder that cannot be read stops the check with a sentence, never with a stack trace nobody can act on. */
function unreadable(path, error) {
    console.error(
        `Tidak bisa membaca ${relative(process.cwd(), path)} (${error.code ?? error.message}). Periksa izin dan isi folder resources/js, lalu ulangi.`,
    );
    process.exit(1);
}

(function walk(dir) {
    let names;

    try {
        names = readdirSync(dir);
    } catch (error) {
        unreadable(dir, error);
    }

    for (const name of names) {
        const path = join(dir, name);

        if (generated.has(path)) {
            continue;
        }

        try {
            if (statSync(path).isDirectory()) {
                walk(path);
            } else if (/\.(ts|tsx)$/.test(name)) {
                files.push(path);
            }
        } catch (error) {
            unreadable(path, error);
        }
    }
})(root);

if (files.length === 0) {
    console.error(`Tidak ada berkas .ts atau .tsx di ${relative(process.cwd(), root)}: pemeriksaan ini tidak memeriksa apa pun, jadi dihentikan.`);
    process.exit(1);
}

const sources = new Map(
    files.map((file) => {
        try {
            return [file, readFileSync(file, 'utf8')];
        } catch (error) {
            return unreadable(file, error);
        }
    }),
);

/** Test files and test setup may use literal colours and localStorage (test data, cleanup). */
const isTest = (file) => /\.test\.tsx?$|\/resources\/js\/test\//.test(file);
const isSharedUi = (file) => file.includes('/components/ui/');

const rules = [
    {
        name: 'Tabel mentah dilarang: pakai components/ui/data-table.tsx (DataTable)',
        test: (line) => /<table[\s>]/.test(line),
        skip: (file) => file.endsWith('components/ui/data-table.tsx'),
    },
    {
        name: 'Warna ditulis langsung: pakai token tema di resources/css/app.css',
        test: (line) => /#[0-9a-fA-F]{3,8}\b|\b(rgb|rgba|hsl|hsla|oklch)\(/.test(line),
        skip: (file) => file.endsWith('components/ui/color-input.tsx') || isTest(file),
    },
    {
        name: 'Baris judul + aksi memakai items-center, bukan items-start justify-between',
        test: (line) => /items-start justify-between/.test(line),
        skip: (file) => file.endsWith('components/ui/combobox.tsx'),
    },
    {
        name: 'Tanggal, waktu, dan angka untuk orang diformat di lib/format.ts saja (formatDate, formatDateTime, formatRelative, formatNumber, rupiah)',
        test: (line) => /from 'date-fns\/locale'|\bIntl\.(NumberFormat|DateTimeFormat)\b|\.toLocale(Date|Time)?String\(/.test(line),
        skip: (file) => isTest(file) || file.endsWith('lib/format.ts') || file.endsWith('components/ui/date-picker.tsx'),
    },
    { name: 'console.log/debugger tertinggal', test: (line) => /\bconsole\.log\(|\bdebugger\b/.test(line), skip: () => false },
    {
        name: 'Palet Tailwind untuk status dilarang: pakai token success/warning/info/danger (-soft, -line, -ink)',
        test: (line) =>
            /\b(bg|text|border|ring|fill|stroke|from|to|divide|outline)-(red|green|emerald|amber|yellow|orange|blue|sky|rose|lime|teal|slate|gray|zinc|neutral|stone|indigo|violet|purple|pink|cyan|fuchsia)-\d{2,3}\b/.test(
                line,
            ),
        skip: () => false,
    },
    {
        name: 'Ukuran teks sembarang dilarang: pakai text-2xs, text-xs, text-sm, atau text-base (token di resources/css/app.css)',
        test: (line) => /\btext-\[\d+(\.\d+)?(px|rem)\]/.test(line),
        skip: () => false,
    },
    {
        name: 'Lapisan layar penuh buatan sendiri dilarang: pakai Modal/ConfirmDialog (components/ui/dialog.tsx) atau Dialog Radix',
        test: (line) => /\bfixed inset-0\b/.test(line),
        skip: (file) => file.endsWith('components/ui/dialog.tsx'),
    },
    {
        name: 'type="number"/type="tel" dilarang: kolom angka memakai MaskedInput atau CurrencyInput, karena type="number" menerima huruf e dan tanda, dan type="tel" menerima huruf',
        test: (line) => /\btype=["']?(number|tel)\b/.test(line) || /\btype:\s*['"](number|tel)['"]/.test(line),
        skip: (file) => isTest(file),
    },
    {
        name: 'Kontrol native dilarang di halaman: pakai Textarea, Checkbox/Switch/RadioGroup, atau Combobox dari components/ui',
        test: (line) => /<textarea\b|<select\b|type=["']?(checkbox|radio)\b/.test(line),
        skip: (file) => isSharedUi(file) || isTest(file),
    },
    { name: 'Tipe any dilarang', test: (line) => /:\s*any\b|\bas any\b|<any>/.test(line), skip: () => false },
    { name: '@ts-ignore/@ts-nocheck dilarang: perbaiki tipenya', test: (line) => /@ts-(ignore|nocheck)/.test(line), skip: () => false, inComments: true }, // weakening-allowed: this is the rule that bans the suppression, not a suppression
    {
        name: 'fetch langsung dilarang: pakai Inertia (router, useForm, useHttp) dan rute Wayfinder',
        test: (line) => /\bfetch\(/.test(line),
        skip: (file) => file.endsWith('hooks/use-network-status.ts'),
    },
    { name: 'dangerouslySetInnerHTML dilarang: tampilkan teks biasa', test: (line) => /dangerouslySetInnerHTML/.test(line), skip: () => false },
    {
        name: 'localStorage/sessionStorage dilarang: rahasia tidak boleh disimpan di peramban',
        test: (line) => /\b(localStorage|sessionStorage)\b/.test(line),
        skip: (file) => isTest(file),
    },
    {
        name: 'Ekspor bawaan hanya untuk halaman, layout, dan app.tsx',
        test: (line) => /^export default\b/.test(line),
        skip: (file) => /\/(pages|layouts)\//.test(file) || file.endsWith('/app.tsx'),
    },
    {
        name: 'key memakai indeks dilarang: pakai id yang stabil',
        test: (line) => /key=\{(i|idx|index)\}/.test(line),
        skip: (file) => file.endsWith('components/ui/data-table.tsx'),
    },
];

let failures = 0;

const report = (file, line, message) => {
    failures++;
    console.error(`${relative(process.cwd(), file)}${line ? `:${line}` : ''}  ${message}`);
};

for (const file of files) {
    const lines = sources.get(file).split('\n');

    lines.forEach((line, index) => {
        const comment = /^\s*(\/\/|\*|\/\*)/.test(line);

        for (const rule of rules) {
            if (comment && !rule.inComments) {
                continue;
            }

            if (!rule.skip(file) && rule.test(line)) {
                report(file, index + 1, rule.name);
            }
        }
    });
}

const lineOf = (text, index) => text.slice(0, index).split('\n').length;

/** Where the `{ ... }` that starts at `start` ends (the value of a JSX attribute). */
function braceEnd(text, start) {
    let depth = 0;
    let quote = null;

    for (let i = start; i < text.length; i++) {
        const char = text[i];

        if (quote) {
            if (char === '\\') {
                i++;
            } else if (char === quote) {
                quote = null;
            }
        } else if (char === '"' || char === "'" || char === '`') {
            quote = char;
        } else if (char === '{') {
            depth++;
        } else if (char === '}' && --depth === 0) {
            return i + 1;
        }
    }

    return text.length;
}

/**
 * Where the value that starts at `start` ends: at the first `,` or closing bracket that is not inside brackets or a string. Enough for the
 * props and column properties checked below; it does not try to be a parser.
 */
function valueEnd(text, start) {
    let depth = 0;
    let quote = null;

    for (let i = start; i < text.length; i++) {
        const char = text[i];

        if (quote) {
            if (char === '\\') {
                i++;
            } else if (char === quote) {
                quote = null;
            }

            continue;
        }

        if (char === '"' || char === "'" || char === '`') {
            quote = char;
        } else if ('({['.includes(char)) {
            depth++;
        } else if (')}]'.includes(char)) {
            if (depth === 0) {
                return i;
            }

            depth--;
        } else if (char === ',' && depth === 0) {
            return i;
        }
    }

    return text.length;
}

for (const file of files) {
    const text = sources.get(file);

    if (isTest(file)) {
        continue;
    }

    // Buttons inside DialogFooter/CardFooter are text only (.ai/rules/js.md, "Tombol").
    for (const match of text.matchAll(/<(DialogFooter|CardFooter)\b[^>]*>([\s\S]*?)<\/\1>/g)) {
        if (/<[A-Z][A-Za-z0-9]*\s*\/>/.test(match[2] ?? '')) {
            report(file, lineOf(text, match.index), 'Tombol di footer dialog/kartu hanya teks, tanpa ikon');
        }
    }

    // A screen that opens a Modal supplies the dialog body with DialogBody (header, body, footer).
    if (/<Modal\b/.test(text) && !/\bDialogBody\b/.test(text) && !file.endsWith('components/ui/dialog.tsx')) {
        report(file, 0, 'Dialog tanpa DialogBody: isi dialog dibungkus DialogBody (header, body, footer)');
    }

    // The actions of a page header are laid out in equal columns (buttons), so a status badge there stretches to the width of a button:
    // the status goes in the `badge` prop, next to the title.
    for (const match of text.matchAll(/<PageHeader\b[^<]*?\bactions=/g)) {
        const start = match.index + match[0].length;

        if (/<Badge\b/.test(text.slice(start, braceEnd(text, start)))) {
            report(
                file,
                lineOf(text, start),
                'PageHeader: tanda status (Badge) tidak boleh di actions karena melebar sebesar tombol; pakai prop badge di sebelah judul',
            );
        }
    }

    // A table cell never wraps onto a second line ("Cells and headers never wrap"): several things in one cell sit in a row that scrolls, they do not wrap.
    for (const match of text.matchAll(/\bcell:\s*/g)) {
        const start = match.index + match[0].length;

        if (/\bflex-wrap\b/.test(text.slice(start, valueEnd(text, start)))) {
            report(
                file,
                lineOf(text, start),
                'Sel tabel dengan flex-wrap dilarang: isi sel tidak membungkus ke baris kedua (tabel yang lebar bergulir menyamping)',
            );
        }
    }

    // PageHeader gives its action buttons equal widths by itself, so `actions` is the buttons (a fragment), never a wrapping div.
    for (const match of text.matchAll(/<PageHeader\b[^>]*?actions=\{\s*<div\b/gs)) {
        report(file, lineOf(text, match.index), 'PageHeader: actions berisi tombolnya langsung (fragment), tanpa div pembungkus, supaya tombolnya sama lebar');
    }
}

// Sidebar labels live in app/Support/Navigation.php: one word, two at most, at most 15 characters, no slash; the long name is the page title.
let menu = '';

try {
    menu = readFileSync(navigation, 'utf8');
} catch (error) {
    unreadable(navigation, error);
}

for (const match of menu.matchAll(/(?:'label' => |self::reference\()'([^']+)'/g)) {
    const label = match[1];

    if (label.length > 15 || label.includes('/') || label.trim().split(/\s+/).length > 2) {
        report(navigation, lineOf(menu, match.index), `Label menu "${label}" terlalu panjang: satu atau dua kata, maksimal 15 karakter, tanpa garis miring`);
    }
}

if (failures > 0) {
    console.error(`\n${failures} pelanggaran standar kode.`);
    process.exit(1);
}

console.log(`Standar kode terpenuhi (${files.length} berkas diperiksa).`);
