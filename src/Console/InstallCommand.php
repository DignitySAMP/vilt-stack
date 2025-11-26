<?php

namespace Laravel\VILT\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Process\Process;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

#[AsCommand(name: 'vilt:install')]
class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'vilt:install
                            {--typescript : Install the TypeScript stack}
                            {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the VILT stack with either JavaScript (using Ziggy.js) or TypeScript (using Wayfinder)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // choose package manager
        $package_mgmr = select(
            label: 'Which package manager are you intending to use?',
            options: [
                'npm' => 'npm',
                'pnpm' => 'pnpm',
                'yarn' => 'yarn'
            ],
            scroll: 3,
        );

        $this->components->info('You chose ' . $package_mgmr . ' as your package manager.');

        // option to choose typescript, default to ziggy.js otherwise
        $router = 'ziggy';
        if ($this->input->isInteractive()) {
            $typescript = confirm(
                label: 'Do you want TypeScript?',
                default: (bool) $this->option('typescript'),
            );

            if ($typescript) {
                $router = 'wayfinder';
            }
        }
        
        // confirm the choice to give a window to cancel using ctrl+c
        $typescript ? $this->components->info('Installing VILT stack with TypeScript and Wayfinder...') : $this->components->info('Installing VILT stack with JavaScript and Ziggy.js...');
            
        // handles composer and copies the right files, based on selection, to root folder
        if (! $this->installViltStack($typescript, $router)) {
            return self::FAILURE;
        }

        // handles node packages
        $this->requireNodePackages($package_mgmr);

        $this->line('');
        $this->components->info('The VILT stack has been installed successfully.');
        return self::SUCCESS;
    }

    /*
    ** Wrapper for calling the right installation methods in order
    */
    protected function installViltStack(bool $typescript, string $router): bool
    {
        // build composer.json:
        if (! $this->requireComposerPackages($router)) {
            return false;
        }

        $this->initBaseStack($typescript); // setup stack with ts/js and ziggy/wayfinder configuration       

        // clean up the laravel welcome page, @/resources/{ts/js}/pages/Welcome.vue replaces this
        (new Filesystem)->delete(resource_path('views/welcome.blade.php'));

        return true;
    }

    /*
    ** Copy the preconfigured files based on earlier stack selection to the right root folder
    */
    protected function initBaseStack(bool $typescript): void
    {
        $stack = $typescript ? 'typescript' : 'default';

        // install inertia configuration and vue.js page layouts
        $this->copyStubDirectory("$stack/resources", resource_path()); // transfers vue-js setup
        $this->copyStubDirectory("$stack/app", base_path('app')); // transfers inertia.js setup (inertia.js middleware)
        $this->copyStub("$stack/views/app.blade.php", base_path('views/app.blade.php')); // configure app.blade.php to include inertia and vite @heads
        $this->copyStub("$stack/routes/web.php", base_path('routes/web.php')); // setup Welcome.vue page using Inertia
        $this->copyStub("$stack/bootstrap/app.php", base_path('bootstrap/app.php')); // hook the Inertia middleware into app.php
        $this->copyStub("$stack/package.json", base_path('package.json')); // copies package.json with either ts/js configuration (ziggy/wayfinder)

        if ($typescript) {
            $this->copyStub("$stack/tsconfig.json", base_path('tsconfig.json'));
            $this->copyStub("$stack/vite.config.ts", base_path('vite.config.ts'));
            // delete original .js configs and files
            (new Filesystem)->delete(base_path('vite.config.js'));
            (new Filesystem)->deleteDirectory(resource_path('js')); // no need for default .js config, axios config from bootstrap/js is preconfigred with inertia

        } else {
            $this->copyStub("$stack/vite.config.js", base_path('vite.config.js'));
            // there shouldn't be any .ts configurations, but clean them if there are
            (new Filesystem)->delete(base_path('vite.config.ts'));
            (new Filesystem)->delete(base_path('resources/js/bootstrap.js')); // axios config no longer needed because inertia preconfiurges it
            (new Filesystem)->delete(base_path('tsconfig.json'));
        }
    }

    /*
    ** Install node dependencies based on previously selected package manager
    */
    protected function requireNodePackages($package_mgmr): void
    {
        $this->components->info('Installing and building Node dependencies.');

        switch($package_mgmr) {
            case 'pnpm' : {
                $this->runCommands(['pnpm install', 'pnpm run build']);
                break;
            }
            case 'yarn': {
                $this->runCommands(['yarn install', 'yarn run build']);
                break;
            }
            default: {
                $this->runCommands(['npm install', 'npm run build']);
            }
        }
        return;
    }

    /*
    ** Build composer.json based on stack selection and install them
    */
    protected function requireComposerPackages($router)
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'require'];
        }

        $packages = [ // include inertia package
            'inertiajs/inertia-laravel:^2.0',
        ];

        switch($router) { // add routing package based on stack (ts/js)
            case 'ziggy': {
                $packages[] = 'tightenco/ziggy:^2.6';
                break;
            }
            case 'wayfinder': {
                $packages[] = 'laravel/wayfinder:^0.1.12';
                break;
            }
        }
        
        $command = array_merge(
            $command ?? ['composer', 'require'],
            $packages
        );

        return (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            }) === 0;
    }

    /*
    ** Helper method to run node installation command(s)
    */
    protected function runCommands($commands)
    {
        $process = Process::fromShellCommandline(implode(' && ', $commands), null, null, null, null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            try {
                $process->setTty(true);
            } catch (RuntimeException $e) {
                $this->output->writeln('  <bg=yellow;fg=black> WARN </> '.$e->getMessage().PHP_EOL);
            }
        }

        $process->run(function ($type, $line) {
            $this->output->write('    '.$line);
        });
    }

    /*
    ** Helper method to copy an entire directory through the /stubs/.
    */
    protected function copyStubDirectory(string $stubPath, string $targetPath): void
    {
        $source = __DIR__.'/../../stubs/'.$stubPath;

        if (! is_dir($source)) {
            return;
        }

        (new Filesystem)->copyDirectory($source, $targetPath);
    }

    /*
    ** Helper method to copy files from specific /stubs/.
    */
    protected function copyStub(string $stubPath, string $targetPath): void
    {
        $source = __DIR__.'/../../stubs/'.$stubPath;

        if (! file_exists($source)) {
            return;
        }

        $filesystem = new Filesystem;
        $filesystem->ensureDirectoryExists(dirname($targetPath));
        $filesystem->copy($source, $targetPath);
    }
}