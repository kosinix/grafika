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
        <pre><code>use Grafika\Grafika; // Import package

//...

$editor->open( $image, 'test.jpg' );
$editor->flip( $image, 'h' ); // Flip horizontally
$editor->save( $image, 'flipH.jpg' );
$editor->flip( $image, 'v' ); // Flip vertically
$editor->save( $image, 'flipV.jpg' );</code></pre>

        <p>Test image: <br>
        <img src="../images/lena.png" alt="lena"></p>
        <p>Flip horizontally: <br>
        <img src="../images/testFlipH.jpg" alt="flip"></p>
        <p>Flip vertically: <br>
        <img src="../images/testFlipV.jpg" alt="flip"></p>
    </div>
<?php include '../parts/sidebar.php'; ?>
<?php include '../parts/bottom.php'; ?>