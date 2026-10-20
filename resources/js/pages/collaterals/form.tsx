import { Head, router, useForm } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { DatePicker } from '@/components/ui/date-picker';
import { CurrencyInput } from '@/components/ui/currency-input';
import { Field } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { MaskedInput } from '@/components/ui/masked-input';
import { Card, CardBody, CardHeader, PageHeader } from '@/components/ui/misc';

type Option = { value: string; label: string };
type Collateral = {
    id: number;
    cbs_id: string | null;
    credit_account: string | null;
    collateral_type_code: string;
    binding_type_code: string | null;
    document_number: string | null;
    description: string | null;
    owner_name: string | null;
    owner_address: string | null;
    region_code: string | null;
    guarantee_value: number;
    adjustment_value: number;
    fair_value: number;
    njop_value: number;
    appraisal_value: number;
    independent_value: number;
    independent_name: string | null;
    independent_at: string | null;
    condition_code: string | null;
    condition_date: string | null;
    insurance_code: string;
    insurance_date: string | null;
    ppap_code: string;
} & Partial<Record<LandField, string | number | null>>;

type Props = {
    collateral: Collateral | null;
    options: {
        types: Option[];
        bindings: Option[];
        conditions: Option[];
        methods: Option[];
        regions: Option[];
        rightKinds: Option[];
        landTypes: string[];
        vehicleTypes: string[];
        vehicleKinds: Option[];
        transmissions: Option[];
    };
};

/** Facts about the land and its building that every appraisal reads; they do not go to core banking. */
const LAND_TEXT = [
    ['province', 'Provinsi', 60],
    ['route_hint', 'Patokan/rute menuju lokasi', 255],
    ['right_holder_name', 'Nama pemegang hak', 100],
    ['survey_document', 'Surat ukur/gambar situasi', 100],
    ['land_office', 'Kantor pertanahan', 100],
    ['parcel_number', 'Nomor identifikasi bidang (NIB)', 50],
    ['acquisition_origin', 'Asal perolehan/akta', 150],
    ['tax_object_number', 'NOP PBB', 30],
    ['building_permit_number', 'Nomor PBG/IMB', 60],
    ['operating_certificate_number', 'Nomor SLF', 60],
    ['permitted_use', 'Peruntukan dalam izin', 100],
] as const;
const LAND_DATES = [
    ['right_issued_at', 'Tanggal terbit hak', 'past'],
    ['right_expires_at', 'Tanggal berakhir hak', 'future'],
    ['survey_document_date', 'Tanggal surat ukur', 'past'],
    ['building_permit_date', 'Tanggal PBG/IMB', 'past'],
    ['operating_certificate_date', 'Tanggal SLF', 'past'],
] as const;
const LAND_NUMBERS = [
    ['certificate_land_area', 'Luas menurut sertipikat (m²)'],
    ['permitted_building_area', 'Luas bangunan menurut izin (m²)'],
] as const;

/** Facts of a motorcycle or a car that every appraisal reads; they do not go to core banking. */
const VEHICLE_TEXT = [
    ['vehicle_brand', 'Merek', 60],
    ['vehicle_type', 'Tipe/varian', 80],
    ['vehicle_model', 'Jenis/model', 60],
    ['vehicle_color', 'Warna', 40],
    ['plate_number', 'Nomor polisi', 20],
    ['chassis_number', 'Nomor rangka (VIN)', 40],
    ['engine_number', 'Nomor mesin', 40],
    ['fuel', 'Bahan bakar', 30],
    ['drive', 'Penggerak', 30],
    ['unit_number', 'Nomor unit/armada', 40],
    ['bpkb_number', 'Nomor BPKB', 40],
    ['bpkb_name', 'Nama pada BPKB', 100],
    ['bpkb_address', 'Alamat pada BPKB', 255],
    ['stnk_number', 'Nomor STNK', 40],
    ['stnk_name', 'Nama pada STNK', 100],
] as const;
const VEHICLE_NUMBERS = [
    ['manufacture_year', 'Tahun pembuatan', 4],
    ['registration_year', 'Tahun registrasi', 4],
    ['engine_capacity', 'Isi silinder (cc)', 5],
] as const;

