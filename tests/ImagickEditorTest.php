<?php
use Grafika\Color;
use Grafika\EditorInterface;
use Grafika\Grafika;
use Grafika\Imagick\Editor;
use Grafika\Imagick\Filter\Blur;
use Grafika\Imagick\Filter\Brightness;
use Grafika\Imagick\Filter\Colorize;
use Grafika\Imagick\Image;

/**
 * Class ImagickEditorTest
 */
class ImagickEditorTest extends PHPUnit_Framework_TestCase {

    protected $dirAssert = DIR_ASSERT_IMAGICK;

    public function testCreateEditor()
    {

        $editor = new \Grafika\Imagick\Editor(); // Explicit Imagick Editor
        $this->assertTrue($editor instanceof EditorInterface);

        return $editor;
    }

    public function testCreateEditorStatic()
    {

        Grafika::setEditorList(array('Imagick')); // Use Imagick only

        $editor = Grafika::createEditor();

        $this->assertTrue($editor instanceof EditorInterface);
    }

    /**
     * @depends testCreateEditor
     * @param EditorInterface $editor
     */
    public function testOpenFail(EditorInterface $editor)
    {
        if (version_compare(PHP_VERSION, '5.6', '>=')) {
            $this->expectException('\Exception');

            $input = 'unreachable.jpg'; // Non existent file

            $editor->open($input);
        }

    }

    /**
     * @depends testCreateEditor
     * @param EditorInterface $editor
     */
    public function testUnknownTypeFail($editor)
    {
        if (version_compare(PHP_VERSION, '5.6', '>=')) {
            $this->expectException('\Exception');

            $input = DIR_TEST_IMG . '/sample.wbm';

            $editor->open($input);
        }
    }

    /**
     * @depends testCreateEditor
     * @param EditorInterface $editor
     */
    public function testOpenJpeg($editor)
    {

        $input = DIR_TEST_IMG . '/sample.jpg';
        $editor->open($input);
        $image = $editor->getImage();

        $this->assertTrue($image instanceof Image);
    }

    /**
     * @depends testCreateEditor
     * @param EditorInterface $editor
     */
    public function testOpenPng($editor)
    {

        $input = DIR_TEST_IMG . '/sample.png';
        $editor->open($input);
        $image = $editor->getImage();

        $this->assertTrue($image instanceof Image);
    }

    /**
     * @depends testCreateEditor
     * @param EditorInterface $editor
     */
    public function testOpenGif($editor)
    {

        $input = DIR_TEST_IMG . '/sample.gif';
        $editor->open($input);
        $image = $editor->getImage();

        $this->assertTrue($image instanceof Image);
    }

    /**
     * @depends testCreateEditor
     * @param EditorInterface $editor
     * @return EditorInterface
     */
    public function testEqual($editor)
    {
        $input1 = $this->dirAssert . '/testEqual.jpg';

        $this->assertTrue($editor->equal($input1, $input1));

        return $editor;
    }

    /**
     * @depends testEqual
     * @param EditorInterface $editor
     * @return EditorInterface
     */
    public function testEqualFalse($editor)
    {
        $input1 = $this->dirAssert . '/testEqual.jpg';
        $input2 = $this->dirAssert . '/testEqualFalse.png';

        $this->assertFalse($editor->equal($input1, $input2));

        return $editor;
    }

    /**
     * Test similarity
     * @depends testCreateEditor
     * @param EditorInterface $editor
     */
    public function testCompare($editor)
    {

        $input = DIR_TEST_IMG . '/lena.png';
        $input2 = DIR_TEST_IMG . '/lena-gray.png';

        $ham = $editor->compare($input,$input2);

        $this->assertLessThan(10,$ham); // hamming distance: 0 is equal, 1-10 is similar, 11+ is different image
    }

