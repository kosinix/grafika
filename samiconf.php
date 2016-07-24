<?php
// To run in console type: php sami.phar update samiconf.php
return new Sami\Sami('src', array(
    'build_dir'            => __DIR__.'/api',
    'cache_dir'            => __DIR__.'/sami-cache'
));