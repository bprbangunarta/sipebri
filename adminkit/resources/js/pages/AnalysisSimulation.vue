<script setup>
import { Head } from '@inertiajs/vue3';
import { Calculator } from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
</script>

<template>
    <Head title="Analisa Kredit" />
    <AppLayout>
        <Card data-testid="analysis-simulation-page">
            <CardHeader class="flex flex-row flex-wrap items-center justify-between gap-2 space-y-0">
                <CardTitle class="flex items-center gap-2">
                    <Calculator class="size-4" /> Analisa Kredit
                </CardTitle>
                <Badge variant="secondary" class="font-medium">Segera hadir</Badge>
            </CardHeader>
            <CardContent>
                <div class="flex min-h-[220px] flex-col items-center justify-center gap-2 rounded-md border border-dashed p-6 text-center">
                    <p class="text-sm font-medium">Simulasi analisa kredit belum tersedia</p>
                    <p class="max-w-md text-xs text-muted-foreground">
                        Halaman ini akan memuat perhitungan kelayakan: angsuran, repayment capacity (RC),
                        dan pengujian terhadap parameter produk (SK Direksi).
                    </p>
                </div>
            </CardContent>
        </Card>
    </AppLayout>
</template>
