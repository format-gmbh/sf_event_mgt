<?php
namespace DERHANSEN\SfEventMgt\Tests\Unit\Command;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Test case for class DERHANSEN\SfEventMgt\Command\CleanupCommandController.
 *
 * @author Torben Hansen <derhansen@gmail.com>
 */
class CleanupCommandControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \DERHANSEN\SfEventMgt\Command\CleanupCommandController
	 */
	protected $subject = NULL;

	/**
	 * Setup
	 *
	 * @return void
	 */
	protected function setUp() {
		$this->subject = new \DERHANSEN\SfEventMgt\Command\CleanupCommandController();
	}

	/**
	 * Teardown
	 *
	 * @return void
	 */
	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 * @return void
	 */
	public function cleanupCommandHandlesExpiredRegistrationsAndCleansCacheTest() {

		// Inject configuration and configurationManager
		$configuration = array(
			'plugin.' => array(
				'tx_sfeventmgt.' => array(
					'settings.' => array(
						'clearCacheUids' => '1,2,3',
						'registration.' => array(
							'deleteExpiredRegistrations' => 0
						)
					)
				)
			)
		);

		$configurationManager = $this->getMock('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager',
			array('getConfiguration'), array(), '', FALSE);
		$configurationManager->expects($this->once())->method('getConfiguration')->will(
			$this->returnValue($configuration));
		$this->inject($this->subject, 'configurationManager', $configurationManager);

		$registrationService = $this->getMock('DERHANSEN\\SfEventMgt\\Service\\RegistrationService',
			array('handleExpiredRegistrations'), array(), '', FALSE);
		$registrationService->expects($this->once())->method('handleExpiredRegistrations')->with(0)->will(
			$this->returnValue($configuration));
		$this->inject($this->subject, 'registrationService', $registrationService);

		$cacheService = $this->getMock('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager',
			array('clearPageCache'), array(), '', FALSE);
		$cacheService->expects($this->once())->method('clearPageCache')->with(array(1,2,3));
		$this->inject($this->subject, 'cacheService', $cacheService);

		$this->subject->cleanupCommand();
	}
}
