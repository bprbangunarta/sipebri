<script setup>
import { computed } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Loader2, Save } from 'lucide-vue-next';

import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardFooter from '@/components/ui/CardFooter.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Checkbox from '@/components/ui/Checkbox.vue';
import Combobox from '@/components/ui/Combobox.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import Switch from '@/components/ui/Switch.vue';
import { ACTION } from '@/constants/labels';
import { digitsOnly, rupiah } from '@/constants/committee';

/** Parameter produk (SK Direksi) — hanya acuan, petugas tetap bisa mengubah saat transaksi. */
const props = defineProps({
    product: { type: Object, required: true },
    parameter: { type: Object, default: null },
    methodOptions: { type: Array, default: () => [] },
    installmentOptions: { type: Array, default: () => [] },
});

const page = usePage();
const canManage = computed(() =>
    (page.props.auth?.user?.permissions ?? []).includes('products.manage'),
);

const form = useForm({
    min_amount: props.parameter?.min_amount ?? '',
    max_amount: props.parameter?.max_amount ?? '',
    min_tenor: props.parameter?.min_tenor ?? '',
    max_tenor: props.parameter?.max_tenor ?? '',
    interest_rate: props.parameter?.interest_rate ?? '',
    provision_rate: props.parameter?.provision_rate ?? '',
    admin_rate: props.parameter?.admin_rate ?? '',
    rc_threshold: props.parameter?.rc_threshold ?? '',
    default_method_id: props.parameter?.default_method_id ? String(props.parameter.default_method_id) : '',
    default_installment_id: props.parameter?.default_installment_id
        ? String(props.parameter.default_installment_id)
        : '',
    allowed_method_ids: props.parameter?.allowed_method_ids ?? [],
    allowed_installment_ids: props.parameter?.allowed_installment_ids ?? [],
    collateral_required: props.parameter?.collateral_required ?? false,
    decree: props.parameter?.decree ?? '',
    note: props.parameter?.note ?? '',
});

const toggle = (field, id) => {
    const list = form[field].map(Number);
    form[field] = list.includes(Number(id)) ? list.filter((v) => v !== Number(id)) : [...list, Number(id)];
};

const isChecked = (field, id) => form[field].map(Number).includes(Number(id));

const methodChoices = computed(() =>
    form.allowed_method_ids.length
        ? props.methodOptions.filter((o) => isChecked('allowed_method_ids', o.value))
        : props.methodOptions,
);
const installmentChoices = computed(() =>
    form.allowed_installment_ids.length
        ? props.installmentOptions.filter((o) => isChecked('allowed_installment_ids', o.value))
        : props.installmentOptions,
);

const asOptions = (list) => list.map((o) => ({ value: String(o.value), label: o.label }));

const submit = () => form.put(`/products/${props.product.id}/parameters`, { preserveScroll: true });
</script>

