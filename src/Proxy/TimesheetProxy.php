<?php
/**
 * @license MIT
 * @website http://freshcoders.nl
 */

 namespace FreshCoders\JST\Proxy;

use FreshCoders\JST\ObjectManager\OdooObjectManager;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraClient;

/**
 * Undocumented class
 *
 * @author Nick Dekker <nick@freshcoders.nl>
 */
class TimesheetProxy
{
	/**
	 * @param array $env Any required parameters to import and export
	 * the timesheet, see restrictions in .....
	 */
	public static function exportToOdoo(
		array $settings = []
	)
	{
		if (!$settings) {
			$settings = self::loadDefaultSettings();
		}

		$jiraTimeSheet = self::fetchJiraTimesheet($settings);

		$odooObjectManager = new OdooObjectManager();
		$odooClient = $odooObjectManager->createOdooClient($settings);
		$odooClient->createTimesheet($jiraTimeSheet);
	}

	private function fetchJiraTimesheet($settings)
	{
		$config = new ArrayConfiguration($settings);
		$issues = new IssueService($config);
		$issues
	}

	public static function loadDefaultSettings() {
		return [];
	}
}
