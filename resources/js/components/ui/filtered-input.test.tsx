import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { useState } from 'react';
import { describe, expect, it } from 'vitest';
import { ALPHANUMERIC_CHARACTER, CODE_CHARACTER, FilteredInput, NAME_CHARACTER } from '@/components/ui/filtered-input';

function Harness({ allowed }: { allowed: RegExp }) {
    const [value, setValue] = useState('');

    return <FilteredInput aria-label="isian" allowed={allowed} value={value} onValueChange={setValue} />;
}

describe('FilteredInput', () => {
    it('drops characters that are not allowed, typed or pasted', async () => {
        render(<Harness allowed={NAME_CHARACTER} />);
        const field = screen.getByLabelText('isian');

        await userEvent.type(field, "Ana-Maria O'Neil, S.E.123!@#");
        expect(field).toHaveValue("Ana-Maria O'Neil, S.E.");

        await userEvent.clear(field);
        await userEvent.click(field);
        await userEvent.paste('Budi99<script>');
        expect(field).toHaveValue('Budiscript');
    });

    it('keeps only letters and digits for codes', async () => {
        render(<Harness allowed={ALPHANUMERIC_CHARACTER} />);
        const field = screen.getByLabelText('isian');

        await userEvent.type(field, 'AB-12 cd_3');

        expect(field).toHaveValue('AB12cd3');
    });

    it('lets a code keep its hyphen but nothing else', async () => {
        render(<Harness allowed={CODE_CHARACTER} />);
        const field = screen.getByLabelText('isian');

        await userEvent.type(field, 'KC-01 /_#');

        expect(field).toHaveValue('KC-01');
    });
});

describe('FilteredInput with odd input', () => {
    async function pasted(text: string) {
        const user = userEvent.setup();
        render(<Harness allowed={NAME_CHARACTER} />);
        await user.click(screen.getByLabelText('isian'));
        await user.paste(text);

        return screen.getByLabelText('isian');
    }

    it('drops emoji, Arabic and Chinese letters, and zero-width joiners, and keeps what is left of the name', async () => {
        expect(await pasted('Budi 🙂 Santoso\u200D 王 محمد')).toHaveValue('Budi  Santoso  ');
    });

    it('keeps a letter written as a base plus a combining accent', async () => {
        expect(await pasted('Jose\u0301 Ren\u00e9e')).toHaveValue('Jose\u0301 Ren\u00e9e');
    });

    it('takes a very long text without choking, and still drops what is not allowed', async () => {
        const field = await pasted(`${'Ab '.repeat(3000)}9`);

        expect((field as HTMLInputElement).value).toHaveLength(9000);
        expect(field).not.toHaveValue(expect.stringContaining('9'));
    });
});

describe('NAME_CHARACTER', () => {
    it('accepts Latin letters (also accented), spaces, and the marks common in names', () => {
        for (const character of ['a', 'Z', 'ë', ' ', '.', ',', "'", '’', '-']) {
            expect(NAME_CHARACTER.test(character), character).toBe(true);
        }
    });

    it('refuses look-alike Cyrillic letters, new lines, tabs, wide spaces, and invisible characters', () => {
        for (const character of ['а', '\n', '\t', '　', '​', '9', '@']) {
            expect(NAME_CHARACTER.test(character), character).toBe(false);
        }
    });
});
