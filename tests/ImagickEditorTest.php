<?php

use Grafika\Color;
use Grafika\EditorInterface;
use Grafika\Grafika;
use Grafika\Imagick\Editor;
use Grafika\Imagick\Filter\Brightness;
use Grafika\Imagick\Filter\Colorize;
use Grafika\Imagick\Image;
use PHPUnit\Framework\TestCase;

/**
 * Class ImagickEditorTest
 */
class ImagickEditorTest extends TestCase
{

    protected $dirAssert = DIR_ASSERT_IMAGICK;

    /**
     * @var EditorInterface
     */
    private $editor;

    // On before every test
    protected function setUp(): void
    {
        $this->editor = new Editor();
        if (false === $this->editor->isAvailable()) {
            $this->markTestSkipped(
                'PHP Imagick is not available.'
            );
        }
    }

    // After every test
    protected function tearDown(): void
    {
        if (CLEAN_DUMP) {
            deleteTmpDirectory(); // Delete images created by a test
        }
    }

    public function testCreateEditorStatic()
    {
        Grafika::setEditorList(array('Imagick')); // Use Imagick only

        $editor = Grafika::createEditor();

        $this->assertTrue($editor instanceof EditorInterface);
    }

    public function testOpenFail(): void
    {
        if (version_compare(PHP_VERSION, '5.6', '>=')) {
            $this->expectException('\Exception');

            $input = 'unreachable.jpg'; // Non existent file

            Grafika::createImage($input);
        }

    }

    public function testUnknownTypeFail(): void
    {
        if (version_compare(PHP_VERSION, '5.6', '>=')) {
            $this->expectException('\Exception');

            $input = DIR_TEST_IMG . '/sample.wbm';

            Grafika::createImage($input);
        }
    }

    public function testOpenJpeg(): void
    {

        $input = DIR_TEST_IMG . '/sample.jpg';
        $image = Grafika::createImage($input);

        $this->assertTrue($image instanceof Image);
    }

    public function testOpenPng(): void
    {

        $input = DIR_TEST_IMG . '/sample.png';
        $image = Grafika::createImage($input);

        $this->assertTrue($image instanceof Image);
    }

    public function testOpenGif(): void
    {

        $input = DIR_TEST_IMG . '/sample.gif';
        $image = Grafika::createImage($input);

        $this->assertTrue($image instanceof Image);
    }

    public function testEqual(): void
    {
        $input1 = $this->dirAssert . '/testEqual.jpg';

        $this->assertTrue($this->editor->equal($input1, $input1));
    }

    public function testEqualFalse(): void
    {
        $input1 = $this->dirAssert . '/testEqual.jpg';
        $input2 = $this->dirAssert . '/testEqualFalse.png';

        $this->assertFalse($this->editor->equal($input1, $input2));
    }

    /**
     * Test similarity
     */
    public function testCompare(): void
    {

        $input = DIR_TEST_IMG . '/lena.png';
        $input2 = DIR_TEST_IMG . '/lena-gray.png';

        $ham = $this->editor->compare($input, $input2);

        $this->assertLessThan(10, $ham); // hamming distance: 0 is equal, 1-10 is similar, 11+ is different image
    }

    public function testSave(): void
    {
        $input = DIR_TEST_IMG . '/sample.png';
        $output1 = DIR_TMP . '/' . __FUNCTION__ . '1.jpg';
        $output2 = DIR_TMP . '/' . __FUNCTION__ . '2.jpg';
        $output3 = DIR_TMP . '/' . __FUNCTION__ . '3.png';

        $image = Grafika::createImage($input);
        $this->editor->save($image, $output1, 'jpg', 100);
        $this->assertEquals(0, $this->editor->compare($input, $output1));

        $this->editor->save($image, $output2, 'jpg', 0);
        $this->assertGreaterThan(0, $this->editor->compare($input, $output2)); // Not exactly similar due to compression

        $this->editor->save($image, $output3, 'png', null);
        $this->assertEquals(0, $this->editor->compare($input, $output3));

    }

