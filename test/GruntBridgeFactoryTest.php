<?php

/*
 * This file is part of the Composer NPM bridge package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Peertopark\Composer\GruntBridge;

use Composer\IO\NullIO;
use PHPUnit_Framework_TestCase;

class GruntBridgeFactoryTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        $this->factory = GruntBridgeFactory::create();
        $this->io = new NullIO();
    }

    public function testCreateBridge() {
        $expected = new GruntBridge($this->io, new GruntVendorFinder(), GruntClient::create());
        $this->assertEquals($expected, $this->factory->createBridge($this->io));
    }

}
