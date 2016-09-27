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

$editor->draw( $image, Grafika::createDrawingObject('Polygon', array(array(0,0), array(50,0), array(0,50)), 1));
$editor->draw( $image, Grafika::createDrawingObject('Polygon', array(array(200-1,0), array(150-1,0), array(200-1,50)), 1));
$editor->draw( $image, Grafika::createDrawingObject('Polygon', array(array(100,0), array(140,50), array(100,100), array(60,50)), 1, null, new Color('#FF0000')));</code></pre>

        <p>The result of the above code:</p>
        <p><img src="../images/testPolygon.png" alt="testPolygon"></p>
    </div>
<?php include '../parts/sidebar.php'; ?>
<?php include '../parts/bottom.php'; ?>