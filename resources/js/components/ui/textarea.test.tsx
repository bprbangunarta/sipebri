import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { useState } from 'react';
import { describe, expect, it } from 'vitest';
import { Field } from '@/components/ui/field';
import { Textarea } from '@/components/ui/textarea';

function Harness({ maxLength }: { maxLength?: number }) {
    const [value, setValue] = useState('');

    return (
        <Field label="Catatan" error="Wajib diisi.">
            <Textarea value={value} maxLength={maxLength} onChange={(event) => setValue(event.target.value)} />
        </Field>
    );
}

describe('Textarea', () => {
    it('counts the characters against the limit, and does not take more than the limit', async () => {
        const user = userEvent.setup();
        render(<Harness maxLength={10} />);

        await user.type(screen.getByLabelText('Catatan'), 'abcdefghijkl');

        expect(screen.getByLabelText('Catatan')).toHaveValue('abcdefghij');
        expect(screen.getByText('10/10')).toBeInTheDocument();
    });

    it('keeps a line break and an emoji whole when it counts', async () => {
        const user = userEvent.setup();
        render(<Harness maxLength={100} />);

        await user.type(screen.getByLabelText('Catatan'), 'satu{Enter}dua');

        expect(screen.getByLabelText('Catatan')).toHaveValue('satu\ndua');
        expect(screen.getByText('8/100')).toBeInTheDocument();
    });

    it('shows no counter without a limit, and the Field label, error, and invalid state reach the text area', () => {
        render(<Harness />);
        const area = screen.getByLabelText('Catatan');

        expect(screen.queryByText(/\/\d+/)).not.toBeInTheDocument();
        expect(area.tagName).toBe('TEXTAREA');
        expect(area).toHaveAttribute('aria-invalid', 'true');
        expect(area).toHaveAccessibleDescription('Wajib diisi.');
    });
});
