<?php include 'init.php'; ?>
<?php include 'parts/top.php'; ?>
    <div id="content" class="content">
        <h1>Animated GIF</h1>
        <p>Grafika can resize animated GIFs either on GD or Imagick, preserving their animation in the process.</p>



        <p>Input:</p>
        <img src="images/sample.gif" alt="">

        <p>Code:</p>
<pre><code>use Grafika\Grafika;

$editor = Grafika::createEditor();
$editor->open( $image, 'animated.gif' );
$editor->resizeFit( $image, 600, 310 );
$editor->save( $image, 'output.gif' );</code></pre>

        <p>Output:</p>
        <img src="images/testResizeFitEnlarge.gif" alt="">

        <h5>Flattening Animated Images</h5>
        <p>Flattening an animated GIF will remove its animation and convert to just a regular GIF.</p>

        <p>Code:</p>
<pre><code>use Grafika\Grafika;

$editor = Grafika::createEditor();
$editor->open( $image, 'animated.gif' );
$editor->flatten( $image );
$editor->save( $image, 'output.gif' );</code></pre>

        <p>Output:</p>
        <p><img src="images/testFlattenAnimatedGif.gif" alt=""></p>


        <p>Note: For now, support on animated GIF is limited to resize operations. Other operations such as applying filters or text will do nothing.</p>
        <ul class="pager">
            <li class="prev"><a href="smart-crop.php">Smart Crop</a></li>
        </ul>
    </div>
<?php include 'parts/sidebar.php'; ?>
<?php include 'parts/bottom.php'; ?>