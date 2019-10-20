<?php
/**
 * @license MIT
 * @website http://freshcoders.nl
 */

namespace FreshCoders\JST\Provider;

/**
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
	
	public function aggregateWorklogs()
	{
		
		// todo get result from *extension* worklogs
		$logs = [];
		
		// todo fix this (looks a lot like atlassian format?)
		foreach ($result as $r) {
			// account is here: $r->payload->author->accountId // todo check if we can find username
			if ($r->payload->author->accountId != $cwUser) continue;
			$logs[$r->issue->key][] = $r->payload;
		}
		return $logs;
	}
}
