<?php
/**
 * @license MIT
 * @website http://freshcoders.nl
 */

 namespace FreshCoders\JST\Portal;

/**
 * Export the timesheet data to Odoo
 *
 * @author Nick Dekker <nick@freshcoders.nl>
 */
class Odoo9Portal
{
	public function __construct(
		array $settings
	)
	{
		$valid = $this->verifySettings($settings);

		if (!$valid) {
			throw \InvalidArgumentException('Odoo settings are incomplete.');
		}

		$this->settings = $settings;
	}

}
