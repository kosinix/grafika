<?php include '../init.php'; ?>
<?php include '../parts/top.php'; ?>
    <div id="content" class="content">
        <h1>Dither</h1>
        <p>Dither image using Floyd-Steinberg algorithm. Dithering will reduce the color to black and white and add noise.</p>
        <h5>Examples</h5>
        <pre><code>use Grafika\Grafika; // Import package

//...

$filter = Grafika::createFilter('Dither'); // Create filter object depending on available editor
$editor->apply( $filter ); // Apply it to an image </code></pre>
        
        <p>Test image:</p>
        <img src="../images/lena.png" alt="lena">
        <p>Result:</p>
        <img src="../images/testDither.jpg" alt="dither">
    </div>
<?php include '../parts/sidebar.php'; ?>
<?php include '../parts/bottom.php'; ?>