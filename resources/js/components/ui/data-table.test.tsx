import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { afterEach, describe, expect, it, vi } from 'vitest';
import { DataTable } from '@/components/ui/data-table';
import type { Column } from '@/components/ui/data-table';

type Row = { id: number; name: string; amount: number };
const rows: Row[] = [
    { id: 1, name: 'Beta', amount: 5 },
    { id: 2, name: 'Alfa', amount: 9 },
];
const columns: Column<Row>[] = [
    { key: 'name', header: 'Nama', cell: (r) => r.name, sort: 'name' },
    { key: 'amount', header: 'Nilai', cell: (r) => r.amount, sort: 'amount' },
    { key: 'note', header: 'Catatan', cell: () => '–', sort: 'note' },
];

afterEach(() => {
    vi.restoreAllMocks();
});

describe('DataTable sorting', () => {
    it('gives every column with data a header button, and leaves a column of row actions plain', () => {
        const withActions: Column<Row>[] = [...columns, { key: 'actions', header: 'Aksi', srOnly: true, cell: () => '…' }];
        render(
            <DataTable
                empty={{ icon: null, title: 'Kosong' }}
                rows={rows}
                rowKey={(r) => r.id}
                columns={withActions}
                sort={{ sort: 'name', direction: 'asc', onSort: () => undefined }}
            />,
        );

        expect(screen.getByRole('button', { name: 'Nama' })).toBeInTheDocument();
        expect(screen.getByRole('button', { name: 'Nilai' })).toBeInTheDocument();
        expect(screen.getByRole('button', { name: 'Catatan' })).toBeInTheDocument();
        expect(screen.queryByRole('button', { name: 'Aksi' })).not.toBeInTheDocument();
    });

    it('warns while developing when a sorting table has a column with data that cannot sort', () => {
        const error = vi.spyOn(console, 'error').mockImplementation(() => undefined);
        const partial: Column<Row>[] = [...columns.slice(0, 2), { key: 'note', header: 'Catatan', cell: () => '–' }];
        render(
            <DataTable
                empty={{ icon: null, title: 'Kosong' }}
                rows={rows}
                rowKey={(r) => r.id}
                columns={partial}
                sort={{ sort: 'name', direction: 'asc', onSort: () => undefined }}
            />,
        );

        expect(error).toHaveBeenCalledWith(expect.stringContaining('no "sort" key on: note'));
    });

    it('writes the header in the smallest text size, uppercase', () => {
        const { container } = render(
            <DataTable
                empty={{ icon: null, title: 'Kosong' }}
                rows={rows}
                rowKey={(r) => r.id}
                columns={columns}
                sort={{ sort: 'name', direction: 'asc', onSort: () => undefined }}
            />,
        );

        expect(container.querySelector('thead')).toHaveClass('text-2xs', 'uppercase');
    });

    it('tells screen readers which column is sorted and in which direction', () => {
        render(
            <DataTable
                empty={{ icon: null, title: 'Kosong' }}
                rows={rows}
                rowKey={(r) => r.id}
                columns={columns}
                sort={{ sort: 'amount', direction: 'desc', onSort: () => undefined }}
            />,
        );

        expect(screen.getByRole('columnheader', { name: 'Nilai' })).toHaveAttribute('aria-sort', 'descending');
        expect(screen.getByRole('columnheader', { name: 'Nama' })).toHaveAttribute('aria-sort', 'none');
    });

    it('asks the screen to sort by the clicked column', async () => {
        const onSort = vi.fn();
        render(
            <DataTable
                empty={{ icon: null, title: 'Kosong' }}
                rows={rows}
                rowKey={(r) => r.id}
                columns={columns}
                sort={{ sort: 'name', direction: 'asc', onSort }}
            />,
        );

        await userEvent.click(screen.getByRole('button', { name: 'Nilai' }));

        expect(onSort).toHaveBeenCalledWith('amount');
    });

    it('warns while developing when a column declares `sort` but the table has no `sort` prop', () => {
        const error = vi.spyOn(console, 'error').mockImplementation(() => undefined);
        render(<DataTable empty={{ icon: null, title: 'Kosong' }} rows={rows} rowKey={(r) => r.id} columns={columns} />);

        expect(error).toHaveBeenCalledWith(expect.stringContaining('no "sort" prop'));
        expect(screen.queryByRole('button', { name: 'Nama' })).not.toBeInTheDocument();
    });

    it('does not warn for a table without sortable columns', () => {
        const error = vi.spyOn(console, 'error').mockImplementation(() => undefined);
        render(
            <DataTable
                empty={{ icon: null, title: 'Kosong' }}
                rows={rows}
                rowKey={(r) => r.id}
                columns={[{ key: 'name', header: 'Nama', cell: (r) => r.name }]}
            />,
        );

        expect(error).not.toHaveBeenCalled();
    });
});