<template>
    <Head :title="`Parameter ${props.product.alias}`" />
    <AppLayout>
        <form class="form-dense space-y-6" novalidate data-testid="product-parameter-page" @submit.prevent="submit">
            <fieldset :disabled="!canManage" class="space-y-6 disabled:opacity-95">
            <Card>
                <CardHeader class="flex flex-row flex-wrap items-center justify-between gap-2 space-y-0">
                    <CardTitle class="flex min-w-0 items-center gap-2">
                        <Button
                            variant="ghost"
                            size="icon"
                            type="button"
                            data-testid="product-parameter-back"
                            @click="router.visit('/products')"
                        >
                            <ArrowLeft class="size-4" />
                        </Button>
                        <span class="truncate">{{ props.product.alias }} — {{ props.product.name }}</span>
                    </CardTitle>
                    <div class="flex items-center gap-2">
                        <Badge variant="secondary" class="font-medium">Kode {{ props.product.code }}</Badge>
                        <Badge :variant="props.parameter ? 'secondary' : 'destructive'" class="font-medium">
                            {{ props.parameter ? 'Parameter Tersimpan' : 'Belum Diatur' }}
                        </Badge>
                    </div>
                </CardHeader>
                <CardContent class="space-y-3">
                    <p class="text-sm font-medium">Plafon &amp; Tenor</p>
                    <div class="grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-4">
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="p-min">Plafon Minimal</Label>
                        <Input
                            id="p-min"
                            :model-value="form.min_amount"
                            inputmode="numeric"
                            data-testid="param-min-amount"
                            @input="form.min_amount = digitsOnly($event.target.value)"
                        />
                        <p class="text-xs text-muted-foreground">{{ rupiah(form.min_amount || null) }}</p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="p-max">Plafon Maksimal</Label>
                        <Input
                            id="p-max"
                            :model-value="form.max_amount"
                            inputmode="numeric"
                            data-testid="param-max-amount"
                            @input="form.max_amount = digitsOnly($event.target.value)"
                        />
                        <p class="text-xs text-muted-foreground">{{ rupiah(form.max_amount || null) }}</p>
                        <p v-if="form.errors.max_amount" class="text-xs font-medium text-destructive" data-testid="param-max-amount-error">
                            {{ form.errors.max_amount }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="p-min-tenor">Tenor Minimal (Bulan)</Label>
                        <Input
                            id="p-min-tenor"
                            :model-value="form.min_tenor"
                            inputmode="numeric"
                            data-testid="param-min-tenor"
                            @input="form.min_tenor = digitsOnly($event.target.value)"
                        />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="p-max-tenor">Tenor Maksimal (Bulan)</Label>
                        <Input
                            id="p-max-tenor"
                            :model-value="form.max_tenor"
                            inputmode="numeric"
                            data-testid="param-max-tenor"
                            @input="form.max_tenor = digitsOnly($event.target.value)"
                        />
                        <p v-if="form.errors.max_tenor" class="text-xs font-medium text-destructive" data-testid="param-max-tenor-error">
                            {{ form.errors.max_tenor }}
                        </p>
                    </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Bunga, Biaya &amp; Kelayakan</CardTitle></CardHeader>
                <CardContent class="grid gap-[var(--field-gap)] sm:grid-cols-2 lg:grid-cols-4">
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="p-interest">Suku Bunga (%)</Label>
                        <Input id="p-interest" v-model="form.interest_rate" inputmode="decimal" data-testid="param-interest" />
                        <p v-if="form.errors.interest_rate" class="text-xs font-medium text-destructive">
                            {{ form.errors.interest_rate }}
                        </p>
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="p-provision">Provisi (%)</Label>
                        <Input id="p-provision" v-model="form.provision_rate" inputmode="decimal" data-testid="param-provision" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="p-admin">Biaya Admin (%)</Label>
                        <Input id="p-admin" v-model="form.admin_rate" inputmode="decimal" data-testid="param-admin" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="p-rc">Ambang RC Maksimal (%)</Label>
                        <Input id="p-rc" v-model="form.rc_threshold" inputmode="decimal" data-testid="param-rc" />
                        <p v-if="form.errors.rc_threshold" class="text-xs font-medium text-destructive">
                            {{ form.errors.rc_threshold }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Metode Bunga &amp; Pola Cicilan</CardTitle></CardHeader>
                <CardContent class="grid items-start gap-x-8 gap-y-[var(--field-gap)] lg:grid-cols-2">
                    <div class="space-y-[var(--field-gap)]">
                        <div class="space-y-[var(--item-gap)]">
                            <div class="flex items-baseline justify-between gap-2">
                                <Label>Metode Bunga Diizinkan</Label>
                                <button
                                    type="button"
                                    class="text-[11px] font-medium text-muted-foreground underline-offset-2 transition-colors hover:text-foreground hover:underline"
                                    data-testid="param-method-clear"
                                    @click="form.allowed_method_ids = []"
                                >
                                    {{ form.allowed_method_ids.length ? `${form.allowed_method_ids.length} dipilih · Bersihkan` : 'Semua diizinkan' }}
                                </button>
                            </div>
                            <div class="h-[168px] overflow-y-auto rounded-md border">
                                <label
                                    v-for="(option, index) in props.methodOptions"
                                    :key="option.value"
                                    class="flex h-8 cursor-pointer items-center gap-2.5 px-3 text-sm normal-case tracking-normal transition-colors hover:bg-muted/50"
                                    :class="index ? 'border-t' : ''"
                                >
                                    <Checkbox
                                        :model-value="isChecked('allowed_method_ids', option.value)"
                                        :data-testid="`param-method-${option.value}`"
                                        @update:model-value="toggle('allowed_method_ids', option.value)"
                                    />
                                    <span class="truncate">{{ option.label }}</span>
                                </label>
                            </div>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label>Metode Bunga Bawaan</Label>
                            <Combobox
                                v-model="form.default_method_id"
                                :options="asOptions(methodChoices)"
                                placeholder="Pilih metode"
                                data-testid="param-default-method"
                            />
                            <p v-if="form.errors.default_method_id" class="text-xs font-medium text-destructive" data-testid="param-default-method-error">
                                {{ form.errors.default_method_id }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-[var(--field-gap)]">
                        <div class="space-y-[var(--item-gap)]">
                            <div class="flex items-baseline justify-between gap-2">
                                <Label>Pola Cicilan Diizinkan</Label>
                                <button
                                    type="button"
                                    class="text-[11px] font-medium text-muted-foreground underline-offset-2 transition-colors hover:text-foreground hover:underline"
                                    data-testid="param-installment-clear"
                                    @click="form.allowed_installment_ids = []"
                                >
                                    {{ form.allowed_installment_ids.length ? `${form.allowed_installment_ids.length} dipilih · Bersihkan` : 'Semua diizinkan' }}
                                </button>
                            </div>
                            <div class="h-[168px] overflow-y-auto rounded-md border">
                                <label
                                    v-for="(option, index) in props.installmentOptions"
                                    :key="option.value"
                                    class="flex h-8 cursor-pointer items-center gap-2.5 px-3 text-sm normal-case tracking-normal transition-colors hover:bg-muted/50"
                                    :class="index ? 'border-t' : ''"
                                >
                                    <Checkbox
                                        :model-value="isChecked('allowed_installment_ids', option.value)"
                                        :data-testid="`param-installment-${option.value}`"
                                        @update:model-value="toggle('allowed_installment_ids', option.value)"
                                    />
                                    <span class="truncate">{{ option.label }}</span>
                                </label>
                            </div>
                        </div>
                        <div class="space-y-[var(--item-gap)]">
                            <Label>Pola Cicilan Bawaan</Label>
                            <Combobox
                                v-model="form.default_installment_id"
                                :options="asOptions(installmentChoices)"
                                placeholder="Pilih pola cicilan"
                                data-testid="param-default-installment"
                            />
                            <p v-if="form.errors.default_installment_id" class="text-xs font-medium text-destructive">
                                {{ form.errors.default_installment_id }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader><CardTitle>Ketentuan Lain</CardTitle></CardHeader>
                <CardContent class="grid gap-[var(--field-gap)] sm:grid-cols-2">
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="p-decree">Nomor SK Direksi</Label>
                        <Input id="p-decree" v-model="form.decree" maxlength="100" data-testid="param-decree" />
                    </div>
                    <div class="space-y-[var(--item-gap)]">
                        <Label for="p-note">Catatan</Label>
                        <Input id="p-note" v-model="form.note" maxlength="255" data-testid="param-note" />
                    </div>
                    <label class="flex h-8 items-center justify-between gap-3 rounded-md border px-3 sm:col-span-2">
                        <span class="text-sm">Wajib Agunan</span>
                        <Switch v-model="form.collateral_required" data-testid="param-collateral" />
                    </label>
                </CardContent>
                <CardFooter class="justify-between">
                    <p class="text-xs text-muted-foreground">
                        Nilai di atas hanya acuan — petugas tetap dapat mengubahnya saat transaksi.
                    </p>
                    <Button
                        v-if="canManage"
                        size="sm"
                        type="submit"
                        :disabled="form.processing"
                        data-testid="param-save"
                    >
                        <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                        <Save v-else class="size-4" />
                        {{ form.processing ? ACTION.saving : ACTION.save }}
                    </Button>
                </CardFooter>
            </Card>
            </fieldset>
        </form>
    </AppLayout>
</template>
