<?php
/**
 * @license MIT
 * @website http://freshcoders.nl
 */

 namespace FreshCoders\JST\ObjectManager;

 use FreshCoders\JST\Portal\Odoo9Portal;
 use FreshCoders\JST\Portal\Odoo12Portal;

/**
 * Export the timesheet data to Odoo
 *
 * @author Nick Dekker <nick@freshcoders.nl>
 */
class OdooObjectManager
{
    public function createOdooPortal($settings)
    {
        switch ($settings['odoo_version']) {
            case 9:
                $odooPortal = new Odoo9Portal($settings);
            break;
            case 12:
                $odooPortal = new Odoo12Portal($settings);
            break;
        }

        return $odooPortal;
    }

    /**
     * Create an Odoo client, verifying the settings beforehand.
     *
     * @param  array $settings
     * @return void
     */
    public function createOutputClient(
        array $settings
    ) {
        $valid = $this->verifySettings($settings);
        
        if (!$valid) {
            throw new \InvalidArgumentException('Odoo settings are incomplete.');
        }

        return $this->createOdooPortal($settings);
    }

    /**
     * Checks if the settings contain all required keys
     *
     * @return bool
     */
    private function verifySettings(array $settings)
    {
        $required = ['ODOO_HOST', 'ODOO_DB', 'ODOO_USER', 'ODOO_PASS'];

        $valid = array_reduce(
            $required, function ($result, $key) use ($settings) {
                return $result === false ? false : 
                array_key_exists(
                    strtolower($key),
                    $settings
                );
            }, true
        );

        return $valid;
    }
}
