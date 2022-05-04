<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Backup;

class backupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup Command';

    protected $backup;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Backup $backup)
    {
        parent::__construct();
        $this->backup = $backup;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data = $this->backup->createBackupFolder(request());
        $this->backup->backupDb();
        $this->backup->backupFolder(base_path('storage/app'));
    }
}