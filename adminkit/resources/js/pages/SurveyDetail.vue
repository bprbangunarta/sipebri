<script setup>
import { computed, ref } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Camera, Images, Loader2, MapPin, Send, Trash2 } from 'lucide-vue-next';
import { notify } from '@/composables/useToast';

import FormActions from '@/components/composite/FormActions.vue';
import AppLayout from '@/components/layout/AppLayout.vue';
import Badge from '@/components/ui/Badge.vue';
import Button from '@/components/ui/Button.vue';
import Card from '@/components/ui/Card.vue';
import CardContent from '@/components/ui/CardContent.vue';
import CardFooter from '@/components/ui/CardFooter.vue';
import CardHeader from '@/components/ui/CardHeader.vue';
import CardTitle from '@/components/ui/CardTitle.vue';
import Dialog from '@/components/ui/Dialog.vue';
import Input from '@/components/ui/Input.vue';
import Label from '@/components/ui/Label.vue';
import Textarea from '@/components/ui/Textarea.vue';
import { ACTION } from '@/constants/labels';
import { rupiah } from '@/constants/committee';

const props = defineProps({
    record: { type: Object, required: true },
    customer: { type: Object, default: null },
    collaterals: { type: Array, default: () => [] },
    photos: { type: Array, default: () => [] },
    survey: { type: Object, default: null },
    maxPhotos: { type: Number, default: 5 },
});

const locked = computed(() => props.record.locked);
const canAddPhoto = computed(() => !locked.value && props.photos.length < props.maxPhotos);

const cameraInput = ref(null);
const galleryInput = ref(null);
const uploading = ref(false);

/** Koordinat wajib: diambil sistem tepat saat foto dipilih. */
const coordinates = () =>
    new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject(new Error('Perangkat tidak mendukung pengambilan koordinat.'));

            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => resolve({ latitude: pos.coords.latitude, longitude: pos.coords.longitude }),
            () => reject(new Error('Izin lokasi ditolak. Aktifkan izin lokasi lalu ulangi.')),
            { enableHighAccuracy: true, timeout: 15000 },
        );
    });

const upload = async (event, source) => {
    const file = event.target.files?.[0];
    event.target.value = '';
    if (!file) return;

    uploading.value = true;

    try {
        const { latitude, longitude } = await coordinates();

        useForm({ photo: file, latitude, longitude, source }).post(
            `/survey-simulation/${props.record.id}/photos`,
            {
                forceFormData: true,
                preserveScroll: true,
                onFinish: () => (uploading.value = false),
            },
        );
    } catch (error) {
        uploading.value = false;
        notify.error(error.message);
    }
};

const photoForm = useForm({});
const removePhoto = (id) =>
    photoForm.delete(`/survey-simulation/${props.record.id}/photos/${id}`, { preserveScroll: true });

const saveForm = useForm({ note: '' });
const submit = () =>
    saveForm.post(`/survey-simulation/${props.record.id}`, { preserveScroll: true });

const showCancel = ref(false);
const cancelForm = useForm({ reason: '' });
const submitCancel = () =>
    cancelForm.post(`/scheduling-simulation/${props.record.id}/cancel`, {
        onSuccess: () => {
            showCancel.value = false;
            router.visit('/survey-simulation');
        },
    });

const mapUrl = (lat, lng) => `https://www.google.com/maps?q=${lat},${lng}`;
</script>

