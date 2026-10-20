import { controlClass } from '@/components/ui/input';
import { cn } from '@/lib/utils';

/**
 * Multi-line text. With `maxLength` and a controlled `value`, the `Field` that holds it shows the character counter in its label row, at the
 * right, so nothing takes space under the control. A description of the field is the `hint` of its `Field` (a tooltip on the label).
 */
export function Textarea({ className, ...props }: React.ComponentProps<'textarea'>) {
    return <textarea className={cn(controlClass, 'h-auto min-h-20 resize-y py-1.5', className)} {...props} />;
}
