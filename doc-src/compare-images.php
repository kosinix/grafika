<?php include 'init.php'; ?>
<?php include 'parts/top.php'; ?>
    <div id="content" class="content">
        <h1>Comparing Images</h1>
        <p>Grafika can compare how similar two images are and can also determine if two images are equal.</p>
        <h2>Finding Similarity</h2>
        <p>Finding similarity between two images using the compare() method:</p>

        <pre><code>require_once 'path/to/grafika/src/autoloader.php'; // Automatically load our needed classes

use Grafika\Grafika; // Import package

$editor = Grafika::createEditor(); // Create editor

$hammingDistance = $editor->compare( "image1.jpg", "image-2.jpg" );</code></pre>

        <p>The compare method will return a hamming distance. The hamming distance determines how similar two images are. A value of 0 indicates a likely similar picture. A value between 1 and 10 is potentially a variation. A value greater than 10 is likely a different image.</p>

        <h5>Examples:</h5>
        <table style="min-width: 60%; max-width: 70%">
            <tbody><tr>
                <th width="20%">Type</th>
                <th width="50%">Image</th>
                <th width="10%">Hamming Distance</th>
            </tr>
            <tr>
                <td>Base Image</td>
                <td><img src="images/testSimilarityBase.jpg" alt="base"></td>
                <td>N/A</td>
            </tr>
            <tr>
                <td>Grayscaled</td>
                <td><img src="images/testSimilarityGray.jpg" alt="gray"></td>
                <td>0</td>
            </tr>
            <tr>
                <td>Dithered</td>
                <td><img src="images/testSimilarityDithered.jpg" alt="dither"></td>
                <td>0</td>
            </tr>
            <tr>
                <td>Smaller, <br> Watermarked</td>
                <td><img src="images/testSimilarityWatermarked.jpg" alt="water"></td>
                <td>1</td>
            </tr>
            <tr>
                <td>Cropped <br> Note: If you wish to test if an image is cropped try setting the threshold to 26. That means check if the hamming distance is <= 26.</td>
                <td><img src="images/testSimilarityCropped.jpg" alt="cropped"></td>
                <td>23</td>
            </tr>
            <tr>
                <td>Totally Different Image</td>
                <td><img src="images/testSimilarityDiff.jpg" alt="diff"></td>
                <td>35</td>
            </tr>
            </tbody>
        </table>
        <h2>Checking Equality</h2>
        <p>Grafika can also do a pixel by pixel comparison to determine if two images are exactly the same using the equal() method:</p>
        <pre><code>require_once 'path/to/grafika/src/autoloader.php'; // Automatically load our needed classes

use Grafika\Grafika; // Import package

$editor = Grafika::createEditor(); // Create editor

$result = $editor-&gt;equal( "image1.jpg", "image-2.jpg" ); // Returns true if images are equal or false if not</code></pre>
        <p>This will return true if both images have exactly the same pixels.</p>
        <p>It will compare if the two images are of the same width and height. If the dimensions differ, it will return false. If the dimensions are equal, it will loop through each pixels. If one of the pixel don't match, it will return false. The pixels are compared using their RGB (Red, Green, Blue) values.</p>
        <p>equal() is expensive the larger the image. Use it when you absolutely need to check if two images are exactly the same. Otherwise you're better off using compare()</p>
        <ul class="pager">
            <li class="prev"><a href="resizing.php">Resizing</a></li>
            <li class="next"><a href="smart-crop.php">Smart Crop</a></li>
        </ul>
    </div>
<?php include 'parts/sidebar.php'; ?>
<?php include 'parts/bottom.php'; ?>