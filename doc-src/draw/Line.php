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
    <p>The following code would print the lines below:</p>
    <pre><code>// ...

$editor->draw(Grafika::createDrawingObject('Line', array(0, 0), array(200, 200), 1, new Color('#FF0000')));
$editor->draw(Grafika::createDrawingObject('Line', array(0, 200), array(200, 0), 1, new Color('#00FF00')));
$editor->draw(Grafika::createDrawingObject('Line', array(0, 0), array(200, 100), 1, new Color('#0000FF')));
$editor->draw(Grafika::createDrawingObject('Line', array(0, 100), array(200, 100)));
$editor->draw(Grafika::createDrawingObject('Line', array(100, 0), array(100, 200)));</code></pre>
    <img src="../images/testLines.jpg" alt="lines">
</div>
<?php include '../parts/sidebar.php'; ?>
<?php include '../parts/bottom.php'; ?>