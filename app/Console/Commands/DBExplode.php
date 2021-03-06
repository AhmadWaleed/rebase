<?php

namespace App\Console\Commands;

use App\Helpers\DBWorkspace;
use App\Domain\Models\Account;
use Illuminate\Console\Command;
use App\Domain\Repositories\Facades\AccountRepository;
use App\Domain\Repositories\Facades\ListingRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DBExplode extends Command
{
    protected $signature = 'db:explode
                                {workspace_name? : optional name of the workspace you\' like to blow up}
                                {--all : wanna blow up everything this is the option for you}';

    protected $description = 'Blow out local shared and/or local workspace databases';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        if (app()->environment() !== 'local') {
            $this->error("This command only runs in local environments");
            exit();
        }

        $this->dropShared();

        if ($this->argument('workspace_name')) {
            $this->dropWorkspace($this->argument('workspace_name'));
        }

        if ($this->option('all')) {
            $this->dropAllWorkspaces();
        }

        $this->line("");
        $this->info("Data go :boom:");
    }

    private function dropShared(): void
    {
        $this->alert("Refreshing Shared Database");

        $this->call("migrate:fresh", [
            '--database' => config('multi-database.shared.connection'),
            '--path' => config('multi-database.shared.migration_path'),
            '--step' => true
        ]);
    }

    private function dropAllWorkspaces(): void
    {
        $allSpaces = DBWorkspace::allSpaces(config('multi-database.workspace.prefix'));

        if ($allSpaces->count() > 0) {
            $this->line("");
            DBWorkspace::allSpaces(config('multi-database.workspace.prefix'))->each(function ($id) {
                DBWorkspace::drop($id);

                $this->line("<comment>Dropped: ".config('multi-database.workspace.prefix')."{$id}</comment>");
            });
        }
    }

    private function dropWorkspace(string $workspaceName): void
    {
        $this->alert("Dropping workspace {$workspaceName}");

        try {
            $listing = ListingRepository::getBySlug($workspaceName);
            DBWorkspace::drop(AccountRepository::getById($listing->account_id));
        } catch (ModelNotFoundException $e) {
            $this->error("Unable to find workspace {$workspaceName}");

            exit();
        }
    }
}
