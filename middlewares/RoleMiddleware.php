<?php

namespace iseeyoucopy\phpmvc\middlewares;

use iseeyoucopy\phpmvc\Application;
use iseeyoucopy\phpmvc\exception\ForbiddenException;

class RoleMiddleware extends BaseMiddleware
{
    protected array $actions;
    protected array $roles;

    public function __construct(array $actions = [], array $roles = [])
    {
        $this->actions = $actions;
        $this->roles = $roles;
    }

    public function execute()
    {
        // Debugging: Log the current action
        error_log("Current action: " . Application::$app->controller->action);

        // Check if the current action requires role-based access control
        if (in_array(Application::$app->controller->action, $this->actions)) {
            // Debugging: Log the user's role
            $userRole = Application::$app->user ? Application::$app->user->getRole() : 'guest';
            error_log("User role: " . $userRole);

            // Debugging: Log the allowed roles for this action
            error_log("Allowed roles: " . implode(', ', $this->roles));

            if (!Application::$app->user || !in_array($userRole, $this->roles)) {
                throw new ForbiddenException();
            }
        }
    }
}