    /**
     * @depends testCreateEditor
     * @param EditorInterface $editor
     */
    public function testSave($editor){
        $input = DIR_TEST_IMG . '/sample.png';
        $output1 = DIR_TMP . '/' . __FUNCTION__ . '1.jpg';
        $output2 = DIR_TMP . '/' . __FUNCTION__ . '2.jpg';
        $output3 = DIR_TMP . '/' . __FUNCTION__ . '3.png';

        $editor->open($input);
        $editor->save($output1, 'jpg', 100);
        $this->assertEquals(0, $editor->compare($input, $output1));

        $editor->open($input);
        $editor->save($output2, 'jpg', 0);
        $this->assertGreaterThan(0, $editor->compare($input, $output2)); // Not exactly similar due to compression

        $editor->open($input);
        $editor->save($output3, 'png', null);
        $this->assertEquals(0, $editor->compare($input, $output3));

    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testAddTextOnBlankImage($editor)
    {

        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->blank(400, 100)->fill(new Color('#ffffff'));
        $editor->text('Lorem ipsum - Liberation Sans');
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testAddTextOnJpeg($editor)
    {

        $input = DIR_TEST_IMG . '/sample.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->resizeFit(300, 300);
        $editor->text('Lorem ipsum - Liberation Sans');
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    /**
     * Test enlarging an image to a dimension larger than its original size.
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testResizeFitEnlarge($editor)
    {

        $input = DIR_TEST_IMG . '/sample.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->resizeFit($editor->getImage()->getWidth() + 100, $editor->getImage()->getHeight() + 100);
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

        // Animated gif
        $input = DIR_TEST_IMG . '/sample.gif';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.gif';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.gif';

        $editor->open($input);
        $editor->resizeFit($editor->getImage()->getWidth() + 100, $editor->getImage()->getHeight() + 100);
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testResizeFitPortrait($editor)
    {

        $input = DIR_TEST_IMG . '/sample.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->resizeFit(200, 200);
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testResizeExact($editor)
    {

        $input = DIR_TEST_IMG . '/sample.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->resizeExact(200, 200);
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

        // Animated gif
        $input = DIR_TEST_IMG . '/sample.gif';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.gif';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.gif';

        $editor->open($input);
        $editor->resizeExact(200, 200);
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct));
    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testResizeExactPortrait($editor)
    {

        $input = DIR_TEST_IMG . '/portrait.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->resizeExact(200, 200);
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testResizeFill($editor)
    {

        $input = DIR_TEST_IMG . '/sample.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->resizeFill(200, 200);
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testResizeFillPortrait($editor)
    {

        $input = DIR_TEST_IMG . '/portrait.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->resizeFill(200, 200);
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testResizeExactWidth($editor)
    {

        $input = DIR_TEST_IMG . '/sample.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->resizeExactWidth(200);
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testResizeExactWidthPortrait($editor)
    {

        $input = DIR_TEST_IMG . '/portrait.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->resizeExactWidth(200);
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testResizeExactHeight($editor)
    {

        $input = DIR_TEST_IMG . '/sample.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->resizeExactHeight(200);
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testResizeExactHeightPortrait($editor)
    {

        $input = DIR_TEST_IMG . '/portrait.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->resizeExactHeight(200);
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testRotate($editor)
    {

        $input = DIR_TEST_IMG . '/sample.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->rotate(45);
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testCubicBezier($editor)
    {

        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->blank(277, 277)->fill(new Color('#FFFFFF'));

        $obj = Grafika::createDrawingObject('CubicBezier', array(42, 230), array(230, 237), array(42, 45), array(230, 43), new Color('#000000'));
        $editor->draw($obj);
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testEllipse($editor)
    {

        $output = DIR_TMP . '/' . __FUNCTION__ . '.png';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.png';

        $editor->blank(200, 200)->fill(new Color('#FFFFFF'));
        $editor->draw(Grafika::createDrawingObject('Ellipse', 100, 50, array(50, 75), 1, new Color('#000000'), new Color('#FF0000')));
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testLines($editor)
    {

        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->blank(200, 200)->fill(new Color('#FFFFFF'));

        $editor->draw(Grafika::createDrawingObject('Line', array(0, 0), array(200, 200), 1, new Color('#FF0000')));
        $editor->draw(Grafika::createDrawingObject('Line', array(0, 200), array(200, 0), 1, new Color('#00FF00')));
        $editor->draw(Grafika::createDrawingObject('Line', array(0, 0), array(200, 100), 1, new Color('#0000FF')));
        $editor->draw(Grafika::createDrawingObject('Line', array(0, 100), array(200, 100)));
        $editor->draw(Grafika::createDrawingObject('Line', array(100, 0), array(100, 200)));

        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($correct, $output));

    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testQuadraticBezier($editor)
    {

        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->blank(277, 277)->fill(new Color('#EEEEEE'));
        $obj = Grafika::createDrawingObject('QuadraticBezier', array(70, 250), array(20, 110), array(220, 60), new Color('#FF0000'));
        $editor->draw($obj);
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testRectangle($editor)
    {

        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->blank(200, 200)->fill(new Color('#CCCCCC'));

        $editor->draw( Grafika::createDrawingObject('Rectangle', 85, 50)); // A 85x50 no filled rectangle with a black 1px border on location 0,0.
        $editor->draw( Grafika::createDrawingObject('Rectangle', 85, 50, array(105, 10), 0, null, new Color('#FF0000'))); // A 85x50 red rectangle with no border.
        $editor->draw( Grafika::createDrawingObject('Rectangle', 85, 50, array(105, 70), 0, null, new Color('#00FF00'))); // A 85x50 green rectangle with no border.
        $editor->draw( Grafika::createDrawingObject('Rectangle', 85, 50, array(0, 60), 1, '#000000', null)); // No fill rectangle

        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testPolygon($editor)
    {

        $output = DIR_TMP . '/' . __FUNCTION__ . '.png';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.png';

        $editor->blank(200, 200)->fill(new Color('#FFFFFF'));

        $editor->draw( Grafika::createDrawingObject('Polygon', array(array(0,0), array(50,0), array(0,50)), 1));
        $editor->draw( Grafika::createDrawingObject('Polygon', array(array(200-1,0), array(150-1,0), array(200-1,50)), 1));
        $editor->draw( Grafika::createDrawingObject('Polygon', array(array(100,0), array(140,50), array(100,100), array(60,50)), 1, null, new Color('#FF0000')));

        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testDither($editor)
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->apply( Grafika::createFilter('Dither') );
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     * TODO: Check why this is failing on travis
     */
    public function testSobel($editor)
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->apply( Grafika::createFilter('Sobel') );
        $editor->save($output);

        //$this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }
    
    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testBlur($editor)
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->apply( new Blur(10) );
        $editor->save($output);
        
        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testBrightness($editor)
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->apply( new Brightness(50) );
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testColorize($editor)
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->apply( new Colorize(-50, -50, -50) );
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testContrast($editor)
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->apply( Grafika::createFilter('Contrast', 50) );
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testGamma($editor)
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->apply( Grafika::createFilter('Gamma', 2.0) );
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }
    
    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testGrayscale($editor)
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->apply( Grafika::createFilter('Grayscale') );
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

        // Test on animated GIF
        $input = DIR_TEST_IMG . '/sample.gif';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.gif';

        $editor->open($input);
        $editor->apply( Grafika::createFilter('Grayscale') );
        $editor->save($output);

        $editor->open($output);
        $image = $editor->getImage();

        $this->assertTrue($image->isAnimated()); // It should still be animated GIF

    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testInvert($editor)
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->apply( Grafika::createFilter('Invert') );
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testPixelate($editor)
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->apply( Grafika::createFilter('Pixelate', 10) );
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testSharpen($editor)
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $editor->open($input);
        $editor->apply( Grafika::createFilter('Sharpen', 50) );
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }
    
    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testCrop($editor)
    {
        $input   = DIR_TEST_IMG . '/crop-test.jpg';

        $output1  = DIR_TMP . '/' . __FUNCTION__ . '1.jpg';
        $correct1 = $this->dirAssert . '/' . __FUNCTION__ . '1.jpg';

        $output2  = DIR_TMP . '/' . __FUNCTION__ . '2.jpg';
        $correct2 = $this->dirAssert . '/' . __FUNCTION__ . '2.jpg';

        $output3  = DIR_TMP . '/' . __FUNCTION__ . '3.jpg';
        $correct3 = $this->dirAssert . '/' . __FUNCTION__ . '3.jpg';

        $output4  = DIR_TMP . '/' . __FUNCTION__ . '4.jpg';
        $correct4 = $this->dirAssert . '/' . __FUNCTION__ . '4.jpg';

        $output5  = DIR_TMP . '/' . __FUNCTION__ . '5.jpg';
        $correct5 = $this->dirAssert . '/' . __FUNCTION__ . '5.jpg';

        $output6  = DIR_TMP . '/' . __FUNCTION__ . '6.jpg';
        $correct6 = $this->dirAssert . '/' . __FUNCTION__ . '6.jpg';

        $output7  = DIR_TMP . '/' . __FUNCTION__ . '7.jpg';
        $correct7 = $this->dirAssert . '/' . __FUNCTION__ . '7.jpg';

        $output8  = DIR_TMP . '/' . __FUNCTION__ . '8.jpg';
        $correct8 = $this->dirAssert . '/' . __FUNCTION__ . '8.jpg';

        $output9  = DIR_TMP . '/' . __FUNCTION__ . '9.jpg';
        $correct9 = $this->dirAssert . '/' . __FUNCTION__ . '9.jpg';

        $editor->open($input);
        $editor->crop(260, 150, 'top-left');
        $editor->save($output1);
        $this->assertLessThanOrEqual(5, $editor->compare($output1, $correct1));

        $editor->open($input);
        $editor->crop(260, 150, 'top-center');
        $editor->save($output2);
        $this->assertLessThanOrEqual(5, $editor->compare($output2, $correct2));

        $editor->open($input);
        $editor->crop(260, 150, 'top-right');
        $editor->save($output3);
        $this->assertLessThanOrEqual(5, $editor->compare($output3, $correct3));

        //
        $editor->open($input);
        $editor->crop(260, 150, 'center-left');
        $editor->save($output4);
        $this->assertLessThanOrEqual(5, $editor->compare($output4, $correct4));

        $editor->open($input);
        $editor->crop(260, 150, 'center');
        $editor->save($output5);
        $this->assertLessThanOrEqual(5, $editor->compare($output5, $correct5));

        $editor->open($input);
        $editor->crop(260, 150, 'center-right');
        $editor->save($output6);
        $this->assertLessThanOrEqual(5, $editor->compare($output6, $correct6));
        //
        $editor->open($input);
        $editor->crop(260, 150, 'bottom-left');
        $editor->save($output7);
        $this->assertLessThanOrEqual(5, $editor->compare($output7, $correct7));

        $editor->open($input);
        $editor->crop(260, 150, 'bottom-center');
        $editor->save($output8);
        $this->assertLessThanOrEqual(5, $editor->compare($output8, $correct8));

        $editor->open($input);
        $editor->crop(260, 150, 'bottom-right');
        $editor->save($output9);
        $this->assertLessThanOrEqual(5, $editor->compare($output9, $correct9));
    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testSmartCrop($editor)
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '1.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '1.jpg';

        $editor->open($input);
        $editor->crop(250,250,'smart');
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($correct, $output)); // Account for minor variations due to different GD versions (GD image that gen. asserts is different on the testing site)

        $input = DIR_TEST_IMG . '/tower.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '2.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '2.jpg';

        $editor->open($input);
        $editor->crop(260,400,'smart');
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($correct, $output)); // Account for minor variations due to different GD versions (GD image that gen. asserts is different on the testing site)

