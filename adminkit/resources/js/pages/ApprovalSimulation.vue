<script setup>
import { Head } from '@inertiajs/vue3';
import { Gavel } from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
</script>

<template>
    <Head title="Persetujuan Komite" />
    <AppLayout>
        <Card data-testid="approval-simulation-page">
            <CardHeader class="flex flex-row flex-wrap items-center justify-between gap-2 space-y-0">
                <CardTitle class="flex items-center gap-2">
                    <Gavel class="size-4" /> Persetujuan Komite
                </CardTitle>
                <Badge variant="secondary" class="font-medium">Segera hadir</Badge>
            </CardHeader>
            <CardContent>
                <div class="flex min-h-[220px] flex-col items-center justify-center gap-2 rounded-md border border-dashed p-6 text-center">
                    <p class="text-sm font-medium">Persetujuan komite belum tersedia</p>
                    <p class="max-w-md text-xs text-muted-foreground">
                        Halaman ini akan menampilkan berkas yang sudah selesai dianalisa, disaring sesuai
                        peranan dan kewenangan komite kredit yang bersangkutan.
                    </p>
                </div>
            </CardContent>
        </Card>
    </AppLayout>
</template>
