<?php include '../init.php'; ?>
<?php include '../parts/top.php'; ?>
<?php
$methodName = basename(__FILE__, '.php');
$doc = new Documentation( new ReflectionClass( '\Grafika\EditorInterface' ), $methodName ); ?>
<?php include '../parts/default-content.php'; ?>
<?php include '../parts/sidebar.php'; ?>
<?php include '../parts/bottom.php'; ?>