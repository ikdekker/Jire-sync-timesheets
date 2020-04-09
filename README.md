#### Jira Timesheet Synchronizer

This library is a one-way synchronization tool for exporting timesheets clocked in Jira to other platforms.

### Installation & requirements

- PHP >= 5.6

### Usage

To start using this library, clone it and configure using a .env file based on your export preference. This can be done by copying the .env.dist into .env and modyfying parameters.

This library aims to make it possible to synchronize timesheets for any selection of users, allowing overrides or additions to extisting timesheets of the target platform.

To run the export action, the CLI can be used, running the sync file in the bin dir. In the current version '1.0.0', there is no time range scoping. A future version '2.0.0' aims to support this as issue #2 indicates.

Running from command line:

`php sync.php`

sync.php uses the src/Proxy/TimeSheetProxy.php object to set up and execute the synchronization.

### Portals

Exported export portals as of 24-10-2019:
- Odoo 9

No additional support is planned at this time.

### License

This library is MIT licensed and may be distributed and modified freely. Refer to /LICENSE for more information.