<?php

use Winter\Storm\Assetic\Filter\StylesheetMinify;

include __DIR__ . '/MockAsset.php';

class StylesheetMinifyTest extends TestCase
{
    public function testSpaceRemoval()
    {
        $input  = 'body{width: calc(99.9% * 1/1 - 0px); height: 0px;}';
        $output = 'body{width:calc(99.9% * 1/1 - 0px);height:0}';

        $mockAsset = new MockAsset($input);
        $result    = new StylesheetMinify();
        $result->filterDump($mockAsset);

        $this->assertEquals($output, $mockAsset->getContent());
    }

    public function testEmptyClassPreserve()
    {
        $input = <<<CSS
        .view { /*
            * Text
            */
            /*
            * Links
            */
            /*
            * Table
            */
            /*
            * Table cell
            */
            /*
            * Images
            */ }
        CSS;
        $output = '.view{}';

        $mockAsset = new MockAsset($input);
        $result    = new StylesheetMinify();
        $result->filterDump($mockAsset);

        $this->assertEquals($output, $mockAsset->getContent());
    }

    public function testSpecialCommentPreservation()
    {
        $input  = 'body {/*! Keep me */}';
        $output = 'body{/*! Keep me */}';

        $mockAsset = new MockAsset($input);
        $result = new StylesheetMinify();
        $result->filterDump($mockAsset);

        $this->assertEquals($output, $mockAsset->getContent());
    }

    public function testCommentRemoval()
    {
        $input  = 'body{/* First comment */} /* Second comment */';
        $output = 'body{}';

        $mockAsset = new MockAsset($input);
        $result    = new StylesheetMinify();
        $result->filterDump($mockAsset);

        $this->assertEquals($output, $mockAsset->getContent());
    }

    public function testCommentPreservationInVar()
    {
        $input  = '--ring-inset: var(--empty, /*!*/ /*!*/);';
        $output = '--ring-inset:var(--empty,/*!*/ /*!*/);';

        $mockAsset = new MockAsset($input);
        $result    = new StylesheetMinify();
        $result->filterDump($mockAsset);

        $this->assertEquals($output, $mockAsset->getContent());
    }

    public function testUnitPreservationInVar()
    {
        $input  = '--offset-width: 0px';
        $output = '--offset-width:0';

        $mockAsset = new MockAsset($input);
        $result    = new StylesheetMinify();
        $result->filterDump($mockAsset);

        $this->assertEquals($output, $mockAsset->getContent());
    }

    public function testAttributeSelectorsWithLess()
    {
        $input = <<<CSS
            [class^="icon-"]:before,
            [class*=" icon-"]:before {
                speak: none;
            }
            /* makes the font 33% larger relative to the icon container */
            .icon-large:before {
                speak: initial;
            }
CSS;

        $output = <<<CSS
[class^="icon-"]:before,[class*=" icon-"]:before{speak:none}.icon-large:before{speak:initial}
CSS;

        $mockAsset = new MockAsset($input);
        $result    = new StylesheetMinify();
        $result->filterDump($mockAsset);

        $this->assertEquals($output, $mockAsset->getContent());
    }
}
