<?php include '../init.php'; ?>
<?php include '../parts/top.php'; ?>
<?php
$methodName = basename(__FILE__, '.php');
$parser = new PhpDocParser(new ReflectionClass('\Grafika\EditorInterface'));
$info = $parser->documentMethod($methodName);
$signature = $parser->methodSignature($info);
$signature = str_replace('$permission = 493', '$permission = 0755', $signature); // Workaround: Reflection class cannot returns octal as dec but we need the octal representation
?>
    <div id="content" class="content">
        <h1><?php echo $info['name']; ?></h1>

        <p><?php echo $info['desc']; ?></p>

        <pre><code><?php echo $signature; ?></code></pre>

        <h5>Parameters</h5>
        <?php if(isset($info['param'])): ?>
            <div class="params">
                <?php foreach($info['param'] as $name=>$param): ?>

                    <h6><?php echo $name; ?></h6>

                    <p><?php echo $param['desc']; ?></p>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>This function has no parameters.</p>
        <?php endif; ?>

        <h5>Returns</h5>
        <?php if(isset($info['return']['desc'])): ?>
            <?php echo $info['return']['desc']; ?>
        <?php else: ?>
            <p>No value is returned.</p>
        <?php endif; ?>

        <h5>Examples</h5>
        <p>Opening a PNG and saving it:</p>
        <pre><code>use Grafika\Grafika;

$editor = Grafika::createEditor();
$editor->open( $image, 'input.png' );
$editor->save( $image, 'output.png' );</code></pre>

        <p>Opening a PNG and saving it as JPEG:</p>

        <pre><code>$editor->open( $image, 'input.png' );
$editor->save( $image, 'output.jpg' );</code></pre>
        <p>Notice the ".jpg" file extension. Grafika will automatically detect the file type based on the file name.</p>

        <p>You can also force saving an image into a specific format despite its file name.</p>
        <pre><code>$editor->open( $image, 'input.png' );
$editor->save( $image, 'output.jpg', 'png' );</code></pre>
        <p>This will save a PNG image to a file named "output.jpg". </p>
        <p>JPEG quality can be set from 0-100. Here we save a JPEG to highest quality with interlacing:</p>
        <pre><code>$editor->open( $image, 'input.png' );
$editor->save( $image, 'output.jpg', null, 100, true );</code></pre>
        <p>Notice that type is set to null which lets Grafika decide the file type based on the file name like in example 2.</p>
        <p>Saving an image to non-existent directory will result to Grafika creating the said directory. The default permission can be changed:</p>
        <pre><code>$editor->open( $image, 'input.png' );
$editor->save( $image, '/non-existent-dir/non-existent-dir/output.png', null, null, false, 0777 );</code></pre>
        <p>This will create the 2 directories with a permission of 0777. </p>
    </div>
<?php include '../parts/sidebar.php'; ?>
<?php include '../parts/bottom.php'; ?>