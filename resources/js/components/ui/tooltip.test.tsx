import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it } from 'vitest';
import { Tip, TooltipProvider } from '@/components/ui/tooltip';

describe('Tip', () => {
    it('works on its own, so a screen outside the app shell (sign-in) can use it', () => {
        expect(() =>
            render(
                <Tip label="Ubah">
                    <button type="button">x</button>
                </Tip>,
            ),
        ).not.toThrow();
    });

    it('shows its label when the keyboard reaches the button', async () => {
        const user = userEvent.setup();
        render(
            <Tip label="Ubah data">
                <button type="button">x</button>
            </Tip>,
        );

        await user.tab();

        expect(await screen.findByRole('tooltip')).toHaveTextContent('Ubah data');
    });

    it('also works inside the provider of the app shell', async () => {
        const user = userEvent.setup();
        render(
            <TooltipProvider delayDuration={0}>
                <Tip label="Hapus">
                    <button type="button">x</button>
                </Tip>
            </TooltipProvider>,
        );

        await user.hover(screen.getByRole('button', { name: 'x' }));

        expect(await screen.findByRole('tooltip')).toHaveTextContent('Hapus');
    });
});
