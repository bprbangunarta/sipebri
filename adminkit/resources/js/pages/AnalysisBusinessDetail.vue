<script setup>
import { computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';

import BusinessFarm from '@/components/composite/analysis/BusinessFarm.vue';
import BusinessOther from '@/components/composite/analysis/BusinessOther.vue';
import BusinessService from '@/components/composite/analysis/BusinessService.vue';
import BusinessTrade from '@/components/composite/analysis/BusinessTrade.vue';
import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import { rupiah } from '@/constants/committee';

const props = defineProps({
    application: { type: Object, required: true },
    business: { type: Object, required: true },
    options: { type: Object, required: true },
});

const FORMS = {
    PERDAGANGAN: BusinessTrade,
    PERTANIAN: BusinessFarm,
    JASA: BusinessService,
    LAINNYA: BusinessOther,
};

const url = computed(() => `/analysis-simulation/${props.application.id}/businesses/${props.business.id}`);
const back = () => router.visit(`/analysis-simulation/${props.application.id}`);
</script>

<template>
    <Head :title="`Usaha ${props.business.code}`" />
    <AppLayout>
        <div class="space-y-4" data-testid="business-detail-page">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <Button variant="ghost" size="icon" data-testid="business-back" @click="back">
                        <ArrowLeft class="size-4" />
                    </Button>
                    <div>
                        <p class="text-sm font-semibold">{{ props.business.name }}</p>
                        <p class="text-xs text-muted-foreground">
                            <span class="font-mono">{{ props.business.code }}</span> ·
                            {{ props.application.application_code }} · {{ props.application.full_name }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <Badge variant="secondary" class="font-medium" data-testid="business-type">
                        {{ props.business.type }}
                    </Badge>
                    <Badge variant="default" class="font-medium" data-testid="business-net">
                        Tersimpan {{ rupiah(props.business.net_profit) }}
                    </Badge>
                </div>
            </div>

            <component
                :is="FORMS[props.business.type]"
                :url="url"
                :business="props.business"
                :options="props.options"
            />

            <p v-if="props.business.updated_at" class="text-xs text-muted-foreground">
                Terakhir disimpan {{ props.business.updated_at }} oleh {{ props.business.updated_by }}
            </p>
        </div>
    </AppLayout>
</template>
