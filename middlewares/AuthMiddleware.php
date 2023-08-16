<?php

namespace iseeyoucopy\phpmvc\middlewares;


use iseeyoucopy\phpmvc\Application;
use iseeyoucopy\phpmvc\exception\ForbiddenException;

/**
 * Class AuthMiddleware
 *
 * @author  iseeyoucopy <iseeyoucopy@yahoo.com>
 * @package iseeyoucopy\phpmvc
 */
class AuthMiddleware extends BaseMiddleware
{
    protected array $actions = [];
    protected array $roles = [];  // <--- New roles property

    public function __construct(array $actions = [], array $roles = [])  // <-- Updated constructor
    {
        $this->actions = $actions;
        $this->roles = $roles;
    }

    public function execute()
    {
        if (Application::isGuest()) {
            if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) {
                throw new ForbiddenException();
            }
        } else {
            // If roles are defined, and the user doesn't have any of the roles, throw a forbidden exception.
            if (!empty($this->roles) && !in_array(Application::$app->user->getRole(), $this->roles)) {
                throw new ForbiddenException();
            }
        }
    }
}