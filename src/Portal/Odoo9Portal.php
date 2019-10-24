<?php
/**
 * @license MIT
 * @website http://freshcoders.nl
 */

 namespace FreshCoders\JST\Portal;
 
 use OdooClient\Client;

/**
 * Export the timesheet data to Odoo
 *
 * @author Nick Dekker <nick@freshcoders.nl>
 */
class Odoo9Portal
{
    private $_odooSheetModel;
    
    public $context;

    public function __construct($context)
    {
        $this->context = $context;
        // Set the odoo model name object for hr_timesheet_sheet.sheet
        $this->_odooSheetModel = 'hr_timesheet_sheet.sheet';

        $this->client = new Client($url, $database, $user, $password);
    }

    public function export($timesheets)
    {
        foreach ($this->getUserIds() as $user => $odooId) {
            $this->createTimesheet($timesheet[$user], $userId);
        }
    }

    public function createTimesheet($timesheet, $userId)
    {
        $sheetData = [];

        foreach ($timesheet as $date => $duration) {
            $sheetData[] = [
                0,
                false,
                [
                    "date" => $date,
                    "is_timesheet" => true,
                    'unit_amount' => $duration,
                    "account_id" => 1,
                    'user_id' => $userId
                ]
            ]
        }

        $data = [
            "employee_id" => $userId,
            "date_from" => Carbon::parse('first day of last month')->format('Y-m-d'),
            "date_to" => Carbon::parse('last day of last month')->format('Y-m-d'),
            "timesheet_ids" => $sheetData
        ];

        $id = $client->create($this->_odooSheetModel, $data);
    }

    public function getUserIds()
    {
        return $this->users;
    }
}
