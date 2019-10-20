<?php
/**
 * @license MIT
 * @website http://freshcoders.nl
 */

namespace FreshCoders\JST\Provider;

use Carbon\Carbon;
use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\JqlQuery;
use JiraRestApi\JiraException;

/**
 * Provider that gathers worklogs through issues. Issues are linked to their worklogs,
 * the worklog entries can be read by fetching them based on the issue key. After
 * fetching a list of the logs, the total time of worklogs is aggregated based on the
 * appropriate clause.
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
    public function __construct($settings, $users = [], $start = null, $end = null)
    {
        $config = new ArrayConfiguration($settings);
        $this->service = new IssueService($config);
        
        $this->start    = $start ?? Carbon::parse('first day of last month')->format('Y-m-d');
        $this->end      = $end   ?? Carbon::parse('last day of last month')->format('Y-m-d');
        $this->users    = $users;

        $this->timeTrackExtension = false;
    }

    /**
     * We require a JQL issue search to find out which issues exist and
     * loop over these issues.
     */
    public function jqlFetchIssues($offset = 0, $batch = 50)
    {
        // Fetch issues of the target project
        // todo: future versions will *probably* support custom JQLs
        // For now, we do not specify a user or any other criteria, because
        // those elements will be filtered in the worklog iteration.
        $jql = new JqlQuery;
        $res = $this->service->search($jql->getQuery(), $offset, $batch);
        return $res;
    }

    public function aggregateWorklogs()
    {
        $offset = 1;
        $total = [];
        $issues = $this->jqlFetchIssues($offset);

        // todo fix replace below with loop JQL issue fetch when git issue #5 is resolved
        // Deleted keys will result in counting too few issues, to account for deleted issues
        // todo see issue #7 : fix this sub-optimal method
        $total = $this->aggregateIssueLogsPerProject('WD', $issues->getTotal() + 25);
        $total = $this->reformatTimestamps($total);

        return $total;
    }

    
    public function reformatTimestamps($total)
    {
        foreach ($total as $clause => $logsByDate) {
            foreach ($logsByDate as $date => $logTime) {
                $total[$clause][$date] = gmdate("H:i", $logTime);
            }
        }

        return $total;
    }

    /**
     * Each issue is iterated and, if within the time range, the worklog
     * value is added to the total of the range (chunk).
     */
    public function aggregateIssueLogsByIssues($issues)
    {
        
        $total = [];
        foreach($issues as $issue) {
            $key = $issue->key;
            $timeSeconds = $issue->fields->progress['progress'];
            if ($timeSeconds === 0 && !$this->timeTrackExtension) { continue;
            }
            if ($this->timeTrackExtension) {
                // $worklogs = $this->getExtensionWorklogs($issue->key);
                // if (empty($worklogs)) continue;
            }
            $issueTotal = $this->aggregateLogsOfIssue($key);
            $this->mergeFoundLogs($total, $issueTotal);
        }
        return $total;
    }

    /**
     * Each issue is iterated and, if within the time range, the worklog
     * value is added to the total of the range (chunk).
     */
    public function aggregateIssueLogsPerProject($project, $maxKey)
    {
        
        $total = [];
        for ($key = 1; $key <= $maxKey; $key++) {
            if ($this->timeTrackExtension) {
                // $worklogs = $this->getExtensionWorklogs($issue->key);
                // if (empty($worklogs)) continue;
            }
            try {
                $projKey = $project . '-' . $key;
                var_dump($projKey);
                $issueTotal = $this->aggregateLogsOfIssue($projKey);
            } catch (JiraException $e) {
                // jira issue probably didnt exist
                // todo: proper handling
                continue;
            }
            $total = $this->mergeFoundLogs($total, $issueTotal);
        }
        return $total;
    }

    public function mergeFoundLogs($total, $logs)
    {
        foreach ($logs as $clause => $logsByDate) {
            foreach ($logsByDate as $date => $logTime) {
                @$total[$clause][$date] += $logTime;
            }
        }
        
        return $total;
    }

    public function aggregateLogsOfIssue($key)
    {
        $total = [];
        // Fetch worklogs through the given issue key
        $worklogs = $this->service->getWorklog($key)->getWorklogs();
        
        foreach ($worklogs as $worklogEntry) {
            // Test worklog for compatability with current terms.
            // if global 'users' setting was not empty, do an in_array test
            // otherwise, always add this worklog to the users.
            // todo: future versions may have variable aggregation clauses.
            // if (
            //     $this->users // empty arrays are falsy, never skip.
            //     && !in_array($worklogEntry['user'], $this->users)
            // )
            // {
            //         continue;
            // }
            $workDate = Carbon::parse($worklogEntry->started)->format('Y-m-d');

            if ($workDate > $this->end || $workDate < $this->start) { continue;
            }
            
            // Default grouping is by user.
            $clause = $worklogEntry->author['name'];
            
            @$total[$clause][$workDate] += $worklogEntry->timeSpentSeconds;
        }
        return $total;
    }
    
    /**
     * Call the extension provider to fetch worklogs.
     *
     * @param  string $key
     * @return Worklogs
     */
    public function getExtensionWorklogs($key)
    {
        if (@!$this->extensionWorklogs) {
            // todo replace with proper extension class method call
            $this->extensionWorklogs = $this->extension->fetchExtensionWorklogs();
        }

        return @$this->extensionWorklogs[$key];
    }
}
