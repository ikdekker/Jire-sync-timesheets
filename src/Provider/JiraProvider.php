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
 * Interface proxy to allow projects to use this library.
 *
 * @author Nick Dekker <nick@freshcoders.nl>
 */
class IssueProvider
{
    /**
     * Issue service, responsible for fetching Jira Issue data. The
     * source of which may vary between versions of this library.
     */
    public $service;

    /**
     * Initialize the IssueService connection, prepare for fetching.
     */
    public function __construct($settings)
    {
		$config = new ArrayConfiguration($settings);
        $this->service = new IssueService($config);
    }

    /**
     * We require a JQL issue search to find out which issues exist and
     * loop over these issues.
     */
    public function jqlFetchIssues()
    {
        // Fetch issues of the target project
        // todo: future versions will *probably* support custom JQLs
        // For now, we do not specify a user or any other criteria, because
        // those elements will be filtered in the worklog iteration.
        // $jql = "project = ($project)"; // disabled, in favour of empty.
        $jql = ''; // empty jql means all issues? (maybe)
        $res = $issueService->search($jql);
        var_dump($res);
        return $res;
    }

    /**
     * Each issue is iterated and, if within the time range, the worklog
     * value is added to the total of the range (chunk).
     */
    public function aggregateWorklogs($start, $end, $user)
    {
        // todo: WARNING: this function holds many placeholder method calls
        // the library can not be used as-is. The fact that this was committed
        // means that development continued elsewhere, but should be disregarded
        // in the future.
        $issues = $this->jqlFetchIssues();
        $total  = [];

        foreach ($issues as $issue) {
            // todo:
            $key = $issue->getKey(); // fixme, untested code
            // Fetch worklogs, as per library sample in readme file.
            $worklogs = $issueService->getWorklog($key)->getWorklogs();
            
            // Unfortunate nested for, since we cannot directly access
            // worklogs. First accumulating all worklogs would not be
            // much better.
            foreach ($worklogs as $worklogEntry) {
                // Test worklog for compatability with current terms.
                // todo, if global 'users' setting was not empty, do an in_array
                // test otherwise, always add this worklog to the users.
                // todo: future versions may have variable aggregation clauses.
                if ($worklogEntry->user !== $user) continue; // fixme, untested code

                $clause = $user;

                $total[$clause] += $worklogEntry->duration // fixme, untested code.
            }
        }
        
        return $termTotal;
    }
}
