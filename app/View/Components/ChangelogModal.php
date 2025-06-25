<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ChangelogModal extends Component
{
    public $versions = [];
    public $contents = [];
    public $currentIndex;

    public function __construct($version = null)
    {
        $user = Auth::user();
        $currentVersion = $version ?? config('custom.version.current');
        $checkFrom = config('custom.version.check_from');
        $lastSeen = $user?->last_seen_version ?? '0.0.0';

        // Récupérer toutes les versions de updates/
        $files = glob(base_path('updates/v*.md'));
        $allVersions = collect($files)
            ->map(function($file) { return basename($file, '.md'); })
            ->sort(function($a, $b) { return version_compare($a, $b); })
            ->values();

        // Filtrer les versions à afficher
        $this->versions = $allVersions->filter(function($v) use ($lastSeen, $currentVersion, $checkFrom) {
            return version_compare($v, $lastSeen, '>') && version_compare($v, $checkFrom, '>=') && version_compare($v, $currentVersion, '<=');
        })->values()->all();

        // Charger le contenu de chaque changelog
        foreach ($this->versions as $v) {
            $path = base_path('updates/' . $v . '.md');
            $this->contents[$v] = file_exists($path) ? Str::markdown(file_get_contents($path)) : null;
        }

        // Index courant (par défaut 0)
        $this->currentIndex = 0;
    }

    public function render()
    {
        return view('components.changelog-modal');
    }
} 