import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import { Alert, ErrorState, PageHeader } from '@/components/ui/misc';

describe('PageHeader', () => {
    it('puts the action buttons of the row in equal-width columns, so a pair never looks uneven', () => {
        render(
            <PageHeader
                title="Audit Logs"
                actions={
                    <>
                        <button type="button">Periksa integritas</button>
                        <button type="button">Ekspor CSV</button>
                    </>
                }
            />,
        );
        const row = screen.getByRole('button', { name: 'Ekspor CSV' }).parentElement;

        expect(row).toHaveClass('grid', 'sm:auto-cols-fr', 'sm:grid-flow-col');
        // On a phone: the full width in two equal columns, like the filters.
        expect(row).toHaveClass('w-full', 'grid-cols-2');
        expect(row?.children).toHaveLength(2);
    });

    it('puts a status next to the title, not in the row of buttons where it would be stretched to a button width', () => {
        render(<PageHeader title="Pengajuan 0070001" badge={<span>Diajukan</span>} actions={<button type="button">Kembali</button>} />);

        const title = screen.getByRole('heading', { level: 1, name: 'Pengajuan 0070001' });

        expect(title.parentElement).toContainElement(screen.getByText('Diajukan'));
        expect(screen.getByRole('button', { name: 'Kembali' }).parentElement).not.toContainElement(screen.getByText('Diajukan'));
    });

    it('shows the title and the description', () => {
        render(<PageHeader title="Audit Logs" description="Catatan perubahan data" />);

        expect(screen.getByRole('heading', { level: 1, name: 'Audit Logs' })).toBeInTheDocument();
        expect(screen.getByText('Catatan perubahan data')).toBeInTheDocument();
    });

    it('is an h2 when it is shown as an example inside a page that already has its h1', () => {
        render(<PageHeader title="Contoh" heading="h2" />);

        expect(screen.getByRole('heading', { level: 2, name: 'Contoh' })).toBeInTheDocument();
        expect(screen.queryByRole('heading', { level: 1 })).not.toBeInTheDocument();
    });
});

describe('ErrorState', () => {
    it('is announced when it appears, and offers a retry only when there is one', async () => {
        const onRetry = vi.fn();
        const { rerender } = render(<ErrorState message="Server tidak dapat dihubungi." />);

        expect(screen.getByRole('alert')).toHaveTextContent('Server tidak dapat dihubungi.');
        expect(screen.queryByRole('button', { name: 'Coba lagi' })).not.toBeInTheDocument();

        rerender(<ErrorState message="Server tidak dapat dihubungi." onRetry={onRetry} />);
        await userEvent.click(screen.getByRole('button', { name: 'Coba lagi' }));
        expect(onRetry).toHaveBeenCalled();
    });
});

describe('Alert', () => {
    it('announces a danger message at once and the other tones politely', () => {
        render(
            <>
                <Alert tone="danger">Gagal.</Alert>
                <Alert tone="success">Berhasil.</Alert>
            </>,
        );

        expect(screen.getByRole('alert')).toHaveTextContent('Gagal.');
        expect(screen.getByRole('status')).toHaveTextContent('Berhasil.');
    });
});
