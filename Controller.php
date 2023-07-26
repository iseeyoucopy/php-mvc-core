<?php

namespace iseeyoucopy\phpmvc;

use iseeyoucopy\phpmvc\middlewares\BaseMiddleware;
/**
 * Class Controller
 *
 * @author  iseeyoucopy <iseeyoucopy@yahoo.com>
 * @package iseeyoucopy\phpmvc
 */
class Controller
{
    public string $layout = 'main';
    public string $action = '';

    /**
     * @var \iseeyoucopy\phpmvc\BaseMiddleware[]
     */
    protected array $middlewares = [];

    public function setLayout($layout): void
    {
        $this->layout = $layout;
    }

    public function render($view, $params = []): string
    {
        return Application::$app->router->renderView($view, $params);
    }

    public function registerMiddleware(BaseMiddleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * @return \iseeyoucopy\phpmvc\middlewares\BaseMiddleware[]
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}