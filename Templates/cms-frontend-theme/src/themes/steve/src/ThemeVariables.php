<?php

namespace Drupal\steve;

class ThemeVariables
{
    static $primary_menu_rendered;

    public function __construct()
    {
        self::$primary_menu_rendered = false;
    }

    public static function setRendered()
    {
        kint(self::$primary_menu_rendered);
        self::$primary_menu_rendered = true;
    }

    public static function getRendered()
    {
        kint(self::$primary_menu_rendered);
        return self::$primary_menu_rendered;
    }
}

?>
