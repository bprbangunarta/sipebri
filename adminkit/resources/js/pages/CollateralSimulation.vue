<script setup>
import { Head } from '@inertiajs/vue3';
import { ShieldCheck } from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
</script>

<template>
    <Head title="Agunan Kredit" />
    <AppLayout>
        <Card data-testid="collateral-simulation-page">
            <CardHeader class="flex flex-row flex-wrap items-center justify-between gap-2 space-y-0">
                <CardTitle class="flex items-center gap-2">
                    <ShieldCheck class="size-4" /> Agunan Kredit
                </CardTitle>
                <Badge variant="secondary" class="font-medium">Segera hadir</Badge>
            </CardHeader>
            <CardContent>
                <div class="flex min-h-[220px] flex-col items-center justify-center gap-2 rounded-md border border-dashed p-6 text-center">
                    <p class="text-sm font-medium">Simulasi agunan belum tersedia</p>
                    <p class="max-w-md text-xs text-muted-foreground">
                        Halaman ini akan memuat perhitungan nilai agunan: jenis agunan, jenis pengikatan,
                        kondisi agunan, dan metode hitung untuk memperoleh nilai yang dapat dijaminkan.
                    </p>
                </div>
            </CardContent>
        </Card>
    </AppLayout>
</template>
