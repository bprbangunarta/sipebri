import { Tooltip as T } from 'radix-ui';
import { createContext, useContext } from 'react';
import type { ComponentProps, ReactNode } from 'react';

const Provided = createContext(false);

/** The tooltip provider of the app shell. A `Tip` works without one (see below), so this is only for the shared delay. */
export function TooltipProvider({ delayDuration = 200, ...props }: ComponentProps<typeof T.Provider>) {
    return (
        <Provided.Provider value>
            <T.Provider delayDuration={delayDuration} {...props} />
        </Provided.Provider>
    );
}

/**
 * A short label for an icon button, shown on hover and keyboard focus. Radix throws when a tooltip has no provider, so a `Tip` that is not
 * inside one brings its own: screens outside the app shell (sign-in) can use it too.
 */
export function Tip({ label, children }: { label: string; children: ReactNode }) {
    const provided = useContext(Provided);
    const tip = (
        <T.Root>
            <T.Trigger asChild>{children}</T.Trigger>
            <T.Portal>
                <T.Content sideOffset={4} className="z-60 max-w-xs rounded bg-ink px-2 py-1 text-xs text-white">
                    {label}
                </T.Content>
            </T.Portal>
        </T.Root>
    );

    return provided ? tip : <TooltipProvider>{tip}</TooltipProvider>;
}
