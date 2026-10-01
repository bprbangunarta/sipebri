import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Badge, Card } from '@/components/ui/misc';
import type { BadgeTone } from '@/components/ui/misc';
import { ReasonDialog } from '@/components/reason-dialog';

export type Corrections = {
    items: {
        id: number;
        status: string;
        status_label: string;
        reason: string;
        requested_at: string | null;
        opened_at: string | null;
        resolved_at: string | null;
        resolution_note: string | null;
    }[];
    approved: boolean;
    running: string | null;
    running_id: number | null;
    can_ask: boolean;
    can_open: boolean;
    can_decide_request: boolean;
    can_close: boolean;
};

const TONES: Record<string, BadgeTone> = {
    requested: 'warning',
    open: 'info',
    closed: 'success',
    declined: 'neutral',
};

type Dialog = 'ask' | 'open' | 'decline' | 'close' | null;

/** Corrections of the analysis of an approved file: ask, open, decline and close them, and the history of them. */
export function CorrectionPanel({
    loanId,
    corrections,
}: {
    loanId: number;
    corrections: Corrections;
}) {
    const [dialog, setDialog] = useState<Dialog>(null);
    const base = `/credit-analysis/${loanId}/corrections`;
    const id = corrections.running_id;

    if (!corrections.approved && corrections.items.length === 0) {
        return null;
    }

    const urls: Record<Exclude<Dialog, null>, string> = {
        ask: base,
        open: base,
        decline: `${base}/${id}/decline`,
        close: `${base}/${id}/close`,
    };

    return (
        <Card className="mb-3">
            <div className="flex flex-wrap items-center justify-between gap-2 border-b border-line px-3 py-2">
                <h2 className="text-sm font-semibold">Koreksi Analisa</h2>
                <div className="flex flex-wrap gap-2">
                    {corrections.can_ask && (
                        <Button
                            size="sm"
                            variant="outline"
                            onClick={() => setDialog('ask')}
                        >
                            Minta koreksi
                        </Button>
                    )}
                    {corrections.can_open && (
                        <Button
                            size="sm"
                            variant="outline"
                            onClick={() => setDialog('open')}
                        >
                            Buka untuk koreksi
                        </Button>
                    )}
                    {corrections.can_decide_request && (
                        <Button
                            size="sm"
                            variant="outline"
                            onClick={() => setDialog('decline')}
                        >
                            Tolak permintaan
                        </Button>
                    )}
                    {corrections.can_close && (
                        <Button size="sm" onClick={() => setDialog('close')}>
                            Selesai koreksi
                        </Button>
                    )}
                </div>
            </div>
            <div className="flex flex-col gap-2 p-3 text-sm">
                {corrections.approved && (
                    <p className="text-xs text-muted">
                        Berkas ini sudah disetujui komite, jadi lembar
                        analisanya terkunci. Kasi Analis berkas ini bisa
                        membukanya untuk koreksi (keterangan, ejaan, atau data
                        lain). Angka yang diputus komite tidak boleh berubah:
                        perubahan seperti itu perlu persetujuan ulang.
                    </p>
                )}
                {corrections.items.length === 0 ? (
                    <p className="text-xs text-muted">Belum ada koreksi.</p>
                ) : (
                    <ul className="flex flex-col gap-1.5">
                        {[...corrections.items].reverse().map((c) => (
                            <li
                                key={c.id}
                                className="rounded-md border border-line p-2"
                            >
                                <div className="flex flex-wrap items-center gap-2">
                                    <Badge tone={TONES[c.status] ?? 'neutral'}>
                                        {c.status_label}
                                    </Badge>
                                    <span className="text-xs text-muted">
                                        {c.opened_at ?? c.requested_at}
                                        {c.resolved_at
                                            ? ` → ${c.resolved_at}`
                                            : ''}
                                    </span>
                                </div>
                                <p className="mt-1">{c.reason}</p>
                                {c.resolution_note && (
                                    <p className="text-xs text-muted">
                                        Catatan: {c.resolution_note}
                                    </p>
                                )}
                            </li>
                        ))}
                    </ul>
                )}
                {corrections.running === 'requested' &&
                    corrections.can_decide_request && (
                        <div>
                            <Button size="sm" onClick={() => setDialog('open')}>
                                Buka permintaan ini
                            </Button>
                        </div>
                    )}
            </div>

            <ReasonDialog
                title={
                    dialog === 'ask'
                        ? 'Minta koreksi analisa'
                        : dialog === 'open'
                          ? corrections.running === 'requested'
                              ? 'Buka permintaan koreksi'
                              : 'Buka untuk koreksi'
                          : dialog === 'decline'
                            ? 'Tolak permintaan koreksi'
                            : 'Selesai koreksi'
                }
                description={
                    dialog === 'ask'
                        ? 'Jelaskan apa yang perlu dikoreksi. Kasi Analis berkas ini yang membukanya.'
                        : dialog === 'open'
                          ? 'Lembar analisa dibuka untuk koreksi. Angka yang diputus komite tetap terkunci.'
                          : dialog === 'decline'
                            ? 'Beri tahu analis kenapa permintaan ini ditolak.'
                            : 'Catat apa yang dikoreksi. Lembar analisa terkunci kembali.'
                }
                action={
                    dialog === null
                        ? null
                        : dialog === 'open' &&
                            corrections.running === 'requested'
                          ? `${base}/${id}/open`
                          : urls[dialog]
                }
                confirmLabel={
                    dialog === 'ask'
                        ? 'Kirim permintaan'
                        : dialog === 'open'
                          ? 'Buka'
                          : dialog === 'decline'
                            ? 'Tolak'
                            : 'Selesai'
                }
                fieldLabel={
                    dialog === 'close'
                        ? 'Catatan koreksi'
                        : dialog === 'decline'
                          ? 'Alasan penolakan'
                          : 'Alasan koreksi'
                }
                tone={dialog === 'decline' ? 'danger' : 'primary'}
                onClose={() => setDialog(null)}
            />
        </Card>
    );
}
