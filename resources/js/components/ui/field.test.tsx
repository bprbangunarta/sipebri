import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it } from 'vitest';
import { Textarea } from '@/components/ui/textarea';
import { Field } from '@/components/ui/field';
import { Input, InputGroup } from '@/components/ui/input';

describe('Field', () => {
    it('connects the label to the control', () => {
        render(
            <Field label="Nama" required>
                <Input />
            </Field>,
        );

        expect(screen.getByLabelText(/Nama/)).toBeInTheDocument();
    });

    it('connects the error to the control and marks it invalid', () => {
        render(
            <Field label="Nama" error="Wajib diisi.">
                <Input />
            </Field>,
        );
        const input = screen.getByLabelText('Nama');

        expect(input).toHaveAttribute('aria-invalid', 'true');
        expect(input).toHaveAccessibleDescription('Wajib diisi.');
    });

    it('connects the hint as the description when there is no error', () => {
        render(
            <Field label="Nama" hint="Minimal 3 huruf">
                <Input />
            </Field>,
        );

        expect(screen.getByLabelText('Nama')).toHaveAccessibleDescription('Minimal 3 huruf');
    });

    it('shows the hint as a tooltip on the label and never as text under the control', async () => {
        render(
            <Field label="Nama" hint="Minimal 3 huruf">
                <Input />
            </Field>,
        );
        const trigger = screen.getByRole('button', { name: 'Lihat keterangan Nama' });

        // Under the control the hint exists only for screen readers (`sr-only`), not for the eye.
        expect(screen.getByText('Minimal 3 huruf')).toHaveClass('sr-only');

        await userEvent.hover(trigger);

        await waitFor(() => expect(screen.getAllByText('Minimal 3 huruf').length).toBeGreaterThan(1));
    });

    it('opens the hint on a tap, which a plain tooltip ignores, and closes it with Escape', async () => {
        const user = userEvent.setup();
        render(
            <Field label="Nama" hint="Minimal 3 huruf">
                <Input />
            </Field>,
        );

        await user.pointer({ keys: '[TouchA]', target: screen.getByRole('button', { name: 'Lihat keterangan Nama' }) });
        expect(await screen.findByRole('tooltip')).toHaveTextContent('Minimal 3 huruf');

        await user.keyboard('{Escape}');
        await waitFor(() => expect(screen.queryByRole('tooltip')).not.toBeInTheDocument());
    });

    it('opens the hint from the keyboard', async () => {
        const user = userEvent.setup();
        render(
            <>
                <Field label="Nama" hint="Minimal 3 huruf">
                    <Input />
                </Field>
            </>,
        );

        await user.tab();
        expect(screen.getByRole('button', { name: 'Lihat keterangan Nama' })).toHaveFocus();
        expect(await screen.findByRole('tooltip')).toHaveTextContent('Minimal 3 huruf');
    });

    it('describes the control by the error and the hint together', () => {
        render(
            <Field label="Nama" error="Wajib diisi." hint="Minimal 3 huruf">
                <Input />
            </Field>,
        );

        expect(screen.getByLabelText('Nama')).toHaveAccessibleDescription('Wajib diisi. Minimal 3 huruf');
    });

    it('shows no info icon when there is no hint', () => {
        render(
            <Field label="Nama">
                <Input />
            </Field>,
        );

        expect(screen.queryByRole('button', { name: /Lihat keterangan/ })).not.toBeInTheDocument();
    });
});

describe('Field with an InputGroup', () => {
    it('wires the label, the error, and the invalid state to the control, not to the row', () => {
        render(
            <Field label="Kata sandi" error="Wajib diisi.">
                <InputGroup>
                    <Input />
                    <button type="button">Buat</button>
                </InputGroup>
            </Field>,
        );
        const input = screen.getByLabelText('Kata sandi');

        expect(input.tagName).toBe('INPUT');
        expect(input).toHaveAttribute('aria-invalid', 'true');
        expect(input).toHaveAccessibleDescription('Wajib diisi.');
        expect(screen.getByRole('button', { name: 'Buat' })).toBeInTheDocument();
    });

    it('shows the character counter beside the label, for a text area it holds directly and for one that a component wraps', () => {
        const Wrapped = (props: { id?: string }) => <textarea id={props.id} defaultValue="abc" />;

        render(
            <>
                <Field label="Langsung">
                    <Textarea value="abcd" maxLength={10} onChange={() => undefined} />
                </Field>
                <Field label="Terbungkus" counter={{ used: 3, max: 2000 }}>
                    <Wrapped />
                </Field>
            </>,
        );

        expect(screen.getByText('4/10')).toBeInTheDocument();
        expect(screen.getByText('3/2000')).toBeInTheDocument();
    });
});
