<?php include '../init.php'; ?>
<?php include '../parts/top.php'; ?>
<?php
$methodName = basename(__FILE__, '.php');
$doc = new Documentation( new ReflectionClass( '\Grafika\Gd\Filter\\'.$methodName ), '__construct' ); ?>
    <div id="content" class="content">
        <h1><?php echo ucwords($methodName); ?></h1>
        <p><?php echo classDoc('\Grafika\Gd\Filter\\'.$methodName); ?></p>

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
        <pre><code>use Grafika\Grafika; // Import package

//...

$filter = Grafika::createFilter('Sharpen', 50);
$editor->apply( $filter ); // Apply it to an image </code></pre>
        
        <p>Test image:</p>
        <img src="../images/lena.png" alt="lena">
        <p>Result:</p>
        <img src="../images/testSharpen.jpg" alt="result">
    </div>
<?php include '../parts/sidebar.php'; ?>
<?php include '../parts/bottom.php'; ?>