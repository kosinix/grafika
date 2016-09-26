<?php include 'init.php'; ?>
<?php include 'parts/top.php'; ?>
    <div id="content" class="content">
        <h1>Smart Crop</h1>
        <p>Image crop or cropping refers to the removal of the outer parts of an image to improve framing, accentuate subject matter or change aspect ratio.</p>
        <h2>Basic Crop</h2>
        <p>Grafika accepts the following crop position:</p>
        <ul>
            <li>top-left</li>
            <li>top-center</li>
            <li>top-right</li>
            <li>center-left</li>
            <li>center</li>
            <li>center-right</li>
            <li>bottom-left</li>
            <li>bottom-center</li>
            <li>bottom-right</li>
        </ul>

        <h5>Examples</h5>
        <p>Given this input image and the code below:</p>
        <p><img src="images/crop-test.jpg" alt=""></p>
        <pre><code>// ...

$input = 'crop-test.jpg';

// Top
$editor->open( $image, $input );
$editor->crop( $image, 260, 150, 'top-left' );
$editor->save( $image, 'testCrop1.jpg' );
$editor->free( $image );

$editor->open( $image, $input );
$editor->crop( $image, 260, 150, 'top-center' );
$editor->save( $image, 'testCrop2.jpg' );
$editor->free( $image );

$editor->open( $image, $input );
$editor->crop( $image, 260, 150, 'top-right' );
$editor->save( $image, 'testCrop3.jpg' );
$editor->free( $image );

// Middle row
$editor->open( $image, $input );
$editor->crop( $image, 260, 150, 'center-left' );
$editor->save( $image, 'testCrop4.jpg' );
$editor->free( $image );

$editor->open( $image, $input );
$editor->crop( $image, 260, 150, 'center' );
$editor->save( $image, 'testCrop5.jpg' );
$editor->free( $image );

$editor->open( $image, $input );
$editor->crop( $image, 260, 150, 'center-right' );
$editor->save( $image, 'testCrop6.jpg' );
$editor->free( $image );

// Bottom row
$editor->open( $image, $input );
$editor->crop( $image, 260, 150, 'bottom-left' );
$editor->save( $image, 'testCrop7.jpg' );
$editor->free( $image );

$editor->open( $image, $input );
$editor->crop( $image, 260, 150, 'bottom-center' );
$editor->save( $image, 'testCrop8.jpg' );
$editor->free( $image );

$editor->open( $image, $input );
$editor->crop( $image, 260, 150, 'bottom-right' );
$editor->save( $image, 'testCrop9.jpg' );
$editor->free( $image );</code></pre>

        <p>The above code would look like this if arranged in a 3x3 grid:</p>

        <table>
            <tr>
                <td><img src="images/testCrop1.jpg" alt="crop"></td>
                <td><img src="images/testCrop2.jpg" alt="crop"></td>
                <td><img src="images/testCrop3.jpg" alt="crop"></td>
            </tr>
            <tr>
                <td><img src="images/testCrop4.jpg" alt="crop"></td>
                <td><img src="images/testCrop5.jpg" alt="crop"></td>
                <td><img src="images/testCrop6.jpg" alt="crop"></td>
            </tr>
            <tr>
                <td><img src="images/testCrop7.jpg" alt="crop"></td>
                <td><img src="images/testCrop8.jpg" alt="crop"></td>
                <td><img src="images/testCrop9.jpg" alt="crop"></td>
            </tr>
        </table>

        <h5>Smart Crop</h5>
        <p>Grafika can also do smart cropping wherein it decides the crop position with the important regions of the images preserved.</p>
        <pre><code>$editor->open( $image, $input );
$editor->crop( $image, 200, 200, 'smart' );
$editor->save( $image, 'output.jpg' );</code></pre>
        <table style="min-width: 60%; max-width: 70%">
            <tbody><tr>
                <th width="10%">Type</th>
                <th width="45%">Image</th>
                <th width="45%">Result</th>
            </tr>
            <tr>
                <td>Face</td>
                <td><img src="images/lena.png" alt="face"></td>
                <td><img src="images/testSmartCrop1.jpg" alt="face"></td>
            </tr>
            <tr>
                <td>Tower</td>
                <td><img src="images/tower.jpg" alt="tower"></td>
                <td><img src="images/testSmartCrop2.jpg" alt="tower"></td>
            </tr>
            <tr>
                <td>Cube</td>
                <td><img src="images/portal-companion-cube.jpg" alt=""></td>
                <td><img src="images/testSmartCrop3.jpg" alt=""></td>
            </tr>
            <tr>
                <td>Strawberries</td>
                <td><img src="images/sample.jpg" alt=""></td>
                <td><img src="images/testSmartCrop4.jpg" alt=""></td>
            </tr>
            <tr>
                <td>Anime</td>
                <td><img src="images/sample.png" alt=""></td>
                <td><img src="images/testSmartCrop5.jpg" alt=""></td>
            </tr>
            </tbody>
        </table>

        <p>Note: This feature is currently experimental and will be continuously improve in future releases.</p>

        <p>See the <a href="<?php echo $doc_src; ?>editor/crop.php">crop</a> API for more info.</p>

        <ul class="pager">
            <li class="prev"><a href="compare-images.php">Compare Images</a></li>
            <li class="next"><a href="animated-gif.php">Animated GIF</a></li>
        </ul>
    </div>
<?php include 'parts/sidebar.php'; ?>
<?php include 'parts/bottom.php'; ?>