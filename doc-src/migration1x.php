<?php include 'init.php'; ?>
<?php include 'parts/top.php'; ?>
    <div id="content" class="content">
        <h1>Migration from 1.x</h1>
        <p>The following are the changes from 1.x to version 2.x</p>
        <h2>Backward Incompatible Changes</h2>
        <h6>Editor Functions and Image instance</h6>

        <p>The Editor API now passes the reference to an Image instance on every editor functions. Previously the Image instance is stored internally when <code>open</code> is called.</p>
        <p>Previously in version 1.x:</p>
        <pre><code>use Grafika\Grafika;

$editor = Grafika::createEditor();

// Open image and assign it internally
$editor->open( "path/to/jpeg/image.jpg" );

// Do something to the image and save it
$editor->resize( 200, 200 );
$editor->save( "path/to/edited.jpg" );</code></pre>
        <p>Now in version 2.x:</p>
        <pre><code>use Grafika\Grafika;

$editor = Grafika::createEditor();

// Open image and assign it to $image variable
$editor->open( $image, "path/to/jpeg/image.jpg" );

// Do something to the image and save it. We pass the $image reference to the editor functions
$editor->resize( $image, 200, 200 );
$editor->save( $image, "path/to/edited.jpg" );</code></pre>
        <p>The code is more verbose, but it actually has advantages:</p>
        <ol>
            <li>Editor functions are now self-contained since the input are passed in the parameters, not in an internal class variable. This prevent unintended side effects.</li>
            <li>Editor functions can now be used inside the editor itself without creating a new Editor instance.</li>
            <li>Its clear what Image instance you are editing at the moment.</li>
        </ol>
        <p>Affected Editor functions:</p>
        <ul>
            <li>apply</li>
            <li>crop</li>
            <li>draw</li>
            <li>fill</li>
            <li>flatten</li>
            <li>flip</li>
            <li>free</li>
            <li>opacity</li>
            <li>open</li>
            <li>overlay</li>
            <li>resize</li>
            <li>resizeExact</li>
            <li>resizeExactHeight</li>
            <li>resizeExactWidth</li>
            <li>resizeFill</li>
            <li>resizeFit</li>
            <li>rotate</li>
            <li>save</li>
            <li>text</li>
        </ul>
        <h6>Removed Editor Functions</h6>
        <ul>
            <li>getImage - There is no need to store the Image internally.</li>
            <li>setImage - The same with getImage.</li>
            <li>blank - Use Grafika::createBlankImage instead.</li>
            <li>overlay - Functionality overlaps with blend. Use blend instead.</li>
        </ul>
        <ul class="pager">
            <li class="prev"><a href="installation.php">Installation</a></li>
            <li class="next"><a href="creating-editors.php">Creating Editors</a></li>
        </ul>
    </div>
<?php include 'parts/sidebar.php'; ?>
<?php include 'parts/bottom.php'; ?>