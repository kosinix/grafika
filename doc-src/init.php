<?php

$root = realpath(__DIR__.'/../');
require_once $root.'/src/autoloader.php'; // Grafika
require_once $root.'/doc-src/classes/PhpDocParser.php';
$needle = 'doc-src/';
$haystack = $_SERVER['SCRIPT_NAME'];
$pos = strpos($haystack, $needle);
$doc_src = '';
if(false !== $pos ){
    $doc_src = substr($haystack, 0, $pos+strlen($needle));
}

function classDoc($classPath){
    $class = new ReflectionClass($classPath);
    $str = preg_replace(array('(\/\*\*[\s]+[*][\s])','([\s]\*\/)'), '', $class->getDocComment());
    $str = preg_split('/(\n)|(\r)/', $str);
    return $str[0];
}