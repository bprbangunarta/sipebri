import type { ComponentProps } from 'react';
import { Input } from '@/components/ui/input';
import { cn } from '@/lib/utils';

const CODE_LENGTH = 6;
const RECOVERY_LENGTH = 11;

/**
 * One-time code field. The constraints travel with the validation: digits only, 6 characters, numeric
 * keyboard and browser/OS autofill of SMS/email codes. With `recovery` it accepts a recovery code instead.
 *
 * There is no native `maxLength`: the browser would cut a pasted "Kode: 482915" or "482 915" at the sixth character before this field
 * could clean it. The text is cleaned first and cut after.
 */
export function CodeInput({
    recovery = false,
    className,
    onValueChange,
    ...props
}: Omit<ComponentProps<typeof Input>, 'onChange' | 'type'> & { recovery?: boolean; onValueChange: (value: string) => void }) {
    return (
        <Input
            {...props}
            type="text"
            inputMode={recovery ? 'text' : 'numeric'}
            autoComplete={recovery ? 'off' : 'one-time-code'}
            pattern={recovery ? undefined : '[0-9]*'}
            spellCheck={false}
            className={cn('text-center font-mono tracking-[0.3em]', recovery && 'tracking-widest', className)}
            onChange={(e) =>
                onValueChange(
                    recovery
                        ? e.target.value.toLowerCase().replace(/\s/g, '').slice(0, RECOVERY_LENGTH)
                        : e.target.value.replace(/\D/g, '').slice(0, CODE_LENGTH),
                )
            }
        />
    );
}
