<?php

/*
 * This file is part of the Composer Grunt bridge package.
 *
 * Copyright (c) 2015 John Bloch
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Peertopark\Composer\GruntBridge;

use Composer\Composer;
use Composer\Package\PackageInterface;

/**
 * The interface implemented by Grunt vendor finders.
 */
interface GruntVendorFinderInterface {
	/**
	 * Find all Grunt bridge enabled vendor packages.
	 *
	 * @param Composer             $composer The Composer object for the root project.
	 * @param GruntBridgeInterface $bridge   The bridge to use.
	 *
	 * @return array<integer,PackageInterface> The list of Grunt bridge enabled vendor packages.
	 */
	public function find( Composer $composer, GruntBridgeInterface $bridge );
}
