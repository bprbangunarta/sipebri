import { Camera, Loader2, Trash2 } from 'lucide-react';
import { useRef } from 'react';
import { Button } from '@/components/ui/button';
import { Tip } from '@/components/ui/tooltip';
import type { Item, Photo } from '@/pages/collaterals/appraisals/types';

type Props = {
    items: Item[];
    photos: Record<string, Photo[]>;
    maxPhotos: number;
    readOnly: boolean;
    uploading: string | null;
    onUpload: (item: string, files: FileList | null) => void;
    onDelete: (id: number) => void;
};

/** The places for photos of the form, each with its pictures, up to a limit, added and removed while the appraisal is a draft. */
export function AppraisalPhotos({ items, photos, maxPhotos, readOnly, uploading, onUpload, onDelete }: Props) {
    return (
        <div className="flex flex-col divide-y divide-line">
            {items.map((item) => (
                <Place
                    key={item.key}
                    item={item}
                    photos={photos[item.key] ?? []}
                    maxPhotos={maxPhotos}
                    readOnly={readOnly}
                    uploading={uploading === item.key}
                    onUpload={onUpload}
                    onDelete={onDelete}
                />
            ))}
        </div>
    );
}

function Place({
    item,
    photos,
    maxPhotos,
    readOnly,
    uploading,
    onUpload,
    onDelete,
}: Omit<Props, 'items' | 'photos' | 'uploading'> & { item: Item; photos: Photo[]; uploading: boolean }) {
    const input = useRef<HTMLInputElement>(null);

    return (
        <div className="flex flex-col gap-2 py-3">
            <div className="flex flex-wrap items-center justify-between gap-2">
                <p className="text-sm font-medium">{item.label}</p>
                <span className="text-xs text-muted">
                    {photos.length}/{maxPhotos} foto
                </span>
            </div>
            {photos.length > 0 && (
                <div className="grid grid-cols-3 gap-2 sm:grid-cols-5">
                    {photos.map((photo) => (
                        <figure key={photo.id} className="overflow-hidden rounded-md border border-line">
                            <a href={photo.url} target="_blank" rel="noreferrer">
                                <img src={photo.url} alt={item.label} className="aspect-square w-full object-cover" />
                            </a>
                            {!readOnly && (
                                <figcaption className="flex justify-end p-1">
                                    <Tip label="Hapus foto">
                                        <Button
                                            size="icon"
                                            variant="ghost"
                                            aria-label={`Hapus foto ${item.label}`}
                                            className="text-danger"
                                            onClick={() => onDelete(photo.id)}
                                        >
                                            <Trash2 />
                                        </Button>
                                    </Tip>
                                </figcaption>
                            )}
                        </figure>
                    ))}
                </div>
            )}
            {!readOnly && (
                <div>
                    {/* Not FileInput: each photo is uploaded the moment it is chosen, so there is no list of chosen files to hold or remove. */}
                    <input
                        ref={input}
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        multiple
                        className="sr-only"
                        tabIndex={-1}
                        aria-hidden="true"
                        onChange={(e) => {
                            onUpload(item.key, e.target.files);
                            e.target.value = '';
                        }}
                    />
                    <Button size="sm" variant="outline" disabled={photos.length >= maxPhotos || uploading} onClick={() => input.current?.click()}>
                        {uploading ? <Loader2 className="animate-spin" /> : <Camera />} Tambah foto
                    </Button>
                </div>
            )}
        </div>
    );
}
