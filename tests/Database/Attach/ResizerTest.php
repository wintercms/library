<?php

use Winter\Storm\Database\Attach\Resizer;

class ResizerTest extends TestCase
{
    use \AssertGD\GDAssertTrait;

    // Controls whether the test compares against existing fixtures or generates new ones
    // Should be false when we are actually running the tests, true when we are generating fixtures
    const GENERATE_NEW_FIXTURES = false;

    // Fixture base paths
    const FIXTURE_PATH = __DIR__ . '/../../fixtures/';
    const FIXTURE_SRC_BASE_PATH = self::FIXTURE_PATH . 'resizer/source/';
    const FIXTURE_TARGET_PATH = self::FIXTURE_PATH . 'resizer/target/';
    const TMP_TEST_FILE_PATH = self::FIXTURE_PATH . 'tmp/';

    // Source image filenames
    const SRC_LANDSCAPE_ROTATED = 'landscape_rotated.jpg';
    const SRC_LANDSCAPE_TRANSPARENT = 'landscape_transparent.png';
    const SRC_PORTRAIT = 'portrait.gif';
    const SRC_SQUARE = 'square.jpg';

    /**
     * Fixtures that are common to multiple tests (reduce number of images and noise for identical results)
     */
    const COMMON_FIXTURES = [
        'reset' => 'testReset_testResize0x0_testResizeAutoLandscape1x1',
        'square' => 'testResizeAutoSquare25x50_testResizeAutoSquare50x25_testResizeAutoSquare50x50_testResizeFitSquare50x50'
    ];

    /** @var string The path to the source image */
    protected $source;

    /** @var string The path to the target image (fixture) */
    protected $target;

    /** @var string The path to the image being generated by the test */
    protected $tmpTarget;

    /** @var string The path to the extension of the target file */
    protected $extension;

    /** @var Resizer The Resizer instance (unit under test) */
    protected $resizer;

    /**
     * Remove the temporary file after running each test.
     */
    protected function tearDown(): void
    {
        @unlink($this->tmpTarget);
        @rmdir(self::TMP_TEST_FILE_PATH);
        parent::tearDown();
    }

    /**
     * Given a Resizer with any image
     * When the resize method is called with altering parameters followed by the reset method
     * Then the saved image should be the same as the original one (size, color and transparency)
     * @throws Exception
     */
    public function testReset()
    {
        $this->setSource(self::SRC_LANDSCAPE_TRANSPARENT);
        $this->createFixtureResizer();
        $this->resizer->resize(5, 5, ['mode' => 'crop']);
        $this->resizer->reset();
        $this->assertImageSameAsFixture(self::COMMON_FIXTURES['reset']);
    }

    /**
     * Given a Resizer with any image
     * When the resize method is called with 0x0
     * Then the saved image should be the same as the original one (size, color and transparency)
     * @throws Exception
     */
    public function testResize0x0()
    {
        $this->setSource(self::SRC_LANDSCAPE_TRANSPARENT);
        $this->createFixtureResizer();
        $this->resizer->resize(0, 0);
        $this->assertImageSameAsFixture(self::COMMON_FIXTURES['reset']);
    }

    /**
     * Given a Resizer with any image
     * When the resize method is called with 20x0
     * Then the saved image should have a width of 20 and its height set automatically
     * @throws Exception
     */
    public function testResize20x0()
    {
        $this->setSource(self::SRC_PORTRAIT);
        $this->createFixtureResizer();
        $this->resizer->resize(20, 0);
        $this->assertImageSameAsFixture(__METHOD__);
    }

    /**
     * Given a Resizer with any image
     * When the resize method is called with 0x20
     * Then the saved image should have a height of 20 and its width set automatically
     * @throws Exception
     */
    public function testResize0x20()
    {
        $this->setSource(self::SRC_PORTRAIT);
        $this->createFixtureResizer();
        $this->resizer->resize(0, 20);
        $this->assertImageSameAsFixture(__METHOD__);
    }

    /**
     * Given a Resizer with a portrait image
     * When the resize method is called with the auto parameter and 25x50 dimensions
     * Then the saved image should have a height of 50 and its width set automatically
     * @throws Exception
     */
    public function testResizeAutoPortrait50()
    {
        $this->setSource(self::SRC_PORTRAIT);
        $this->createFixtureResizer();
        $this->resizer->resize(25, 50, ['mode' => 'auto']);
        $this->assertImageSameAsFixture(__METHOD__);
    }

    /**
     * Given a Resizer with a landscape image
     * When the resize method is called with the auto parameter and 25x50 dimensions
     * Then the saved image should have a width of 25 and its height set automatically
     * @throws Exception
     */
    public function testResizeAutoLandscape25()
    {
        $this->setSource(self::SRC_LANDSCAPE_TRANSPARENT);
        $this->createFixtureResizer();
        $this->resizer->resize(25, 50, ['mode' => 'auto']);
        $this->assertImageSameAsFixture(__METHOD__);
    }

