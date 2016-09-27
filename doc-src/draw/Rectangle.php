<?php include '../init.php'; ?>
<?php include '../parts/top.php'; ?>
<?php
$fileName = basename(__FILE__, '.php');
$parser = new PhpDocParser(new ReflectionClass('\Grafika\DrawingObject\\'.$fileName));
$info = $parser->documentMethod('__construct');
?>
    <div id="content" class="content">
        <h1><?php echo $fileName; ?></h1>

        <p><?php echo $info['desc']; ?></p>

        <h5>Parameters</h5>
        <?php if(isset($info['param'])): ?>
            <div class="params">
                <?php foreach($info['param'] as $name=>$param): ?>

                    <h6><?php echo $name; ?></h6>

                    <p><?php echo $param['desc']; ?></p>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>This function has no parameters.</p>
        <?php endif; ?>

        <h5>Examples</h5>
        <pre><code>//...

$editor->draw( $image, Grafika::createDrawingObject('Rectangle', 85, 50)); // A 85x50 no filled rectangle with a black 1px border on location 0,0.
$editor->draw( $image, Grafika::createDrawingObject('Rectangle', 85, 50, array(105, 10), 0, null, new Color('#FF0000'))); // A 85x50 red rectangle with no border.
$editor->draw( $image, Grafika::createDrawingObject('Rectangle', 85, 50, array(105, 70), 0, null, new Color('#00FF00'))); // A 85x50 green rectangle with no border.
$editor->draw( $image, Grafika::createDrawingObject('Rectangle', 85, 50, array(0, 60), 1, '#000000', null)); // No fill rectangle</code></pre>

        <p>The result of the above code:</p>
        <p><img src="../images/testRectangle.jpg" alt="testRectangle"></p>
    </div>
<?php include '../parts/sidebar.php'; ?>
<?php include '../parts/bottom.php'; ?>