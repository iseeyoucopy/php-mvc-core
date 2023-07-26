<?php

namespace iseeyoucopy\phpmvc\exception;


use iseeyoucopy\phpmvc\Application;

/**
 * Class ForbiddenException
 *
 * @author  iseeyoucopy <iseeyoucopy@yahoo.com>
 * @package iseeyoucopy\phpmvc\exception
 */
class ForbiddenException extends \Exception
{
    protected $message = 'You don\'t have permission to access this page';
    protected $code = 403;
}