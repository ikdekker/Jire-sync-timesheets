<?php
/**
 * @license MIT
 * @website http://freshcoders.nl
 */

 namespace FreshCoders\JST\Portal;

use Carbon\Carbon;
use OdooClient\Client;

/**
 * Export the timesheet data to Odoo
 *
 * @author Nick Dekker <nick@freshcoders.nl>
 */
class Odoo12Portal extends OdooPortal
{
    private $_odooSheetModel;
    
    public $context;

    public function __construct($settings)
    {
        // Set the odoo model name object for hr_timesheet_sheet.sheet
        $this->_odooSheetModel = 'account.analytic.line'; // todo: update?
        
        $this->client = new Client(
            $settings['odoo_host'] . '/xmlrpc/2',
            $settings['odoo_db'],
            $settings['odoo_user'],
            $settings['odoo_pass']
        );

        $this->employees = $settings['user_mapping'];
    }

    public function export($timesheets)
    {
        foreach ($this->getEmployeeIds() as $user => $employeeId) {
            $state = $this->createTimesheet($timesheets[$user], $employeeId);
            if (!$state) {
                echo "Something went wrong creating sheet for $user \n";
            }
        }
    }

    public function createTimesheet($timesheet, $employeeId)
    {
        $ids = [];
        $userId = $this->getUserId($employeeId);
        foreach ($timesheet as $date => $duration) {
            $data = [
                "date" => $date,
                'unit_amount' => $duration,
                "project_id" => 10,
                "name" => 'Import from Jira Clocking - ' . Carbon::parse('first day of last month')->format('M'),
                'user_id' => $userId
            ];
            
            $ids[] = $this->client->create($this->_odooSheetModel, $data);
        }
        

        return [$ids];
    }

    public function getUserId($uid)
    {
        // todo: should call on user from odoo
        $map = [
            8 => 13,
            14 => 26,
            15 => 27
        ];
        return $map[$uid];
    }

    public function getEmployeeIds()
    {
        return $this->employees;
    }
}
