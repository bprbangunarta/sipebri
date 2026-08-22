import {
    Banknote,
    ClipboardList,
    FileText,
    KeyRound,
    Landmark,
    MessagesSquare,
    ShieldCheck,
    Store,
} from 'lucide-vue-next';

/**
 * Kerangka lembar Analisa Kredit (v1).
 * Isi tiap bagian masih kosong — dibahas & dibangun satu per satu.
 */
export const ANALYSIS_SECTIONS = [
    {
        key: 'usaha',
        label: 'Analisa Usaha',
        icon: Store,
        subs: [
            { key: 'perdagangan', label: 'Usaha Perdagangan' },
            { key: 'pertanian', label: 'Usaha Pertanian' },
            { key: 'jasa', label: 'Usaha Jasa' },
            { key: 'lainnya', label: 'Usaha Lainnya' },
        ],
    },
    { key: 'keuangan', label: 'Analisa Keuangan', icon: Banknote, subs: [] },
    { key: 'kepemilikan', label: 'Analisa Kepemilikan', icon: KeyRound, subs: [] },
    { key: 'agunan', label: 'Analisa Agunan', icon: Landmark, subs: [] },
    { key: 'lima-c', label: 'Analisa 5C', icon: ShieldCheck, subs: [] },
    { key: 'kualitatif', label: 'Analisa Kualitatif', icon: MessagesSquare, subs: [] },
    { key: 'memorandum', label: 'Memorandum', icon: FileText, subs: [] },
    { key: 'administrasi', label: 'Administrasi', icon: ClipboardList, subs: [] },
];
