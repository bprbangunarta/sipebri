import { usePage, useForm } from '@inertiajs/react';
import { Download } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { DialogBody, DialogFooter, Modal } from '@/components/ui/dialog';
import { Field } from '@/components/ui/field';
import { FileInput } from '@/components/ui/file-input';

type Props = {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    label: string;
    url: string;
    templateUrl: string;
};

const MAX_BYTES = 2 * 1024 * 1024;

/** Upload dialog of a reference table that accepts an Excel import: template link, one .xlsx, and the rows the server refused. */
export function ReferenceImportDialog({ open, onOpenChange, label, url, templateUrl }: Props) {
    const form = useForm<{ file: File | null }>({ file: null });
    const [refused, setRefused] = useState<string>();
    const issues = (usePage().props.flash as { issues?: string[] | null }).issues ?? [];

    const close = (next: boolean) => {
        if (!next) {
            form.reset();
            form.clearErrors();
            setRefused(undefined);
        }

        onOpenChange(next);
    };

    const submit = (event: React.FormEvent) => {
        event.preventDefault();
        form.post(url, { forceFormData: true, preserveScroll: true, onSuccess: () => close(false) });
    };

    return (
        <Modal open={open} onOpenChange={close} persistent title={`Import ${label}`} description="Unggah berkas Excel (.xlsx) yang mengikuti template.">
            <form onSubmit={submit} noValidate>
                <DialogBody className="flex flex-col gap-3">
                    <div>
                        <Button variant="outline" size="sm" onClick={() => window.location.assign(templateUrl)}>
                            <Download /> Unduh template
                        </Button>
                    </div>
                    <Field
                        label="Berkas"
                        required
                        error={form.errors.file ?? refused}
                        hint="Hanya .xlsx, paling besar 2 MB dan 1.000 baris data. Satu baris salah berarti tidak ada yang disimpan."
                    >
                        <FileInput
                            value={form.data.file ? [form.data.file] : []}
                            onValueChange={(files) => {
                                setRefused(undefined);
                                form.clearErrors();
                                form.setData('file', files[0] ?? null);
                            }}
                            accept=".xlsx"
                            maxBytes={MAX_BYTES}
                            onReject={setRefused}
                            aria-invalid={!!(form.errors.file ?? refused)}
                        />
                    </Field>
                    {form.errors.file && issues.length > 0 && (
                        <ul
                            aria-label="Baris yang ditolak"
                            className="bg-surface-muted max-h-48 list-disc overflow-y-auto rounded-md border border-line py-2 pr-3 pl-7 text-xs text-danger"
                        >
                            {issues.map((issue) => (
                                <li key={issue}>{issue}</li>
                            ))}
                        </ul>
                    )}
                </DialogBody>
                <DialogFooter>
                    <Button variant="outline" onClick={() => close(false)}>
                        Batal
                    </Button>
                    <Button type="submit" loading={form.processing} disabled={form.data.file === null}>
                        Impor
                    </Button>
                </DialogFooter>
            </form>
        </Modal>
    );
}
