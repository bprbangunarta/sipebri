import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { useState } from 'react';
import { describe, expect, it, vi } from 'vitest';
import { AnswerInput, optionsOf, showComputed } from '@/components/appraisal/answer-input';

/** An input that keeps what it is given, as the form does. */
function Held({ type, initial = '', onValue }: { type: string; initial?: string; onValue?: (value: string) => void }) {
    const [value, setValue] = useState(initial);

    return (
        <AnswerInput
            item={{ type }}
            label="Isian"
            value={value}
            onChange={(next) => {
                setValue(next);
                onValue?.(next);
            }}
        />
    );
}

describe('AnswerInput', () => {
    it('takes only digits for a whole number', async () => {
        const onValue = vi.fn();
        render(<Held type="integer" onValue={onValue} />);

        await userEvent.type(screen.getByLabelText('Isian'), '12ab3');

        expect(onValue).toHaveBeenLastCalledWith('123');
    });

    it('keeps a percentage to digits and a dot, and an adjustment may also be negative', async () => {
        const percent = render(<Held type="percent" />);
        await userEvent.type(screen.getByLabelText('Isian'), '-12.5x');
        expect(screen.getByLabelText('Isian')).toHaveValue('12.5');
        percent.unmount();

        render(<Held type="adjustment" />);
        await userEvent.type(screen.getByLabelText('Isian'), '-3.5');
        expect(screen.getByLabelText('Isian')).toHaveValue('-3.5');
    });

    it('sends a money amount as digits without the separators it shows', async () => {
        const onValue = vi.fn();
        render(<Held type="money" onValue={onValue} />);

        await userEvent.type(screen.getByLabelText('Isian'), '1500000');

        expect(onValue).toHaveBeenLastCalledWith('1500000');
        expect(screen.getByLabelText('Isian')).toHaveValue('1.500.000');
    });

    it('offers the fixed answers of a check and sends the one picked', async () => {
        const onValue = vi.fn();
        render(<Held type="yesno" onValue={onValue} />);

        await userEvent.click(screen.getByRole('radio', { name: 'Tidak' }));

        expect(onValue).toHaveBeenLastCalledWith('no');
        expect(screen.getByRole('radio', { name: 'Ya' })).toBeInTheDocument();
    });

    it('cannot be changed while disabled', async () => {
        render(<AnswerInput item={{ type: 'text' }} label="Isian" value="a" onChange={vi.fn()} disabled />);

        expect(screen.getByLabelText('Isian')).toBeDisabled();
    });
});

describe('optionsOf', () => {
    it('lists the scale of a rating and of a risk in order, and the options of a choice as given', () => {
        expect(optionsOf({ type: 'rating' }).map((o) => o.value)).toEqual(['good', 'fair', 'poor']);
        expect(optionsOf({ type: 'risk' }).map((o) => o.label)).toEqual(['Rendah', 'Sedang', 'Tinggi']);
        expect(optionsOf({ type: 'choice', options: { b: 'B', a: 'A' } }).map((o) => o.value)).toEqual(['b', 'a']);
    });
});

describe('showComputed', () => {
    it('writes a figure the system worked out, and a dash when there is none', () => {
        expect(showComputed(1500000, 'money')).toContain('1.500.000');
        expect(showComputed(65.79, 'percent')).toBe('65,79%');
        expect(showComputed('M', 'text')).toBe('M');
        expect(showComputed(null, 'money')).toBe('–');
    });
});
