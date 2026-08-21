<script setup>
import { Head } from '@inertiajs/vue3';
import { MapPinned } from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
</script>

<template>
    <Head title="Survei Kredit" />
    <AppLayout>
        <Card data-testid="survey-simulation-page">
            <CardHeader class="flex flex-row flex-wrap items-center justify-between gap-2 space-y-0">
                <CardTitle class="flex items-center gap-2">
                    <MapPinned class="size-4" /> Survei Kredit
                </CardTitle>
                <Badge variant="secondary" class="font-medium">Segera hadir</Badge>
            </CardHeader>
            <CardContent>
                <div class="flex min-h-[220px] flex-col items-center justify-center gap-2 rounded-md border border-dashed p-6 text-center">
                    <p class="text-sm font-medium">Form survei belum tersedia</p>
                    <p class="max-w-md text-xs text-muted-foreground">
                        Halaman ini akan memuat berkas berstatus PENJADWALAN sesuai staff analis dan
                        tanggal survei berjalan, dengan foto lokasi beserta koordinat (lintang &amp; bujur).
                    </p>
                </div>
            </CardContent>
        </Card>
    </AppLayout>
</template>
