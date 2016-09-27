<h1><?php echo $info['name']; ?></h1>

<p><?php echo $info['desc']; ?></p>

<pre><code><?php echo $parser->methodSignature($info); ?></code></pre>

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