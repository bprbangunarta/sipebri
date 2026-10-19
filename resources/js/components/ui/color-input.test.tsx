import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { useState } from 'react';
import { describe, expect, it } from 'vitest';
import { ColorInput } from '@/components/ui/color-input';
import { Field } from '@/components/ui/field';

function Harness({ initial = '', swatches }: { initial?: string; swatches?: readonly string[] | false }) {
    const [value, setValue] = useState(initial);

    return (
        <>
            <Field label="Warna utama">
                <ColorInput value={value} onValueChange={setValue} swatches={swatches} />
            </Field>
            <output data-testid="value">{value}</output>
        </>
    );
}

describe('ColorInput', () => {
    it('writes a hex colour in lower case with its #, and drops what is not a hex digit', async () => {
        const user = userEvent.setup();
        render(<Harness />);

        await user.type(screen.getByLabelText('Warna utama'), '33G47F9fAB');

        expect(screen.getByTestId('value')).toHaveTextContent('#3347f9');
        expect(screen.getByLabelText('Warna utama')).toHaveValue('#3347f9');
    });

    it('keeps a partial colour as typed and is empty when cleared', async () => {
        const user = userEvent.setup();
        render(<Harness initial="#33479f" />);
        const field = screen.getByLabelText('Warna utama');

        await user.clear(field);
        expect(screen.getByTestId('value')).toHaveTextContent('');

        await user.type(field, '0f7');
        expect(screen.getByTestId('value')).toHaveTextContent('#0f7');
    });

    it('chooses a ready-made colour and marks the one that is chosen', async () => {
        const user = userEvent.setup();
        render(<Harness swatches={['#0f766e', '#b91c1c']} />);

        await user.click(screen.getByRole('button', { name: 'Warna #b91c1c' }));

        expect(screen.getByTestId('value')).toHaveTextContent('#b91c1c');
        expect(screen.getByRole('button', { name: 'Warna #b91c1c' })).toHaveAttribute('aria-pressed', 'true');
        expect(screen.getByRole('button', { name: 'Warna #0f766e' })).toHaveAttribute('aria-pressed', 'false');
    });

    it('can hide the ready-made colours, and names its native picker', () => {
        render(<Harness swatches={false} />);

        expect(screen.queryByRole('group', { name: 'Warna siap pakai' })).not.toBeInTheDocument();
        expect(screen.getByLabelText('Pilih warna')).toHaveAttribute('type', 'color');
    });
});
