<?php
require_once 'src/autoloader.php';


define('CLEAN_DUMP', 1); // Clear tmp folder after tests?
define('DIR_TEST', __DIR__); // /tests/ directory
define('DIR_TEST_IMG', __DIR__.'/images'); // /tests/images directory
define('DIR_TMP', __DIR__ . '/tmp'); // Holds test generated images 
define('DIR_ASSERT_GD', __DIR__ . '/assert-gd'); // Contains correct images to test upon on GD
define('DIR_ASSERT_IMAGICK', __DIR__ . '/assert-imagick'); // Contains correct images to test upon on Imagick

function deleteTmpDirectory()
{
    $dir = __DIR__ . '/tmp';
    foreach (scandir($dir) as $file) {
        if ('.' === $file || '..' === $file) {
            continue;
        }
        if (is_dir("$dir/$file")) {
            rmdirRecursive("$dir/$file");
        } else {
            unlink("$dir/$file");
        }
    }
}

function rmdirRecursive($dir)
{
    foreach (scandir($dir) as $file) {
        if ('.' === $file || '..' === $file) {
            continue;
        }
        if (is_dir("$dir/$file")) {
            rmdirRecursive("$dir/$file");
        } else {
            unlink("$dir/$file");
        }
    }
    return rmdir($dir);
}