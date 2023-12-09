<?php

namespace iseeyoucopy\phpmvc\form;

use iseeyoucopy\phpmvc\Model;
use iseeyoucopy\phpmvc\Application;
use iseeyoucopy\phpmvc\middlewares\TokenMiddleware;

/**
 * Class Form
 *
 * @author  iseeyoucopy <iseeyoucopy@yahoo.com>
 * @package php-mvc-core\form
 */
class Form
{
    public static function begin($action, $method, $options = [])
    {
        $attributes = [];
        foreach ($options as $key => $value) {
            $attributes[] = htmlspecialchars($key) . "=\"" . htmlspecialchars($value) . "\"";
        }

        // Generate CSRF token
        $csrfToken = TokenMiddleware::generateCsrfToken();
        $tokenName = Application::$app->config['csrfTokenName'] ?? '_csrf_token';

        // Begin the form with hidden input for CSRF token
        echo sprintf('<form class="account-form contact-form" action="%s" method="%s" %s>', htmlspecialchars($action), htmlspecialchars($method), implode(" ", $attributes));
        echo sprintf('<input type="hidden" name="%s" value="%s">', htmlspecialchars($tokenName), htmlspecialchars($csrfToken));  // Add hidden input for CSRF token

        return new Form();
    }

    public static function end()
    {
        echo '</form>';
    }

    public function field(Model $model, $attribute)
    {
        return new Field($model, $attribute);
    }

}