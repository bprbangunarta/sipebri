import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { useState } from 'react';
import { describe, expect, it } from 'vitest';
import { CurrencyInput } from '@/components/ui/currency-input';
import { Field } from '@/components/ui/field';

function Harness({ initial = null, prefix }: { initial?: number | null; prefix?: string | null }) {
    const [value, setValue] = useState<number | null>(initial);

    return (
        <>
            <Field label="Nominal">
                <CurrencyInput value={value} onValueChange={setValue} prefix={prefix} />
            </Field>
            <output data-testid="value">{value === null ? 'null' : String(value)}</output>
        </>
    );
}

describe('CurrencyInput', () => {
    it('formats digits with thousands separators and keeps a plain number as the value', async () => {
        render(<Harness />);
        await userEvent.type(screen.getByLabelText('Nominal'), '1000000');

        expect(screen.getByLabelText('Nominal')).toHaveValue('1.000.000');
        expect(screen.getByTestId('value')).toHaveTextContent('1000000');
    });

    it('ignores letters and symbols', async () => {
        render(<Harness />);
        await userEvent.type(screen.getByLabelText('Nominal'), 'a1b2,c3-');

        expect(screen.getByLabelText('Nominal')).toHaveValue('123');
    });

    it('becomes null when cleared and strips leading zeros', async () => {
        render(<Harness initial={5} />);
        const input = screen.getByLabelText('Nominal');
        await userEvent.clear(input);
        expect(screen.getByTestId('value')).toHaveTextContent('null');

        await userEvent.type(input, '007');
        expect(input).toHaveValue('7');
    });

    it('shows the Rp prefix unless it is turned off', () => {
        const { unmount } = render(<Harness initial={1000} />);
        expect(screen.getByText('Rp')).toBeInTheDocument();
        unmount();

        render(<Harness initial={1000} prefix={null} />);
        expect(screen.queryByText('Rp')).not.toBeInTheDocument();
    });
});

describe('CurrencyInput pasted amounts', () => {
    async function paste(text: string, initial: number | null = null) {
        const user = userEvent.setup();
        render(<Harness initial={initial} />);
        await user.click(screen.getByLabelText('Nominal'));
        await user.keyboard('{Control>}a{/Control}');
        await user.paste(text);

        return { shown: screen.getByLabelText('Nominal'), value: screen.getByTestId('value') };
    }

    it.each([
        ['Indonesian decimals', '1.500.000,50', 1500000],
        ['English decimals', '1500000.00', 1500000],
        ['one decimal digit', '15.5', 15],
        ['a prefix and a space', 'Rp 1.500.000', 1500000],
        ['thousands only', '1.500', 1500],
        ['plain digits', '2500000', 2500000],
        ['spaces and an emoji', '🙂 12 34', 1234],
    ])('keeps the amount of %s', async (_name, text, expected) => {
        const { value } = await paste(text);

        expect(value.textContent).toBe(String(expected));
    });

    it('does not turn a pasted negative amount into a positive one', async () => {
        const { shown, value } = await paste('-5.000', 1000);

        expect(value.textContent).toBe('1000');
        expect(shown).toHaveValue('1.000');
    });

    it('stops at the most digits a number holds exactly', async () => {
        const { value } = await paste('99999999999999999999');

        expect(value.textContent).toBe('999999999999999');
    });

    it('shows a value that is replaced from outside', () => {
        const { rerender } = render(<CurrencyInput aria-label="Nominal" value={2500} onValueChange={() => undefined} />);
        expect(screen.getByLabelText('Nominal')).toHaveValue('2.500');

        rerender(<CurrencyInput aria-label="Nominal" value={null} onValueChange={() => undefined} />);
        expect(screen.getByLabelText('Nominal')).toHaveValue('');
    });
});
