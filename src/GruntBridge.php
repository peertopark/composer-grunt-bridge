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
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;

/**
 * Runs Grunt tasks for Composer projects.
 */
class GruntBridge {

    /**
     * Construct a new Composer Grunt bridge plugin.
     *
     * @param IOInterface|null                $io           The i/o interface to use.
     * @param GruntVendorFinderInterface|null $vendorFinder The vendor finder to use.
     * @param GruntClientInterface|null       $client       The Grunt client to use.
     */
    public function __construct(
    IOInterface $io = null, GruntVendorFinder $vendorFinder = null, GruntClient $client = null
    ) {

        $this->io = $io;
        $this->vendorFinder = $vendorFinder;
        $this->client = $client;
    }

    public function runGruntTasks(Composer $composer, $isDevMode = null) {
        $this->io->write('<info>Running Grunt tasks for root project</info>');

        if ($this->isDependantPackage($composer->getPackage(), $isDevMode)) {
            $tasks = $this->getTask($composer->getPackage());
            $this->client->runTask($tasks);
        } else {
            $this->write('Nothing to grunt');
        }

        $this->installForVendors($composer);
    }

    /**
     * Returns true if the supplied package requires the Composer Grunt bridge.
     *
     * @param PackageInterface $package                The package to inspect.
     * @param boolean|null     $includeDevDependencies True if the dev dependencies should also be inspected.
     *
     * @return boolean True if the package requires the bridge.
     */
    public function isDependantPackage(PackageInterface $package, $includeDevDependencies = null) {
        if (null === $includeDevDependencies) {
            $includeDevDependencies = false;
        }

        foreach ($package->getRequires() as $link) {
            if ('peertopark/composer-grunt-bridge' === $link->getTarget()) {
                return true;
            }
        }

        if ($includeDevDependencies) {
            foreach ($package->getDevRequires() as $link) {
                if ('peertopark/composer-grunt-bridge' === $link->getTarget()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param PackageInterface $package
     *
     * @return string|array|null
     */
    public function getTask(PackageInterface $package) {
        $extra = $package->getExtra();
        if (!empty($extra) && !empty($extra['grunt-task'])) {
            return $extra['grunt-task'];
        }

        return null;
    }

    /**
     * Run Grunt tasks for all Composer dependencies that use the bridge.
     *
     * @param Composer $composer The main Composer object.
     *
     * @throws Exception\GruntNotFoundException      If the grunt executable cannot be located.
     * @throws Exception\GruntCommandFailedException If the operation fails.
     */
    protected function installForVendors(Composer $composer) {
        $this->io->write(
                '<info>Running Grunt tasks for Composer dependencies</info>'
        );

        $packages = $this->vendorFinder->find($composer, $this);
        if (count($packages) > 0) {
            foreach ($packages as $package) {
                $this->io->write(
                        sprintf(
                                '<info>Running Grunt tasks for %s</info>', $package->getPrettyName()
                        )
                );

                $this->client->runTask($this->getTask($package), $composer->getInstallationManager()->getInstallPath($package)
                );
            }
        } else {
            $this->io->write('Nothing to grunt');
        }
    }

    private $io;
    private $vendorFinder;
    private $client;

}
