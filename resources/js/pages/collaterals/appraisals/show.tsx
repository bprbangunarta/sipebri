import { Head, Link, router, usePage } from '@inertiajs/react';
import { ArrowLeft, CircleAlert, Gavel, Trash2 } from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';
import { AppraisalBlock } from '@/components/appraisal/appraisal-block';
import { Button } from '@/components/ui/button';
import { ConfirmDialog } from '@/components/ui/dialog';
import { PanelMenu } from '@/components/ui/panel-menu';
import { Alert, Badge, Card, CardBody, CardFooter, CardHeader, PageHeader } from '@/components/ui/misc';
import { formatDate, formatDateTime, formatNumber } from '@/lib/format';
import { Stat } from '@/pages/credit-analysis/parts';
import type { Answers, CollateralSummary, Computed, Photo, Section, Summary } from '@/pages/collaterals/appraisals/types';

type Props = {
    appraisal: Summary & { loan_linked: boolean };
    collateral: CollateralSummary;
    template: { key: string; version: number; sections: Section[] };
    values: Record<string, string>;
    notes: Record<string, string>;
    computed: Computed;
    progress: Record<string, [number, number]>;
    photos: Record<string, Photo[]>;
    locked: string[];
    blockers: string[];
    maxPhotos: number;
    canEdit: boolean;
    section: string | null;
};

/** The keys of the items of a section that are answered, as the server counts them (not what the system works out, not photos). */
const answerKeys = (section: Section): string[] =>
    section.blocks.flatMap((block) =>
        block.layout === 'grid'
            ? (block.rows ?? []).flatMap((row) =>
                  (block.columns ?? []).filter((column) => !row.computed && !column.computed).map((column) => `${section.key}.${row.key}.${column.key}`),
              )
            : block.layout === 'photos'
              ? []
              : (block.items ?? []).filter((item) => item.type !== 'computed').map((item) => item.key),
    );

const initialAnswers = (sections: Section[], values: Record<string, string>, notes: Record<string, string>): Answers =>
    Object.fromEntries(sections.flatMap(answerKeys).map((key) => [key, { value: values[key] ?? '', note: notes[key] ?? '' }]));

