import { useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { DialogFooter, Modal } from '@/components/ui/dialog';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';

/**
 * A dialog that asks for a reason and posts it to `action` (null = closed). Used wherever an action needs a recorded
 * justification (cancelling a schedule, voiding an application, a surveyor asking for a new schedule).
 */
export function ReasonDialog({
    title,
    description,
    action,
    confirmLabel,
    onClose,
    fieldLabel = 'Alasan',
    hint,
    tone = 'danger',
    extra,
}: {
    title: string;
    description: string;
    action: string | null;
    confirmLabel: string;
    onClose: () => void;
    fieldLabel?: string;
    hint?: string;
    tone?: 'danger' | 'primary';
    /** Extra fields sent along with the reason. */
    extra?: Record<string, string>;
}) {
    const form = useForm({ reason: '' });

    return (
        <Modal
            open={action !== null}
            onOpenChange={(open) => !open && onClose()}
            title={title}
            description={description}
        >
            <form
                onSubmit={(e) => {
                    e.preventDefault();
                    if (action) {
                        form.transform((data) => ({ ...data, ...extra }));
                        form.post(action, {
                            preserveScroll: true,
                            onSuccess: () => {
                                form.reset();
                                onClose();
                            },
                        });
                    }
                }}
                noValidate
            >
                <div className="p-4">
                    <Field
                        label={fieldLabel}
                        required
                        error={form.errors.reason}
                        hint={hint}
                    >
                        <Input
                            autoFocus
                            value={form.data.reason}
                            maxLength={255}
                            onChange={(e) =>
                                form.setData('reason', e.target.value)
                            }
                            aria-invalid={!!form.errors.reason}
                        />
                    </Field>
                </div>
                <DialogFooter>
                    <Button variant="outline" onClick={onClose}>
                        Kembali
                    </Button>
                    <Button
                        type="submit"
                        variant={tone === 'danger' ? 'danger' : 'primary'}
                        loading={form.processing}
                    >
                        {confirmLabel}
                    </Button>
                </DialogFooter>
            </form>
        </Modal>
    );
}
