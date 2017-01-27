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

use Composer\Composer;
use Composer\Package\Link;
use Composer\Package\Package;
use Composer\Package\RootPackage;
use Eloquent\Phony\Phpunit\Phony;
use PHPUnit_Framework_TestCase;

class GruntBridgeTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        $this->io = Phony::mock('Composer\IO\IOInterface');
        $this->vendorFinder = Phony::mock('Peertopark\Composer\GruntBridge\GruntVendorFinder');
        $this->client = Phony::mock('Peertopark\Composer\GruntBridge\GruntClient');
        $this->bridge = new GruntBridge($this->io->mock(), $this->vendorFinder->mock(), $this->client->mock());

        $this->composer = new Composer();

        $this->rootPackage = new RootPackage('vendor/package', '1.0.0.0', '1.0.0');
        $this->packageA = new Package('vendorA/packageA', '1.0.0.0', '1.0.0');
        $this->packageB = new Package('vendorB/packageB', '1.0.0.0', '1.0.0');

        $this->linkRoot1 = new Link('vendor/package', 'vendorX/packageX');
        $this->linkRoot2 = new Link('vendor/package', 'vendorY/packageY');
        $this->linkRoot3 = new Link('vendor/package', 'peertopark/composer-grunt-bridge');

        $this->installationManager = Phony::mock('Composer\Installer\InstallationManager');
        $this->installationManager->getInstallPath($this->packageA)->returns('/path/to/install/a');
        $this->installationManager->getInstallPath($this->packageB)->returns('/path/to/install/b');

        $this->composer->setPackage($this->rootPackage);
        $this->composer->setInstallationManager($this->installationManager->mock());
    }

    public function testInstall() {
        $this->rootPackage->setRequires(array($this->linkRoot1, $this->linkRoot2, $this->linkRoot3));
        $this->vendorFinder->find($this->composer, $this->bridge)->returns(array($this->packageA, $this->packageB));
        $this->bridge->runGruntTasks($this->composer);
        Phony::inOrder($this->io->write->calledWith('<info>Running Grunt tasks for root project</info>'));
    }

   

    public function testUpdate() {
        $this->rootPackage->setRequires(array($this->linkRoot1, $this->linkRoot2, $this->linkRoot3));
        $this->vendorFinder->find($this->composer, $this->bridge)->returns(array($this->packageA, $this->packageB));
        $this->bridge->runGruntTasks($this->composer);

        Phony::inOrder(
                $this->io->write->calledWith('<info>Running Grunt tasks for root project</info>')
        );
    }


    public function testIsDependantPackage() {
        $this->packageA->setRequires(array($this->linkRoot3));
        $this->packageB->setDevRequires(array($this->linkRoot3));

        $this->assertTrue($this->bridge->isDependantPackage($this->packageA));
        $this->assertFalse($this->bridge->isDependantPackage($this->packageB));
        $this->assertTrue($this->bridge->isDependantPackage($this->packageA, false));
        $this->assertFalse($this->bridge->isDependantPackage($this->packageB, false));
        $this->assertTrue($this->bridge->isDependantPackage($this->packageA, true));
        $this->assertTrue($this->bridge->isDependantPackage($this->packageB, true));
    }

}
