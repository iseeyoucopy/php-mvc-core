<?php

namespace iseeyoucopy\phpmvc;

use iseeyoucopy\phpmvc\db\DbModel;

/**
 * Class UserModel
 *
 * @author  iseeyoucopy <iseeyoucopy@yahoo.com>
 * @package iseeyoucopy\phpmvc
 */
abstract class UserModel extends DbModel
{
    abstract public function getDisplayName(): string;
}