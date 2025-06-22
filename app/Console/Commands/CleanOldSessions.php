<?php

namespace App\Console\Commands;

use App\Models\Session;
use Illuminate\Console\Command;

class CleanOldSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:clean {--hours=24 : Nombre d\'heures d\'inactivité avant suppression}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoie les anciennes sessions de caisse';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hours = $this->option('hours');
        $cutoffTime = now()->subHours($hours);

        $this->info("Nettoyage des sessions inactives depuis plus de {$hours} heures...");

        // Compter les sessions à supprimer
        $sessionsToDelete = Session::where('last_activity', '<', $cutoffTime)
            ->where('status', 'active')
            ->count();

        if ($sessionsToDelete === 0) {
            $this->info('Aucune session à nettoyer.');
            return 0;
        }

        // Supprimer les sessions et leurs items
        $deletedSessions = Session::where('last_activity', '<', $cutoffTime)
            ->where('status', 'active')
            ->delete();

        $this->info("{$deletedSessions} sessions supprimées avec succès.");

        return 0;
    }
} 