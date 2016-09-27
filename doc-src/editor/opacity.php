<?php include '../init.php'; ?>
<?php include '../parts/top.php'; ?>
<?php
$methodName = basename(__FILE__, '.php');
$parser = new PhpDocParser(new ReflectionClass('\Grafika\EditorInterface'));
$info = $parser->documentMethod($methodName);
?>
    <div id="content" class="content">
        <?php include '../parts/default-content.php'; ?>
        <p><strong>Warning:</strong> On GD editor, this function loops on each pixel manually which can be slow on large images.</p>
    </div>
<?php include '../parts/sidebar.php'; ?>
<?php include '../parts/bottom.php'; ?>