type LandField =
    | (typeof LAND_TEXT)[number][0]
    | (typeof LAND_DATES)[number][0]
    | (typeof LAND_NUMBERS)[number][0]
    | (typeof VEHICLE_TEXT)[number][0]
    | (typeof VEHICLE_NUMBERS)[number][0]
    | 'right_kind'
    | 'postal_code'
    | 'vehicle_kind'
    | 'transmission'
    | 'bpkb_date';

const VALUES = [
    ['guarantee_value', 'Nilai jaminan'],
    ['adjustment_value', 'Nilai penyesuaian'],
    ['fair_value', 'Nilai wajar'],
    ['njop_value', 'Nilai NJOP'],
    ['appraisal_value', 'Nilai taksasi'],
    ['independent_value', 'Nilai independen'],
] as const;

function Section({ title, children }: { title: string; children: ReactNode }) {
    return (
        <Card>
            <CardHeader title={title} />
            <CardBody className="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">{children}</CardBody>
        </Card>
    );
}

export default function CollateralForm({ collateral, options }: Props) {
    const isEdit = collateral !== null;
    const form = useForm({
        cbs_id: collateral?.cbs_id ?? '',
        credit_account: collateral?.credit_account ?? '',
        collateral_type_code: collateral?.collateral_type_code ?? '',
        binding_type_code: collateral?.binding_type_code ?? '',
        document_number: collateral?.document_number ?? '',
        description: collateral?.description ?? '',
        owner_name: collateral?.owner_name ?? '',
        owner_address: collateral?.owner_address ?? '',
        region_code: collateral?.region_code ?? '',
        guarantee_value: collateral?.guarantee_value ?? (0 as number | null),
        adjustment_value: collateral?.adjustment_value ?? (0 as number | null),
        fair_value: collateral?.fair_value ?? (0 as number | null),
        njop_value: collateral?.njop_value ?? (0 as number | null),
        appraisal_value: collateral?.appraisal_value ?? (0 as number | null),
        independent_value: collateral?.independent_value ?? (0 as number | null),
        independent_name: collateral?.independent_name ?? '',
        independent_at: collateral?.independent_at ?? '',
        condition_code: collateral?.condition_code ?? '',
        condition_date: collateral?.condition_date ?? '',
        insurance_code: collateral?.insurance_code ?? 'T',
        insurance_date: collateral?.insurance_date ?? '',
        ppap_code: collateral?.ppap_code ?? '1',
        ...(Object.fromEntries(
            [
                'postal_code',
                'right_kind',
                'vehicle_kind',
                'transmission',
                'bpkb_date',
                ...[...LAND_TEXT, ...LAND_DATES, ...LAND_NUMBERS, ...VEHICLE_TEXT, ...VEHICLE_NUMBERS].map(([name]) => name),
            ].map((name) => [name, String(collateral?.[name as LandField] ?? '')]),
        ) as Record<LandField, string>),
    });
    const { data, setData, errors } = form;
    const text = (name: keyof typeof data, upper = false) => ({
        id: name,
        className: upper ? 'uppercase' : undefined,
        value: data[name] as string,
        onChange: (e: React.ChangeEvent<HTMLInputElement>) => setData(name, e.target.value as never),
        'aria-invalid': !!errors[name],
    });
    const isLand = options.landTypes.includes(data.collateral_type_code);
    const isVehicle = options.vehicleTypes.includes(data.collateral_type_code);
    const today = new Date();

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        if (isEdit) {
            form.put(`/collaterals/${collateral.id}`);
        } else {
            form.post('/collaterals');
        }
    };

    return (
        <>
            <Head title={isEdit ? 'Ubah jaminan' : 'Tambah jaminan'} />
            <PageHeader
                actions={
                    isEdit && (
                        <Button variant="outline" onClick={() => router.visit(`/collaterals/${collateral.id}/appraisals`)}>
                            Riwayat penilaian
                        </Button>
                    )
                }
                title={isEdit ? `Ubah ${collateral.cbs_id ?? `jaminan #${collateral.id}`}` : 'Tambah jaminan'}
                description={
                    isEdit ? 'Kondisi, asuransi, dan taksasi wajib diisi pada tahap ini. Penilai tercatat atas nama Anda.' : 'Kolom bertanda * wajib diisi'
                }
            />

            <form onSubmit={submit} className="flex flex-col gap-3" noValidate>
                <Section title="Jaminan">
                    <Field label="ID agunan" error={errors.cbs_id} hint="ID core banking, bila sudah ada">
                        <Input {...text('cbs_id', true)} maxLength={50} />
                    </Field>
                    <Field label="Rekening kredit" error={errors.credit_account}>
                        <Input {...text('credit_account')} maxLength={30} />
                    </Field>
                    <Field label="Jenis agunan" required error={errors.collateral_type_code}>
                        <Combobox
                            id="collateral_type_code"
                            options={options.types}
                            value={data.collateral_type_code}
                            onChange={(v) => setData('collateral_type_code', v ?? '')}
                            invalid={!!errors.collateral_type_code}
                        />
                    </Field>
                    <Field label="Jenis pengikatan" error={errors.binding_type_code}>
                        <Combobox
                            id="binding_type_code"
                            clearable
                            options={options.bindings}
                            value={data.binding_type_code}
                            onChange={(v) => setData('binding_type_code', v ?? '')}
                        />
                    </Field>
                    <Field label="Nomor dokumen" required error={errors.document_number} className="lg:col-span-2">
                        <Input {...text('document_number', true)} maxLength={100} />
                    </Field>
                    <Field label="Keterangan" required error={errors.description} className="lg:col-span-2">
                        <Input {...text('description', true)} maxLength={255} />
                    </Field>
                </Section>

                <Section title="Pemilik & lokasi">
                    <Field label="Nama pemilik" required error={errors.owner_name} className="lg:col-span-2">
                        <Input {...text('owner_name', true)} maxLength={100} />
                    </Field>
                    <Field label="Lokasi (kabupaten/kota)" required error={errors.region_code} className="lg:col-span-2">
                        <Combobox
                            id="region_code"
                            options={options.regions}
                            value={data.region_code}
                            onChange={(v) => setData('region_code', v ?? '')}
                            invalid={!!errors.region_code}
                        />
                    </Field>
                    <Field label="Alamat agunan" required error={errors.owner_address} className="sm:col-span-2 lg:col-span-4">
                        <Input {...text('owner_address', true)} maxLength={255} />
                    </Field>
                </Section>

                {isLand && (
                    <Section title="Data tanah dan bangunan">
                        <Field label="Jenis hak" error={errors.right_kind}>
                            <Combobox
                                id="right_kind"
                                clearable
                                searchable={false}
                                options={options.rightKinds}
                                value={data.right_kind || null}
                                onChange={(v) => setData('right_kind', v ?? '')}
                            />
                        </Field>
                        <Field label="Kode pos" error={errors.postal_code}>
                            <MaskedInput id="postal_code" mask="99999" value={data.postal_code} onValueChange={(v) => setData('postal_code', v)} />
                        </Field>
                        {LAND_TEXT.map(([name, label, max]) => (
                            <Field key={name} label={label} error={errors[name]} className={max > 100 ? 'sm:col-span-2' : undefined}>
                                <Input {...text(name)} maxLength={max} />
                            </Field>
                        ))}
                        {LAND_NUMBERS.map(([name, label]) => (
                            <Field key={name} label={label} error={errors[name]}>
                                <MaskedInput
                                    id={name}
                                    mask="99999999"
                                    value={data[name]}
                                    onValueChange={(v) => setData(name, v)}
                                    className="text-right tabular-nums"
                                />
                            </Field>
                        ))}
                        {LAND_DATES.map(([name, label, when]) => (
                            <Field key={name} label={label} error={errors[name]}>
                                <DatePicker
                                    id={name}
                                    min={new Date(1950, 0, 1)}
                                    max={when === 'past' ? today : new Date(2100, 11, 31)}
                                    value={data[name]}
                                    onChange={(v) => setData(name, v)}
                                    invalid={!!errors[name]}
                                />
                            </Field>
                        ))}
                    </Section>
                )}

                {isVehicle && (
                    <Section title="Data kendaraan">
                        <Field label="Jenis kendaraan" error={errors.vehicle_kind} hint="Menentukan formulir penilaian: roda 2 atau roda 4.">
                            <Combobox
                                id="vehicle_kind"
                                clearable
                                searchable={false}
                                options={options.vehicleKinds}
                                value={data.vehicle_kind || null}
                                onChange={(v) => setData('vehicle_kind', v ?? '')}
                            />
                        </Field>
                        <Field label="Transmisi" error={errors.transmission}>
                            <Combobox
                                id="transmission"
                                clearable
                                searchable={false}
                                options={options.transmissions}
                                value={data.transmission || null}
                                onChange={(v) => setData('transmission', v ?? '')}
                            />
                        </Field>
                        {VEHICLE_TEXT.map(([name, label, max]) => (
                            <Field key={name} label={label} error={errors[name]} className={max > 100 ? 'sm:col-span-2' : undefined}>
                                <Input {...text(name, name === 'plate_number' || name === 'chassis_number' || name === 'engine_number')} maxLength={max} />
                            </Field>
                        ))}
                        {VEHICLE_NUMBERS.map(([name, label, digits]) => (
                            <Field key={name} label={label} error={errors[name]}>
                                <MaskedInput
                                    id={name}
                                    mask={'9'.repeat(digits)}
                                    value={data[name]}
                                    onValueChange={(v) => setData(name, v)}
                                    className="text-right tabular-nums"
                                />
                            </Field>
                        ))}
                        <Field label="Tanggal BPKB" error={errors.bpkb_date}>
                            <DatePicker
                                id="bpkb_date"
                                min={new Date(1950, 0, 1)}
                                max={today}
                                value={data.bpkb_date}
                                onChange={(v) => setData('bpkb_date', v)}
                                invalid={!!errors.bpkb_date}
                            />
                        </Field>
                    </Section>
                )}

                <Section title="Nilai">
                    {VALUES.map(([name, label]) => (
                        <Field key={name} label={label} error={errors[name]}>
                            <CurrencyInput id={name} value={data[name]} onValueChange={(v) => setData(name, v)} aria-invalid={!!errors[name]} />
                        </Field>
                    ))}
                    <Field label="Penilai independen" error={errors.independent_name}>
                        <Input {...text('independent_name', true)} maxLength={100} />
                    </Field>
                    <Field label="Tanggal penilaian independen" error={errors.independent_at}>
                        <DatePicker
                            id="independent_at"
                            min={new Date(2000, 0, 1)}
                            max={today}
                            value={data.independent_at}
                            onChange={(v) => setData('independent_at', v)}
                            invalid={!!errors.independent_at}
                        />
                    </Field>
                </Section>

                <Section title="Kondisi & asuransi">
                    <Field label="Kondisi" required={isEdit} error={errors.condition_code}>
                        <Combobox
                            id="condition_code"
                            clearable={!isEdit}
                            options={options.conditions}
                            value={data.condition_code}
                            onChange={(v) => setData('condition_code', v ?? '')}
                            invalid={!!errors.condition_code}
                        />
                    </Field>
                    <Field label="Tanggal kondisi" required={isEdit} error={errors.condition_date}>
                        <DatePicker
                            id="condition_date"
                            min={new Date(2000, 0, 1)}
                            max={today}
                            value={data.condition_date}
                            onChange={(v) => setData('condition_date', v)}
                            invalid={!!errors.condition_date}
                        />
                    </Field>
                    <Field label="Diasuransikan" required={isEdit} error={errors.insurance_code}>
                        <Combobox
                            id="insurance_code"
                            searchable={false}
                            options={[
                                { value: 'Y', label: 'Ya' },
                                { value: 'T', label: 'Tidak' },
                            ]}
                            value={data.insurance_code}
                            onChange={(v) => setData('insurance_code', v ?? 'T')}
                        />
                    </Field>
                    <Field label="Tanggal mulai asuransi" required={isEdit} error={errors.insurance_date}>
                        <DatePicker
                            id="insurance_date"
                            min={new Date(2000, 0, 1)}
                            max={new Date(today.getFullYear() + 5, 11, 31)}
                            value={data.insurance_date}
                            onChange={(v) => setData('insurance_date', v)}
                            invalid={!!errors.insurance_date}
                        />
                    </Field>
                    <Field label="Metode penilaian" error={errors.ppap_code}>
                        <Combobox
                            id="ppap_code"
                            searchable={false}
                            options={options.methods}
                            value={data.ppap_code}
                            onChange={(v) => setData('ppap_code', v ?? '1')}
                        />
                    </Field>
                </Section>

                <div className="flex items-center justify-between gap-2">
                    <Button variant="outline" onClick={() => router.visit('/collaterals')}>
                        Batal
                    </Button>
                    <Button type="submit" loading={form.processing}>
                        {isEdit ? 'Simpan perubahan' : 'Buat jaminan'}
                    </Button>
                </div>
            </form>
        </>
    );
}
