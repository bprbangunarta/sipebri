#!/usr/bin/env node
/**
 * ui:check — penjaga konsistensi UI SIPEBRI.
 * Jalankan `yarn ui:check` SEBELUM menyatakan pekerjaan UI selesai.
 * Aturan lengkap: /app/memory/ui_rules.md
 */
import { readdirSync, readFileSync, statSync } from 'node:fs';
import { join } from 'node:path';

const ROOTS = ['resources/js/pages', 'resources/js/components/composite'];
const EXEMPT = ['resources/js/pages/auth/Login.vue'];

/** Placeholder yang diizinkan. Selain ini = pelanggaran. */
const PLACEHOLDER_OK = [
    /^-- Pilih --$/,
    /^\(Opsional\)$/,
    /^Semua .+$/, // filter toolbar
    /^Cari/, // kolom pencarian
    /^Minimal 8 karakter$/, // kata sandi
    /^Pilih produk dahulu$/, // select bergantung
];

const files = [];
const walk = (dir) => {
    for (const name of readdirSync(dir)) {
        const full = join(dir, name);
        if (statSync(full).isDirectory()) walk(full);
        else if (name.endsWith('.vue')) files.push(full);
    }
};
ROOTS.forEach(walk);

const problems = [];
const add = (file, line, message) => problems.push(`${file}:${line}  ${message}`);

/** Komponen/ikon dipakai di template tapi tidak di-import (pernah bikin judul kartu hilang). */
const checkImports = (file, source) => {
    const template = source.slice(source.indexOf('<template>'));
    const used = new Set();
    for (const m of template.matchAll(/<([A-Z][A-Za-z0-9]*)[\s/>]/g)) used.add(m[1]);
    for (const m of template.matchAll(/:(?:submit-icon|is)="([A-Z][A-Za-z0-9]*)"/g)) used.add(m[1]);

    const script = source.slice(0, source.indexOf('<template>'));
    for (const name of used) {
        if (['template', 'component'].includes(name)) continue;
        const declared = new RegExp(`\\b${name}\\b`).test(script);
        if (!declared) add(file, 1, `<${name}> dipakai di template tapi tidak di-import`);
    }
};

for (const file of files) {
    if (EXEMPT.includes(file)) continue;
    const source = readFileSync(file, 'utf8');
    const lines = source.split('\n');
    checkImports(file, source);

    lines.forEach((line, i) => {
        const at = i + 1;

        const placeholder = line.match(/[^:]placeholder="([^"]*)"/);
        if (placeholder && !PLACEHOLDER_OK.some((re) => re.test(placeholder[1]))) {
            add(file, at, `placeholder "${placeholder[1]}" tidak baku — pakai "-- Pilih --" (wajib), "(Opsional)", atau "Semua …" (filter)`);
        }

        if (/<Input[^>]*type="date"/.test(line)) add(file, at, 'pakai DatePicker.vue, bukan <Input type="date">');
        if (/<Input[^>]*type="number"/.test(line)) {
            add(file, at, 'pakai NumberInput/DecimalInput/DigitsInput, bukan <Input type="number">');
        }
        if (/size="icon-sm"|size="lg"|size="xs"/.test(line)) add(file, at, 'ukuran tombol tidak baku (pakai sm / icon)');
    });

    // Blok footer wajib memakai FormActions.
    const blocks = [
        ...source.matchAll(/<CardFooter[\s\S]*?<\/CardFooter>/g),
        ...source.matchAll(/<template #footer>[\s\S]*?<\/template>/g),
    ];

    for (const block of blocks) {
        const body = block[0];
        const at = source.slice(0, block.index).split('\n').length;
        if (!body.includes('<FormActions')) {
            add(file, at, 'footer harus memakai <FormActions> (tombol Batal kiri, aksi utama kanan)');
        }
        if (/justify-(end|center|start)/.test(body)) {
            add(file, at, 'jangan menimpa perataan footer — FormActions sudah mengatur kiri–kanan');
        }
    }
}

if (problems.length) {
    console.error(`\nui:check GAGAL — ${problems.length} pelanggaran:\n`);
    problems.forEach((p) => console.error('  ' + p));
    console.error('\nAturan: /app/memory/ui_rules.md\n');
    process.exit(1);
}

console.log(`ui:check OK — ${files.length} berkas .vue sesuai standar.`);
