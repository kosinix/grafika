<?php include 'init.php'; ?>
<?php include 'parts/top.php'; ?>
    <div id="content" class="content">
        <h1>Installation</h1>
        <h2>Manual</h2>

        <ul>
            <li>Download the <a href="https://github.com/kosinix/grafika/archive/master.zip">zip file</a> from the Github repository.</li>
            <li>Unpack the zip file and include the files in your project.</li>
            <li>Include the autoloader.php found in grafika/src/:
                <pre><code>require_once '/path/to/src/autoloader.php'; // Change this to the correct path</code></pre>
            </li>
        </ul>



        <h2>Composer</h2>

        <p>Inside your project directory, open the command line and type:</p>
        <pre><code>composer require kosinix/grafika:dev-master --prefer-dist</code></pre>
        <p>Include the autoload.php found in vendor/:</p>
        <pre><code>require_once '/path/to/vendor/autoload.php'; // Change this to the correct path</code></pre>

        <h6>Older versions</h6>
        <p>If you want version 1.x use:</p>
        <pre><code>composer require kosinix/grafika:1.* --prefer-dist</code></pre>
        <p>All previous releases can be found <a href="https://github.com/kosinix/grafika/releases">here</a>.</p>

        <ul class="pager">
            <li class="prev"><a href="requirements.php">Requirements</a></li>
            <li class="next"><a href="migration1x.php">Migration from 1.x</a></li>
        </ul>
    </div>
<?php include 'parts/sidebar.php'; ?>
<?php include 'parts/bottom.php'; ?>