<?php include 'init.php'; ?>
<?php include 'parts/top.php'; ?>
    <div id="content" class="content">
        <h1>Resizing</h1>
        <p>Grafika support 5 ways of resizing an image: fit, fill, exact, exactWidth, and exactHeight.</p>
        <h2>Opening an Image</h2>
        <p>To open an existing image, all you need is to create an editor instance and call the open method:</p>

        <pre><code>require_once 'path/to/grafika/src/autoloader.php'; // Automatically load our needed classes

use Grafika\Grafika; // Import package

// Create editor
$editor = Grafika::createEditor();

// Open jpeg image and store it in $image variable
$editor->open( $image, "images/sample-jpeg.jpg" );</code></pre>

        <h2>Resize Fit</h2>
        <p>Resize Fit will fit the image within the given dimension:</p>

        <pre><code>$editor->open( $image1, "images/sample.jpg" ); // Open jpeg image for editing
$editor->resizeFit( $image1, 200, 200 ); // Fit an image to 200 x 200 box
$editor->save( $image1, "images/testResizeFit.jpg"); // Save the image as jpeg

$editor->open( $image2, "images/portrait.jpg" ); // Open another image for editing
$editor->resizeFit( $image2, 200, 200 ); // Fit an image to 200 x 200 box
$editor->save( $image2, "images/testResizeFitPortrait.jpg"); // Save the image as jpeg</code></pre>

        <p>The gray box shows the 200x200 dimension that the images fit in.</p>
        <div>
            <div style="float: left; background: #ccc; width: 200px; height: 200px; margin-right: 5px;"><img src="images/testResizeFit.jpg" alt="testResizeFit"></div>
            <div style="float: left; background: #ccc; width: 200px; height: 200px; text-align: center"><img src="images/testResizeFitPortrait.jpg" alt="testResizeFitPortrait"></div>
            <div style="clear: both"></div>
        </div>

        <h2>Resize Exact</h2>
        <p>Resize Exact will use the exact dimensions given, ignoring the aspect ratio. Here images appear squashed. It is useful if you want to force exact dimensions:</p>

        <pre><code>$editor->open( $image1, "images/sample.jpg" );
$editor->resizeExact( $image1, 200, 200 );
$editor->save( $image1, "images/testResizeExact.jpg");

$editor->open( $image2, "images/portrait.jpg" );
$editor->resizeExact( $image2, 200, 200 );
$editor->save( $image2, "images/testResizeExactPortrait.jpg");</code></pre>

        <div>
            <img src="images/testResizeExact.jpg" alt="testResizeExact">
            <img src="images/testResizeExactPortrait.jpg" alt="testResizeExactPortrait">
        </div>

        <h2>Resize Fill</h2>
        <p>Resize Fill will fill the entire dimension given. Excess parts are cropped:</p>

        <pre><code>$editor->open( $image1, "images/sample.jpg" );
$editor->resizeFill( $image1, 200, 200 );
$editor->save( $image1, "images/testResizeFill.jpg");

$editor->open( $image2, "images/portrait.jpg" );
$editor->resizeFill( $image2, 200, 200 );
$editor->save( $image2, "images/testResizeFillPortrait.jpg");</code></pre>

        <div>
            <img src="images/testResizeFill.jpg" alt="testResizeFill">
            <img src="images/testResizeFillPortrait.jpg" alt="testResizeFillPortrait">
        </div>

        <h2>Resize Exact Width</h2>
        <p>With resizeExactWidth, the height is auto calculated. Useful if you want column of images to be exactly the same width:</p>
        <pre><code>$editor->open( $image1, "images/sample.jpg" );
$editor->resizeExactWidth( $image1, 100 );
$editor->save( $image1, "images/testResizeExactWidth.jpg");

$editor->open( $image2, "images/portrait.jpg" );
$editor->resizeExactWidth( $image2, 100 );
$editor->save( $image2, "images/testResizeExactWidthPortrait.jpg");</code></pre>

        <div>
            <div><img src="images/testResizeExactWidth.jpg" alt="testResizeExactWidth"></div>
            <div><img src="images/testResizeExactWidthPortrait.jpg" alt="testResizeExactWidthPortrait"></div>
        </div>


        <h2>Resize Exact Height</h2>
        <p>With resizeExactHeight, the width is auto calculated. Useful if you want row of images to be exactly the same height:</p>
        <pre><code>$editor->open( $image1, "images/sample.jpg" );
$editor->resizeExactHeight( $image1, 100 );
$editor->save( $image1, "images/testResizeExactHeight.jpg");

$editor->open( $image2, "images/portrait.jpg" );
$editor->resizeExactHeight( $image2, 100 );
$editor->save( $image2, "images/testResizeExactHeightPortrait.jpg");</code></pre>

        <div>
            <img src="images/testResizeExactHeight.jpg" alt="testResizeExactHeight">
            <img src="images/testResizeExactHeightPortrait.jpg" alt="testResizeExactHeightPortrait">
        </div>

        <ul class="pager">
            <li class="prev"><a href="creating-images.php">Creating Images</a></li>
            <li class="next"><a href="compare-images.php">Compare Images</a></li>
        </ul>
    </div>
<?php include 'parts/sidebar.php'; ?>
<?php include 'parts/bottom.php'; ?>