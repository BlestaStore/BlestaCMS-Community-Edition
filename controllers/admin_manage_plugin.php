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
class AdminManagePlugin extends AppController
{
    private function init()
    {
        // Require Login
        $this->parent->requireLogin();

        // Load Language
        Language::loadLang('blesta_cms', null, PLUGINDIR . 'blesta_cms' . DS . 'language' . DS);

        $this->company_id = Configure::get('Blesta.company_id');
        $this->plugin_id  = (isset($this->get[0]) ? $this->get[0] : null);
        $this->Javascript = $this->parent->Javascript;
        $this->parent->structure->set('page_title', Language::_('blesta_cms.page_title', true));
        $this->view->setView(null, 'BlestaCms.default');
        $this->uses(['PluginManager']);

        // Get Plugin ID
        if (isset($this->plugin_id)) {
            $plugins = $this->PluginManager->get($this->plugin_id, true);
        }

        // Load Components
        Loader::loadComponents($this, ['Record']);
    }

    public function index()
    {
        $this->init();
        $this->uses(['BlestaCms.CmsPages', 'Settings', 'Plugins']);
        // Manage actions

        if (!empty($this->post)) {
              switch ($this->post['type']) {
                  case 'add':
                  $add = $this->CmsPages->addLang($this->post['title'], $this->post['lang']);
                  if ($add) {
                      $this->parent->flashMessage('message', Language::_('blesta_cms.added_language', true));
                  } else {
                      $this->parent->flashMessage('error', Language::_('blesta_cms.!error.requested_action', true));
                  }
                  break;
                  case 'edit':
                      $edit = $this->CmsPages->editLang($this->post['title'], $this->post['lang'], $this->post['old_lang']);
                      if ($edit) {
                          $this->parent->flashMessage('message', Language::_('blesta_cms.success', true));
                      } else {
                          $this->parent->flashMessage('error', Language::_('blesta_cms.!error.requested_action', true));
                      }
                      break;
                    case 'delete':
                        $delete = $this->CmsPages->deleteLang($this->post['lang']);
                        if ($delete) {
                            $this->parent->flashMessage('message', Language::_('blesta_cms.deleted_language', true));
                        } else {
                            $this->parent->flashMessage('error', Language::_('blesta_cms.!error.requested_action', true));
                        }
                        break;
                    case 'caching':
                        if ($this->post['caching'] == 1) {
                            $this->Settings->setSetting('BlestaCms.Caching', true);
                        } else {
                            $this->Settings->setSetting('BlestaCms.Caching', false);
                        }

                        $this->parent->flashMessage('message', Language::_('blesta_cms.success', true));
                        break;
                    default:
                        $this->parent->flashMessage('error', Language::_('blesta_cms.!error.requested_action', true));
                        break;
                    }
            $this->redirect($this->base_uri . 'settings/company/plugins/manage/' . $this->plugin_id . '/');
        }

        // Get all installed languages
        $langs        = $this->CmsPages->getAllLang();
        $default_lang = $this->CmsPages->getDefaultLang();
        $caching      = $this->Settings->getSetting('BlestaCms.Caching');
      	$Licensing = new stdClass();
      	Loader::loadModels($Licensing, array("settings"));

        return $this->partial('admin_manage_plugin', compact('langs', 'default_lang', 'caching'));
    }
  	public function debug($a) {
  		echo "<pre>";
  		print_r($a);
  		echo "</pre>";
  	}
}
