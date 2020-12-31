<?php

namespace Mini\Routing;

use Mini\Foundation\Application;

class Route
{
    protected $app;

    protected $router;

    protected $namespace = '';

    public function __construct(protected $method, protected $uri, protected $action)
    {
        $this->parseAction($this->action);
    }

    protected function parseAction($action)
    {
        if ($action instanceof \Closure) {
            $this->action = [];
            $this->action['uses'] = $action;
        }

        if (is_string($action)) {
            $this->action = explode('@', $action);
        }

        if (is_array($action)) {
            $action = $action[0].'@'.$action[1];
        }

        $this->action['controller'] = $action;
        $this->action['uses'] = $action;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getActionName()
    {
        return $this->action['controller'] ?? 'Closure';
    }

    public function getActionMethod()
    {
        return end(explode('@', $this->getActionName()));
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function run()
    {
        if ($this->isControllerAction()) {
            return $this->runController();
        }

        return $this->runCallable();
    }

    protected function runCallable()
    {
        $callable = $this->action['uses'];
        $parameters = $this->app->buildMethod(new \ReflectionFunction($callable));
        return $callable(...array_values($parameters));
    }

    protected function runController()
    {
        $controller = $this->getController();
        $method = $this->getControllerMethod();

        $parameters = $this->app->buildMethod(new \ReflectionMethod($controller, $method));

        return $controller->$method(...array_values($parameters));
    }

    protected function getController()
    {
        $class = explode('@', $this->action['uses'])[0];

        $class = str_starts_with($class, $this->namespace) ? $class : $this->namespace.'\\'.$class;

        if (class_exists($class)) {
            return $this->app->make($class);
        }

        throw new \RuntimeException("The $class was not found");
    }

    protected function getControllerMethod()
    {
        return  explode('@', $this->action['uses'])[1];
    }

    protected function isControllerAction()
    {
        return is_string($this->action['uses']);
    }

    public function setRouter(Router $router)
    {
        $this->router = $router;
        return $this;
    }

    public function setApplication(Application $app)
    {
        $this->app = $app;
        return $this;
    }

}