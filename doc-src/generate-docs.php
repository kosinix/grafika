<?php

$root = realpath(__DIR__.'/../');
$webRootUrl = 'http://localhost/github.com/grafika';
$docSrcUrl = $webRootUrl.'/doc-src';
$docSrc = $root.'/doc-src';
$doc = $root.'/docs';
delete_dir($doc);
recurse_copy($docSrc.'/css', $doc.'/css');
echo 'Directory '.$docSrc.'/css'.' copied.. <br>';

recurse_copy($docSrc.'/images', $doc.'/images');
echo 'Directory '.$docSrc.'/images'.' copied.. <br>';

recurse_copy($docSrc.'/js', $doc.'/js');
echo 'Directory '.$docSrc.'/js'.' copied.. <br>';

mkdir($doc . '/editor', 0755);
mkdir($doc . '/filters', 0755);
mkdir($doc . '/draw', 0755);
mkdir($doc . '/image', 0755);
genHtml('');
genHtml('editor');
genHtml('filters');
genHtml('draw');
genHtml('image');

function genHtml($relPath){
    global $docSrcUrl, $docSrc, $doc;
    
    foreach(lsPages($docSrc.'/'.$relPath) as $page){


        $htmlName = basename($page, '.php').'.html';
        $contents = file_get_contents($docSrcUrl.'/'.$relPath.'/'.$page);
        if($relPath==='editor' or $relPath === 'draw' or $relPath === 'filters' or $relPath === 'image'){
            $contents = str_replace('/github.com/grafika/doc-src/', '../', $contents);
        } else {
            $contents = str_replace('/github.com/grafika/doc-src/', '', $contents);
        }

        file_put_contents($doc.'/'.$relPath.'/'.$htmlName, str_replace('.php">', '.html">', $contents));

        echo 'Page '.$htmlName.' generated.. <br>';
    }
}

function lsPages($path) {
    $dir = opendir($path);
    $files = array();
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' ) && ( $file != basename(__FILE__) ) && ( $file != 'README.txt' )) {
            if(false === is_dir($path.'/'.$file)){
                $files[] = $file;
            }
        }
    }
    closedir($dir);
    return $files;

}

function recurse_copy($src, $dst) {
    $dir = opendir($src);
    @mkdir( $dst, 0777, true );
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                recurse_copy($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

function delete_dir($dirPath) {
    if (is_dir($dirPath)) {
        $objects = scandir($dirPath);
        foreach ($objects as $object) {
            if ($object != "." && $object !="..") {
                if (filetype($dirPath . DIRECTORY_SEPARATOR . $object) == "dir") {
                    delete_dir($dirPath . DIRECTORY_SEPARATOR . $object);
                } else {
                    unlink($dirPath . DIRECTORY_SEPARATOR . $object);
                }
            }
        }
        reset($objects);
        rmdir($dirPath);
    }
}