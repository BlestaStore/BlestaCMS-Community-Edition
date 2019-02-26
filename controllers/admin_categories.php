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
class AdminCategories extends BlestaCmsController
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
        $this->structure->set('page_title', Language::_('blesta_cms.categories', true));
    }

    /**
     * List current categories.
     */
    public function index()
    {
        // Fetch categories
        $categories = $this->CmsPages->getAllCategories();

        // Set variables to the view
        $this->set('categories', $categories);
    }

    /**
     * Add a new category.
     */
    public function add()
    {
        // Add category
        if (!empty($this->post)) {
            $result = $this->CmsPages->addCategory($this->post);

            // Parse result
            if ($result) {
                $this->flashMessage('message', Language::_('blesta_cms.success', true), null, false);
                $this->redirect($this->base_uri . 'plugin/blesta_cms/admin_categories/');
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
     * Edit a existing category.
     */
    public function edit()
    {
        // Redirect if an ID has not been given
        if (empty($this->get[0])) {
            $this->redirect($this->base_uri . 'plugin/blesta_cms/admin_categories/');
        }

        // Edit category
        if (!empty($this->post)) {
            $vars   = $this->post;
            $result = $this->CmsPages->editCategory($this->get[0], $this->post);

            // Parse result
            if ($result) {
                $this->flashMessage('message', Language::_('blesta_cms.success', true), null, false);
                $this->redirect($this->base_uri . 'plugin/blesta_cms/admin_categories/');
            } else {
                $this->setMessage('error', Language::_('blesta_cms.!error.empty', true), false, null, false);
            }
        } else {
            $vars = $this->CmsPages->getCategory($this->get[0]);
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
     * Delete a category.
     */
    public function delete()
    {
        // Delete category
        if (!empty($this->get[0])) {
            $result = $this->CmsPages->deleteCategory($this->get[0]);

            // Parse result
            if ($result) {
                $this->flashMessage('message', Language::_('blesta_cms.success', true), null, false);
            } else {
                $this->flashMessage('error', Language::_('blesta_cms.!error.empty', true), null, false);
            }

            // Redirect
            $this->redirect($this->base_uri . 'plugin/blesta_cms/admin_categories/');
        }
    }
}
