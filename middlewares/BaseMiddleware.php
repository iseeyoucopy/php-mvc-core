<?php

namespace iseeyoucopy\phpmvc\middlewares;


/**
 * Class BaseMiddleware
 *
 * @author  iseeyoucopy <iseeyoucopy@yahoo.com>
 * @package iseeyoucopy\phpmvc
 */
abstract class BaseMiddleware
{
    abstract public function execute();
}