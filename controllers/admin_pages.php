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
class AdminPages extends BlestaCmsController
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
        $this->structure->set('page_title', Language::_('blesta_cms.pages', true));
    }

    /**
     * List current pages.
     */
    public function index()
    {
        // Fetch pages
        $pages = $this->CmsPages->getAllPages();

        // Set variables to the view
        $this->set('pages', $pages);
    }

    /**
     * Add a new page.
     */
    public function add()
    {
        // Add page
        if (!empty($this->post)) {
            $result = $this->CmsPages->addPage($this->post);

            // Parse result
            if ($result) {
                $this->flashMessage('message', Language::_('blesta_cms.success', true), null, false);
                $this->redirect($this->base_uri . 'plugin/blesta_cms/admin_pages/');
            } else {
                $this->setMessage('error', Language::_('blesta_cms.!error.empty', true), false, null, false);
            }
        }

        $tags = ['{base_url}', '{blesta_url}', '{admin_url}', '{client_url}', '{plugins}'];

        // Get all installed languages
        $langs = $this->CmsPages->getAllLang();

        // Set variables to the view
        $this->set('vars', (object) $this->post);
        $this->set('tags', $tags);
        $this->set('langs', $langs);
    }

    /**
     * Edit a existing page.
     */
    public function edit()
    {
        // Redirect if an ID has not been given
        if (empty($this->get[0])) {
            $this->redirect($this->base_uri . 'plugin/blesta_cms/admin_pages/');
        }

        // Edit page
        if (!empty($this->post)) {
            $vars   = $this->post;
            $result = $this->CmsPages->editPage($this->get[0], $this->post);

            // Parse result
            if ($result) {
                $this->flashMessage('message', Language::_('blesta_cms.success', true), null, false);
                $this->redirect($this->base_uri . 'plugin/blesta_cms/admin_pages/');
            } else {
                $this->setMessage('error', Language::_('blesta_cms.!error.empty', true), false, null, false);
            }
        } else {
            $vars = $this->CmsPages->getPage($this->get[0]);
        }

        $tags = ['{base_url}', '{blesta_url}', '{admin_url}', '{client_url}', '{plugins}'];

        // Get all installed languages
        $langs = $this->CmsPages->getAllLang();

        // Set variables to the view
        $this->set('vars', (object) $vars);
        $this->set('tags', $tags);
        $this->set('langs', $langs);
    }

    /**
     * Delete a page.
     */
    public function delete()
    {
        // Delete page
        if (!empty($this->get[0])) {
            $result = $this->CmsPages->deletePage($this->get[0]);

            // Parse result
            if ($result) {
                $this->flashMessage('message', Language::_('blesta_cms.success', true), null, false);
            } else {
                $this->flashMessage('error', Language::_('blesta_cms.!error.empty', true), null, false);
            }
            // Redirect
            $this->redirect($this->base_uri . 'plugin/blesta_cms/admin_pages/');
        }
    }
}
