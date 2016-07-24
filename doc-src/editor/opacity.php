<?php include '../init.php'; ?>
<?php include '../parts/top.php'; ?>
<?php
$methodName = basename(__FILE__, '.php');
$doc = new Documentation( new ReflectionClass( '\Grafika\EditorInterface' ), $methodName ); ?>
<div id="content" class="content">
    <h1><?php echo $methodName; ?></h1>

    <p><?php echo $doc->description; ?></p>

    <pre><code><?php echo $doc->signature; ?></code></pre>

    <p><strong>Warning:</strong> On GD editor, this function loops on each pixel manually which can be slow on large images.</p>
    <h5>Parameters</h5>
    <?php if($doc->params): ?>
        <div class="params">
            <?php foreach($doc->params as $i=>$param): ?>

                <h6><?php echo $param['name']; ?></h6>

                <p><?php echo $param['desc']; ?></p>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>This function has no parameters.</p>
    <?php endif; ?>

    <h5>Returns</h5>
    <?php if($doc->returnType): ?>
        <?php echo ($doc->returnType) ? $doc->returnType. ' - ' : ''; ?>
        <?php echo $doc->returnDesc; ?>
    <?php else: ?>
        <p>No value is returned.</p>
    <?php endif; ?>
</div>
<?php include '../parts/sidebar.php'; ?>
<?php include '../parts/bottom.php'; ?>