    /**
     * Given a Resizer with a square image
     * When the resize method is called with the auto parameter and a 25x50 dimension
     * Then the saved image should have be 50x50 (largest dimension takes over)
     * @throws Exception
     */
    public function testResizeAutoSquare25x50()
    {
        $this->setSource(self::SRC_SQUARE);
        $this->createFixtureResizer();
        $this->resizer->resize(25, 50, ['mode' => 'auto']);
        $this->assertImageSameAsFixture(self::COMMON_FIXTURES['square']);
    }

    /**
     * Given a Resizer with a square image
     * When the resize method is called with the auto parameter and a 50x25 dimension
     * Then the saved image should have be 50x50 (largest dimension takes over)
     * @throws Exception
     */
    public function testResizeAutoSquare50x25()
    {
        $this->setSource(self::SRC_SQUARE);
        $this->createFixtureResizer();
        $this->resizer->resize(50, 25, ['mode' => 'auto']);
        $this->assertImageSameAsFixture(self::COMMON_FIXTURES['square']);
    }

    /**
     * Given a Resizer with a square image
     * When the resize method is called with the auto parameter and a 50x50 dimension
     * Then the saved image should have be 50x50
     * @throws Exception
     */
    public function testResizeAutoSquare50x50()
    {
        $this->setSource(self::SRC_SQUARE);
        $this->createFixtureResizer();
        $this->resizer->resize(50, 50, ['mode' => 'auto']);
        $this->assertImageSameAsFixture(self::COMMON_FIXTURES['square']);
    }

    /**
     * Given a Resizer with a transparent landscape image
     * When the resize method is called with the auto parameter and 1x1 dimensions
     * Then the saved image should be the same as the original one (size, color and transparency)
     * @throws Exception
     */
    public function testResizeAutoLandscape1x1()
    {
        $this->setSource(self::SRC_LANDSCAPE_TRANSPARENT);
        $this->createFixtureResizer();
        $this->resizer->resize(1, 1);
        $this->assertImageSameAsFixture(self::COMMON_FIXTURES['reset']);
    }

    /**
     * Given a Resizer with a transparent landscape image
     * When the resize method is called with the auto parameter and 1x5 dimensions
     * Then the saved image should be have a height of 5 and an automatic width
     * @throws Exception
     */
    public function testResizeAutoLandscape1x5()
    {
        $this->setSource(self::SRC_LANDSCAPE_TRANSPARENT);
        $this->createFixtureResizer();
        $this->resizer->resize(1, 5);
        $this->assertImageSameAsFixture(__METHOD__);
    }

    /**
     * Given a Resizer with a transparent landscape image
     * When the resize method is called with the auto parameter and 50x1 dimensions
     * Then the saved image should have a width a 25 and an automatic height
     * @throws Exception
     */
    public function testResizeAutoLandscape25x1()
    {
        $this->setSource(self::SRC_LANDSCAPE_TRANSPARENT);
        $this->createFixtureResizer();
        $this->resizer->resize(25, 1);
        $this->assertImageSameAsFixture(__METHOD__);
    }

    /**
     * Given a Resizer with a square image
     * When the resize method is called with the exact mode and 10x1=15 dimensions
     * Then the saved image should be 10x15 and distorted
     * @throws Exception
     */
    public function testResizeExact10x15()
    {
        $this->setSource(self::SRC_SQUARE);
        $this->createFixtureResizer();
        $this->resizer->resize(10, 15, ['mode' => 'exact']);
        $this->assertImageSameAsFixture(__METHOD__);
    }

    /**
     * Given a Resizer with any image
     * When the resize method is called with the landscape mode and 10x1 dimensions
     * Then the saved image should have a width of 10 and an automatic height
     * @throws Exception
     */
    public function testResizeLandscape10x1()
    {
        $this->setSource(self::SRC_PORTRAIT);
        $this->createFixtureResizer();
        $this->resizer->resize(10, 1, ['mode' => 'landscape']);
        $this->assertImageSameAsFixture(__METHOD__);
    }

    /**
     * Given a Resizer with any image
     * When the resize method is called with the portrait mode and 1x10 dimensions
     * Then the saved image should have a width of 10 and an automatic height
     * @throws Exception
     */
    public function testResizePortrait1x10()
    {
        $this->setSource(self::SRC_PORTRAIT);
        $this->createFixtureResizer();
        $this->resizer->resize(1, 10, ['mode' => 'portrait']);
        $this->assertImageSameAsFixture(__METHOD__);
    }

    /**
     * Given a Resizer with a landscape image
     * When the resize method is called with the fit mode and 30x30 dimensions
     * Then the saved image should have a width of 30 and an automatic height
     * @throws Exception
     */
    public function testResizeFitLandscape30x30()
    {
        $this->setSource(self::SRC_LANDSCAPE_TRANSPARENT);
        $this->createFixtureResizer();
        $this->resizer->resize(30, 30, ['mode' => 'fit']);
        $this->assertImageSameAsFixture(__METHOD__);
    }

