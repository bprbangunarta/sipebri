import { LayoutGrid, ShieldCheck } from 'lucide-vue-next';

/**
 * Konfigurasi navigasi terpusat.
 * `perm` = izin yang wajib dimiliki agar item muncul. Penyembunyian menu di
 * sini hanya kosmetik — penegakan sesungguhnya ada di middleware backend.
 */
/** Metadata area (label & deskripsi diambil dari basis data bila tersedia). */
export const AREA_META = {
    member: { icon: LayoutGrid, description: 'Pekerjaan harian' },
    admin: { icon: ShieldCheck, description: 'Pengelolaan sistem' },
};

export const DEFAULT_AREA_ID = 'member';

const ROUTE_TRAILS = [
    [/^\/$/, ['Dashboard']],
    [/^\/profile$/, ['Profil Pengguna']],
    [/^\/users$/, ['Pengguna']],
    [/^\/users\/create$/, ['Pengguna', 'Tambah']],
    [/^\/users\/\d+\/edit$/, ['Pengguna', 'Ubah']],
    [/^\/permissions$/, ['Perizinan']],
    [/^\/roles$/, ['Peranan']],
    [/^\/roles\/\d+$/, ['Peranan', 'Detail']],
    [/^\/appearance$/, ['Penampilan']],
    [/^\/menus$/, ['Menu Sidebar']],
    [/^\/products\/\d+$/, ['Referensi', 'Data Produk', 'Parameter']],
    [/^\/offices$/, ['Referensi', 'Data Kantor']],
    [/^\/institutions$/, ['Referensi', 'Data Instansi']],
    [/^\/products$/, ['Referensi', 'Data Produk']],
    [/^\/installments$/, ['Referensi', 'Sistem Cicilan']],
    [/^\/methods$/, ['Referensi', 'Sistem Bunga']],
    [/^\/committees$/, ['Referensi', 'Komite Kredit']],
    [/^\/committees\/\d+$/, ['Referensi', 'Komite Kredit', 'Detail']],
    [/^\/collateral-types$/, ['Agunan', 'Jenis Agunan']],
    [/^\/collateral-bindings$/, ['Agunan', 'Jenis Pengikatan']],
    [/^\/collateral-conditions$/, ['Agunan', 'Kondisi Agunan']],
    [/^\/collateral-methods$/, ['Agunan', 'Metode Hitung']],
    [/^\/collateral-simulation$/, ['Simulasi', 'Agunan Kredit']],
    [/^\/collateral-simulation\/create$/, ['Simulasi', 'Agunan Kredit', 'Tambah']],
    [/^\/collateral-simulation\/\d+\/edit$/, ['Simulasi', 'Agunan Kredit', 'Ubah']],
    [/^\/collateral-simulation\/\d+$/, ['Simulasi', 'Agunan Kredit', 'Detail']],
    [/^\/loan-simulation$/, ['Simulasi', 'Pengajuan Kredit']],
    [/^\/loan-simulation\/create$/, ['Simulasi', 'Pengajuan Kredit', 'Tambah']],
    [/^\/loan-simulation\/\d+\/edit$/, ['Simulasi', 'Pengajuan Kredit', 'Ubah']],
    [/^\/regions$/, ['Referensi', 'Data Wilayah']],
    [/^\/analysis-simulation$/, ['Simulasi', 'Analisa Kredit']],
    [/^\/object-storage$/, ['Object Storage']],
    [/^\/audit-trail$/, ['Audit Trail']],
    [/^\/audit-trail\/\d+$/, ['Audit Trail', 'Detail']],
    [/^\/schema-drafts$/, ['Skema Migrasi']],
    [/^\/schema-drafts\/\d+$/, ['Skema Migrasi', 'Rancangan']],
];

const ADMIN_ROUTES = [/^\/users/, /^\/permissions/, /^\/roles/, /^\/appearance/, /^\/menus/, /^\/object-storage/, /^\/audit-trail/, /^\/schema-drafts/];

/** Id area yang memiliki sebuah pathname. */
export const areaIdOf = (pathname) =>
    ADMIN_ROUTES.some((re) => re.test(pathname)) ? 'admin' : 'member';

/** Jejak breadcrumb untuk sebuah pathname. */
export function getBreadcrumb(pathname) {
    const match = ROUTE_TRAILS.find(([pattern]) => pattern.test(pathname));
    return { trail: match ? match[1] : ['Dashboard'] };
}
