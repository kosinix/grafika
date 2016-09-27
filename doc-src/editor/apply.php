<?php include '../init.php'; ?>
<?php include '../parts/top.php'; ?>
<?php
$methodName = basename(__FILE__, '.php');
$parser = new PhpDocParser(new ReflectionClass('\Grafika\EditorInterface'));
$info = $parser->documentMethod($methodName);
?>
    <div id="content" class="content">
        <?php include '../parts/default-content.php'; ?>
        <h5>Examples</h5>
        <pre><code>use Grafika\Grafika;

$editor = Grafika::createEditor();
$editor->open( $image, 'test.jpg' );

$filter = Grafika::createFilter('Blur', 10); // Apply a blur of 10. Possible values 1-100
$editor->apply( $image, $filter ); // Apply it to an image </code></pre>

        <p>Test image: <br>
            <img src="../images/lena.png" alt="lena"></p>
        <p>Blur filter: <br>
            <img src="../images/testBlur.jpg" alt="test"></p>
    </div>
<?php include '../parts/sidebar.php'; ?>
<?php include '../parts/bottom.php'; ?>