<?php include 'init.php'; ?>
<?php include 'parts/top.php'; ?>
    <div id="content" class="content">
        <h1>Creating Editors</h1>
        <p>The editors are use to manipulate images. The recommended way is to use Grafika::createEditor(). It automatically selects the best editor available. It will check if Imagick is available. If not, it will fall back to using GD.</p>
        <pre><code>use Grafika\Grafika; // Import package

$editor = Grafika::createEditor(); // Create the best available editor</code></pre>

        <h2>Imagick Editor</h2>
        <p>You can also use the Imagick editor only.</p>

        <pre><code>use Grafika\Imagick\Editor; // Import package

$editor = new Editor(); // Imagick editor</code></pre>

        <p>Be careful when using the Imagick editor as some PHP installs dont have it by default. You need to add some safety checks:</p>

        <pre><code>use Grafika\Imagick\Editor; // Import package

$editor = new Editor(); // Imagick editor

if( $editor->isAvailable() ) { // Safety check

    // Your code here

}</code></pre>

        <h2>GD Editor</h2>
        <p>You can also use the GD editor only.</p>
        <pre><code>use Grafika\Gd\Editor; // Import package

$editor = new Editor(); // Gd editor

if( $editor->isAvailable() ) { // Safety check

    // Your code here

}</code></pre>

        <h2>Try..Catch Statement</h2>
        <p>You can also wrap the code inside a try..catch statement to catch all possible errors. You don't need to use isAvailable.</p>

        <pre><code>use Grafika\Grafika; // Import package

try {

    $editor = Grafika::createEditor(); // Create best available editor

    // Do something

} catch (Exception $e){ // Catch exceptions for safety
    echo $e->getMessage();
}</code></pre>

        <h2>Change Editor Order</h2>
        <p>You can change the order of editor evaluation. For example, to always check for GD first:</p>
        <pre><code></code>use Grafika\Grafika; // Import package

try {

    $editor = Grafika::createEditor( array('Gd', 'Imagick') ); // Create best available editor

    // Do something

} catch (Exception $e){ // Catch exceptions for safety
    echo $e->getMessage();
}</pre>
        <p>However, you might not need the code above as GD is mostly available. You can just create a GD editor directly.</p>
        <h3>Change Editor Order Globally</h3>
        <p>The previous code will only change the order of editor evaluation one time for that function call. Sometimes it is useful to change the order of evaluation globally. For example if you prefer to use GD when creating drawing objects or filters. Below, all succeeding function calls will use the new order of evaluation:</p>
        <pre><code></code>use Grafika\Grafika; // Import package

try {

    Grafika::setEditorList(array('Gd', 'Imagick')); // Change order globally

    // createFilter and createDrawingObject will now use the new order
    $dither = Grafika::createFilter('Dither');
    $line = Grafika::createDrawingObject('Line', array(100, 0), array(100, 200))

} catch (Exception $e){ // Catch exceptions for safety
    echo $e->getMessage();
}</pre>
        <ul class="pager">
            <li class="prev"><a href="installation.php">Installation</a></li>
            <li class="next"><a href="creating-images.php">Creating Images</a></li>
        </ul>
    </div>
<?php include 'parts/sidebar.php'; ?>
<?php include 'parts/bottom.php'; ?>