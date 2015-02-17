<?php namespace Barryvdh\HttpCache\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ClearCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'httpcache:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the entire HttpCache';

    /**
     * The file system instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new HttpCache clear command instance.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {

        $cacheDir = $this->laravel['http_cache.cache_dir'];

        if($this->files->cleanDirectory($cacheDir)){
            $this->info('HttpCache cleared!');
        }else{
            $this->error('Could not clear HttpCache');
        }

    }


}
