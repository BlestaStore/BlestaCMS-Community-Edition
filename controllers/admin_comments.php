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
class AdminComments extends BlestaCmsController
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
        $this->structure->set('page_title', Language::_('blesta_cms.comments', true));
    }

    /**
     * List current pages.
     */
    public function index()
    {
        // Fetch pages
        $comments = $this->CmsPages->getAllComments();

        // Set variables to the view
        $this->set('comments', $comments);
    }

    /**
     * Approve a comment.
     */
    public function approve()
    {
        // Approve comment
        if (!empty($this->get[0])) {
            $result = $this->CmsPages->approveComment($this->get[0]);

            // Parse result
            if ($result) {
                $this->flashMessage('message', Language::_('blesta_cms.success', true), null, false);
            } else {
                $this->flashMessage('error', Language::_('blesta_cms.!error.empty', true), null, false);
            }

            // Redirect
            $this->redirect($this->base_uri . 'plugin/blesta_cms/admin_comments/');
        }
    }

    /**
     * Delete a page.
     */
    public function delete()
    {
        // Delete comment
        if (!empty($this->get[0])) {
            $result = $this->CmsPages->deleteComment($this->get[0]);

            // Parse result
            if ($result) {
                $this->flashMessage('message', Language::_('blesta_cms.success', true), null, false);
            } else {
                $this->flashMessage('error', Language::_('blesta_cms.!error.empty', true), null, false);
            }

            // Redirect
            $this->redirect($this->base_uri . 'plugin/blesta_cms/admin_comments/');
        }
    }
}
