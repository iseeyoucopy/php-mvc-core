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

    /**
     * Sets the layout for the object.
     *
     * @param mixed $layout The layout to set.
     * @return void
     */
    public function setLayout($layout): void
    {
        // Assign the provided layout to the object's layout property.
        $this->layout = $layout;
    }

    public function render($view, $params = []): string
    {
        return Application::$app->router->renderView($view, $params);
    }

    public function renderAdmin($view, $params = []): string
    {
        return Application::$app->router->renderAdminView($view, $params);
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