        $input = DIR_TEST_IMG . '/portal-companion-cube.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '3.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '3.jpg';

        $editor->open($input);
        $editor->crop(200,200,'smart');
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($correct, $output)); // Account for minor variations due to different GD versions (GD image that gen. asserts is different on the testing site)

        $input = DIR_TEST_IMG . '/sample.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '4.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '4.jpg';

        $editor->open($input);
        $editor->crop(200,200,'smart');
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($correct, $output)); // Account for minor variations due to different GD versions (GD image that gen. asserts is different on the testing site)

        $input = DIR_TEST_IMG . '/sample.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '5.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '5.jpg';

        $editor->open($input);
        $editor->crop(200,200,'smart');
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($correct, $output)); // Account for minor variations due to different GD versions (GD image that gen. asserts is different on the testing site)
    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testFlattenAnimatedGif($editor)
    {

        // Animated gif
        $input = DIR_TEST_IMG . '/sample.gif';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.gif';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.gif';

        $editor->open($input);
        $editor->flatten();
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct));
    }

    /**
     * @depends testEqualFalse
     * @param EditorInterface $editor
     */
    public function testFlip($editor)
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . 'H.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . 'H.jpg';

        $editor->open($input);
        $editor->flip('h');
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

        $output = DIR_TMP . '/' . __FUNCTION__ . 'V.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . 'V.jpg';

        $editor->open($input);
        $editor->flip('v');
        $editor->save($output);

        $this->assertLessThanOrEqual(5, $editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }

    // On before every test
    protected function setUp()
    {
        $editor = new Editor();
        if (false === $editor->isAvailable()) {
            $this->markTestSkipped(
                'PHP Imagick is not available.'
            );
        }
    }

    // After every test
    protected function tearDown()
    {
        if (CLEAN_DUMP) {
            deleteTmpDirectory(); // Delete images created by a test
        }
    }


    public static function setUpBeforeClass()
    {

    }

    public static function tearDownAfterClass()
    {

    }
}