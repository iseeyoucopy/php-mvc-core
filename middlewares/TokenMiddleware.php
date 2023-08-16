<?php
namespace iseeyoucopy\phpmvc\middlewares;


use iseeyoucopy\phpmvc\Application;
use iseeyoucopy\phpmvc\exception\ForbiddenException;

class TokenMiddleware extends BaseMiddleware
{
    public function execute()
    {
        $this->validateCsrfToken();
    }
    public static function generateCsrfToken() {
        $tokenName = Application::$app->config['csrfTokenName'] ?? '_csrf_token';  // access the custom name from the application config, or use a default

        if (!isset($_SESSION[$tokenName])) {
            $_SESSION[$tokenName] = bin2hex(random_bytes(32));
        }
        return $_SESSION[$tokenName];
    }
    public function validateCsrfToken()
    {
        $tokenName = Application::$app->config['csrfTokenName'] ?? '_csrf_token';  // access the custom name from the application config, or use a default

        if (!isset($_POST[$tokenName]) || $_POST[$tokenName] !== $_SESSION[$tokenName]) {
            // CSRF token is invalid or missing.
            throw new \Exception('Invalid CSRF token');
        }

        unset($_SESSION[$tokenName]);  // Optionally, destroy the CSRF token once verified
    }
}
