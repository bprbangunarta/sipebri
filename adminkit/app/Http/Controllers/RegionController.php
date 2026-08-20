<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegionController extends ReferenceController
{
    protected function model(): string
    {
        return Region::class;
    }

    protected function slug(): string
    {
        return 'regions';
    }

    protected function label(): string
    {
        return 'Data Wilayah';
    }

    protected function fields(): array
    {
        return [
            ['key' => 'code', 'label' => 'Kode'],
            ['key' => 'regency', 'label' => 'Kabupaten/Kota'],
            ['key' => 'district', 'label' => 'Kecamatan', 'hide_below' => 'md'],
            ['key' => 'village', 'label' => 'Kelurahan/Desa', 'hide_below' => 'lg'],
            ['key' => 'postal_code', 'label' => 'Kode Pos', 'hide_below' => 'xl'],
        ];
    }

    /** Pilihan bertahap untuk form agunan: kabupaten → kecamatan → kelurahan. */
    public function options(Request $request): JsonResponse
    {
        $regency = (string) $request->query('regency', '');
        $district = (string) $request->query('district', '');

        if ($regency === '') {
            $rows = Region::query()
                ->select('regency')
                ->distinct()
                ->orderBy('regency')
                ->pluck('regency')
                ->map(fn ($r) => ['value' => $r, 'label' => $r]);

            return response()->json(['level' => 'regency', 'options' => $rows]);
        }

        if ($district === '') {
            $rows = Region::query()
                ->where('regency', $regency)
                ->select('district')
                ->distinct()
                ->orderBy('district')
                ->pluck('district')
                ->map(fn ($d) => ['value' => $d, 'label' => $d]);

            return response()->json(['level' => 'district', 'options' => $rows]);
        }

        $rows = Region::query()
            ->where('regency', $regency)
            ->where('district', $district)
            ->orderBy('village')
            ->get(['id', 'code', 'village', 'postal_code'])
            ->map(fn (Region $r) => [
                'value' => (string) $r->id,
                'label' => $r->postal_code ? "{$r->village} ({$r->postal_code})" : $r->village,
                'code' => $r->code,
                'village' => $r->village,
            ]);

        return response()->json(['level' => 'village', 'options' => $rows]);
    }
}