    public function testAddTextOnBlankImage(): void
    {
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $blank = Grafika::createBlankImage(400, 100);
        $this->editor->fill($blank, new Color('#ffffff'));
        $this->editor->text($blank, 'Lorem ipsum - Liberation Sans');
        $this->editor->save($blank, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testAddTextOnJpeg(): void
    {
        $input = DIR_TEST_IMG . '/sample.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->resizeFit($image, 300, 300);
        $this->editor->text($image, 'Lorem ipsum - Liberation Sans');
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function addAlignedTextOnBlankImageDataProvider(): Generator
    {
        $x = [
            EditorInterface::ALIGNMENT_X_LEFT,
            EditorInterface::ALIGNMENT_X_CENTRE,
            EditorInterface::ALIGNMENT_X_RIGHT,
        ];
        $y = [
            EditorInterface::ALIGNMENT_Y_TOP,
            EditorInterface::ALIGNMENT_Y_MIDDLE,
            EditorInterface::ALIGNMENT_Y_BOTTOM,
        ];
        $angles = [
            0,
            -30,
            30,
            -90,
            90,
            135,
            -135,
            180,
        ];
        $offsets = [
            0,
            10,
            20,
        ];

        foreach ($angles as $angle) {
            foreach ($x as $alignmentX) {
                foreach ($y as $alignmentY) {
                    foreach ($offsets as $offsetX) {
                        foreach ($offsets as $offsetY) {
                            yield [$angle, $alignmentX, $alignmentY, $offsetX, $offsetY];
                        }
                    }
                }
            }
        }
    }

    /**
     * @dataProvider addAlignedTextOnBlankImageDataProvider
     * @throws Exception
     */
    public function testAddAlignedTextOnBlankImage(int $angle, string $alignmentX, string $alignmentY, int $offsetX, int $offsetY): void
    {
        $color = new Color('#000000');
        $guideLines = [
            Grafika::createDrawingObject('Line', [0, 12], [400, 12], 1, '#999999'),
            Grafika::createDrawingObject('Line', [0, 200], [400, 200], 1, '#999999'),
            Grafika::createDrawingObject('Line', [0, 388], [400, 388], 1, '#999999'),
            Grafika::createDrawingObject('Line', [12, 0], [12, 400], 1, '#999999'),
            Grafika::createDrawingObject('Line', [200, 0], [200, 400], 1, '#999999'),
            Grafika::createDrawingObject('Line', [388, 0], [388, 400], 1, '#999999'),
        ];

        $string = 'S123456789E';
        $file = sprintf('X%sY%sA%dPx%dPy%d.png', $alignmentX, $alignmentY, $angle, $offsetX, $offsetY);
        $output = DIR_TMP . '/' . __FUNCTION__ . '/' . $file;
        $expected = $this->dirAssert . '/' . __FUNCTION__ . '/' . $file;
        $blank = Grafika::createBlankImage(400, 400);
        $this->editor->fill($blank, new Color('#ffffff'));
        foreach ($guideLines as $line) {
            $this->editor->draw($blank, $line);
        }
        $text = sprintf('A: %d X: %s Y: %s p.X: %d p.Y: %d', $angle, $alignmentX, $alignmentY, $offsetX, $offsetY);
        $this->editor->text($blank, $text, 10, 20, 320, new Color('#FF0000'));
        $this->editor->textAligned($blank, $string, $alignmentX, $alignmentY, $offsetX, $offsetY, $color, 14, '', $angle);
        $this->editor->save($blank, $output, 'png');
        $this->assertLessThanOrEqual(1, $this->editor->compare($output, $expected), $file);
    }

    /**
     * Test enlarging an image to a dimension larger than its original size.
     */
    public function testResizeFitEnlarge(): void
    {

        $input = DIR_TEST_IMG . '/sample.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->resizeFit($image, $image->getWidth() + 100, $image->getHeight() + 100);
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

        // Animated gif
        $input = DIR_TEST_IMG . '/sample.gif';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.gif';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.gif';

        $image = Grafika::createImage($input);
        $this->editor->resizeFit($image, $image->getWidth() + 100, $image->getHeight() + 100);
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testResizeFitPortrait(): void
    {
        $input = DIR_TEST_IMG . '/sample.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->resizeFit($image, 200, 200);
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testResizeExact(): void
    {
        $input = DIR_TEST_IMG . '/sample.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->resizeExact($image, 200, 200);
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

        // Animated gif
        $input = DIR_TEST_IMG . '/sample.gif';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.gif';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.gif';

        $image = Grafika::createImage($input);
        $this->editor->resizeExact($image, 200, 200);
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct));
    }

    public function testResizeExactPortrait(): void
    {
        $input = DIR_TEST_IMG . '/portrait.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->resizeExact($image, 200, 200);
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testResizeFill(): void
    {
        $input = DIR_TEST_IMG . '/sample.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->resizeFill($image, 200, 200);
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testResizeFillPortrait(): void
    {
        $input = DIR_TEST_IMG . '/portrait.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->resizeFill($image, 200, 200);
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testResizeExactWidth(): void
    {
        $input = DIR_TEST_IMG . '/sample.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->resizeExactWidth($image, 200);
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testResizeExactWidthPortrait(): void
    {
        $input = DIR_TEST_IMG . '/portrait.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->resizeExactWidth($image, 200);
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testResizeExactHeight(): void
    {
        $input = DIR_TEST_IMG . '/sample.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->resizeExactHeight($image, 200);
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testResizeExactHeightPortrait(): void
    {
        $input = DIR_TEST_IMG . '/portrait.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->resizeExactHeight($image, 200);
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testRotate(): void
    {
        $input = DIR_TEST_IMG . '/sample.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->rotate($image, 45);
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testCubicBezier(): void
    {
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createBlankImage(277, 277);
        $this->editor->fill($image, new Color('#FFFFFF'));

        $obj = Grafika::createDrawingObject('CubicBezier', array(42, 230), array(230, 237), array(42, 45), array(230, 43), new Color('#000000'));
        $this->editor->draw($image, $obj);
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testEllipse(): void
    {
        $output = DIR_TMP . '/' . __FUNCTION__ . '.png';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.png';

        $image = Grafika::createBlankImage(200, 200);
        $this->editor->fill($image, new Color('#FFFFFF'));

        $obj = Grafika::createDrawingObject('Ellipse', 100, 50, array(50, 75), 1, new Color('#000000'), new Color('#FF0000'));
        $this->editor->draw($image, $obj);
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testLines(): void
    {
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createBlankImage(200, 200);
        $this->editor->fill($image, new Color('#FFFFFF'));

        $this->editor->draw($image, Grafika::createDrawingObject('Line', array(0, 0), array(200, 200), 1, new Color('#FF0000')));
        $this->editor->draw($image, Grafika::createDrawingObject('Line', array(0, 200), array(200, 0), 1, new Color('#00FF00')));
        $this->editor->draw($image, Grafika::createDrawingObject('Line', array(0, 0), array(200, 100), 1, new Color('#0000FF')));
        $this->editor->draw($image, Grafika::createDrawingObject('Line', array(0, 100), array(200, 100)));
        $this->editor->draw($image, Grafika::createDrawingObject('Line', array(100, 0), array(100, 200)));

        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($correct, $output));
    }

    public function testQuadraticBezier(): void
    {
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createBlankImage(277, 277);
        $this->editor->fill($image, new Color('#EEEEEE'));

        $obj = Grafika::createDrawingObject('QuadraticBezier', array(70, 250), array(20, 110), array(220, 60), new Color('#FF0000'));
        $this->editor->draw($image, $obj);
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }

    public function testRectangle(): void
    {
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createBlankImage(200, 200);
        $this->editor->fill($image, new Color('#CCCCCC'));

        $this->editor->draw($image, Grafika::createDrawingObject('Rectangle', 85, 50)); // A 85x50 no filled rectangle with a black 1px border on location 0,0.
        $this->editor->draw($image, Grafika::createDrawingObject('Rectangle', 85, 50, array(105, 10), 0, null, new Color('#FF0000'))); // A 85x50 red rectangle with no border.
        $this->editor->draw($image, Grafika::createDrawingObject('Rectangle', 85, 50, array(105, 70), 0, null, new Color('#00FF00'))); // A 85x50 green rectangle with no border.
        $this->editor->draw($image, Grafika::createDrawingObject('Rectangle', 85, 50, array(0, 60), 1, '#000000', null)); // No fill rectangle

        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testPolygon(): void
    {
        $output = DIR_TMP . '/' . __FUNCTION__ . '.png';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.png';

        $image = Grafika::createBlankImage(200, 200);
        $this->editor->fill($image, new Color('#FFFFFF'));

        $this->editor->draw($image, Grafika::createDrawingObject('Polygon', array(array(0, 0), array(50, 0), array(0, 50)), 1));
        $this->editor->draw($image, Grafika::createDrawingObject('Polygon', array(array(200 - 1, 0), array(150 - 1, 0), array(200 - 1, 50)), 1));
        $this->editor->draw($image, Grafika::createDrawingObject('Polygon', array(array(100, 0), array(140, 50), array(100, 100), array(60, 50)), 1, null, new Color('#FF0000')));

        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

    }

    public function testDither(): void
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . 'Diffusion.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . 'Diffusion.jpg';

        $image = Grafika::createImage($input);
        $this->editor->apply($image, Grafika::createFilter('Dither', 'diffusion'));
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . 'Ordered.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . 'Ordered.jpg';

        $image = Grafika::createImage($input);
        $this->editor->apply($image, Grafika::createFilter('Dither', 'ordered'));
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    /**
     * TODO: Check why this is failing on travis
     */
    public function testSobel(): void
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->apply($image, Grafika::createFilter('Sobel'));
        $this->editor->save($image, $output);

        //$this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testBlur(): void
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->apply($image, Grafika::createFilter('Blur', 10));
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testBrightness(): void
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->apply($image, new Brightness(50));
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testColorize(): void
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->apply($image, new Colorize(-50, -50, -50));
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testContrast(): void
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->apply($image, Grafika::createFilter('Contrast', 50));
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testGamma(): void
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->apply($image, Grafika::createFilter('Gamma', 2.0));
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testGrayscale(): void
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->apply($image, Grafika::createFilter('Grayscale'));
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

        // Test on animated GIF
        $input = DIR_TEST_IMG . '/sample.gif';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.gif';

        $image = Grafika::createImage($input);
        $this->editor->apply($image, Grafika::createFilter('Grayscale'));
        $this->editor->save($image, $output);

        // TODO It looks like it's animated, however only the first frame is grayscale.
        $this->assertTrue($image->isAnimated()); // It should still be animated GIF
    }

    public function testInvert(): void
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->apply($image, Grafika::createFilter('Invert'));
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testPixelate(): void
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->apply($image, Grafika::createFilter('Pixelate', 10));
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testSharpen(): void
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image = Grafika::createImage($input);
        $this->editor->apply($image, Grafika::createFilter('Sharpen', 50));
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testCrop(): void
    {
        $input = DIR_TEST_IMG . '/crop-test.jpg';

        $output1 = DIR_TMP . '/' . __FUNCTION__ . '1.jpg';
        $correct1 = $this->dirAssert . '/' . __FUNCTION__ . '1.jpg';

        $output2 = DIR_TMP . '/' . __FUNCTION__ . '2.jpg';
        $correct2 = $this->dirAssert . '/' . __FUNCTION__ . '2.jpg';

        $output3 = DIR_TMP . '/' . __FUNCTION__ . '3.jpg';
        $correct3 = $this->dirAssert . '/' . __FUNCTION__ . '3.jpg';

        $output4 = DIR_TMP . '/' . __FUNCTION__ . '4.jpg';
        $correct4 = $this->dirAssert . '/' . __FUNCTION__ . '4.jpg';

        $output5 = DIR_TMP . '/' . __FUNCTION__ . '5.jpg';
        $correct5 = $this->dirAssert . '/' . __FUNCTION__ . '5.jpg';

        $output6 = DIR_TMP . '/' . __FUNCTION__ . '6.jpg';
        $correct6 = $this->dirAssert . '/' . __FUNCTION__ . '6.jpg';

        $output7 = DIR_TMP . '/' . __FUNCTION__ . '7.jpg';
        $correct7 = $this->dirAssert . '/' . __FUNCTION__ . '7.jpg';

        $output8 = DIR_TMP . '/' . __FUNCTION__ . '8.jpg';
        $correct8 = $this->dirAssert . '/' . __FUNCTION__ . '8.jpg';

        $output9 = DIR_TMP . '/' . __FUNCTION__ . '9.jpg';
        $correct9 = $this->dirAssert . '/' . __FUNCTION__ . '9.jpg';

        $image = Grafika::createImage($input);
        $this->editor->crop($image, 260, 150, 'top-left');
        $this->editor->save($image, $output1);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output1, $correct1));

        $image = Grafika::createImage($input);
        $this->editor->crop($image, 260, 150, 'top-center');
        $this->editor->save($image, $output2);
        $this->assertLessThanOrEqual(5, $this->editor->compare($output2, $correct2));

        $image = Grafika::createImage($input);
        $this->editor->crop($image, 260, 150, 'top-right');
        $this->editor->save($image, $output3);
        $this->assertLessThanOrEqual(5, $this->editor->compare($output3, $correct3));

        //
        $image = Grafika::createImage($input);
        $this->editor->crop($image, 260, 150, 'center-left');
        $this->editor->save($image, $output4);
        $this->assertLessThanOrEqual(5, $this->editor->compare($output4, $correct4));

        $image = Grafika::createImage($input);
        $this->editor->crop($image, 260, 150, 'center');
        $this->editor->save($image, $output5);
        $this->assertLessThanOrEqual(5, $this->editor->compare($output5, $correct5));

        $image = Grafika::createImage($input);
        $this->editor->crop($image, 260, 150, 'center-right');
        $this->editor->save($image, $output6);
        $this->assertLessThanOrEqual(5, $this->editor->compare($output6, $correct6));
        //
        $image = Grafika::createImage($input);
        $this->editor->crop($image, 260, 150, 'bottom-left');
        $this->editor->save($image, $output7);
        $this->assertLessThanOrEqual(5, $this->editor->compare($output7, $correct7));

        $image = Grafika::createImage($input);
        $this->editor->crop($image, 260, 150, 'bottom-center');
        $this->editor->save($image, $output8);
        $this->assertLessThanOrEqual(5, $this->editor->compare($output8, $correct8));

        $image = Grafika::createImage($input);
        $this->editor->crop($image, 260, 150, 'bottom-right');
        $this->editor->save($image, $output9);
        $this->assertLessThanOrEqual(5, $this->editor->compare($output9, $correct9));
    }

    public function testSmartCrop(): void
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '1.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '1.jpg';

        $image = Grafika::createImage($input);
        $this->editor->crop($image, 250, 250, 'smart');
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($correct, $output)); // Account for minor variations due to different GD versions (GD image that gen. asserts is different on the testing site)

        $input = DIR_TEST_IMG . '/tower.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '2.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '2.jpg';

        $image = Grafika::createImage($input);
        $this->editor->crop($image, 260, 400, 'smart');
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($correct, $output)); // Account for minor variations due to different GD versions (GD image that gen. asserts is different on the testing site)

        $input = DIR_TEST_IMG . '/portal-companion-cube.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '3.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '3.jpg';

        $image = Grafika::createImage($input);
        $this->editor->crop($image, 200, 200, 'smart');
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($correct, $output)); // Account for minor variations due to different GD versions (GD image that gen. asserts is different on the testing site)

        $input = DIR_TEST_IMG . '/sample.jpg';
        $output = DIR_TMP . '/' . __FUNCTION__ . '4.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '4.jpg';

        $image = Grafika::createImage($input);
        $this->editor->crop($image, 200, 200, 'smart');
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($correct, $output)); // Account for minor variations due to different GD versions (GD image that gen. asserts is different on the testing site)

        $input = DIR_TEST_IMG . '/sample.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '5.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '5.jpg';

        $image = Grafika::createImage($input);
        $this->editor->crop($image, 200, 200, 'smart');
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($correct, $output)); // Account for minor variations due to different GD versions (GD image that gen. asserts is different on the testing site)
    }

    public function testFlattenAnimatedGif(): void
    {
        // Animated gif
        $input = DIR_TEST_IMG . '/sample.gif';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.gif';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.gif';

        $image = Grafika::createImage($input);
        $this->editor->flatten($image);
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct));
    }

    public function testFlip(): void
    {
        $input = DIR_TEST_IMG . '/lena.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . 'H.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . 'H.jpg';

        $image = Grafika::createImage($input);
        $this->editor->flip($image, 'h');
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.

        $output = DIR_TMP . '/' . __FUNCTION__ . 'V.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . 'V.jpg';

        $image = Grafika::createImage($input);
        $this->editor->flip($image, 'v');
        $this->editor->save($image, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }

    public function testBlend(): void
    {
        $input1 = DIR_TEST_IMG . '/lena.png';
        $input2 = DIR_TEST_IMG . '/blend.png';
        $output = DIR_TMP . '/' . __FUNCTION__ . '.jpg';
        $correct = $this->dirAssert . '/' . __FUNCTION__ . '.jpg';

        $image1 = Grafika::createImage($input1);
        $image2 = Grafika::createImage($input2);
        $this->editor->blend($image1, $image2, 'overlay', 0.5, 'center'); // overlay blend, opacity 50%, center position
        $this->editor->save($image1, $output);

        $this->assertLessThanOrEqual(5, $this->editor->compare($output, $correct)); // Account for windows and linux generating different text sizes given the same font size.
    }
}
