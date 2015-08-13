<?php

namespace App;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Philo\Blade\Blade;

use App\Core\Config;
use App\Core\Database;
use App\Core\Allias;

class Application extends Container
{
    /**
     * Indicates if the class aliases have been registered.
     *
     * @var bool
     */
    protected static $aliasesRegistered = false;

    /**
     * The base path of the application installation.
     *
     * @var string
     */
    protected $basePath;

    /**
     * The configuration path of the application installation.
     *
     * @var string
     */
    protected $configPath;

    /**
     * The resource path of the application installation.
     *
     * @var string
     */
    protected $resourcePath;

    /**
     * All of the loaded configuration files.
     *
     * @var array
     */
    protected $loadedConfigurations = [];

    /**
     * The loaded service providers.
     *
     * @var array
     */
    protected $loadedProviders = [];

    /**
     * Initialize configuration.
     */
    public function __construct($basePath)
    {
        $this->basePath = $basePath;
        $this->setupPaths($basePath);

        $this->bootstrapContainer();

        // $this->config = new Config();
        // $this->config->loadConfigurationFiles(
        //     $this->paths['config'],
        //     // $this->getEnvironment()            
        // );
    }

    /**
     * Bootstrap the application container.
     *
     * @return void
     */
    protected function bootstrapContainer()
    {
        static::setInstance($this);

        $this->instance('app', $this);

        $this->boot();
    }

    protected function boot()
    {
        $this->registerConfigBindings();
        $this->registerEventBindings();
        $this->registerDatabaseBindings();
        $this->registerCapsuleBindings();
        $this->registerViewBindings();
    }

    /**
     * Register a service provider with the application.
     *
     * @param  \Illuminate\Support\ServiceProvider|string  $provider
     * @param  array  $options
     * @param  bool   $force
     * @return \Illuminate\Support\ServiceProvider
     */
    public function register($provider, $options = [], $force = false)
    {
        if (!$provider instanceof ServiceProvider) {
            $provider = new $provider($this);
        }

        if (array_key_exists($providerName = get_class($provider), $this->loadedProviders)) {
            return;
        }

        $this->loadedProviders[$providerName] = true;

        $provider->register();
        $provider->boot();
    }

    /**
     * Configure and load the given component and provider.
     *
     * @param  string  $config
     * @param  array|string  $providers
     * @param  string|null  $return
     * @return mixed
     */
    protected function loadComponent($config, $providers, $return = null)
    {
        $this->configure($config);

        foreach ((array) $providers as $provider) {
            $this->register($provider);
        }

        return $this->make($return ?: $config);
    }

    /**
     * Load a configuration file into the application.
     *
     * @return void
     */
    public function configure($name)
    {
        if (isset($this->loadedConfigurations[$name])) {
            return;
        }

        $this->loadedConfigurations[$name] = true;

        $path = $this->getConfigurationPath($name);

        if ($path) {
            $this->make('config')->set($name, require $path);
        }
    }

    /**
     * Get the path to the given configuration file.
     *
     * @param  string  $name
     * @return string
     */
    protected function getConfigurationPath($name)
    {
        $appConfigPath = ($this->configPath ?: $this->basePath('config')).'/'.$name.'.php';

        if (file_exists($appConfigPath)) {
            return $appConfigPath;
        } elseif (file_exists($path = DIR_APPLICATION.'/config/'.$name.'.php')) {
            return $path;
        }
    }

    /**
     * Get the base path for the application.
     *
     * @param  string  $path
     * @return string
     */
    public function basePath($path = null)
    {
        if (isset($this->basePath)) {
            return $this->basePath.($path ? '/'.$path : $path);
        }

        if (php_sapi_name() == 'cli') {
            $this->basePath = getcwd();
        } else {
            $this->basePath = realpath(getcwd().'/../');
        }

        return $this->basePath($path);
    }

    /**
     * Register container bindings for the application.
     *
     * @return void
     */
    protected function registerConfigBindings()
    {
        $configPath = $this->configPath;
        $this->singleton('config', function () use ($configPath) {
            return new Config($configPath);
        });
    }

    /**
     * Register container bindings for the application.
     *
     * @return void
     */
    protected function registerEventBindings()
    {
        $this->singleton('events', function () {
            $this->register('Illuminate\Events\EventServiceProvider');

            return $this->make('events');
        });
    }

    /**
     * Register container bindings for the application.
     *
     * @return void
     */
    protected function registerDatabaseBindings()
    {
        $this->singleton('db', function () {
            return $this->loadComponent(
                'database', [
                    'Illuminate\Database\DatabaseServiceProvider',
                    /*'Illuminate\Pagination\PaginationServiceProvider'*/],
                'db'
            );
        });
        // $this->singleton('db', function () {
        //     return new Database();
        // });
    }

    /**
     * Register container bindings for the application.
     *
     * @return void
     */
    protected function registerCapsuleBindings()
    {
        new Database();
        $this->singleton('capsule', function () {
            return new Capsule;
        });
    }

    /**
     * Register container bindings for the application.
     *
     * @return void
     */
    protected function registerViewBindings()
    {
        $resourcePath = $this->resourcePath;
        $this->singleton('view', function () use ($resourcePath) {
            return new Blade($resourcePath . '/template', $resourcePath . '/cache');
        });
    }

    /**
     * Initialize the paths.
     */
    protected function setupPaths($basePath)
    {
        // $this->paths['env_file_path'] = __DIR__ . '/../';
        // $this->paths['env_file']      = $this->paths['env_file_path'].'.env';
        $rootPath = __DIR__ . '/../../';
        $this->configPath = $basePath . '/config';
        $this->resourcePath = $basePath . '/view';
    }

    public function withAliases(array $aliases = [])
    {
        new Allias($aliases);
    }
}