<?php
spl_autoload_register(function ($class) {
    if ( 0 === strpos( $class, 'Grafika' ) ) { // Autoload our packages only

        $base_dir = __DIR__ . '/';
        $file = str_replace('\\', '/', $base_dir . $class . '.php'); // Change \ to /

        require_once $file;
    }
});