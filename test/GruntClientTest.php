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

use Composer\Util\ProcessExecutor;
use Eloquent\Phony\Phpunit\Phony;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Process\ExecutableFinder;

class GruntClientTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        $this->processExecutor = Phony::mock('Composer\Util\ProcessExecutor');
        $this->executableFinder = Phony::mock('Symfony\Component\Process\ExecutableFinder');
        $this->getcwd = Phony::stub();
        $this->chdir = Phony::stub();
        $this->client = new GruntClient($this->processExecutor->mock(), $this->executableFinder->mock(), $this->getcwd, $this->chdir);

        $this->processExecutor->execute('*')->returns(0);
        $this->executableFinder->find('grunt', 'node_modules/.bin/grunt')->returns('/path/to/grunt');
        $this->getcwd->returns('/path/to/cwd');
    }

    public function testRunTask() {
        $this->assertNull($this->client->runTask(null));
        Phony::inOrder(
                $this->executableFinder->find->calledWith('grunt', 'node_modules/.bin/grunt'), $this->processExecutor->execute->calledWith("'/path/to/grunt'")
        );
    }
    
    
    public function testRunTestTask() {
        $this->assertNull($this->client->runTask('test'));
        Phony::inOrder(
                $this->executableFinder->find->calledWith('grunt', 'node_modules/.bin/grunt'), $this->processExecutor->execute->calledWith("'/path/to/grunt' 'test'")
        );
    }

    public function testInstallFailureGruntNotFound() {
        $this->executableFinder->find('grunt', 'node_modules/.bin/grunt')->returns(null);

        $this->setExpectedException('Peertopark\Composer\GruntBridge\Exception\GruntNotFoundException');
        $this->client->runTask(null);
    }

    public function testInstallFailureCommandFailed() {
        $this->processExecutor->execute('*')->returns(1);

        $this->setExpectedException('Peertopark\Composer\GruntBridge\Exception\GruntCommandFailedException');
        $this->client->runTask(null);
    }

}
