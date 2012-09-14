<?php
/**
 * Default autoload
 */

//composer autoload
require_once __DIR__ . '/vendor/autoload.php';
//symfony autoload
require_once __DIR__ . '/vendor/pear-pear.symfony-project.com/symfony/' .
    'symfony/lib/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();