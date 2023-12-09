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

    public function __construct($actions = [])
    {
        $this->actions = $actions;
    }

    public function execute()
    {
        if (Application::isGuest()) {
            if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)) {
                throw new ForbiddenException();
            }
        }
    }

    public function handle($request, $next) {
        // Check if user is logged in.
        if(!isset($_SESSION['user_id'])) {
            header('Location: /login');  // redirect to login page if not authenticated
            exit();
        }
        return $next($request);
    }
}