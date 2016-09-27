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
        <p>The following code would draw a 100x50 ellipse at 50,75:</p>
        <pre><code>use Grafika\Grafika;

//...

$drawingObject = Grafika::createDrawingObject('Ellipse', 100, 50, array(50, 75), 1, new Color('#000000'), new Color('#FF0000'));
$editor->draw( $image, $drawingObject ); // Draw on an image </code></pre>
        <img src="../images/testEllipse.jpg" alt="lines">
    </div>
<?php include '../parts/sidebar.php'; ?>
<?php include '../parts/bottom.php'; ?>