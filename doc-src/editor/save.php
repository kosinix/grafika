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
    </div>
<?php include '../parts/sidebar.php'; ?>
<?php include '../parts/bottom.php'; ?>