describe('DataTable on a narrow screen', () => {
    it('never hides a column: every header and cell stays, and the table scrolls sideways inside its card', () => {
        const { container } = render(<DataTable empty={{ icon: null, title: 'Kosong' }} rows={rows} rowKey={(r) => r.id} columns={columns} />);

        expect(screen.getAllByRole('columnheader')).toHaveLength(3);
        expect(container.querySelector('.overflow-x-auto')).toBeInTheDocument();
        expect(container.querySelector('th.hidden, td.hidden, [class*="sm:table-cell"], [class*="md:table-cell"], [class*="lg:table-cell"]')).toBeNull();
        expect(screen.getAllByRole('cell')[0]).toHaveClass('whitespace-nowrap');
    });
});

describe('DataTable header with a node', () => {
    it('names a sortable column by its header, not by its key', () => {
        render(
            <DataTable
                empty={{ icon: null, title: 'Kosong' }}
                rows={rows}
                rowKey={(r) => r.id}
                columns={[{ key: 'amount', header: <span>Plafon (Rp)</span>, cell: (r) => r.amount, sort: 'amount' }]}
                sort={{ sort: 'amount', direction: 'asc', onSort: () => undefined }}
            />,
        );

        expect(screen.getByRole('button', { name: 'Plafon (Rp)' })).toBeInTheDocument();
        expect(screen.queryByRole('button', { name: 'amount' })).not.toBeInTheDocument();
    });
});

describe('DataTable rows that open something', () => {
    const open = (onRowClick: (row: Row) => void) => (
        <DataTable
            empty={{ icon: null, title: 'Kosong' }}
            rows={rows}
            rowKey={(r) => r.id}
            columns={[
                { key: 'name', header: 'Nama', cell: (r) => r.name },
                { key: 'actions', header: 'Aksi', srOnly: true, cell: (r) => <button type="button">Ubah {r.name}</button> },
            ]}
            onRowClick={onRowClick}
        />
    );

    it('can be reached and opened with the keyboard', async () => {
        const onRowClick = vi.fn();
        const user = userEvent.setup();
        render(open(onRowClick));

        await user.tab();
        expect(screen.getByRole('row', { name: /Beta/ })).toHaveFocus();
        await user.keyboard('{Enter}');

        expect(onRowClick).toHaveBeenCalledWith(rows[0]);
    });

    it('is opened by a click, but not by a click on its own actions or a key pressed inside them', async () => {
        const onRowClick = vi.fn();
        const user = userEvent.setup();
        render(open(onRowClick));

        await user.click(screen.getByRole('cell', { name: 'Beta' }));
        expect(onRowClick).toHaveBeenCalledTimes(1);

        await user.click(screen.getByRole('button', { name: 'Ubah Alfa' }));
        screen.getByRole('button', { name: 'Ubah Alfa' }).focus();
        await user.keyboard('{Enter}');

        expect(onRowClick).toHaveBeenCalledTimes(1);
    });

    it('has no tab stop when rows open nothing', async () => {
        const user = userEvent.setup();
        render(
            <DataTable
                empty={{ icon: null, title: 'Kosong' }}
                rows={rows}
                rowKey={(r) => r.id}
                columns={[{ key: 'name', header: 'Nama', cell: (r) => r.name }]}
            />,
        );

        await user.tab();

        expect(document.body).toHaveFocus();
    });
});

describe('DataTable states', () => {
    const base = { empty: { icon: null, title: 'Belum ada data', description: 'Tambahkan satu.' }, rowKey: (r: Row) => r.id, columns };

    it('says it is loading, for a screen reader too, and marks the table busy', () => {
        render(<DataTable {...base} rows={[]} loading />);

        expect(screen.getByRole('status')).toHaveTextContent('Memuat data');
        expect(screen.getByRole('table')).toHaveAttribute('aria-busy', 'true');
    });

    it('keeps the old rows, dimmed and busy, while a new page loads', () => {
        render(<DataTable {...base} rows={rows} loading />);

        expect(screen.getByRole('table')).toHaveAttribute('aria-busy', 'true');
        expect(screen.getByText('Beta')).toBeInTheDocument();
    });

    it('says why it is empty and offers the action', () => {
        render(<DataTable {...base} rows={[]} empty={{ ...base.empty, action: <button type="button">Tambah</button> }} />);

        expect(screen.getByText('Belum ada data')).toBeInTheDocument();
        expect(screen.getByText('Tambahkan satu.')).toBeInTheDocument();
        expect(screen.getByRole('button', { name: 'Tambah' })).toBeInTheDocument();
        expect(screen.getByRole('table')).not.toHaveAttribute('aria-busy', 'true');
    });

    it('shows the error instead of the table, and retries from its button', async () => {
        const onRetry = vi.fn();
        render(<DataTable {...base} rows={rows} error="Server tidak dapat dihubungi." onRetry={onRetry} />);

        expect(screen.getByRole('alert')).toHaveTextContent('Server tidak dapat dihubungi.');
        expect(screen.queryByRole('table')).not.toBeInTheDocument();

        await userEvent.click(screen.getByRole('button', { name: 'Coba lagi' }));
        expect(onRetry).toHaveBeenCalled();
    });
});
