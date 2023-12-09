<?php

namespace iseeyoucopy\phpmvc\middlewares;

use iseeyoucopy\phpmvc\Application;
use iseeyoucopy\phpmvc\exception\ForbiddenException;

class RoleMiddleware extends BaseMiddleware
{
    protected array $roles;
    protected array $actions;

    public function __construct(array $actions = [], array $roles = [])
    {
        $this->actions = $actions;
        $this->roles = $roles;
    }

    public function execute()
    {
        if (in_array(Application::$app->controller->action, $this->actions)) {
            // Check if there is a logged-in user
            if (!Application::$app->user || !in_array(Application::$app->user->role, $this->roles)) {
                throw new ForbiddenException();
            }
        }
    }
}