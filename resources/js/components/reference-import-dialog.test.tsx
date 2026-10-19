import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { ReferenceImportDialog } from '@/components/reference-import-dialog';

const post = vi.hoisted(() => vi.fn());
const state = vi.hoisted(() => ({ errors: {} as Record<string, string>, issues: [] as string[] }));

vi.mock('@inertiajs/react', async () => {
    const react = await import('react');

    return {
        usePage: () => ({ props: { flash: { issues: state.issues } } }),
        useForm: (initial: { file: File | null }) => {
            const [data, setData] = react.useState(initial);

            return {
                data,
                errors: state.errors,
                processing: false,
                setData: (key: string, value: File | null) => setData({ ...data, [key]: value }),
                clearErrors: () => undefined,
                reset: () => setData(initial),
                post,
            };
        },
    };
});

const xlsx = () => new File(['x'], 'resorts.xlsx', { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });

const chooser = () => document.querySelector<HTMLInputElement>('input[type="file"]') as HTMLInputElement;

function open() {
    render(
        <ReferenceImportDialog
            open
            onOpenChange={() => undefined}
            label="Resort"
            url="/references/resorts/import"
            templateUrl="/references/resorts/import/template"
        />,
    );
}

beforeEach(() => {
    post.mockReset();
    state.errors = {};
    state.issues = [];
});

describe('ReferenceImportDialog', () => {
    it('keeps Impor disabled until a file is chosen, then sends it as a form upload', async () => {
        const user = userEvent.setup();
        open();

        expect(screen.getByRole('button', { name: 'Impor' })).toBeDisabled();

        await user.upload(chooser(), xlsx());
        await user.click(screen.getByRole('button', { name: 'Impor' }));

        expect(post).toHaveBeenCalledWith('/references/resorts/import', expect.objectContaining({ forceFormData: true }));
    });

    it('refuses a file that is not .xlsx before it reaches the server', async () => {
        const user = userEvent.setup({ applyAccept: false });
        open();

        await user.upload(chooser(), new File(['x'], 'resorts.csv', { type: 'text/csv' }));

        expect(screen.getByText('Jenis berkas tidak diterima.')).toBeInTheDocument();
        expect(screen.getByRole('button', { name: 'Impor' })).toBeDisabled();
    });

    it('lists the rows the server refused, next to the reason', () => {
        state.errors = { file: 'Berkas ditolak: 2 masalah ditemukan dan tidak ada yang disimpan.' };
        state.issues = ['Baris 3: kode wajib diisi.', 'Baris 5: kode sudah muncul di baris 2.'];
        open();

        expect(screen.getByText(/Berkas ditolak: 2 masalah/)).toBeInTheDocument();
        expect(screen.getByRole('list', { name: 'Baris yang ditolak' }).textContent).toBe('Baris 3: kode wajib diisi.Baris 5: kode sudah muncul di baris 2.');
    });

    it('offers the template download', async () => {
        const assign = vi.fn();
        vi.stubGlobal('location', { ...window.location, assign });
        const user = userEvent.setup();
        open();

        await user.click(screen.getByRole('button', { name: /Unduh template/ }));

        expect(assign).toHaveBeenCalledWith('/references/resorts/import/template');
        vi.unstubAllGlobals();
    });
});
