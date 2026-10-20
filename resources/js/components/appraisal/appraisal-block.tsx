import { AnswerInput, showComputed } from '@/components/appraisal/answer-input';
import { AppraisalPhotos } from '@/components/appraisal/appraisal-photos';
import { Field } from '@/components/ui/field';
import { Textarea } from '@/components/ui/textarea';
import { Stat } from '@/pages/credit-analysis/parts';
import type { Answers, Block, Column, Computed, Item, Photo, Row } from '@/pages/collaterals/appraisals/types';

export type BlockProps = {
    section: string;
    block: Block;
    answers: Answers;
    onAnswer: (key: string, patch: Partial<{ value: string; note: string }>) => void;
    computed: Computed;
    errors: Record<string, string>;
    /** The appraisal can no longer be changed. */
    readOnly: boolean;
    /** Items that come from the loan file and are not typed. */
    locked: string[];
    photos: Record<string, Photo[]>;
    maxPhotos: number;
    onUpload: (item: string, files: FileList | null) => void;
    onDeletePhoto: (id: number) => void;
    uploading: string | null;
};

/** The longest note the server accepts. */
const NOTE_MAX = 500;

const answerOf = (answers: Answers, key: string) => answers[key] ?? { value: '', note: '' };

/** One block of a section, laid out as the form lays it out: fields, a list of checks or ratings, a grid, or places for photos. */
export function AppraisalBlock(props: BlockProps) {
    const { block, section } = props;

    switch (block.layout) {
        case 'checks':
        case 'ratings':
            return <Checks {...props} items={block.items ?? []} />;
        case 'grid':
            return <Grid {...props} rows={block.rows ?? []} columns={block.columns ?? []} section={section} />;
        case 'photos':
            return (
                <AppraisalPhotos
                    items={block.items ?? []}
                    photos={props.photos}
                    maxPhotos={props.maxPhotos}
                    readOnly={props.readOnly}
                    uploading={props.uploading}
                    onUpload={props.onUpload}
                    onDelete={props.onDeletePhoto}
                />
            );
        default:
            return <Fields {...props} items={block.items ?? []} />;
    }
}

function Fields({ items, answers, onAnswer, computed, errors, readOnly, locked, block }: BlockProps & { items: Item[] }) {
    return (
        <div className={`grid gap-x-3 gap-y-4 sm:grid-cols-2 ${block.cols === 4 ? 'lg:grid-cols-4' : ''}`}>
            {items.map((item) => {
                if (item.type === 'computed') {
                    return <Stat key={item.key} label={item.label} value={showComputed(computed[item.key], item.format)} strong />;
                }

                const wide = item.type === 'textarea' && !item.half;

                return (
                    <Field
                        key={item.key}
                        label={item.unit ? `${item.label} (${item.unit})` : item.label}
                        hint={item.hint}
                        labelClassName="capitalize"
                        error={errors[`values.${item.key}`]}
                        className={wide ? 'sm:col-span-2' : undefined}
                    >
                        <AnswerInput
                            item={item}
                            value={answerOf(answers, item.key).value}
                            onChange={(value) => onAnswer(item.key, { value })}
                            disabled={readOnly || locked.includes(item.key)}
                        />
                    </Field>
                );
            })}
        </div>
    );
}

/** A list of statements each answered on a fixed scale; the note for the evidence or the mitigation has the whole line under its statement. */
function Checks({ items, answers, onAnswer, errors, readOnly, block }: BlockProps & { items: Item[] }) {
    const note = block.layout === 'ratings' ? 'Mitigasi/syarat' : 'Bukti/keterangan';

    return (
        <div className="flex flex-col divide-y divide-line">
            {items.map((item) => (
                <div key={item.key} className="flex flex-col gap-2 py-3 first:pt-0 last:pb-0">
                    <div className="flex flex-wrap items-center justify-between gap-x-4 gap-y-1.5">
                        <p className="min-w-0 flex-1 basis-64 text-sm font-medium">{item.label}</p>
                        <AnswerInput
                            item={item}
                            label={item.label}
                            value={answerOf(answers, item.key).value}
                            onChange={(value) => onAnswer(item.key, { value })}
                            disabled={readOnly}
                            invalid={Boolean(errors[`values.${item.key}`])}
                        />
                    </div>
                    <Textarea
                        aria-label={`${note}: ${item.label}`}
                        placeholder={`${note} (isi bila perlu)`}
                        rows={1}
                        maxLength={NOTE_MAX}
                        disabled={readOnly}
                        className="field-sizing-content min-h-8 resize-none"
                        value={answerOf(answers, item.key).note}
                        onChange={(e) => onAnswer(item.key, { note: e.target.value })}
                    />
                    {errors[`values.${item.key}`] && <p className="text-xs text-danger">{errors[`values.${item.key}`]}</p>}
                </div>
            ))}
        </div>
    );
}

/** Rows by columns, as the comparables, the adjustments, the buildings, and the reconciliation are laid out in the form. */
function Grid({ section, rows, columns, answers, onAnswer, computed, errors, readOnly }: BlockProps & { rows: Row[]; columns: Column[]; section: string }) {
    return (
        <div className="relative overflow-x-auto">
            <div className="grid min-w-max gap-x-2 gap-y-1" style={{ gridTemplateColumns: `minmax(12rem,1.4fr) repeat(${columns.length}, minmax(9rem,1fr))` }}>
                <span />
                {columns.map((column) => (
                    <span key={column.key} className="pb-1 text-2xs font-medium tracking-wide text-muted uppercase">
                        {column.label}
                    </span>
                ))}
                {rows.map((row) => (
                    <GridRow
                        key={row.key}
                        section={section}
                        row={row}
                        columns={columns}
                        answers={answers}
                        onAnswer={onAnswer}
                        computed={computed}
                        errors={errors}
                        readOnly={readOnly}
                    />
                ))}
            </div>
        </div>
    );
}

function GridRow({
    section,
    row,
    columns,
    answers,
    onAnswer,
    computed,
    errors,
    readOnly,
}: Pick<BlockProps, 'answers' | 'onAnswer' | 'computed' | 'errors' | 'readOnly' | 'section'> & { row: Row; columns: Column[] }) {
    return (
        <>
            <span className="self-center text-sm">{row.label}</span>
            {columns.map((column) => {
                const key = `${section}.${row.key}.${column.key}`;

                if (row.computed || column.computed) {
                    return (
                        <span key={column.key} className="self-center text-right text-sm font-medium tabular-nums">
                            {showComputed(computed[key], row.format ?? column.format)}
                        </span>
                    );
                }

                const item = { type: row.type ?? column.type ?? 'text', options: row.options ?? null };

                return (
                    <div key={column.key} className="flex flex-col gap-0.5">
                        <AnswerInput
                            item={item}
                            compact
                            label={`${row.label}: ${column.label}`}
                            value={answerOf(answers, key).value}
                            onChange={(value) => onAnswer(key, { value })}
                            disabled={readOnly}
                            invalid={Boolean(errors[`values.${key}`])}
                        />
                        {errors[`values.${key}`] && <p className="text-xs text-danger">{errors[`values.${key}`]}</p>}
                    </div>
                );
            })}
        </>
    );
}
