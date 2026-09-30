import { router } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';

type Params = Record<string, string | number | boolean>;

/**
 * Shared behaviour of server-driven lists: filters live in the query string, changes
 * re-fetch only the listed props, search is debounced, and loading / network errors are tracked.
 * Values equal to `defaults` (or empty) are dropped so URLs stay clean.
 */
export function useListQuery<F extends { search?: string }>({
    url,
    filters,
    defaults,
    only,
}: {
    url: string;
    filters: F;
    defaults: Partial<Record<keyof F, unknown>>;
    only: string[];
}) {
    const [search, setSearch] = useState(filters.search ?? '');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const timer = useRef<ReturnType<typeof setTimeout>>(undefined);

    useEffect(
        () =>
            router.on('networkError', () =>
                setError(
                    'Unable to reach the server. Check your connection and try again.',
                ),
            ),
        [],
    );
    useEffect(() => () => clearTimeout(timer.current), []);

    const visit = (changes: Partial<F & { page: number }>) => {
        const next: Record<string, unknown> = {
            ...filters,
            page: undefined,
            ...changes,
        };
        const params = Object.fromEntries(
            Object.entries(next).filter(([key, value]) => {
                if (value === null || value === undefined || value === '') {
                    return false;
                }

                return defaults[key as keyof F] !== value;
            }),
        );

        router.get(url, params as Params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            only,
            onStart: () => {
                setLoading(true);
                setError(null);
            },
            onFinish: () => setLoading(false),
        });
    };

    const onSearch = (value: string) => {
        setSearch(value);
        clearTimeout(timer.current);
        timer.current = setTimeout(
            () =>
                visit({ search: value.trim() } as Partial<
                    F & { page: number }
                >),
            300,
        );
    };

    /** Clears the search box plus any other filter keys given. */
    const clear = (others: Partial<F> = {}) => {
        setSearch('');
        visit({ search: '', ...others } as Partial<F & { page: number }>);
    };

    return { visit, search, onSearch, clear, loading, error };
}
