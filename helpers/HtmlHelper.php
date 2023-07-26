<?php
namespace iseeyoucopy\phpmvc\helpers;

class HtmlHelper
{
    public static function htmlout($string) {
        echo htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}



