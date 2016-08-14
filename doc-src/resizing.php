<?php include 'init.php'; ?>
<?php include 'parts/top.php'; ?>
    <div id="content" class="content">
        <h1>Resizing</h1>
        <p>Grafika support 5 ways of resizing an image: fit, fill, exact, exactWidth, and exactHeight.</p>
        <h2>Open an Image</h2>
        <p>To open an existing image, all you need is to create an editor instance and call the open method:</p>

        <pre><code>require_once 'path/to/grafika/src/autoloader.php'; // Automatically load our needed classes

use Grafika\Grafika; // Import package

$editor = Grafika::createEditor(); // Create editor

$editor->open( "images/sample-jpeg.jpg" ); // Open jpeg image for editing</code></pre>

        <h2>Resize Fit</h2>
        <p>Resize Fit will fit the image within the given dimension:</p>

        <pre><code>require_once 'path/to/grafika/src/autoloader.php'; // Automatically load our needed classes

use Grafika\Grafika; // Import package

$editor = Grafika::createEditor(); // Create editor

$editor->open( "images/sample.jpg" ); // Open jpeg image for editing
$editor->resizeFit( 200, 200 ); // Fit an image to 200 x 200 box
$editor->save("images/testResizeFit.jpg"); // Save the image as jpeg

$editor->open( "images/portrait.jpg" ); // Open jpeg image for editing
$editor->resizeFit( 200, 200 ); // Fit an image to 200 x 200 box
$editor->save("images/testResizeFitPortrait.jpg"); // Save the image as jpeg</code></pre>

        <p>The gray box shows the 200x200 dimension that the images fit in.</p>
        <div>
            <div style="float: left; background: #ccc; width: 200px; height: 200px; margin-right: 5px;"><img src="images/testResizeFit.jpg" alt="testResizeFit"></div>
            <div style="float: left; background: #ccc; width: 200px; height: 200px; text-align: center"><img src="images/testResizeFitPortrait.jpg" alt="testResizeFitPortrait"></div>
            <div style="clear: both"></div>
        </div>

        <h2>Resize Exact</h2>
        <p>Resize Exact will use the exact dimensions given, ignoring the aspect ratio. Here images appear squashed. It is useful if you want to force exact dimensions:</p>

        <pre><code>use Grafika\Grafika;

$editor = Grafika::createEditor();

$editor->open( "images/sample.jpg" );
$editor->resizeExact( 200, 200 );
$editor->save("images/testResizeExact.jpg");

$editor->open( "images/portrait.jpg" );
$editor->resizeExact( 200, 200 );
$editor->save("images/testResizeExactPortrait.jpg");</code></pre>

        <div>
            <img src="images/testResizeExact.jpg" alt="testResizeExact">
            <img src="images/testResizeExactPortrait.jpg" alt="testResizeExactPortrait">
        </div>

        <h2>Resize Fill</h2>
        <p>Resize Fill will fill the entire dimension given. Excess parts are cropped:</p>

        <pre><code>use Grafika\Grafika;

$editor = Grafika::createEditor();

$editor->open( "images/sample.jpg" );
$editor->resizeFill( 200, 200 );
$editor->save("images/testResizeFill.jpg");

$editor->open( "images/portrait.jpg" );
$editor->resizeFill( 200, 200 );
$editor->save("images/testResizeFillPortrait.jpg");</code></pre>

        <div>
            <img src="images/testResizeFill.jpg" alt="testResizeFill">
            <img src="images/testResizeFillPortrait.jpg" alt="testResizeFillPortrait">
        </div>

        <h2>Resize Exact Width</h2>
        <p>With resizeExactWidth, the height is auto calculated. Useful if you want column of images to be exactly the same width:</p>
        <pre><code>use Grafika\Grafika;

$editor = Grafika::createEditor();

$editor->open( "images/sample.jpg" );
$editor->resizeExactWidth( 100 );
$editor->save("images/testResizeExactWidth.jpg");

$editor->open( "images/portrait.jpg" );
$editor->resizeExactWidth( 100 );
$editor->save("images/testResizeExactWidthPortrait.jpg");</code></pre>

        <div>
            <div><img src="images/testResizeExactWidth.jpg" alt="testResizeExactWidth"></div>
            <div><img src="images/testResizeExactWidthPortrait.jpg" alt="testResizeExactWidthPortrait"></div>
        </div>


        <h2>Resize Exact Height</h2>
        <p>With resizeExactHeight, the width is auto calculated. Useful if you want row of images to be exactly the same height:</p>
        <pre><code>use Grafika\Grafika;

$editor = Grafika::createEditor();

$editor->open( "images/sample.jpg" );
$editor->resizeExactHeight( 100 );
$editor->save("images/testResizeExactHeight.jpg");

$editor->open( "images/portrait.jpg" );
$editor->resizeExactHeight( 100 );
$editor->save("images/testResizeExactHeightPortrait.jpg");</code></pre>

        <div>
            <img src="images/testResizeExactHeight.jpg" alt="testResizeExactHeight">
            <img src="images/testResizeExactHeightPortrait.jpg" alt="testResizeExactHeightPortrait">
        </div>

        <ul class="pager">
            <li class="prev"><a href="creating-editors.php">Creating Editors</a></li>
            <li class="next"><a href="compare-images.php">Compare Images</a></li>
        </ul>
    </div>
<?php include 'parts/sidebar.php'; ?>
<?php include 'parts/bottom.php'; ?>