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
        <ul class="pager">
            <li class="prev"><a href="installation.php">Installation</a></li>
            <li class="next"><a href="resizing.php">Resizing</a></li>
        </ul>
    </div>
<?php include 'parts/sidebar.php'; ?>
<?php include 'parts/bottom.php'; ?>