<?php
/**
 * Main plugin controller.
 *
 * @package Blesta
 * @subpackage Blesta.plugins
 * @copyright Copyright (c) 2018 Blesta.Store. All Rights Reserved.
 * @license Blesta.Store End User License Agreement
 * @author Blesta.Store <cms@blesta.store>
 */
class BlestaCmsController extends AppController
{
    /**
     * Set the default view to all plugin controllers.
     */
    public function preAction()
    {
        // Set structure view
        $this->structure->setDefaultView(APPDIR);

        // Parent pre-action
        parent::preAction();

        // Load language
        Language::loadLang('blesta_cms', null, PLUGINDIR . 'blesta_cms' . DS . 'language' . DS);

        // Load models
        $this->uses(['PluginManager']);

        // Load components
        $this->uses(['Plugins']);

        // Set company ID
        $this->company_id = Configure::get('Blesta.company_id');

        // Set called uri
        $this->uri_str = $_SERVER['REQUEST_URI'];

        // Override default view directory
        $this->view->view      = 'default';
        $this->original_view   = $this->structure->view;
        $this->structure->view = 'default';

        // Check if the license are valid and show a message if the license has been expired
        $plugin = $this->Plugins->create('blesta_cms');
    }
}
