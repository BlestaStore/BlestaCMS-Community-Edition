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
class AdminPosts extends BlestaCmsController
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
        $this->uses(['PluginManager', 'BlestaCms.CmsPages', 'Staff']);

        // Restore structure view location of the admin portal
        $this->structure->setDefaultView(APPDIR);
        $this->structure->setView(null, $this->original_view);
        $this->structure->set('page_title', Language::_('blesta_cms.posts', true));

        // Include WYSIWYG
        $this->Javascript->setFile('ckeditor/ckeditor.js', 'head', VENDORWEBDIR);
        $this->Javascript->setFile('ckeditor/adapters/jquery.js', 'head', VENDORWEBDIR);
    }

    /**
     * List current posts.
     */
    public function index()
    {
        // Fetch posts
        $posts = $this->CmsPages->getAllPost();

        // Set variables to the view
        $this->set('posts', $posts);
    }

    /**
     * Add a new post.
     */
    public function add()
    {
        // Add post
        if (!empty($this->post)) {
            $result = $this->CmsPages->addPost($this->post);

            // Parse result
            if ($result) {
                $this->flashMessage('message', Language::_('blesta_cms.success', true), null, false);
                $this->redirect($this->base_uri . 'plugin/blesta_cms/admin_posts/');
            } else {
                $this->setMessage('error', Language::_('blesta_cms.!error.empty', true), false, null, false);
            }
        }

        $tags = ['{base_url}', '{blesta_url}', '{admin_url}', '{client_url}', '{plugins}'];

        // Get all installed languages
        $langs = $this->CmsPages->getAllLang();

        // Get all categories
        $categories = $this->CmsPages->getAllCategories();
        foreach ($categories as $category) {
            $categories_field[$category->id] = $category->title[$this->CmsPages->getDefaultLang()];
        }

        // Set variables to the view
        $this->set('vars', (object) $this->post);
        $this->set('tags', $tags);
        $this->set('langs', $langs);
        $this->set('categories', $categories_field);
    }

    /**
     * Edit a existing post.
     */
    public function edit()
    {
        // Redirect if an ID has not been given
        if (empty($this->get[0])) {
            $this->redirect($this->base_uri . 'plugin/blesta_cms/admin_posts/');
        }

        // Edit post
        if (!empty($this->post)) {
            $vars   = $this->post;
            $result = $this->CmsPages->editPost($this->get[0], $this->post);

            // Parse result
            if ($result) {
                $this->flashMessage('message', Language::_('blesta_cms.success', true), null, false);
                $this->redirect($this->base_uri . 'plugin/blesta_cms/admin_posts/');
            } else {
                $this->setMessage('error', Language::_('blesta_cms.!error.empty', true), false, null, false);
            }
        } else {
            $vars = $this->CmsPages->getPost($this->get[0]);
        }

        $tags = ['{base_url}', '{blesta_url}', '{admin_url}', '{client_url}', '{plugins}'];

        // Get all installed languages
        $langs = $this->CmsPages->getAllLang();

        // Get all categories
        $categories = $this->CmsPages->getAllCategories();
        foreach ($categories as $category) {
            $categories_field[$category->id] = $category->title[$this->CmsPages->getDefaultLang()];
        }

        // Set variables to the view
        $this->set('vars', (object) $vars);
        $this->set('tags', $tags);
        $this->set('langs', $langs);
        $this->set('categories', $categories_field);
    }

    /**
     * Delete a post.
     */
    public function delete()
    {
        // Delete post
        if (!empty($this->get[0])) {
            $result = $this->CmsPages->deletePost($this->get[0]);

            // Parse result
            if ($result) {
                $this->flashMessage('message', Language::_('blesta_cms.success', true), null, false);
            } else {
                $this->flashMessage('error', Language::_('blesta_cms.!error.empty', true), null, false);
            }

            // Redirect
            $this->redirect($this->base_uri . 'plugin/blesta_cms/admin_posts/');
        }
    }
}
