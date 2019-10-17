<?php
/**
 * @license MIT
 * @website http://freshcoders.nl
 */

 namespace FreshCoders\JST\Proxy;

use FreshCoders\JST\ObjectManager\OdooObjectManager;

/**
 * Interface proxy to allow projects to use this library.
 *
 * @author Nick Dekker <nick@freshcoders.nl>
 */
class TimesheetProxy
{
	/**
	 * A provider that is able to aggregate worklogs.
	 */
	public $workLogProvider;

	/**
	 * @param array $settings
	 * @param Provider $workLogProvider Provider (abstract / interface will be added)
	 * with a 'aggregateWorklogs' method. This allows for an issue-style aggregation,
	 * or a worklog style aggregation.
	 */
	public function __construct(
		array $settings,
		$workLogProvider
	)
	{
		if (!$settings) {
			$settings = $this->loadDefaultSettings();
		}
		$this->workLogProvider = $workLogProvider;
	}

	/**
	 * Export Jira timesheet to an odoo timesheet. Requires Jira settings:
	 *  - jira_access_token
	 * also requires Odoo settings:
	 *  - odoo_username
	 *  - odoo_password
	 * if not all users should be exported:
	 *  - users
	 * finally, global settings
	 *  - override (bool)
	 * 
	 * @param array $settings Any required parameters to import and export
	 * the timesheet, see restrictions in the function descriptor.
	 */
	public function exportToOdoo(
		array $settings = []
	)
	{
		// todo: change this to array of multiple sheets
		// Get the Jira Timesheet model of the given time range.
		$jiraTimeSheet = $this->fetchJiraTimesheet($settings);

		// Export the jira time sheet to Odoo.
		$odooObjectManager = new OdooObjectManager();
		$odooClient = $odooObjectManager->createOdooClient($settings);
		// todo: enable this, add override option aka force delete existing
		// maybe merge all sheets. 
		// $odooClient->createTimesheet($jiraTimeSheet);
	}

	/**
	 * Accumulate all Jira worklogs of the given time range and return it
	 * as a model.
	 */
	private function fetchJiraTimesheet($settings)
	{
		// todo: check if settings need to build the worklog earlier or
		// prune the worklogs differently.
		$this->workLogProvider->aggregateTimeSheets($settings);
	}

	/**
	 * Initialize the settings array with some default values.
	 */
	public function loadDefaultSettings() {
		return [];
	}
}
