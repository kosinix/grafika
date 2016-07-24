<?php include '../init.php'; ?>
<?php include '../parts/top.php'; ?>
    <div id="content" class="content">
        <h1>Sobel</h1>
        <p>Sobel filter is an edge detection filter.</p>
        <h5>Examples</h5>
        <pre><code>use Grafika\Grafika; // Import package

//...

$filter = Grafika::createFilter('Sobel'); // Create filter object depending on available editor
$editor->apply( $filter ); // Apply it to an image </code></pre>
        
        <p>Test image:</p>
        <img src="../images/lena.png" alt="lena">
        <p>Result:</p>
        <img src="../images/testSobel.jpg" alt="Sobel">
    </div>
<?php include '../parts/sidebar.php'; ?>
<?php include '../parts/bottom.php'; ?>