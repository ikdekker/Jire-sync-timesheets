<?php
/**
 * @license MIT
 * @website http://freshcoders.nl
 */

 namespace FreshCoders\JST\ObjectManager;

/**
 * Export the timesheet data to Odoo
 *
 * @author Nick Dekker <nick@freshcoders.nl>
 */
class OdooObjectManager
{
    /**
     * Create an Odoo client, verifying the settings beforehand.
     *
     * @param  array $settings
     * @return void
     */
    public function createOdooClient(
        array $settings
    ) {
        $valid = $this->verifySettings($settings);
        
        if (!$valid) {
            throw \InvalidArgumentException('Odoo settings are incomplete.');
        }

        
    }

    /**
     * Checks if the settings contain all required keys
     *
     * @return bool
     */
    private function verifySettings(array $settings)
    {
        $required = ['url', 'database', 'user', 'password'];

        $valid = array_reduce(
            $required, function ($result, $key) use ($settings) {
                return $result === false ? false : in_array($key, $settings);
            }, true
        );

        return $valid;
    }
}
