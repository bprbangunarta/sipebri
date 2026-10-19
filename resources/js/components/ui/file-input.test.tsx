import { fireEvent, render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import { FileInput } from '@/components/ui/file-input';

const pdf = new File(['x'], 'berkas.pdf', { type: 'application/pdf' });
const png = new File(['x'], 'foto.png', { type: 'image/png' });
const big = new File([new Uint8Array(3 * 1024 * 1024)], 'besar.pdf', { type: 'application/pdf' });

function drop(files: File[]) {
    const zone = screen.getByText(/pilih berkas/i).closest('label') as HTMLElement;
    fireEvent.drop(zone, { dataTransfer: { files } });
}

describe('FileInput', () => {
    it('takes a chosen file and lists it with its size and a named remove button', async () => {
        const onValueChange = vi.fn();
        const user = userEvent.setup();
        const { rerender } = render(<FileInput aria-describedby="x" value={[]} onValueChange={onValueChange} accept=".pdf" />);

        await user.upload(document.querySelector('input[type="file"]') as HTMLInputElement, pdf);
        expect(onValueChange).toHaveBeenCalledWith([pdf]);

        rerender(<FileInput value={[pdf]} onValueChange={onValueChange} accept=".pdf" />);
        expect(screen.getByText('berkas.pdf')).toBeInTheDocument();
        expect(screen.getByRole('button', { name: 'Hapus berkas.pdf' })).toBeInTheDocument();
    });

    it('refuses a file over the limit and says so', () => {
        const onValueChange = vi.fn();
        const onReject = vi.fn();
        render(<FileInput value={[]} onValueChange={onValueChange} maxBytes={2 * 1024 * 1024} onReject={onReject} />);

        drop([big]);

        expect(onReject).toHaveBeenCalledWith('Berkas melebihi batas 2.0 MB.');
        expect(onValueChange).not.toHaveBeenCalled();
    });

    it('refuses a dropped file of a kind the field does not accept, as the chooser would', () => {
        const onValueChange = vi.fn();
        const onReject = vi.fn();
        render(<FileInput value={[]} onValueChange={onValueChange} accept=".pdf" onReject={onReject} />);

        drop([png]);

        expect(onValueChange).not.toHaveBeenCalled();
        expect(onReject).toHaveBeenCalledWith('Jenis berkas tidak diterima.');
    });

    it.each([
        ['an extension', '.pdf', pdf, true],
        ['an extension in upper case', '.PDF', pdf, true],
        ['a wildcard type', 'image/*', png, true],
        ['an exact type', 'application/pdf', pdf, true],
        ['a list with the right kind', '.jpg,image/*', png, true],
        ['a wildcard that does not fit', 'image/*', pdf, false],
        ['an extension that does not fit', '.jpg,.jpeg', png, false],
    ])('judges the dropped file against %s', (_name, accept, file, taken) => {
        const onValueChange = vi.fn();
        render(<FileInput value={[]} onValueChange={onValueChange} accept={accept} />);

        drop([file]);

        if (taken) {
            expect(onValueChange).toHaveBeenCalledWith([file]);
        } else {
            expect(onValueChange).not.toHaveBeenCalled();
        }
    });

    it('keeps one file when it is not multiple, and adds to the list when it is', () => {
        const single = vi.fn();
        const many = vi.fn();
        const { unmount } = render(<FileInput value={[pdf]} onValueChange={single} />);
        drop([png]);
        expect(single).toHaveBeenCalledWith([png]);
        unmount();

        render(<FileInput multiple value={[pdf]} onValueChange={many} />);
        drop([png]);
        expect(many).toHaveBeenCalledWith([pdf, png]);
    });

    it('keeps the file that was chosen before when a new drop is refused', () => {
        const onValueChange = vi.fn();
        render(<FileInput value={[pdf]} onValueChange={onValueChange} accept=".pdf" />);

        drop([png]);

        expect(onValueChange).not.toHaveBeenCalled();
        expect(screen.getByText('berkas.pdf')).toBeInTheDocument();
    });

    it('does nothing with a drop while disabled', () => {
        const onValueChange = vi.fn();
        render(<FileInput disabled value={[]} onValueChange={onValueChange} />);

        drop([pdf]);

        expect(onValueChange).not.toHaveBeenCalled();
    });
});
