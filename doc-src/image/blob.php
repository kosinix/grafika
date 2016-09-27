<?php include '../init.php'; ?>
<?php include '../parts/top.php'; ?>
<?php
$methodName = basename(__FILE__, '.php');
$parser = new PhpDocParser(new ReflectionClass('\Grafika\ImageInterface'));
$info = $parser->documentMethod($methodName);
?>
<div id="content" class="content">
    <?php include '../parts/default-content.php'; ?>
    <h5>Examples</h5>
        <p>This will output a PNG image directly to the browser:</p>
        <pre><code>use Grafika/Grafika;

$image = Grafika::createImage( 'source.png' );

header('Content-type: image/png'); // Tell the browser we're sending a png image
$image->blob('PNG'); // Output raw binary png format</code></pre>

</div>
<?php include '../parts/sidebar.php'; ?>
<?php include '../parts/bottom.php'; ?>