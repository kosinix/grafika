<?php include '../init.php'; ?>
<?php include '../parts/top.php'; ?>
<?php
$methodName = basename(__FILE__, '.php');
$doc = new Documentation( new ReflectionClass( '\Grafika\DrawingObject\\'.$methodName ), '__construct' ); ?>
    <div id="content" class="content">
        <h1><?php echo $methodName; ?></h1>
        <p><?php echo $doc->description; ?></p>

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
        <h5>Examples</h5>
        <p>The following code would draw a 100x50 ellipse at 50,75:</p>
        <pre><code>$editor->ellipse(100, 50, array(50, 75), 1, new Color('#000000'), new Color('#FF0000'));</code></pre>
        <img src="../images/testEllipse.jpg" alt="lines">
    </div>
<?php include '../parts/sidebar.php'; ?>
<?php include '../parts/bottom.php'; ?>