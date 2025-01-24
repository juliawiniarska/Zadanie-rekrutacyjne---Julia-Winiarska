<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;
use ZipArchive;
use Illuminate\Support\Facades\Log;

class ModuleController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:background,typo',
            'width' => 'required|numeric|min:1|max:100',
            'height' => 'required|numeric|min:1|max:100',
            'link' => 'required|url',
            'positionX' => 'required|numeric|min:0|max:100',
            'positionY' => 'required|numeric|min:0|max:100',
        ]);

        if ($validated['type'] === 'background') {
            $backgroundValidated = $request->validate([
                'color' => 'required|regex:/^#([A-Fa-f0-9]{6})$/',
            ]);
            $validated = array_merge($validated, $backgroundValidated);
            $validated['content'] = null;
        }

        if ($validated['type'] === 'typo') {
            $typoValidated = $request->validate([
                'content' => 'nullable|string',
            ]);
            $validated = array_merge($validated, $typoValidated);
            $validated['content'] = $request->input('content', 'Zadanie rekrutacyjne-Julia-Winiarska');
            $validated['color'] = null;
        }

        $module = Module::create($validated);

        return response()->json(['id' => $module->id], 201);
    }

    public function download($id)
    {
        $module = Module::find($id);

        if (!$module) {
            return response()->json(['error' => 'Module not found'], 404);
        }

        $tempDir = storage_path('app/temp_modules/' . $module->id);

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        $htmlPath = $tempDir . '/index.html';
        $cssPath = $tempDir . '/style.css';
        $jsPath = $tempDir . '/script.js';
        $zipPath = $tempDir . '/module.zip';

        if ($module->type === 'background') {
            file_put_contents($htmlPath, $this->generateBackgroundHtml($module));
            file_put_contents($cssPath, $this->generateBackgroundCss($module));
            file_put_contents($jsPath, $this->generateBackgroundJs($module));
        } elseif ($module->type === 'typo') {
            file_put_contents($htmlPath, $this->generateTypoHtml($module));
            file_put_contents($cssPath, $this->generateTypoCss($module));
            file_put_contents($jsPath, $this->generateTypoJs($module));
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $zip->addFile($htmlPath, 'index.html');
            $zip->addFile($cssPath, 'style.css');
            $zip->addFile($jsPath, 'script.js');
            $zip->close();
        } else {
            return response()->json(['error' => 'Could not create ZIP file'], 500);
        }

        return response()->download($zipPath, 'module.zip')->deleteFileAfterSend(true);
    }

    private function generateBackgroundHtml($module)
    {
        return <<<HTML
        
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Background Module</title>
            <link rel="stylesheet" href="style.css">
            <script defer src="script.js"></script>
        </head>
        <body>
            <div class="container">
                <div id="background-module"></div>
            </div>
        </body>
        </html>
        HTML;
    }

    private function generateBackgroundCss($module)
    {
        return <<<CSS

        .container {
        position: relative;
        width: 320px;
        height: 480px;
        border: solid #3E454B 2px;
        }

        #background-module {
        position: absolute;
        top: {$module->positionY}%;
        left: {$module->positionX}%;
        width: {$module->width}%;
        height: {$module->height}%;
        background-color: {$module->color};
        cursor: pointer;
        }
        CSS;
    }

    private function generateBackgroundJs($module)
    {
        return <<<JS
        document.querySelector("#background-module").addEventListener("click", () => {
        window.open("{$module->link}");
        });
        JS;
    }

    private function generateTypoHtml($module)
    {
        return <<<HTML

        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Typo Module</title>
            <link rel="stylesheet" href="style.css">
            <script defer src="script.js"></script>
        </head>
        <body>
            <div class="container">
                <div id="typo-module">{$module->content}</div>
            </div>
        </body>
        </html>
        HTML;
    }

    private function generateTypoCss($module)
    {
        return <<<CSS

        .container {
        position: relative;
        width: 320px;
        height: 480px;
        border: solid #3E454B 2px;
        }

        #typo-module {
        position: absolute;
        top: {$module->positionY}%;
        left: {$module->positionX}%;
        width: {$module->width}%;
        height: {$module->height}%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        cursor: pointer;
        }
        CSS;
    }

    private function generateTypoJs($module)
    {
        return <<<JS

        document.querySelector("#typo-module").addEventListener("click", () => {
        window.open("{$module->link}");
        });
        JS;
    }
}