import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { afterEach, describe, expect, it, vi } from 'vitest';
import { DatePicker } from '@/components/ui/date-picker';
import { Field } from '@/components/ui/field';

const range = { min: new Date(2026, 9, 1), max: new Date(2026, 9, 31) };

afterEach(() => {
    vi.useRealTimers();
});

describe('DatePicker', () => {
    it('gives back the chosen day as a plain ISO date and closes', async () => {
        const onChange = vi.fn();
        const user = userEvent.setup();
        render(<DatePicker aria-label="Tanggal" value="2026-10-10" onChange={onChange} {...range} />);

        await user.click(screen.getByRole('button', { name: 'Tanggal' }));
        await user.click(screen.getByRole('button', { name: /15 Oktober 2026/ }));

        expect(onChange).toHaveBeenCalledWith('2026-10-15');
        expect(screen.queryByRole('grid')).not.toBeInTheDocument();
    });

    it('does not let a day outside the range be chosen', async () => {
        const onChange = vi.fn();
        const user = userEvent.setup();
        render(<DatePicker aria-label="Tanggal" value="2026-10-10" onChange={onChange} {...range} />);

        await user.click(screen.getByRole('button', { name: 'Tanggal' }));
        const outside = screen.queryByRole('button', { name: /1 November 2026/ });

        if (outside) {
            await user.click(outside);
        }

        expect(onChange).not.toHaveBeenCalled();
    });

    it('shows the placeholder, not a crash, for a value it cannot read', async () => {
        const user = userEvent.setup();
        render(<DatePicker aria-label="Tanggal" value="bukan-tanggal" onChange={() => undefined} {...range} />);
        const trigger = screen.getByRole('button', { name: 'Tanggal' });

        expect(trigger).toHaveTextContent('Pilih tanggal');

        await user.click(trigger);

        expect(screen.getByRole('grid')).toBeInTheDocument();
    });

    it('shows the chosen date in Indonesian', () => {
        render(<DatePicker aria-label="Tanggal" value="2026-10-04" onChange={() => undefined} {...range} />);

        expect(screen.getByRole('button', { name: 'Tanggal' })).toHaveTextContent('04 Okt 2026');
    });

    it('opens on the current month when nothing is chosen, not on the end of the range', async () => {
        vi.useFakeTimers({ toFake: ['Date'], now: new Date(2026, 4, 20) });
        const user = userEvent.setup({ advanceTimers: vi.advanceTimersByTime });
        render(<DatePicker aria-label="Tanggal" value="" onChange={() => undefined} min={new Date(2020, 0, 1)} max={new Date(2030, 11, 31)} />);

        await user.click(screen.getByRole('button', { name: 'Tanggal' }));

        expect(screen.getByRole('grid', { name: /Mei 2026/ })).toBeInTheDocument();
    });

    it('is labelled by the Field, and the error describes it', () => {
        render(
            <Field label="Tanggal lahir" error="Wajib diisi.">
                <DatePicker value="" onChange={() => undefined} {...range} />
            </Field>,
        );
        const trigger = screen.getByRole('button', { name: /Tanggal lahir/ });

        expect(trigger).toHaveAttribute('aria-invalid', 'true');
        expect(trigger).toHaveAccessibleDescription('Wajib diisi.');
    });
});
