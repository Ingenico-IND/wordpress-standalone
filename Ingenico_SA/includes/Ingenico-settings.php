<?php

require_once __DIR__.'/../templates/ingenico-settings-templates.php';

class Ingenico_Settings
{ 
    public function __construct()
    {
        add_action('admin_menu', array($this, 'wordpressIngenicoAdminSetup'));
        add_action('admin_init', array($this, 'displayOptions'));

        $this->template = new Ingenico_Templates();
    }

    function wordpressIngenicoAdminSetup()
    {
    	add_menu_page('Ingenico Payment Gateway', 'Ingenico', 'manage_options', 'ingenico', array($this, 'adminOptions'),'dashicons-money-alt');
    }

    function adminOptions()
    {
        $this->template->adminOptions();
    }

    function displayOptions()
    {
        $this->template->displayOptions();
    }
}