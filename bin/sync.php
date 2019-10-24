<?php

use Dotenv\Dotenv;
use FreshCoders\JST\Provider\IssueProvider;
use FreshCoders\JST\Proxy\TimesheetProxy;

require_once '../vendor/autoload.php';

$dotenv = Dotenv::create(__DIR__ . '/..');
$dotenv->load();

$issueProvider = new IssueProvider(
	[
		'jiraHost' => getenv('JIRA_HOST'),
		'jiraUser' => getenv('JIRA_USER'),
		'jiraPassword' => getenv('JIRA_TOKEN'),
	]
);

$parsedUsers = yaml_parse_file(__DIR__ . '/../config/');
var_dump($parsedUsers);exit;

$proxy = new TimesheetProxy([
	'odoo_user' => getenv('ODOO_USER'),
	'odoo_pass' => getenv('ODOO_PASS'),
	'odoo_host' => getenv('ODOO_HOST'),
	'user_mapping' => $parsedUsers
], $issueProvider);

$proxy->exportToOdoo();