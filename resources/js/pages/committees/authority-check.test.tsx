import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { AuthorityCheckButton } from '@/pages/committees/authority-check';

const get = vi.hoisted(() => vi.fn());

vi.mock('@/actions/App/Http/Controllers/CommitteeController', () => ({ authority: { url: () => '/committees/authority' } }));
vi.mock('@inertiajs/react', async () => {
    const react = await import('react');

    return {
        useHttp: (initial: Record<string, unknown>) => {
            const [data, setData] = react.useState(initial);

            return {
                data,
                processing: false,
                setData: (keyOrAll: string | Record<string, unknown>, value?: unknown) =>
                    setData(typeof keyOrAll === 'string' ? { ...data, [keyOrAll]: value } : keyOrAll),
                get: (...args: unknown[]) => {
                    get(...args);

                    return Promise.resolve();
                },
            };
        },
    };
});

const options = { productOptions: [{ value: 1, label: 'KRU · Kredit' }], conditionMap: { global: [''] }, committeeMembers: [] };

async function open() {
    const user = userEvent.setup();
    render(<AuthorityCheckButton options={options} />);
    await user.click(screen.getByRole('button', { name: /Cek wewenang/ }));

    return user;
}

beforeEach(() => get.mockReset());

describe('AuthorityCheckButton', () => {
    it('opens a dialog with the header, the body of fields, and a footer of Batal and Cek', async () => {
        await open();

        const dialog = screen.getByRole('dialog', { name: 'Cek wewenang' });

        expect(dialog).toBeInTheDocument();
        expect(screen.getByRole('button', { name: 'Batal' })).toBeInTheDocument();
        expect(screen.getByRole('button', { name: 'Cek' })).toBeDisabled();
    });

    it('asks the server only once a plafon is typed, and also when Enter is pressed in the field', async () => {
        const user = await open();
        const amount = screen.getAllByRole('textbox').at(-1) as HTMLElement;

        await user.type(amount, '5000000');
        expect(screen.getByRole('button', { name: 'Cek' })).toBeEnabled();

        await user.keyboard('{Enter}');

        expect(get).toHaveBeenCalledTimes(1);
        expect(get.mock.calls[0][0]).toBe('/committees/authority');
    });

    it('closes with Batal without asking anything', async () => {
        const user = await open();

        await user.click(screen.getByRole('button', { name: 'Batal' }));

        expect(screen.queryByRole('dialog')).not.toBeInTheDocument();
        expect(get).not.toHaveBeenCalled();
    });
});
