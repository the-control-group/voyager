<?php

namespace TCG\Voyager\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'voyager:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Voyager Admin package';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function getOptions()
    {
        return [
            ['existing', null, InputOption::VALUE_NONE, 'install on existing laravel application', null],
            ['no-dummy-data', null, InputOption::VALUE_NONE, 'install without seeding dummy data', null],
        ];
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        if (file_exists(getcwd().'/composer.phar')) {
            return '"'.PHP_BINARY.'" '.getcwd().'/composer.phar';
        }

        return 'composer';
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if (!$this->option('existing')) {
            $this->info('Generating the default authentication scaffolding');
            Artisan::call('make:auth');
        }

        $this->info('Publishing the Voyager assets, database, and config files');
        Artisan::call('vendor:publish', ['--provider' => \TCG\Voyager\VoyagerServiceProvider::class]);
        Artisan::call('vendor:publish', ['--provider' => \Intervention\Image\ImageServiceProviderLaravel5::class]);

        $this->info('Migrating the database tables into your application');
        Artisan::call('migrate');

        $this->info('Dumping the autoloaded files and reloading all new files');

        $composer = $this->findComposer();

        $process = new Process($composer.' dump-autoload');
        $process->setWorkingDirectory(base_path())->run();

        $this->info('Seeding data into the database');
        if ($this->option('no-dummy-data')) {
            Artisan::call('db:seed', ['--class' => 'DataTypesTableSeeder']);
            Artisan::call('db:seed', ['--class' => 'DataRowsTableSeeder']);
        } else {
            Artisan::call('db:seed', ['--class' => 'VoyagerDatabaseSeeder']);
        }

        $this->info('Adding the storage symlink to your public folder');
        Artisan::call('storage:link');

        $this->info('Successfully installed Voyager! Enjoy :)');
    }
}
