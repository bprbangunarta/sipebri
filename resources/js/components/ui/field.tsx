import { Info } from 'lucide-react';
import { Tooltip as T } from 'radix-ui';
import { cloneElement, isValidElement, useId, useState } from 'react';
import type { ReactElement, ReactNode } from 'react';
import { Textarea } from '@/components/ui/textarea';
import { TooltipProvider } from '@/components/ui/tooltip';
import { cn } from '@/lib/utils';

/**
 * The info icon beside a label. A Radix tooltip opens on hover and keyboard focus only (it ignores a touch and closes on click), so on a phone
 * the hint could never be read. Here a click or a tap opens it too; a tap elsewhere or Escape closes it.
 */
function HintTip({ label, hint }: { label: string; hint: string }) {
    const [open, setOpen] = useState(false);

    return (
        <TooltipProvider>
            <T.Root open={open} onOpenChange={setOpen}>
                <T.Trigger asChild>
                    <button
                        type="button"
                        aria-label={`Lihat keterangan ${label}`}
                        onClick={(event) => {
                            event.preventDefault();
                            setOpen(true);
                        }}
                        className="flex size-4 cursor-help items-center justify-center rounded-full text-muted hover:text-ink focus-visible:ring-2 focus-visible:ring-primary focus-visible:outline-none [&_svg]:size-3.5"
                    >
                        <Info />
                    </button>
                </T.Trigger>
                <T.Portal>
                    <T.Content sideOffset={4} className="z-60 max-w-xs rounded bg-ink px-2 py-1 text-xs text-white">
                        {hint}
                    </T.Content>
                </T.Portal>
            </T.Root>
        </TooltipProvider>
    );
}

/**
 * A label, its control, and the error. A description for the field is the `hint`: it is shown as a tooltip on an info icon beside the
 * label, never as text under the control (only the error shows there). Screen readers still get the hint as the control's description.
 */
export function Field({
    label,
    error,
    required,
    hint,
    className,
    children,
}: {
    label: string;
    error?: string;
    required?: boolean;
    hint?: string;
    className?: string;
    children: ReactNode;
}) {
    const id = useId();
    const errorId = error ? `${id}-error` : undefined;
    const hintId = hint ? `${id}-hint` : undefined;
    const note = [errorId, hintId].filter(Boolean).join(' ') || undefined;
    // Connect the label, hint, and error to the control: the label's `for`, plus aria-describedby and aria-invalid.
    const control = isValidElement<{ id?: string; 'aria-describedby'?: string; 'aria-invalid'?: boolean }>(children)
        ? cloneElement(children as ReactElement<{ id?: string; 'aria-describedby'?: string; 'aria-invalid'?: boolean }>, {
              id: children.props.id ?? id,
              'aria-describedby': children.props['aria-describedby'] ?? note,
              'aria-invalid': children.props['aria-invalid'] ?? (error ? true : undefined),
          })
        : children;
    // A text area with a limit shows how much of it is used, beside the label.
    const counted = isValidElement<{ maxLength?: number; value?: unknown }>(children) && children.type === Textarea ? children.props : null;
    const target = isValidElement<{ id?: string }>(children) ? (children.props.id ?? id) : undefined;

    return (
        <div className={cn('flex min-w-0 flex-col gap-1', className)}>
            <div className="flex items-center gap-1">
                <label htmlFor={target} className="text-xs font-medium text-ink">
                    {label}
                    {required && <span className="ml-0.5 text-danger">*</span>}
                </label>
                {hint && <HintTip label={label} hint={hint} />}
                {counted && counted.maxLength !== undefined && typeof counted.value === 'string' && (
                    <span className="ml-auto text-xs text-muted tabular-nums" aria-hidden="true">
                        {counted.value.length}/{counted.maxLength}
                    </span>
                )}
            </div>
            {control}
            {hint && (
                <p id={hintId} className="sr-only">
                    {hint}
                </p>
            )}
            {error && (
                <p id={errorId} role="alert" className="text-xs text-danger">
                    {error}
                </p>
            )}
        </div>
    );
}
