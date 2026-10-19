import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import { Combobox } from '@/components/ui/combobox';
import { Field } from '@/components/ui/field';

const options = [
    { value: 'a', label: 'Alfa' },
    { value: 'b', label: 'Beta', description: 'Kode B-2' },
    { value: 'c', label: 'Gamma' },
];

describe('Combobox keyboard', () => {
    it('without a search box: opens on Enter, moves with the arrows, picks with Enter, and gives focus back', async () => {
        const onChange = vi.fn();
        const user = userEvent.setup();
        render(<Combobox searchable={false} aria-label="Pilih huruf" options={options} value="a" onChange={onChange} />);
        const trigger = screen.getByRole('combobox', { name: 'Pilih huruf' });

        await user.tab();
        await user.keyboard('{Enter}');
        expect(screen.getByRole('listbox')).toHaveFocus();

        await user.keyboard('{ArrowDown}{Enter}');

        expect(onChange).toHaveBeenCalledWith('b');
        expect(screen.queryByRole('listbox')).not.toBeInTheDocument();
        expect(trigger).toHaveFocus();
    });

    it('opens from the closed field with the arrow key', async () => {
        const user = userEvent.setup();
        render(<Combobox searchable={false} aria-label="Pilih huruf" options={options} value="a" onChange={() => undefined} />);

        await user.tab();
        await user.keyboard('{ArrowDown}');

        expect(screen.getByRole('listbox')).toBeInTheDocument();
    });

    it('with a search box: typing narrows the list and Enter picks the first match', async () => {
        const onChange = vi.fn();
        const user = userEvent.setup();
        render(<Combobox aria-label="Pilih huruf" options={options} value={null} onChange={onChange} />);

        await user.click(screen.getByRole('combobox'));
        await user.keyboard('gam{Enter}');

        expect(onChange).toHaveBeenCalledWith('c');
    });

    it('says which option is highlighted (aria-activedescendant) and which is chosen', async () => {
        const user = userEvent.setup();
        render(<Combobox searchable={false} aria-label="Pilih huruf" options={options} value="b" onChange={() => undefined} />);

        await user.click(screen.getByRole('combobox'));
        const list = screen.getByRole('listbox');
        const active = screen.getByRole('option', { name: /Beta/ });

        expect(list).toHaveAttribute('aria-activedescendant', active.id);
        expect(active).toHaveAttribute('aria-selected', 'true');

        await user.keyboard('{ArrowDown}');
        expect(list).toHaveAttribute('aria-activedescendant', screen.getByRole('option', { name: 'Gamma' }).id);

        await user.keyboard('{Home}');
        expect(list).toHaveAttribute('aria-activedescendant', screen.getByRole('option', { name: 'Alfa' }).id);

        await user.keyboard('{End}');
        expect(list).toHaveAttribute('aria-activedescendant', screen.getByRole('option', { name: 'Gamma' }).id);
    });

    it('Escape closes the list and returns focus to the field', async () => {
        const user = userEvent.setup();
        render(<Combobox searchable={false} aria-label="Pilih huruf" options={options} value="a" onChange={() => undefined} />);
        const trigger = screen.getByRole('combobox');

        await user.click(trigger);
        await user.keyboard('{Escape}');

        expect(screen.queryByRole('listbox')).not.toBeInTheDocument();
        expect(trigger).toHaveFocus();
    });

    it('names its parts: the list, the search box, and the field that announces a popup', async () => {
        const user = userEvent.setup();
        render(<Combobox aria-label="Pilih huruf" options={options} value="a" onChange={() => undefined} />);
        const trigger = screen.getByRole('combobox', { name: 'Pilih huruf' });

        expect(trigger).toHaveAttribute('aria-haspopup', 'listbox');
        // Nothing is controlled while the list is not in the page.
        expect(trigger).not.toHaveAttribute('aria-controls');

        await user.click(trigger);

        expect(screen.getByRole('listbox', { name: 'Pilih huruf' })).toBeInTheDocument();
        expect(screen.getByRole('textbox', { name: 'Cari' })).toBeInTheDocument();
        expect(trigger).toHaveAttribute('aria-controls', screen.getByRole('listbox').id);
    });
});

describe('Combobox content', () => {
    it('searches the description too, and says when nothing matches', async () => {
        const user = userEvent.setup();
        render(<Combobox aria-label="Pilih huruf" options={options} value={null} onChange={() => undefined} />);

        await user.click(screen.getByRole('combobox'));
        await user.type(screen.getByRole('textbox', { name: 'Cari' }), 'b-2');
        expect(screen.getAllByRole('option')).toHaveLength(1);

        await user.clear(screen.getByRole('textbox', { name: 'Cari' }));
        await user.type(screen.getByRole('textbox', { name: 'Cari' }), 'zzz');
        expect(screen.queryAllByRole('option')).toHaveLength(0);
        expect(screen.getByText('Tidak ada hasil')).toBeInTheDocument();
    });

    it('matches a numeric value to its option and shows the placeholder when nothing is chosen', () => {
        const { rerender } = render(
            <Combobox aria-label="Baris" options={[{ value: 10, label: '10 / halaman' }]} value={10} onChange={() => undefined} placeholder="Pilih…" />,
        );
        expect(screen.getByRole('combobox')).toHaveTextContent('10 / halaman');

        rerender(<Combobox aria-label="Baris" options={[{ value: 10, label: '10 / halaman' }]} value={null} onChange={() => undefined} placeholder="Pilih…" />);
        expect(screen.getByRole('combobox')).toHaveTextContent('Pilih…');
    });

    it('clears the choice from the keyboard and reports null', async () => {
        const onChange = vi.fn();
        const user = userEvent.setup();
        render(<Combobox aria-label="Pilih huruf" clearable options={options} value="a" onChange={onChange} />);

        await user.tab();
        await user.tab();
        expect(screen.getByRole('button', { name: 'Hapus pilihan' })).toHaveFocus();
        await user.keyboard('{Enter}');

        expect(onChange).toHaveBeenCalledWith(null);
    });
});

describe('Combobox inside Field', () => {
    it('is labelled by the Field, and the error and hint describe it', () => {
        render(
            <Field label="Jenis" error="Wajib diisi." hint="Pilih satu">
                <Combobox options={options} value={null} onChange={() => undefined} />
            </Field>,
        );
        const trigger = screen.getByRole('combobox', { name: /Jenis/ });

        expect(trigger).toHaveAttribute('aria-invalid', 'true');
        expect(trigger).toHaveAccessibleDescription('Wajib diisi. Pilih satu');
    });
});
