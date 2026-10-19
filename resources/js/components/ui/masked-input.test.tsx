import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { useState } from 'react';
import { describe, expect, it } from 'vitest';
import { Field } from '@/components/ui/field';
import { MaskedInput } from '@/components/ui/masked-input';
import { MASKS } from '@/lib/mask';

function Harness() {
    const [raw, setRaw] = useState('');

    return (
        <>
            <Field label="Telepon">
                <MaskedInput mask={MASKS.phone} value={raw} onValueChange={setRaw} />
            </Field>
            <output data-testid="raw">{raw}</output>
        </>
    );
}

describe('MaskedInput', () => {
    it('masks while typing and reports the unmasked value', async () => {
        render(<Harness />);
        await userEvent.type(screen.getByLabelText('Telepon'), '0812x3456 7890');

        expect(screen.getByLabelText('Telepon')).toHaveValue('0812-3456-7890');
        expect(screen.getByTestId('raw')).toHaveTextContent('081234567890');
    });

    it('deletes the digit before a literal when backspace lands right after it', async () => {
        render(<Harness />);
        const input = screen.getByLabelText('Telepon');
        await userEvent.type(input, '08123');
        expect(input).toHaveValue('0812-3');

        await userEvent.keyboard('{ArrowLeft}{Backspace}');
        expect(input).toHaveValue('0813');
    });

    it('removes trailing digits one by one', async () => {
        render(<Harness />);
        const input = screen.getByLabelText('Telepon');
        await userEvent.type(input, '08123');
        await userEvent.keyboard('{Backspace}{Backspace}');

        expect(input).toHaveValue('081');
    });
});

describe('MaskedInput with odd input', () => {
    async function pasted(text: string) {
        const user = userEvent.setup();
        render(<Harness />);
        await user.click(screen.getByLabelText('Telepon'));
        await user.paste(text);

        return { shown: screen.getByLabelText('Telepon'), raw: screen.getByTestId('raw') };
    }

    it.each([
        ['digits with spaces', '0812 3456 7890', '0812-3456-7890'],
        ['digits with dashes already in place', '0812-3456-7890', '0812-3456-7890'],
        ['letters and an emoji between the digits', '08🙂12abc3456', '0812-3456'],
        ['more digits than the mask holds', '0812345678901234567890', '0812-3456-7890'],
        ['only text', 'abc', ''],
        ['full-width digits and Arabic-Indic digits', '٠٨١٢０８１２', ''],
    ])('keeps the right characters of %s', async (_name, text, shown) => {
        const { shown: field } = await pasted(text);

        expect(field).toHaveValue(shown);
    });

    it('reports the unmasked digits, not the text on screen', async () => {
        const { raw } = await pasted('0812 3456 7890');

        expect(raw.textContent).toBe('081234567890');
    });

    it('keeps the caret where the person is typing when a digit goes in the middle', async () => {
        const user = userEvent.setup();
        render(<Harness />);
        const input = screen.getByLabelText('Telepon') as HTMLInputElement;

        await user.type(input, '08123456');
        input.setSelectionRange(2, 2);
        await user.keyboard('9');

        expect(input).toHaveValue('0891-2345-6');
        expect(input.selectionStart).toBe(3);
    });

    it('replaces a selected part with what is typed over it', async () => {
        const user = userEvent.setup();
        render(<Harness />);
        const input = screen.getByLabelText('Telepon') as HTMLInputElement;

        await user.type(input, '081234567890');
        input.setSelectionRange(0, 4);
        await user.keyboard('0899');

        expect(input).toHaveValue('0899-3456-7890');
    });

    it('shows the data of a value that is set from outside, cut to what the mask holds', () => {
        render(<MaskedInput aria-label="NIK" mask={MASKS.nik} value="31710123456789012345" onValueChange={() => undefined} />);

        expect(screen.getByLabelText('NIK')).toHaveValue('3171 0123 4567 8901');
    });
});