export default function AppraisalShow({
    appraisal,
    collateral,
    template,
    values,
    notes,
    computed,
    progress,
    photos,
    locked,
    blockers,
    maxPhotos,
    canEdit,
    section,
}: Props) {
    const errors = usePage().props.errors as Record<string, string>;
    const [active, setActive] = useState(template.sections.some((s) => s.key === section) ? (section as string) : template.sections[0].key);
    const saved = useMemo(() => initialAnswers(template.sections, values, notes), [template.sections, values, notes]);
    const [answers, setAnswers] = useState<Answers>(saved);
    const [saving, setSaving] = useState(false);
    const [uploading, setUploading] = useState<string | null>(null);
    const [showFacts, setShowFacts] = useState(false);
    const [finalizing, setFinalizing] = useState(false);
    const [removing, setRemoving] = useState(false);
    const [busy, setBusy] = useState(false);
    const current = template.sections.find((s) => s.key === active) ?? template.sections[0];
    const base = `/collateral-appraisals/${appraisal.id}`;

    const keysOf = (s: Section) => answerKeys(s).filter((key) => !locked.includes(key));
    const dirty = (s: Section) => keysOf(s).some((key) => answers[key]?.value !== saved[key]?.value || answers[key]?.note !== saved[key]?.note);

    const unsaved = canEdit && template.sections.some(dirty);

    // Leaving the page by a link would drop what was typed and not saved.
    useEffect(() => {
        if (!unsaved) {
            return;
        }

        const stop = router.on('before', (event) => {
            if (event.detail.visit.method === 'get' && !window.confirm('Ada isian yang belum disimpan. Tinggalkan halaman ini?')) {
                event.preventDefault();
            }
        });
        const warn = (event: BeforeUnloadEvent) => event.preventDefault();

        window.addEventListener('beforeunload', warn);

        return () => {
            stop();
            window.removeEventListener('beforeunload', warn);
        };
    }, [unsaved]);

    const save = () =>
        router.put(
            `${base}/sections/${current.key}`,
            { answers: keysOf(current).map((key) => ({ key, value: answers[key].value, note: answers[key].note })) },
            { preserveScroll: true, onStart: () => setSaving(true), onFinish: () => setSaving(false) },
        );

    const reset = () => setAnswers((all) => ({ ...all, ...Object.fromEntries(keysOf(current).map((key) => [key, saved[key]])) }));

    /** Photos are sent one by one, so a failure stops at that file and the rest are not lost silently. */
    const upload = async (item: string, files: FileList | null) => {
        if (!files?.length) {
            return;
        }

        setUploading(item);

        for (const file of Array.from(files)) {
            const ok = await new Promise<boolean>((resolve) =>
                router.post(
                    `${base}/photos`,
                    { item, photo: file },
                    {
                        forceFormData: true,
                        preserveScroll: true,
                        onSuccess: () => resolve(true),
                        onError: () => resolve(false),
                        onCancel: () => resolve(false),
                    },
                ),
            );

            if (!ok) {
                break;
            }
        }

        setUploading(null);
    };

    const isFinal = appraisal.status === 'final';

    return (
        <>
            <Head title={`Penilaian ke-${appraisal.sequence} ${collateral.label}`} />
            <PageHeader
                title={`Penilaian ke-${appraisal.sequence} · ${collateral.label}`}
                description={`${appraisal.purpose_label}${appraisal.effective_date ? ` · nilai per ${formatDate(appraisal.effective_date)}` : ''}`}
                badge={<Badge tone={isFinal ? 'success' : 'warning'}>{isFinal ? 'Final' : 'Draf'}</Badge>}
                actions={
                    <>
                        <Button variant="outline" onClick={() => router.visit(`/collaterals/${collateral.id}/appraisals`)}>
                            <ArrowLeft /> Riwayat
                        </Button>
                        {canEdit && (
                            <>
                                <Button variant="outline" onClick={() => setRemoving(true)}>
                                    <Trash2 /> Hapus draf
                                </Button>
                                <Button disabled={blockers.length > 0} onClick={() => setFinalizing(true)}>
                                    <Gavel /> Finalkan
                                </Button>
                            </>
                        )}
                    </>
                }
            />

            {isFinal && (
                <Alert tone="neutral" className="mb-3">
                    Penilaian ini final{appraisal.finalized_at ? `, ${formatDateTime(appraisal.finalized_at)}` : ''}, dan menjadi riwayat yang tidak bisa
                    diubah. Mulai penilaian baru dari halaman riwayat untuk memperbarui nilainya.
                </Alert>
            )}
            {canEdit && blockers.length > 0 && (
                <Alert tone="warning" className="mb-3" icon={<CircleAlert />}>
                    <p className="font-medium">Belum bisa difinalkan:</p>
                    <ul className="list-disc pl-4">
                        {blockers.map((blocker) => (
                            <li key={blocker}>{blocker}</li>
                        ))}
                    </ul>
                </Alert>
            )}
            {errors.finalize && (
                <Alert tone="danger" className="mb-3">
                    {errors.finalize}
                </Alert>
            )}

            <Card className="mb-3">
                <CardHeader
                    title="Data tetap agunan"
                    description="Dari data jaminan; berlaku untuk semua penilaian."
                    actions={
                        <>
                            <Button variant="ghost" size="sm" aria-expanded={showFacts} onClick={() => setShowFacts(!showFacts)}>
                                {showFacts ? 'Sembunyikan' : 'Tampilkan'}
                            </Button>
                            <Link href={`/collaterals/${collateral.id}/edit`} className="text-xs text-primary hover:underline">
                                Ubah data jaminan
                            </Link>
                        </>
                    }
                />
                {showFacts && (
                    <CardBody className="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        {collateral.facts.map((fact) => (
                            <Stat
                                key={fact.label}
                                label={fact.label}
                                value={
                                    fact.type === 'date'
                                        ? formatDate(String(fact.value))
                                        : fact.type === 'integer'
                                          ? formatNumber(Number(fact.value))
                                          : fact.value
                                }
                            />
                        ))}
                    </CardBody>
                )}
            </Card>

            <Card className="grid lg:grid-cols-[16rem_minmax(0,1fr)]">
                <PanelMenu
                    label="Bagian penilaian"
                    items={template.sections.map((s) => ({ key: s.key, label: s.label, unsaved: canEdit && dirty(s) }))}
                    active={active}
                    onSelect={setActive}
                />

                <div className="min-w-0">
                    <CardHeader
                        title={current.label.replace(/(^|[\s(/])(\p{L})/gu, (_, before: string, letter: string) => before + letter.toUpperCase())}
                        actions={
                            <>
                                {canEdit && dirty(current) && <Badge tone="warning">Belum disimpan</Badge>}
                                <Badge tone={(progress[current.key]?.[0] ?? 0) === (progress[current.key]?.[1] ?? 0) ? 'success' : 'info'}>
                                    {`${progress[current.key]?.[0] ?? 0}/${progress[current.key]?.[1] ?? 0}`}
                                </Badge>
                            </>
                        }
                    />
                    <CardBody className="flex flex-col gap-4">
                        {current.blocks.map((block, index) => (
                            <div key={`${current.key}-${index}`} className={index > 0 ? 'border-t border-line pt-4' : undefined}>
                                <AppraisalBlock
                                    section={current.key}
                                    block={block}
                                    answers={answers}
                                    onAnswer={(key, patch) => setAnswers((all) => ({ ...all, [key]: { ...all[key], ...patch } }))}
                                    computed={computed}
                                    errors={errors}
                                    readOnly={!canEdit}
                                    locked={locked}
                                    photos={photos}
                                    maxPhotos={maxPhotos}
                                    onUpload={upload}
                                    onDeletePhoto={(id) => router.delete(`${base}/photos/${id}`, { preserveScroll: true })}
                                    uploading={uploading}
                                />
                            </div>
                        ))}
                    </CardBody>
                    {canEdit && answerKeys(current).length > 0 && (
                        <CardFooter className="sticky bottom-0 rounded-b-lg bg-surface">
                            <Button type="button" variant="outline" disabled={!dirty(current) || saving} onClick={reset}>
                                Kembalikan
                            </Button>
                            <Button type="button" loading={saving} disabled={!dirty(current)} onClick={save}>
                                Simpan semua
                            </Button>
                        </CardFooter>
                    )}
                </div>
            </Card>

            <ConfirmDialog
                open={finalizing}
                onOpenChange={(open) => !busy && setFinalizing(open)}
                title="Finalkan penilaian?"
                description="Penilaian yang sudah final menjadi riwayat dan tidak bisa diubah atau dihapus. Pastikan semua bagian sudah benar."
                confirmLabel="Finalkan"
                loading={busy}
                onConfirm={() =>
                    router.post(
                        `${base}/finalize`,
                        {},
                        {
                            onStart: () => setBusy(true),
                            onFinish: () => {
                                setBusy(false);
                                setFinalizing(false);
                            },
                        },
                    )
                }
            />
            <ConfirmDialog
                open={removing}
                onOpenChange={(open) => !busy && setRemoving(open)}
                title="Hapus draf penilaian?"
                description="Draf ini dan semua fotonya dihapus. Penilaian yang sudah final tidak bisa dihapus."
                loading={busy}
                onConfirm={() =>
                    router.delete(base, {
                        onStart: () => setBusy(true),
                        onFinish: () => {
                            setBusy(false);
                            setRemoving(false);
                        },
                    })
                }
            />
        </>
    );
}
