import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import { Checkbox, RadioGroup, Switch } from '@/components/ui/choice';

describe('Checkbox', () => {
    it('toggles from its label and from the keyboard, and says which state it is in', async () => {
        const onCheckedChange = vi.fn();
        const user = userEvent.setup();
        render(<Checkbox label="Ingat saya" description="Hanya di perangkat ini" onCheckedChange={onCheckedChange} />);
        const box = screen.getByRole('checkbox', { name: /Ingat saya/ });

        expect(box).toHaveAccessibleDescription('');
        expect(screen.getByText('Hanya di perangkat ini')).toBeInTheDocument();
        expect(box).toHaveAttribute('aria-checked', 'false');

        await user.click(screen.getByText('Ingat saya'));
        expect(onCheckedChange).toHaveBeenLastCalledWith(true);

        box.focus();
        await user.keyboard(' ');
        expect(onCheckedChange).toHaveBeenLastCalledWith(false);
    });

    it('does nothing while disabled', async () => {
        const onCheckedChange = vi.fn();
        const user = userEvent.setup();
        render(<Checkbox disabled label="Ingat saya" onCheckedChange={onCheckedChange} />);

        await user.click(screen.getByText('Ingat saya'));

        expect(onCheckedChange).not.toHaveBeenCalled();
        expect(screen.getByRole('checkbox')).toBeDisabled();
    });

    it('submits its value in a form only while it is checked', async () => {
        const user = userEvent.setup();
        let sent: FormData | null = null;
        render(
            <form
                onSubmit={(event) => {
                    event.preventDefault();
                    sent = new FormData(event.currentTarget);
                }}
            >
                <Checkbox name="remember" value="1" label="Ingat saya" />
                <button type="submit">Kirim</button>
            </form>,
        );

        await user.click(screen.getByRole('button', { name: 'Kirim' }));
        expect((sent as FormData | null)?.get('remember')).toBeNull();

        await user.click(screen.getByRole('checkbox'));
        await user.click(screen.getByRole('button', { name: 'Kirim' }));
        expect((sent as FormData | null)?.get('remember')).toBe('1');
    });
});

describe('Switch', () => {
    it('is a switch that flips on click and on Space, named by its label', async () => {
        const onCheckedChange = vi.fn();
        const user = userEvent.setup();
        render(<Switch label="Tampilkan semua jalur" onCheckedChange={onCheckedChange} />);
        const toggle = screen.getByRole('switch', { name: 'Tampilkan semua jalur' });

        await user.click(toggle);
        expect(onCheckedChange).toHaveBeenLastCalledWith(true);
        expect(toggle).toHaveAttribute('aria-checked', 'true');

        toggle.focus();
        await user.keyboard(' ');
        expect(onCheckedChange).toHaveBeenLastCalledWith(false);
    });
});

describe('RadioGroup', () => {
    const options = [
        { value: 'a', label: 'Bulanan' },
        { value: 'b', label: 'Triwulan', description: 'Setiap tiga bulan' },
        { value: 'c', label: 'Tahunan', disabled: true },
    ];

    it('is one group of radios, chosen with the arrow keys, skipping a disabled option', async () => {
        const onValueChange = vi.fn();
        const user = userEvent.setup();
        render(<RadioGroup aria-label="Sistem angsuran" defaultValue="a" options={options} onValueChange={onValueChange} />);

        expect(screen.getByRole('radiogroup', { name: 'Sistem angsuran' })).toBeInTheDocument();
        expect(screen.getAllByRole('radio')).toHaveLength(3);

        await user.tab();
        expect(screen.getByRole('radio', { name: 'Bulanan' })).toHaveFocus();
        // Radix moves focus a moment after the key goes down and chooses the option that got focus while the key is still held.
        await user.keyboard('{ArrowDown>}');
        await waitFor(() => expect(onValueChange).toHaveBeenLastCalledWith('b'));
        await user.keyboard('{/ArrowDown}');
        expect(screen.getByRole('radio', { name: /Triwulan/ })).toBeChecked();

        await user.keyboard('{ArrowDown>}');
        await waitFor(() => expect(onValueChange).toHaveBeenLastCalledWith('a'));
        await user.keyboard('{/ArrowDown}');
        expect(screen.getByRole('radio', { name: 'Tahunan' })).toBeDisabled();
    });

    it('chooses from the label text too', async () => {
        const onValueChange = vi.fn();
        const user = userEvent.setup();
        render(<RadioGroup aria-label="Sistem angsuran" options={options} onValueChange={onValueChange} />);

        await user.click(screen.getByText('Triwulan'));

        expect(onValueChange).toHaveBeenCalledWith('b');
    });
});
