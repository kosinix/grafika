<?php

$root = realpath(__DIR__.'/../');
require_once $root.'/src/autoloader.php'; // Grafika
require_once $root.'/doc-src/classes/DocBlock.php';
require_once $root.'/doc-src/classes/Documentation.php';
$needle = 'doc-src/';
$haystack = $_SERVER['SCRIPT_NAME'];
$pos = strpos($haystack, $needle);
$doc_src = '';
if(false !== $pos ){
    $doc_src = substr($haystack, 0, $pos+strlen($needle));
}
