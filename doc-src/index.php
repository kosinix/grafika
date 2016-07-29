<?php include 'init.php'; ?>
<?php include 'parts/top.php'; ?>
    <div id="content" class="content">
        <h1>What is Grafika?</h1>
        <p>Grafika is an image processing library for PHP. It aims to be an image processing library not an image manipulation library. Image manipulation (resize, crop, transform and save to different formats) is a smaller subset of image processing.</p>

        <a href="https://github.com/kosinix/grafika/archive/master.zip" class="button">Download .zip</a>
        <a href="https://github.com/kosinix/grafika/" class="button">Github</a>

        <h2>Why Grafika?</h2>
        <p>These features are unique to Grafika and not currently found in other libs:</p>
        <ul>
            <li>Smart crop - Guess the crop position based on the image content where the most important regions are preserved.</li>
            <li>Image compare - Compare image similarity and equality using perceptual hash.</li>
            <li>Perceptual hash - Hash an image using perceptual hash algorithms.</li>
            <li>Advance image processing filters</li>
            <li>Bezier curves</li>
        </ul>

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

$editor->open( "path/to/jpeg/image.jpg" );
$editor->resizeExact( 200, 200 );
$editor->save( "path/to/edited.jpg", null, 90 );</code></pre>

        <p>You can even chain the api calls (think jQuery):</p>
<pre><code>use Grafika\Grafika;

Grafika::createEditor()
        ->open( "path/to/jpeg/image.jpg" )
        ->resizeExact( 200, 200 )
        ->save( "path/to/edited.jpg", null, 90 );</code></pre>

        <p>Other than resizing to exact width and height, Grafika has many more modes for resizing an image. See docs for more info.</p>


        <ul class="pager">
            <li class="next"><a href="requirements.php">Requirements</a></li>
        </ul>
    </div>
    <?php include 'parts/sidebar.php'; ?>
<?php include 'parts/bottom.php'; ?>
