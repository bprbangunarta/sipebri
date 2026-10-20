import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import { PanelMenu } from '@/components/ui/panel-menu';

const items = [
    { key: 'a', label: 'A. Identitas' },
    { key: 'b', label: 'B. Objek', unsaved: true },
];

describe('PanelMenu', () => {
    it('marks the active part, says which part is not saved, and reports the part that is chosen', async () => {
        const onSelect = vi.fn();
        render(<PanelMenu label="Bagian" items={items} active="a" onSelect={onSelect} />);

        expect(screen.getByRole('navigation', { name: 'Bagian' })).toBeInTheDocument();
        expect(screen.getByRole('button', { name: 'A. Identitas' })).toHaveAttribute('aria-current', 'true');
        expect(screen.getByRole('button', { name: /B\. Objek/ })).toHaveTextContent('Belum disimpan');

        await userEvent.click(screen.getByRole('button', { name: /B\. Objek/ }));

        expect(onSelect).toHaveBeenCalledWith('b');
    });
});
