<?php include 'init.php'; ?>
<?php include 'parts/top.php'; ?>
    <div id="content" class="content">
        <h1>What is Grafika?</h1>
        <p>Grafika is an advance image processing and graphics library for PHP.</p>

        <a href="https://github.com/kosinix/grafika/archive/master.zip" class="button">Download .zip</a>
        <a href="https://github.com/kosinix/grafika/" class="button">Github</a>

        <h2>Why Grafika?</h2>
        <p>Why another image manipulation lib? There are a bazillion other libs for PHP already, why reinvent the wheel? Well...</p>
        <h6>Unique Features</h6>
        <p>These are the features that makes Grafika unique from other libs:</p>
        <ul>
            <li><strong>Smart Crop</strong> - Grafika can guess the crop position based on the image content where the most important regions are preserved.</li>
            <li><strong>Animated GIF Support</strong> - It can resize animated GIFs on both GD and Imagick. On GD, Grafika uses its own GIF parser to do this.</li>
            <li><strong>5 Resize Modes</strong> - Resize is a first class citizen in Grafika. Call them directly using resizeFit, resizeFill, resizeExact, resizeExactWidth, and resizeExactHeight or use the generic resize api.</li>
            <li><strong>Image Compare</strong> - Find how similar two images are or check if they are exactly equal.</li>
            <li><strong>Advance Filters</strong> - Sobel edge-detection, diffusion and ordered dithering. More will be added in future releases.</li>
            <li><strong>Image Blending</strong> - Blend 2 images using the following modes: normal, multiply, overlay and screen.</li>
            <li><strong>Normalized API</strong> - No need to worry about the differences between GD and Imagick API, Grafika normalizes these operations for you.</li>
        </ul>
        <h6>Basic Filters</h6>
        <p>Grafika also support the basic filters commonly found in other libs:</p>
        <ul>
            <li>Blur</li>
            <li>Brightness</li>
            <li>Colorize</li>
            <li>Contrast</li>
            <li>Gamma</li>
            <li>Invert</li>
            <li>Pixelate</li>
            <li>Sharpen</li>
        </ul>
        <p>See Filters section for more info.</p>
        <h6>Drawing Objects</h6>
        <ul>
            <li>CubicBezier</li>
            <li>Ellipse</li>
            <li>Line</li>
            <li>Polygon</li>
            <li>QuadraticBezier</li>
            <li>Rectangle</li>
        </ul>
        <p>See Drawing Objects section for more info.</p>

        <h6>Easy Image Manipulation</h6>
        <p>Grafika makes it easier to do image manipulation in PHP.</p>
        <p>Consider the following code using PHP's default built-in image lib, GD. <br>It will resize a jpeg image to exactly 200x200 pixels:</p>
        <pre><code>$gd = imagecreatefromjpeg( 'path/to/jpeg/image.jpg' ); // Open jpeg file

$newImage = imagecreatetruecolor(200, 200); // Create a blank image

// Resize image to 200x200
imagecopyresampled(
    $newImage,
    $gd,
    0,
    0,
    0,
    0,
    200,
    200,
    imagesx($gd),
    imagesy($gd)
);

imagedestroy($gd); // Free up memory

imagejpeg( $newImage, 'path/to/edited.jpg', 90 ); // Save resized image with 90% quality

imagedestroy($newImage); // Free up memory
            </code></pre>

        <p>Now consider doing the same using grafika:</p>
        <pre><code>use Grafika\Grafika;

$editor = Grafika::createEditor();

$editor->open( $image, "path/to/jpeg/image.jpg" );
$editor->resizeExact( $image, 200, 200 );
$editor->save( $image, "path/to/edited.jpg", null, 90 );</code></pre>

        <p>You can even chain the api calls (think jQuery):</p>
<pre><code>use Grafika\Grafika;

Grafika::createEditor()
        ->open( $image, "path/to/jpeg/image.jpg" )
        ->resizeExact( $image, 200, 200 )
        ->save( $image, "path/to/edited.jpg", null, 90 );</code></pre>

        <p>See docs for more info.</p>


        <ul class="pager">
            <li class="next"><a href="requirements.php">Requirements</a></li>
        </ul>
    </div>
    <?php include 'parts/sidebar.php'; ?>
<?php include 'parts/bottom.php'; ?>
