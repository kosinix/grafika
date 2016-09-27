<?php include 'init.php'; ?>
<?php include 'parts/top.php'; ?>
    <div id="content" class="content">
        <h1>Creating Images</h1>
        <p>Before you can perform image processing, you need to create an instance of Image. The Image object unifies the differences in GD and Imagick and holds all the pertinent info about the image. See Image Functions section for more info.</p>
        <h6>Editor Open</h6>
        <p>The common way of creating an image is by using the editor open method:</p>
        <pre><code>use Grafika\Grafika;

$editor = Grafika::createEditor();
$editor->open( $image, 'path/to/image.jpg');</code></pre>
        <p>Now $image holds an Image instance that you can pass along to editor methods:</p>
<pre><code>//...
$editor->resizeExact( $image, 200, 100 );
//...
$editor->flip( $image, 'h' );</code></pre>

        <h6>Grafika::createImage</h6>
        <p>If you dont have an editor instance, you can create an image directly:</p>
        <pre><code>use Grafika\Grafika;

$image = Grafika::createImage('path/to/image.jpg');
</code></pre>

        <h6>Blank Image</h6>
        <p>You can also create a blank image:</p>
        <pre><code>use Grafika\Grafika;

$image = Grafika::createBlankImage(100,100);</code></pre>

        <h6>Making a Copy</h6>
        <p>Creating a copy of an image is as easy as using the clone keyword:</p>
        <pre><code>$copy = clone $image;</code></pre>
        <p>Now $copy is independent of the changes you will make in $image. You can leverage this functionality to easily make a backup:</p>

        <pre><code>//...

$backup = clone $image; // Full copy

$editor->crop( $image, 100, 100 ); // Crop it

$editor->save( $image, 'cropped.jpg' ); // Cropped version
$editor->save( $backup, 'original.jpg' ); // Unaffected by crop version</code></pre>

        <ul class="pager">
            <li class="prev"><a href="creating-editors.php">Creating Editors</a></li>
            <li class="next"><a href="resizing.php">Resizing</a></li>
        </ul>
    </div>
<?php include 'parts/sidebar.php'; ?>
<?php include 'parts/bottom.php'; ?>