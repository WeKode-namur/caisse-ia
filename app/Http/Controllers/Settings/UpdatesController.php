<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UpdatesController extends Controller
{
    public function index()
    {
        return view('panel.settings.updates.index');
    }

    public function getVersions()
    {
        $updatesPath = base_path('updates');
        $files = File::files($updatesPath);
        $checkFrom = config('custom.version.check_from', 'v0.0.1');
        $versions = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'md') {
                $filename = $file->getFilename();
                $version = str_replace('.md', '', $filename);

                if (version_compare($version, $checkFrom, '>=') === false) continue;

                // Extraire la date de modification
                $modifiedAt = date('d/m/Y H:i', $file->getMTime());

                // Lire le contenu pour extraire le titre
                $content = File::get($file->getPathname());
                $lines = explode("\n", $content);
                $title = '';

                // Chercher le premier titre (# ou ##)
                foreach ($lines as $line) {
                    if (preg_match('/^#{1,2}\s+(.+)$/', trim($line), $matches)) {
                        $title = trim($matches[1]);
                        break;
                    }
                }

                // Si pas de titre trouvé, utiliser le nom du fichier
                if (empty($title)) {
                    $title = "Mise à jour " . $version;
                }

                $versions[] = [
                    'version' => $version,
                    'title' => $title,
                    'modified_at' => $modifiedAt,
                    'filename' => $filename
                ];
            }
        }

        // Trier par version (ordre décroissant)
        usort($versions, function ($a, $b) {
            return version_compare($b['version'], $a['version']);
        });

        return response()->json($versions);
    }

    public function getVersionContent(Request $request)
    {
        $version = $request->input('version');
        $filename = $version . '.md';
        $filepath = base_path('updates/' . $filename);

        if (!File::exists($filepath)) {
            return response()->json(['error' => 'Version non trouvée'], 404);
        }

        $content = File::get($filepath);
        $htmlContent = Str::markdown($content);

        return response()->json([
            'version' => $version,
            'content' => $htmlContent
        ]);
    }

    public function searchVersions(Request $request)
    {
        $search = $request->input('search', '');

        $updatesPath = base_path('updates');
        $files = File::files($updatesPath);
        $checkFrom = config('custom.version.check_from', 'v0.0.1');

        $versions = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'md') {
                $filename = $file->getFilename();
                $version = str_replace('.md', '', $filename);

                if (version_compare($version, $checkFrom, '>=') === false) continue;

                // Lire le contenu pour la recherche
                $content = File::get($file->getPathname());

                // Rechercher dans le titre et le contenu
                $searchLower = strtolower($search);
                $contentLower = strtolower($content);
                $versionLower = strtolower($version);

                if (empty($search) ||
                    strpos($contentLower, $searchLower) !== false ||
                    strpos($versionLower, $searchLower) !== false) {

                    $modifiedAt = date('d/m/Y H:i', $file->getMTime());

                    // Extraire le titre
                    $lines = explode("\n", $content);
                    $title = '';

                    foreach ($lines as $line) {
                        if (preg_match('/^#{1,2}\s+(.+)$/', trim($line), $matches)) {
                            $title = trim($matches[1]);
                            break;
                        }
                    }

                    if (empty($title)) {
                        $title = "Mise à jour " . $version;
                    }

                    $versions[] = [
                        'version' => $version,
                        'title' => $title,
                        'modified_at' => $modifiedAt,
                        'filename' => $filename
                    ];
                }
            }
        }

        // Trier par version (ordre décroissant)
        usort($versions, function ($a, $b) {
            return version_compare($b['version'], $a['version']);
        });

        return response()->json($versions);
    }

    public function getStats()
    {
        $updatesPath = base_path('updates');
        $files = File::files($updatesPath);
        $checkFrom = config('custom.version.check_from', 'v0.0.1');
        $totalVersions = 0;
        $lastUpdateDate = null;
        $currentVersion = null;

        foreach ($files as $file) {
            if ($file->getExtension() === 'md') {
                $filename = $file->getFilename();
                $version = str_replace('.md', '', $filename);

                if (version_compare($version, $checkFrom, '>=') === false) continue;

                $totalVersions++;

                // Trouver la version la plus récente
                if (!$lastUpdateDate || $file->getMTime() > strtotime($lastUpdateDate)) {
                    $lastUpdateDate = date('d/m/Y H:i', $file->getMTime());
                }

                // Trouver la version la plus élevée
                if (!$currentVersion || version_compare($version, $currentVersion) > 0) {
                    $currentVersion = $version;
                }
            }
        }

        return view('panel.settings.updates.partials._stats', compact('totalVersions', 'lastUpdateDate', 'currentVersion'));
    }
}
