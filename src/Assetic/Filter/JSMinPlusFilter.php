<?php namespace Winter\Storm\Assetic\Filter;

/*
 * This file is part of the Assetic package, an OpenSky project.
 *
 * (c) 2010-2014 OpenSky Project Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Winter\Storm\Assetic\Asset\AssetInterface;

/**
 * Filters assets through JSMinPlus.
 *
 * All credit for the filter itself is mentioned in the file itself.
 *
 * @link https://raw.github.com/mrclay/minify/master/min/lib/JSMinPlus.php
 * @author Brunoais <brunoaiss@gmail.com>
 */
class JSMinPlusFilter implements FilterInterface
{
    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $asset->setContent(\JSMinPlus::minify($asset->getContent()));
    }
}
