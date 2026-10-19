<?php

namespace App\Http\Controllers;

use App\Support\ReferenceImport\Importer;
use App\Support\ReferenceImport\ImportRefused;
use App\Support\ReferenceImport\Template;
use App\Support\ReferenceResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Excel import for the reference tables that opt in with `import` in config/references.php.
 */
class ReferenceImportController extends Controller
{
    private const MAX_KILOBYTES = 2048;

    private const SHOWN_ISSUES = 50;

    public function template(string $resource, Template $template): BinaryFileResponse
    {
        $definition = $this->importable($resource);

        return response()->download($template->build($definition), "template-{$resource}.xlsx")->deleteFileAfterSend();
    }

    public function store(Request $request, string $resource, Importer $importer): RedirectResponse
    {
        $definition = $this->importable($resource);
        $request->validate(['file' => ['required', 'file', 'max:'.self::MAX_KILOBYTES, 'extensions:xlsx']], [], ['file' => 'berkas']);

        try {
            $result = $importer->run($definition, (string) $request->file('file')->getRealPath());
        } catch (ImportRefused $refused) {
            $shown = array_slice($refused->issues, 0, self::SHOWN_ISSUES);
            $more = count($refused->issues) - count($shown);

            $back = back()->withErrors(['file' => $refused->getMessage()]);

            return $shown === [] ? $back : $back->with('issues', $more > 0 ? [...$shown, "Dan $more masalah lain."] : $shown);
        }

        $noun = mb_strtolower($definition->label());

        return back()->with('success', sprintf('%d %s dibuat, %d diperbarui, %d tidak berubah.', $result['created'], $noun, $result['updated'], $result['unchanged']));
    }

    private function importable(string $resource): ReferenceResource
    {
        $definition = ReferenceResource::find($resource);
        abort_unless($definition->importable(), 404);

        return $definition;
    }
}
