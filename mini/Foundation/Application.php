<?php

namespace Mini\Foundation;

use App\Providers\RouteServiceProvider;
use Mini\Routing\Router;

class Application extends Container
{
    protected $serviceProviders = [];
    protected $loadedProviders = [];

    public function __construct(protected $basePath = null)
    {
        $this->bindPathsInContainer();
        $this->registerBaseBindings();
        $this->registerBaseServiceProviders();
        $this->registerCoreContainerAliases();
    }

    public function registerServiceProviders(array $providers)
    {
        foreach ($providers as $provider) {
            if (isset($this->loadedProviders[$provider])) {
                continue;
            }
            $this->registerProvider($provider);
        }
    }

    public function boot()
    {
        foreach ($this->serviceProviders as $provider) {
            if (method_exists($provider, 'boot')) {
                call_user_func([$provider, 'boot']);
            }
        }
    }

    protected function registerProvider($provider)
    {
        $provider = new $provider($this);
        $provider->register();

        if (property_exists($provider, 'bindings')) {
            foreach ($provider->bindings as $key => $value) {
                $this->bind($key, $value);
            }
        }
        if (property_exists($provider, 'singletons')) {
            foreach ($provider->singletons as $key => $value) {
                $this->singleton($key, $value);
            }
        }

        $this->serviceProviders[] = $provider;
        $this->loadedProviders[$provider::class] = true;
    }

    protected function bindPathsInContainer()
    {
        $this->instance('path.base', $this->basePath);
        $this->instance('path.app', $this->basePath.DIRECTORY_SEPARATOR.'app');
        $this->instance('path.config', $this->basePath.DIRECTORY_SEPARATOR.'config');
    }

    protected function registerBaseBindings()
    {
        self::$instance = $this;
        $this->instance('app', $this);
        $this->instance(Application::class, $this);
    }

    protected function registerBaseServiceProviders()
    {
        $this->registerProvider(RouteServiceProvider::class);
    }

    protected function registerCoreContainerAliases()
    {
        foreach ([
            'router' => Router::class,
            'request' => Request::class
        ] as $key => $alias) {
            $this->alias($key, $alias);
        }
    }
}