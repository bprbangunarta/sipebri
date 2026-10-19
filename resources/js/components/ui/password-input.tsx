import { Eye, EyeOff } from 'lucide-react';
import { useState } from 'react';
import type { ComponentProps } from 'react';
import { Input } from '@/components/ui/input';
import { Tip } from '@/components/ui/tooltip';
import { cn } from '@/lib/utils';

/** Password field with an eye button that reveals / hides what was typed. */
export function PasswordInput({ className, ...props }: Omit<ComponentProps<typeof Input>, 'type'>) {
    const [visible, setVisible] = useState(false);

    return (
        <div className="relative">
            <Input {...props} type={visible ? 'text' : 'password'} className={cn('pr-8', className)} />
            <Tip label={visible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'}>
                <button
                    type="button"
                    onClick={() => setVisible((current) => !current)}
                    aria-label={visible ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'}
                    aria-pressed={visible}
                    className="absolute top-1/2 right-1.5 flex size-6 -translate-y-1/2 cursor-pointer items-center justify-center rounded text-muted hover:text-ink focus-visible:ring-2 focus-visible:ring-primary focus-visible:outline-none"
                >
                    {visible ? <EyeOff className="size-3.5" /> : <Eye className="size-3.5" />}
                </button>
            </Tip>
        </div>
    );
}
