import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it } from 'vitest';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';

function Example() {
    return (
        <Tabs defaultValue="levels">
            <TabsList aria-label="Data Komite">
                <TabsTrigger value="levels">Jenjang</TabsTrigger>
                <TabsTrigger value="rules">Aturan khusus</TabsTrigger>
                <TabsTrigger value="members">Anggota</TabsTrigger>
            </TabsList>
            <TabsContent value="levels">Isi jenjang</TabsContent>
            <TabsContent value="rules">Isi aturan</TabsContent>
            <TabsContent value="members">Isi anggota</TabsContent>
        </Tabs>
    );
}

describe('Tabs', () => {
    it('shows one panel at a time and moves between tabs with the arrow keys', async () => {
        const user = userEvent.setup();
        render(<Example />);

        expect(screen.getByRole('tab', { name: 'Jenjang' })).toHaveAttribute('aria-selected', 'true');
        expect(screen.getByRole('tabpanel')).toHaveTextContent('Isi jenjang');

        await user.tab();
        await user.keyboard('{ArrowRight}');
        expect(screen.getByRole('tab', { name: 'Aturan khusus' })).toHaveFocus();
        expect(screen.getByRole('tabpanel')).toHaveTextContent('Isi aturan');

        await user.keyboard('{End}');
        expect(screen.getByRole('tabpanel')).toHaveTextContent('Isi anggota');
    });
});
