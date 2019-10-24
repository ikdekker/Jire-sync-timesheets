<?php

use Dotenv\Dotenv;
use FreshCoders\JST\Provider\IssueProvider;
use FreshCoders\JST\Proxy\TimesheetProxy;
use Symfony\Component\Yaml\Yaml;

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

$parsedUsers = Yaml::parseFile(__DIR__ . '/../config/user_map.yml');

$proxy = new TimesheetProxy(
    [
    'odoo_user' => getenv('ODOO_USER'),
    'odoo_db' => getenv('ODOO_DB'),
    'odoo_pass' => getenv('ODOO_PASS'),
    'odoo_host' => getenv('ODOO_HOST'),
    'user_mapping' => $parsedUsers['users']
    ], $issueProvider
);

$proxy->exportToOdoo();