    /**
     * Given a Resizer with a landscape image
     * When the resize method is called with the fit mode and 30x30 dimensions
     * Then the saved image should have a height of 30 and an automatic width
     * @throws Exception
     */
    public function testResizeFitPortrait30x30()
    {
        $this->setSource(self::SRC_PORTRAIT);
        $this->createFixtureResizer();
        $this->resizer->resize(30, 30, ['mode' => 'fit']);
        $this->assertImageSameAsFixture(__METHOD__);
    }

    /**
     * Given a Resizer with a square image
     * When the resize method is called with the fit mode and 50x50 dimensions
     * Then the saved image should have be 50x50
     * @throws Exception
     */
    public function testResizeFitSquare50x50()
    {
        $this->setSource(self::SRC_SQUARE);
        $this->createFixtureResizer();
        $this->resizer->resize(50, 50, ['mode' => 'fit']);
        $this->assertImageSameAsFixture(self::COMMON_FIXTURES['square']);
    }

    /**
     * Given a Resizer with a JPG image which has an EXIF tag (Rotation=8)
     * When the resize method is called with the auto mode and 30x30 dimensions
     * Then the saved image should have the EXIF rotation applied and
     * @throws Exception
     */
    public function testResizeAutoExifRotated30x30()
    {
        if (!function_exists('exif_read_data')) {
            $this->markTestSkipped('Missing exif extension');
        }

        $this->setSource(self::SRC_LANDSCAPE_ROTATED);
        $this->createFixtureResizer();
        $this->resizer->resize(30, 30);
        $this->assertImageSameAsFixture(__METHOD__);
    }

    /**
     * Given a Resizer with any image
     * When the sharpen method is called with a valid value
     * Then the saved image should be sharpened by the given value
     * @throws Exception
     */
    public function testSharpen()
    {
        $this->setSource(self::SRC_SQUARE);
        $this->createFixtureResizer();
        $this->resizer->resize(25, 25, ['sharpen' => 50]);
        $this->assertImageSameAsFixture(__METHOD__);
    }

    /**
     * Given a Resizer with any image
     * When the crop method is called with startX, startY, newWidth and newHeight parameters
     * Then the saved image should be cropped as expected
     * @throws Exception
     */
    public function testCrop10x15()
    {
        $this->setSource(self::SRC_PORTRAIT);
        $this->createFixtureResizer();
        $this->resizer->crop(3, 5, 10, 15);
        $this->assertImageSameAsFixture(__METHOD__);
    }

    /**
     * Set the source path and set the extension to match.
     * @param string $source Path to the source image for the Resizer
     */
    protected function setSource(string $source)
    {
        $this->source = $source;
        $this->extension = pathinfo($this->source, PATHINFO_EXTENSION);
    }

    /**
     * Create the Resizer instance from the declared source image.
     * @throws Exception
     */
    protected function createFixtureResizer()
    {
        $this->resizer = new Resizer(self::FIXTURE_SRC_BASE_PATH . $this->source);
    }

    /**
     * Build the full path to the target fixture from a test method name.
     * @param string $methodName Method name
     * @return string Full path to target fixture
     */
    protected function buildTargetFixturePath(string $methodName)
    {
        $filename = str_replace(__CLASS__ . '::', '', $methodName);

        if (!is_dir(self::TMP_TEST_FILE_PATH)) {
            mkdir(self::TMP_TEST_FILE_PATH);
        }

        $this->tmpTarget = self::TMP_TEST_FILE_PATH . $filename . '.' . $this->extension;
        $this->target = self::FIXTURE_TARGET_PATH . $filename . '.' . $this->extension;
    }

    /**
     * Assert that the current Resizer image, once saved, is the same as the fixture which corresponds to the given
     * method name.
     * @param string $methodName Method name
     * @throws Exception
     */
    protected function assertImageSameAsFixture(string $methodName)
    {
        if (self::GENERATE_NEW_FIXTURES) {
            $this->generateFixture($methodName);
        }
        else {
            $this->buildTargetFixturePath($methodName);

            // Save resizer result to temp file
            $this->resizer->save($this->tmpTarget);

            // Assert file is the same as expected output with 1% error permitted to account for library updates and whatnot
            $this->assertSimilarGD(
                $this->tmpTarget,
                $this->target,
                $methodName . ' result did not match ' . $this->target,
                0.01
            );
        }
    }

    /**
     * Generate a fixture image for the given method name using current Resizer instance.
     * This image has to be validated manually once to ensure the result is as expected.
     * @param string $methodName
     * @throws Exception
     */
    protected function generateFixture(string $methodName)
    {
        $this->buildTargetFixturePath($methodName);
        $this->resizer->save($this->target);
    }
}