<template>
    <Head :title="`Survei ${props.record.application_code}`" />
    <AppLayout>
        <div class="space-y-4" data-testid="survey-detail-page">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <Button variant="ghost" size="icon" data-testid="survey-back" @click="router.visit('/survey-simulation')">
                        <ArrowLeft class="size-4" />
                    </Button>
                    <div>
                        <p class="font-mono text-sm font-semibold">{{ props.record.application_code }}</p>
                        <p class="text-xs text-muted-foreground">
                            {{ props.record.full_name }} · {{ props.record.nik }}
                        </p>
                    </div>
                </div>
                <Badge :variant="locked ? 'default' : 'secondary'" class="font-medium" data-testid="survey-status">
                    {{ props.record.status }}
                </Badge>
            </div>

            <Card>
                <CardHeader><CardTitle>Data Pengajuan</CardTitle></CardHeader>
                <CardContent class="grid gap-3 text-sm sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-muted-foreground">Produk</p>
                        <p>{{ props.record.product_label ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-muted-foreground">Plafon / Jangka</p>
                        <p class="tabular-nums">
                            {{ rupiah(props.record.requested_amount) }} ·
                            {{ props.record.requested_tenor ? `${props.record.requested_tenor} bln` : '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-muted-foreground">Penggunaan</p>
                        <p>{{ props.record.usage_type ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-muted-foreground">Jadwal Survei</p>
                        <p>{{ props.record.survey_date_label ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-muted-foreground">Kasi Analis</p>
                        <p>{{ props.record.supervisor_name ?? '—' }}</p>
                    </div>
                    <div class="lg:col-span-3">
                        <p class="text-[11px] uppercase tracking-wider text-muted-foreground">Catatan Penjadwalan</p>
                        <p>{{ props.record.schedule_note ?? '—' }}</p>
                    </div>
                    <div class="sm:col-span-2 lg:col-span-2">
                        <p class="text-[11px] uppercase tracking-wider text-muted-foreground">Alamat Pemohon</p>
                        <p>{{ props.customer?.address ?? '—' }}</p>
                        <p class="text-xs text-muted-foreground">{{ props.customer?.region_label ?? '' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-muted-foreground">No. HP</p>
                        <p>{{ props.customer?.phone ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-muted-foreground">Tempat Bekerja</p>
                        <p>{{ props.customer?.employer_name ?? '—' }}</p>
                    </div>
                </CardContent>
            </Card>

            <Card data-testid="survey-collaterals">
                <CardHeader><CardTitle>Data Agunan</CardTitle></CardHeader>
                <CardContent>
                    <div v-if="props.collaterals.length" class="overflow-x-auto rounded-md border">
                        <table class="w-full text-sm">
                            <thead class="bg-muted/40">
                                <tr>
                                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wider text-muted-foreground">Agunan</th>
                                    <th class="px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wider text-muted-foreground">Pemilik</th>
                                    <th class="hidden px-3 py-2 text-left text-[11px] font-medium uppercase tracking-wider text-muted-foreground md:table-cell">Keterangan</th>
                                    <th class="px-3 py-2 text-right text-[11px] font-medium uppercase tracking-wider text-muted-foreground">Taksasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border/60">
                                <tr v-for="row in props.collaterals" :key="row.id">
                                    <td class="whitespace-nowrap px-3 py-2 font-mono text-xs font-semibold">
                                        {{ row.collateral_id ?? `#${row.id}` }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-2">{{ row.owner_name ?? '—' }}</td>
                                    <td class="hidden px-3 py-2 text-xs text-muted-foreground md:table-cell">
                                        {{ row.description ?? '—' }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-2 text-right tabular-nums">
                                        {{ rupiah(row.appraisal_value) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-else class="py-4 text-center text-sm text-muted-foreground">
                        Berkas ini tanpa agunan.
                    </p>
                </CardContent>
            </Card>

            <Card data-testid="survey-result">
                <CardHeader class="flex flex-row flex-wrap items-center justify-between gap-2 space-y-0">
                    <CardTitle>Hasil Survei</CardTitle>
                    <div class="flex items-center gap-2">
                        <Badge v-if="!locked" variant="secondary" class="font-medium" data-testid="survey-draft-badge">
                            Draf
                        </Badge>
                        <Badge variant="secondary" class="font-medium">
                            {{ props.photos.length }}/{{ props.maxPhotos }} foto
                        </Badge>
                    </div>
                </CardHeader>
                <CardContent class="form-dense space-y-3">
                    <div v-if="!locked" class="flex flex-wrap items-center gap-2">
                        <Button
                            size="sm"
                            :disabled="!canAddPhoto || uploading"
                            data-testid="survey-camera"
                            @click="cameraInput.click()"
                        >
                            <Loader2 v-if="uploading" class="size-4 animate-spin" />
                            <Camera v-else class="size-4" /> Ambil Foto
                        </Button>
                        <Button
                            variant="outline"
                            size="sm"
                            :disabled="!canAddPhoto || uploading"
                            data-testid="survey-gallery"
                            @click="galleryInput.click()"
                        >
                            <Images class="size-4" /> Dari Galeri
                        </Button>
                        <span class="text-xs text-muted-foreground">
                            Koordinat lokasi diambil otomatis saat foto dipilih — izin lokasi harus diaktifkan.
                            Foto &amp; catatan masih berstatus draf: bisa ditambah atau dihapus sampai Anda menekan
                            Simpan &amp; Ajukan.
                        </span>
                        <input
                            ref="cameraInput"
                            type="file"
                            accept="image/*"
                            capture="environment"
                            class="hidden"
                            data-testid="survey-camera-input"
                            @change="upload($event, 'KAMERA')"
                        />
                        <input
                            ref="galleryInput"
                            type="file"
                            accept="image/*"
                            class="hidden"
                            data-testid="survey-gallery-input"
                            @change="upload($event, 'GALERI')"
                        />
                    </div>

                    <div v-if="props.photos.length" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="photo in props.photos"
                            :key="photo.id"
                            class="overflow-hidden rounded-md border"
                            :data-testid="`survey-photo-${photo.id}`"
                        >
                            <img :src="photo.url" :alt="`Foto survei ${photo.id}`" class="h-40 w-full object-cover" />
                            <div class="flex items-start justify-between gap-2 p-2">
                                <div class="min-w-0">
                                    <a
                                        :href="mapUrl(photo.latitude, photo.longitude)"
                                        target="_blank"
                                        rel="noopener"
                                        class="flex items-center gap-1 truncate text-xs font-medium text-primary hover:underline"
                                    >
                                        <MapPin class="size-3.5 shrink-0" />
                                        {{ photo.latitude.toFixed(6) }}, {{ photo.longitude.toFixed(6) }}
                                    </a>
                                    <p class="text-[11px] text-muted-foreground">
                                        {{ photo.source }} · {{ photo.created_at }}
                                    </p>
                                </div>
                                <Button
                                    v-if="!locked && !photo.saved"
                                    variant="outline"
                                    size="sm"
                                    class="shrink-0 text-destructive hover:text-destructive"
                                    :data-testid="`survey-photo-delete-${photo.id}`"
                                    @click="removePhoto(photo.id)"
                                >
                                    <Trash2 class="size-4" /> Hapus
                                </Button>
                            </div>
                        </div>
                    </div>
                    <p v-else class="rounded-md border border-dashed p-6 text-center text-sm text-muted-foreground">
                        Belum ada foto lokasi. Minimal satu foto wajib diunggah.
                    </p>

                    <div v-if="!locked" class="space-y-[var(--item-gap)]">
                        <Label for="survey-note">Catatan Hasil Survei</Label>
                        <Textarea
                            id="survey-note"
                            v-model="saveForm.note"
                            rows="3"
                            maxlength="500"
                            placeholder="(Opsional)"
                            data-testid="survey-note"
                        />
                        <p v-if="saveForm.errors.note" class="text-xs font-medium text-destructive">
                            {{ saveForm.errors.note }}
                        </p>
                    </div>

                    <div v-else class="rounded-md border bg-muted/40 p-3">
                        <p class="text-[11px] uppercase tracking-wider text-muted-foreground">Catatan Hasil Survei</p>
                        <p class="text-sm">{{ props.survey?.note ?? '—' }}</p>
                        <p class="mt-1 text-xs text-muted-foreground">
                            Disimpan {{ props.survey?.created_at }} oleh {{ props.survey?.created_by }}
                        </p>
                    </div>
                </CardContent>
                <CardFooter v-if="!locked">
                    <FormActions
                        cancel-testid="survey-cancel"
                        cancel-label="Batal & Minta Jadwal Ulang"
                        submit-testid="survey-save"
                        submit-label="Simpan & Ajukan"
                        submit-busy-label="Menyimpan…"
                        :submit-icon="Send"
                        :processing="saveForm.processing"
                        :disabled="!props.photos.length"
                        @cancel="showCancel = true"
                        @submit="submit"
                    />
                </CardFooter>
            </Card>

            <Dialog :open="showCancel" title="Batalkan Survei" @update:open="showCancel = $event">
                <div class="form-dense space-y-[var(--item-gap)]">
                    <Label for="survey-reason">Alasan Pembatalan <span class="text-destructive">*</span></Label>
                    <Input
                        id="survey-reason"
                        v-model="cancelForm.reason"
                        maxlength="255"
                        
                        data-testid="survey-cancel-reason"
                    />
                    <p v-if="cancelForm.errors.reason" class="text-xs font-medium text-destructive">
                        {{ cancelForm.errors.reason }}
                    </p>
                    <p v-else class="text-xs text-muted-foreground">
                        Berkas dikembalikan ke Kasi Analis untuk dijadwalkan ulang. Histori jadwal sebelumnya tetap tersimpan.
                    </p>
                </div>

                <template #footer>
                    <FormActions
                        cancel-testid="survey-cancel-close"
                        submit-testid="survey-cancel-submit"
                        submit-label="Kirim Permintaan"
                        submit-busy-label="Mengirim…"
                        :submit-icon="Send"
                        :processing="cancelForm.processing"
                        @cancel="showCancel = false"
                        @submit="submitCancel"
                    />
                </template>
            </Dialog>
        </div>
    </AppLayout>
</template>
