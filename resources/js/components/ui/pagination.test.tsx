import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import { Pagination } from '@/components/ui/pagination';
import type { PageMeta } from '@/components/ui/pagination';

const meta = (current: number, last: number, total = last * 15): PageMeta => ({
    current_page: current,
    last_page: last,
    from: total === 0 ? null : (current - 1) * 15 + 1,
    to: Math.min(current * 15, total),
    total,
});

function setup(page: PageMeta, handlers: { onPage?: (page: number) => void; onPerPage?: (perPage: number) => void } = {}) {
    return render(
        <Pagination
            meta={page}
            perPage={15}
            perPageOptions={[15, 50, 100]}
            onPage={handlers.onPage ?? (() => undefined)}
            onPerPage={handlers.onPerPage ?? (() => undefined)}
        />,
    );
}

describe('Pagination', () => {
    it('says which rows are shown, and "0 hasil" for an empty list without page buttons', () => {
        const { unmount } = setup(meta(2, 4));
        expect(screen.getByText('16–30 dari 60')).toBeInTheDocument();
        unmount();

        setup(meta(1, 1, 0));
        expect(screen.getByText('0 hasil')).toBeInTheDocument();
        expect(screen.queryByRole('navigation', { name: 'Halaman' })).not.toBeInTheDocument();
    });

    it('shows the first, the last, and the neighbours of the current page, with a gap between', () => {
        setup(meta(5, 10));
        const pages = screen
            .getAllByRole('button')
            .filter((button) => /^\d+$/.test(button.textContent ?? ''))
            .map((button) => button.textContent);

        expect(pages).toEqual(['1', '4', '5', '6', '10']);
        expect(screen.getAllByText('…')).toHaveLength(2);
        expect(screen.getByRole('button', { name: '5' })).toHaveAttribute('aria-current', 'page');
    });

    it('turns the page with the buttons, and cannot go before the first or after the last', async () => {
        const onPage = vi.fn();
        const user = userEvent.setup();
        const { unmount } = setup(meta(1, 3), { onPage });

        expect(screen.getByRole('button', { name: 'Halaman sebelumnya' })).toBeDisabled();
        await user.click(screen.getByRole('button', { name: 'Halaman berikutnya' }));
        expect(onPage).toHaveBeenLastCalledWith(2);
        unmount();

        setup(meta(3, 3), { onPage });
        expect(screen.getByRole('button', { name: 'Halaman berikutnya' })).toBeDisabled();
        await user.click(screen.getByRole('button', { name: '1' }));
        expect(onPage).toHaveBeenLastCalledWith(1);
    });

    it('changes the number of rows per page from the keyboard', async () => {
        const onPerPage = vi.fn();
        const user = userEvent.setup();
        setup(meta(1, 3), { onPerPage });

        await user.tab();
        expect(screen.getByRole('combobox', { name: 'Baris per halaman' })).toHaveFocus();
        await user.keyboard('{Enter}{ArrowDown}{Enter}');

        expect(onPerPage).toHaveBeenCalledWith(50);
    });
});
