<?php

require_once __DIR__ . '/../templates/worldline-settings-templates.php';

class worldline_Settings
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'wordpressworldlineAdminSetup'));
        add_action('admin_init', array($this, 'displayOptions'));

        $this->template = new worldline_Templates();
    }

    function wordpressworldlineAdminSetup()
    {
        add_menu_page('Worldline Payment Gateway', 'Worldline', 'manage_options', 'worldline', array($this, 'adminOptions'), 'dashicons-money-alt');
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
