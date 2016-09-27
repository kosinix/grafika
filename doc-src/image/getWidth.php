<?php include '../init.php'; ?>
<?php include '../parts/top.php'; ?>
<?php
$methodName = basename(__FILE__, '.php');
$parser = new PhpDocParser(new ReflectionClass('\Grafika\ImageInterface'));
$info = $parser->documentMethod($methodName);
?>
    <div id="content" class="content">
        <?php include '../parts/default-content.php'; ?>
    </div>
<?php include '../parts/sidebar.php'; ?>
<?php include '../parts/bottom.php'; ?>