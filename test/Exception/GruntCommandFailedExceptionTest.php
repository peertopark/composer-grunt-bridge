<?php

/*
 * This file is part of the Composer NPM bridge package.
 *
 * Copyright © 2016 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Peertopark\Composer\GruntBridge\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class GruntCommandFailedExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $cause = new Exception();
        $exception = new GruntCommandFailedException('command', $cause);

        $this->assertSame('command', $exception->command());
        $this->assertSame("Execution of 'command' failed.", $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($cause, $exception->getPrevious());
    }
}
