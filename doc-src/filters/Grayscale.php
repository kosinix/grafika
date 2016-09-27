<?php include '../init.php'; ?>
<?php include '../parts/top.php'; ?>
<?php
$fileName = basename(__FILE__, '.php');
$parser = new PhpDocParser(new ReflectionClass('\Grafika\Gd\Filter\\'.$fileName));
$info = $parser->documentMethod('__construct');
?>
    <div id="content" class="content">
        <h1><?php echo $fileName; ?></h1>

        <p><?php echo $parser->classDesc(); ?></p>

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
        <pre><code>use Grafika\Grafika; // Import package

//...

$filter = Grafika::createFilter('Grayscale'); // Create filter object depending on available editor
$editor->apply( $image, $filter ); // Apply it to an image </code></pre>
        
        <p>Test image:</p>
        <img src="../images/lena.png" alt="lena">
        <p>Result:</p>
        <img src="../images/testGrayscale.jpg" alt="Sobel">
    </div>
<?php include '../parts/sidebar.php'; ?>
<?php include '../parts/bottom.php'; ?>