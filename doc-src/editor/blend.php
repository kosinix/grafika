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
        <pre><code>$input1 = 'lena.png';
$input2 = 'blend.png';
$output = 'test.jpg';

$image1 = Grafika::createImage( $input1 );
$image2 = Grafika::createImage( $input2 );
$editor->blend( $image1, $image2, 'overlay', 0.5, 'center' ); // overlay blend, opacity 50%, center position
$editor->save( $image1, $output );</code></pre>

        <p>Image 1: <br>
            <img src="../images/lena.png" alt="lena"> <br>
            Image 2: <br>
            <img src="../images/blend.png" alt="blend"></p>
        <p>Output: <br>
            <img src="../images/testBlend.jpg" alt="test"></p>
    </div>
<?php include '../parts/sidebar.php'; ?>
<?php include '../parts/bottom.php'; ?>