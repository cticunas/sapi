<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FilesystemLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filesystem:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the link for filesystem path';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $filesystem_dir=env('UPLOADS_PATH');
        $link=storage_path("../public/storage");
        $terminal_command="ln -s $filesystem_dir $link";
        //windows: mklink /D "E:\Link_Path\" "F:\Real_Path"
        if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
        $terminal_command="mklink /D $link $filesystem_dir";
        exec($terminal_command);
        $this->line("Enlace creado: $terminal_command");
        //$this->line("Verifiquelo con ls -l public/, para eliminarlo: rm -rf public/storage");
    }
}
