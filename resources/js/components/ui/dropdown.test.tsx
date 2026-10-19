import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import { DropdownContent, DropdownItem, DropdownLabel, DropdownMenu, DropdownSeparator, DropdownTrigger } from '@/components/ui/dropdown';

function Menu({ onEdit, onDelete }: { onEdit: () => void; onDelete: () => void }) {
    return (
        <DropdownMenu>
            <DropdownTrigger asChild>
                <button type="button" aria-label="Aksi baris">
                    ⋯
                </button>
            </DropdownTrigger>
            <DropdownContent>
                <DropdownLabel>Beta</DropdownLabel>
                <DropdownItem onSelect={onEdit}>Ubah</DropdownItem>
                <DropdownSeparator />
                <DropdownItem danger onSelect={onDelete}>
                    Hapus
                </DropdownItem>
            </DropdownContent>
        </DropdownMenu>
    );
}

describe('Dropdown', () => {
    it('opens from the keyboard, moves with the arrows, selects with Enter, and gives focus back', async () => {
        const onEdit = vi.fn();
        const onDelete = vi.fn();
        const user = userEvent.setup();
        render(<Menu onEdit={onEdit} onDelete={onDelete} />);
        const trigger = screen.getByRole('button', { name: 'Aksi baris' });

        await user.tab();
        await user.keyboard('{Enter}');
        expect(screen.getByRole('menu')).toBeInTheDocument();

        await user.keyboard('{ArrowDown}{ArrowDown}{Enter}');

        expect(onDelete).toHaveBeenCalled();
        expect(onEdit).not.toHaveBeenCalled();
        expect(screen.queryByRole('menu')).not.toBeInTheDocument();
        expect(trigger).toHaveFocus();
    });

    it('closes with Escape without choosing anything', async () => {
        const onEdit = vi.fn();
        const user = userEvent.setup();
        render(<Menu onEdit={onEdit} onDelete={() => undefined} />);

        await user.click(screen.getByRole('button', { name: 'Aksi baris' }));
        await user.keyboard('{Escape}');

        expect(screen.queryByRole('menu')).not.toBeInTheDocument();
        expect(onEdit).not.toHaveBeenCalled();
    });

    it('marks a dangerous item by its colour and still names it in text', async () => {
        const user = userEvent.setup();
        render(<Menu onEdit={() => undefined} onDelete={() => undefined} />);

        await user.click(screen.getByRole('button', { name: 'Aksi baris' }));

        expect(screen.getByRole('menuitem', { name: 'Hapus' })).toHaveClass('text-danger');
    });
});
