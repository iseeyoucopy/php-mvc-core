<?php

namespace iseeyoucopy\phpmvc;


/**
 * Class Session
 *
 * @author  iseeyoucopy <iseeyoucopy@yahoo.com>
 * @package iseeyoucopy\phpmvc
 */
class Session
{
    protected const FLASH_KEY = 'flash_messages';

    public function __construct()
    {
        session_start();

        // Check if session is expired
        if ($this->isSessionExpired()) {
            $this->destroy();
        } else {
            // Renew session time
            $_SESSION['last_activity'] = time();
        }

        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage['remove'] = true;
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public function setFlash($key, $message)
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    public function getFlash($key)
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        return $_SESSION[$key] ?? false;
    }

    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public function __destruct()
    {
        $this->removeFlashMessages();
    }

    private function removeFlashMessages()
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => $flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
    public function secureSession()
    {
        if (!isset($_SESSION['user_ip']) && !isset($_SESSION['user_agent'])) {
            $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        } else {
            if ($_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR'] ||
                $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
                $this->destroy();
            }
        }
    }

    private function isSessionExpired()
    {
        $timeout_duration = 1800;  // 30 minutes
        if (isset($_SESSION['last_activity']) &&
            (time() - $_SESSION['last_activity']) > $timeout_duration) {
            return true;
        }
        return false;
    }
    public function getBasket()
    {
        return $_SESSION['basket'] ?? [];
    }

    public function setBasket($basket)
    {
        $_SESSION['basket'] = $basket;
    }

    public function destroy()
    {
        session_destroy();
    }
}