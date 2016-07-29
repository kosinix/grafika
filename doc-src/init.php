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

// Bandaid helper. class doc should be one liner only or this breaks.
function classDoc($classPath){
    $class = new ReflectionClass($classPath);
    return preg_replace(array('(\/\*\*[\s]+[*][\s])','([\s]\*\/)'), '', $class->getDocComment());
}