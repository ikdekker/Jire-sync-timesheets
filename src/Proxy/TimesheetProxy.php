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
     * @param array    $settings        Any required parameters to import and export
     *                                  the timesheet, see restrictions in the
     *                                  function descriptor.          
     
     * @param Provider $workLogProvider Provider (abstract / interface will be added)
     *                                  with a 'aggregateWorklogs' method. This allows for an issue-style aggregation,
     *                                  or a worklog style aggregation.
     */
    public function __construct(
        array $settings,
        $workLogProvider
    ) {
        $this->settings = $settings ?? $this->loadDefaultSettings();
        $this->workLogProvider = $workLogProvider;
    }

    /**
     * Import Jira timesheet and output to a portal that is created using
     * an output object manager that spawns the appropriate client.
     *
     * @param  [type] $outputObjectManager
     * @return void
     */
    public function importExport($outputObjectManager)
    {
        $outputClient = $outputObjectManager->createOutputClient($this->settings);
        // todo: change this to array of multiple sheets
        // Get the Jira Timesheet model of the given time range.
        $jiraTimesheetData = $this->_fetchJiraTimesheet();
        // todo: enable this, add override option aka force delete existing
        // maybe merge all sheets. 
        // todo: add loop per user
        $outputClient->export($jiraTimesheetData);
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
     */
    public function exportToOdoo()
    {
        // Export manager to export the jira time sheet to Odoo.
        $outputObjectManager = new OdooObjectManager();
        $this->importExport($outputObjectManager);
    }

    /**
     * Accumulate all Jira worklogs of the given time range and return it
     * as a model/array of models (maybe an array for now).
     * todo: return an actual model, if beneficial.
     */
    private function _fetchJiraTimesheet()
    {
        // todo: check if settings need to build the worklog earlier or
        // prune the worklogs differently.
        $workLogList = $this->workLogProvider->aggregateWorklogs();

        return $workLogList;
    }

    /**
     * Initialize the settings array with some default values.
     */
    public function loadDefaultSettings()
    {
        return [];
    }
}
