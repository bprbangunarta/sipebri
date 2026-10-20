import { render, screen, within } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import { AppraisalBlock } from '@/components/appraisal/appraisal-block';
import type { BlockProps } from '@/components/appraisal/appraisal-block';

const base: Omit<BlockProps, 'block'> = {
    section: 'sec',
    answers: {},
    onAnswer: vi.fn(),
    computed: {},
    errors: {},
    readOnly: false,
    locked: [],
    photos: {},
    maxPhotos: 5,
    onUpload: vi.fn(),
    onDeletePhoto: vi.fn(),
    uploading: null,
};

describe('AppraisalBlock', () => {
    it('shows a figure the system works out in the grid cell it belongs to, not as something to type', () => {
        render(
            <AppraisalBlock
                {...base}
                computed={{ 'sec.main.new_cost': 240000000 }}
                block={{
                    layout: 'grid',
                    rows: [{ key: 'main', label: 'Bangunan utama' }],
                    columns: [
                        { key: 'area', label: 'Luas', type: 'integer' },
                        { key: 'new_cost', label: 'Biaya baru', computed: true, format: 'money' },
                    ],
                }}
            />,
        );

        expect(screen.getByLabelText('Bangunan utama: Luas')).toBeInTheDocument();
        expect(screen.getByText(/240\.000\.000/)).toBeInTheDocument();
        expect(screen.queryByLabelText('Bangunan utama: Biaya baru')).not.toBeInTheDocument();
    });

    it('reports an answer and a note of a check separately', async () => {
        const onAnswer = vi.fn();
        render(
            <AppraisalBlock
                {...base}
                onAnswer={onAnswer}
                block={{ layout: 'checks', items: [{ key: 'legal.no_dispute', label: 'Tidak terdapat sengketa', type: 'yesno' }] }}
            />,
        );

        await userEvent.click(screen.getByRole('radio', { name: 'Ya' }));
        await userEvent.type(screen.getByLabelText('Bukti/keterangan: Tidak terdapat sengketa'), 'S');

        expect(onAnswer).toHaveBeenCalledWith('legal.no_dispute', { value: 'yes' });
        expect(onAnswer).toHaveBeenLastCalledWith('legal.no_dispute', { note: 'S' });
    });

    it('shows the error of a field under its own label and locks a field that comes from the loan file', () => {
        render(
            <AppraisalBlock
                {...base}
                locked={['h:debtor_name']}
                errors={{ 'values.h:credit_ceiling': 'Data ini diambil dari berkas pengajuan.' }}
                block={{
                    layout: 'fields',
                    items: [
                        { key: 'h:debtor_name', label: 'Nama debitur', type: 'text' },
                        { key: 'h:credit_ceiling', label: 'Plafon', type: 'money' },
                    ],
                }}
            />,
        );

        expect(screen.getByLabelText('Nama debitur')).toBeDisabled();
        expect(screen.getByText('Data ini diambil dari berkas pengajuan.')).toBeInTheDocument();
    });

    it('lets a draft add and remove photos but a final appraisal only show them', async () => {
        const photos = { 'photos.front': [{ id: 7, url: '/a.jpg', created_by: 'Rina' }] };
        const onDeletePhoto = vi.fn();
        const block = { layout: 'photos' as const, items: [{ key: 'photos.front', label: 'Tampak depan', type: 'photo' }] };
        const draft = render(<AppraisalBlock {...base} photos={photos} onDeletePhoto={onDeletePhoto} block={block} />);

        expect(screen.getByText('1/5 foto')).toBeInTheDocument();
        await userEvent.click(screen.getByRole('button', { name: 'Hapus foto Tampak depan' }));
        expect(onDeletePhoto).toHaveBeenCalledWith(7);
        draft.unmount();

        render(<AppraisalBlock {...base} photos={photos} readOnly block={block} />);

        expect(within(screen.getByRole('img', { name: 'Tampak depan' }).closest('figure') as HTMLElement).queryByRole('button')).not.toBeInTheDocument();
        expect(screen.queryByRole('button', { name: /Tambah foto/ })).not.toBeInTheDocument();
    });

    it('stops adding photos at the limit', () => {
        const photos = { 'photos.front': Array.from({ length: 5 }, (_, i) => ({ id: i + 1, url: `/${i}.jpg`, created_by: 'Rina' })) };

        render(
            <AppraisalBlock {...base} photos={photos} block={{ layout: 'photos', items: [{ key: 'photos.front', label: 'Tampak depan', type: 'photo' }] }} />,
        );

        expect(screen.getByRole('button', { name: /Tambah foto/ })).toBeDisabled();
    });
});
