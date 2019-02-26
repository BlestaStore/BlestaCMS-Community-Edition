<?php
/**
 * Manage the plugin settings from the admin side.
 *
 * @package Blesta
 * @subpackage Blesta.plugins
 * @copyright Copyright (c) 2018 Blesta.Store. All Rights Reserved.
 * @license Blesta.Store End User License Agreement
 * @author Blesta.Store <cms@blesta.store>
 */
class AdminMenuItems extends BlestaCmsController
{
    /**
     * Prepare the controller.
     */
    public function preAction()
    {
        // Parent pre-action
        parent::preAction();

        // Require login
        $this->requireLogin();

        // Load models
        $this->uses(['PluginManager', 'BlestaCms.CmsPages']);

        // Restore structure view location of the admin portal
        $this->structure->setDefaultView(APPDIR);
        $this->structure->setView(null, $this->original_view);
        $this->structure->set('page_title', Language::_('blesta_cms.menu_items', true));
    }

    /**
     * List current menu items.
     */
    public function index()
    {
        // Fetch menu items
        $items = $this->CmsPages->getAllMenuItems();

        // Get all parents
        $parents = $this->CmsPages->getMenuParentsField();

        // Set variables to the view
        $this->set('items', $items);
        $this->set('parents', $parents);
    }

    /**
     * Add a new menu item.
     */
    public function add()
    {
        // Add menu item
        if (!empty($this->post)) {
            $result = $this->CmsPages->addMenuItem($this->post);

            // Parse result
            if ($result) {
                $this->flashMessage('message', Language::_('blesta_cms.success', true), null, false);
                $this->redirect($this->base_uri . 'plugin/blesta_cms/admin_menu_items/');
            } else {
                $this->setMessage('error', Language::_('blesta_cms.!error.empty', true), false, null, false);
            }
        }

        // Get all parents
        $parents = $this->CmsPages->getMenuParentsField();

        // Get all installed languages
        $langs = $this->CmsPages->getAllLang();

        // Set variables to the view
        $this->set('vars', (object) $this->post);
        $this->set('parents', $parents);
        $this->set('langs', $langs);
    }

    /**
     * Edit a existing menu item.
     */
    public function edit()
    {
        // Redirect if an ID has not been given
        if (empty($this->get[0])) {
            $this->redirect($this->base_uri . 'plugin/blesta_cms/admin_menu_items/');
        }

        // Edit menu item
        if (!empty($this->post)) {
            $vars   = $this->post;
            $result = $this->CmsPages->editMenuItem($this->get[0], $this->post);

            // Parse result
            if ($result) {
                $this->flashMessage('message', Language::_('blesta_cms.success', true), null, false);
                $this->redirect($this->base_uri . 'plugin/blesta_cms/admin_menu_items/');
            } else {
                $this->setMessage('error', Language::_('blesta_cms.!error.empty', true), false, null, false);
            }
        } else {
            $vars = $this->CmsPages->getMenuItem($this->get[0]);
        }

        // Get all parents
        $parents = $this->CmsPages->getMenuParentsField();

        // Get all installed languages
        $langs = $this->CmsPages->getAllLang();

        // Set variables to the view
        $this->set('vars', (object) $vars);
        $this->set('parents', $parents);
        $this->set('langs', $langs);
    }

    /**
     * Delete a page.
     */
    public function delete()
    {
        // Delete page
        if (!empty($this->get[0])) {
            $result = $this->CmsPages->deleteMenuItem($this->get[0]);

            // Parse result
            if ($result) {
                $this->flashMessage('message', Language::_('blesta_cms.success', true), null, false);
            } else {
                $this->flashMessage('error', Language::_('blesta_cms.!error.empty', true), null, false);
            }

            // Redirect
            $this->redirect($this->base_uri . 'plugin/blesta_cms/admin_menu_items/');
        }
    }
}
