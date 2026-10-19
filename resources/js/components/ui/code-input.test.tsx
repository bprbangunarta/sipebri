import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { useState } from 'react';
import { describe, expect, it } from 'vitest';
import { CodeInput } from '@/components/ui/code-input';

function Harness({ recovery = false }: { recovery?: boolean }) {
    const [value, setValue] = useState('');

    return <CodeInput aria-label="Kode" recovery={recovery} value={value} onValueChange={setValue} />;
}

async function pasted(text: string, recovery = false): Promise<HTMLElement> {
    const user = userEvent.setup();
    render(<Harness recovery={recovery} />);
    await user.click(screen.getByLabelText('Kode'));
    await user.paste(text);

    return screen.getByLabelText('Kode');
}

describe('CodeInput', () => {
    it('keeps only digits, at most six, as they are typed', async () => {
        const user = userEvent.setup();
        render(<Harness />);

        await user.type(screen.getByLabelText('Kode'), '12a3-4 56789');

        expect(screen.getByLabelText('Kode')).toHaveValue('123456');
    });

    it.each([
        ['a code with a space', '123 456', '123456'],
        ['a code with a dash', '482-915', '482915'],
        ['a code inside a sentence', 'Kode: 482915', '482915'],
        ['a code with spaces around it', '  482915  ', '482915'],
        ['more digits than a code has', '4829151234', '482915'],
    ])('keeps %s that was pasted', async (_name, text, expected) => {
        expect(await pasted(text)).toHaveValue(expected);
    });

    it('asks for the numeric keyboard and lets the phone offer the code from SMS or email', () => {
        render(<Harness />);
        const input = screen.getByLabelText('Kode');

        expect(input).toHaveAttribute('inputmode', 'numeric');
        expect(input).toHaveAttribute('autocomplete', 'one-time-code');
    });

    it('takes a recovery code in lower case without the spaces around it', async () => {
        expect(await pasted('  ABCDE-FGHIJ  ', true)).toHaveValue('abcde-fghij');
    });

    it('does not take more than a recovery code holds', async () => {
        expect(await pasted('abcde-fghij-klmno', true)).toHaveValue('abcde-fghij');
    });
});
