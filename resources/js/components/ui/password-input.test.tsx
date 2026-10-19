import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it } from 'vitest';
import { Field } from '@/components/ui/field';
import { PasswordInput } from '@/components/ui/password-input';

describe('PasswordInput', () => {
    it('hides what is typed until the eye button is pressed, and says what the button will do', async () => {
        const user = userEvent.setup();
        render(
            <Field label="Kata sandi">
                <PasswordInput />
            </Field>,
        );
        const input = screen.getByLabelText('Kata sandi');
        await user.type(input, 'rahasia');

        expect(input).toHaveAttribute('type', 'password');

        await user.click(screen.getByRole('button', { name: 'Tampilkan kata sandi' }));
        expect(input).toHaveAttribute('type', 'text');
        expect(input).toHaveValue('rahasia');
        expect(screen.getByRole('button', { name: 'Sembunyikan kata sandi' })).toHaveAttribute('aria-pressed', 'true');
    });

    it('can be pressed with the keyboard, in the order of the page', async () => {
        const user = userEvent.setup();
        render(
            <Field label="Kata sandi">
                <PasswordInput />
            </Field>,
        );

        await user.tab();
        expect(screen.getByLabelText('Kata sandi')).toHaveFocus();
        await user.tab();
        await user.keyboard('{Enter}');

        expect(screen.getByLabelText('Kata sandi')).toHaveAttribute('type', 'text');
    });

    it('does not let the browser keep it as a text field once hidden again', async () => {
        const user = userEvent.setup();
        render(<PasswordInput aria-label="Kata sandi" autoComplete="current-password" />);

        await user.click(screen.getByRole('button', { name: 'Tampilkan kata sandi' }));
        await user.click(screen.getByRole('button', { name: 'Sembunyikan kata sandi' }));

        expect(screen.getByLabelText('Kata sandi')).toHaveAttribute('type', 'password');
        expect(screen.getByLabelText('Kata sandi')).toHaveAttribute('autocomplete', 'current-password');
    });
});
