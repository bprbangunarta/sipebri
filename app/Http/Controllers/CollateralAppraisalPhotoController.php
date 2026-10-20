<?php

namespace App\Http\Controllers;

use App\Models\AppraisalPhoto;
use App\Models\CollateralAppraisal;
use App\Support\Appraisal\AppraisalGuard;
use App\Support\Appraisal\AppraisalTemplates;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * The photos of an appraisal, kept under the places for photos of its form. Position is never read from a photo: the place of a
 * collateral is a fact of the collateral.
 */
class CollateralAppraisalPhotoController extends Controller
{
    public function store(Request $request, CollateralAppraisal $collateralAppraisal): RedirectResponse
    {
        if ($refused = AppraisalGuard::refuseIfFinal($collateralAppraisal)) {
            return $refused;
        }

        $places = collect(AppraisalTemplates::items(AppraisalTemplates::of($collateralAppraisal)))->where('type', 'photo')->pluck('key')->all();
        $data = $request->validate([
            'item' => ['required', 'string', Rule::in($places)],
            'photo' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ], [], ['photo' => 'foto', 'item' => 'tempat foto']);

        $max = (int) config('credit.max_appraisal_photos');

        if ($collateralAppraisal->photos()->where('item', $data['item'])->count() >= $max) {
            return back()->with('error', "Maksimal {$max} foto untuk tiap butir.");
        }

        $collateralAppraisal->photos()->create([
            'item' => $data['item'],
            'path' => AppraisalPhoto::disk()->putFile("appraisals/{$collateralAppraisal->collateral_id}/{$collateralAppraisal->id}", $request->file('photo')),
            'created_by' => $request->user()->name,
        ]);

        return back()->with('success', 'Foto berhasil disimpan.');
    }

    public function destroy(CollateralAppraisal $collateralAppraisal, AppraisalPhoto $photo): RedirectResponse
    {
        abort_unless($photo->collateral_appraisal_id === $collateralAppraisal->id, 404);

        if ($refused = AppraisalGuard::refuseIfFinal($collateralAppraisal)) {
            return $refused;
        }

        AppraisalPhoto::disk()->delete($photo->path);
        $photo->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}
