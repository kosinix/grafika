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
        <p>Detect and create based on available editor:</p>
    <pre><code>use Grafika\Grafika;
use Grafika\Gd\DrawingObject\QuadraticBezier as GdQuadraticBezier;
use Grafika\Imagick\DrawingObject\QuadraticBezier as ImagickQuadraticBezier;

//...

$editorName = Grafika::detectAvailableEditor();
if('Imagick'===$editorName){
    $drawingObject = new ImagickQuadraticBezier(array(70, 250), array(20, 110), array(220, 60), '#FF0000');
} else if ('Gd'===$editorName) {
    $drawingObject = new GdQuadraticBezier(array(70, 250), array(20, 110), array(220, 60), '#FF0000');
}
$editor->draw( $image, $drawingObject );
</code></pre>

        <p>Or let Grafika do it automatically using createDrawingObject:</p>
    <pre><code>use Grafika\Grafika;

//...

$drawingObject = Grafika::createDrawingObject('QuadraticBezier', array(70, 250), array(20, 110), array(220, 60), '#FF0000');
$editor->draw( $image, $drawingObject ); // Draw on an image </code></pre>
        <p>The result of the above code:</p>
        <p><img src="../images/testQuadraticBezier.jpg" alt="testQuadraticBezier"></p>
    </div>
<?php include '../parts/sidebar.php'; ?>
<?php include '../parts/bottom.php'; ?>