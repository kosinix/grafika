<?php
use Grafika\Gd\Editor;
use Grafika\Gd\Image;
use Grafika\ImageType;

class GdImageTest extends PHPUnit_Framework_TestCase {

    // Instance tests
    public function testConstructorInstance() {
        
        $image = new Image(imagecreatetruecolor(1, 1), '', 1, 1, ImageType::UNKNOWN);
        
        $this->assertTrue($image instanceof \Grafika\Gd\Image);
    }

    public function testCreateFromFile() {

        $imageFile = DIR_TEST_IMG.'/sample.jpg';
        $image = Image::createFromFile($imageFile);

        $this->assertTrue($image instanceof \Grafika\Gd\Image);
    }

    public function testCreateJpegFail() {
        if (version_compare(PHP_VERSION, '5.6', '>=')) {
            $this->expectException('\Exception');

            $input = realpath(DIR_TEST_IMG . '/png-disguised-as-jpeg.jpg');
            $image = Image::createJpeg($input);

            $this->assertTrue($image instanceof \Grafika\Gd\Image);
        }
    }

    // On before every test
    protected function setUp() {
        $editor = new Editor();
        if ( false === $editor->isAvailable() ) {
            $this->markTestSkipped(
                'PHP GD is not available.'
            );
        }
    }

    // After every test
    protected function tearDown() {
        if(CLEAN_DUMP) {
            deleteTmpDirectory(); // Delete images created by a test
        }
    }


    public static function setUpBeforeClass() {

    }

    public static function tearDownAfterClass() {

    }
}