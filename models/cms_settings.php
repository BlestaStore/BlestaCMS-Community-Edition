<?php
/**
 * Manage the content, pages and links of the CMS.
 *
 * @package Blesta
 * @subpackage Blesta.plugins
 * @copyright Copyright (c) 2018 Blesta.Store. All Rights Reserved.
 * @license Blesta.Store End User License Agreement
 * @author Blesta.Store <cms@blesta.store>
 */
class CmsSettings extends AppModel
{
    /**
     * Initializes a CmsPages object.
     */
    public function __construct()
    {
        parent::__construct();
        Language::loadLang('blesta_cms', null, PLUGINDIR . 'blesta_cms' . DS . 'language' . DS);
        Loader::loadComponents($this, ['Session', 'Record']);
        Loader::loadModels($this, ['Staff', 'Companies']);
    }
    public function getSettings($value){
      // Get company id
      $company_id = Configure::get('Blesta.company_id');

      $records = $this->Record->select(array("settings_value", "settings_1", "settings_2"))->from("blestacms_settings")->where("settings_key", "=", $value)->where("company_id", '=', $company_id);
      // Unserialize the data
      $result = $records->fetch();
      return $result;
    }
    public function getAllSettings(){
      // Get company id
      $company_id = Configure::get('Blesta.company_id');

      $records = $this->Record->select()->from("blestacms_settings")->where("settings_key", "=", 'recaptcha')->where("company_id", "=", 1);

      // Unserialize the data
      $result = $records->fetchAll();
      return $result;
    }
    public function updateSettings($settings1, $settings2, $settings_key){

      $company_id = Configure::get('Blesta.company_id');

      if($settings1 == '' && $settings2 == ''){
        $this->Record->where('settings_key', '=', $settings_key)->where('company_id', '=', $company_id)->update('blestacms_settings', [
            'settings_1'  => '',
            'settings_2' => '',
            'settings_value' => ''
        ]);
      }else{
        $this->Record->where('settings_key', '=', $settings_key)->where('company_id', '=', $company_id)->update('blestacms_settings', [
            'settings_1'  => htmlentities($settings1),
            'settings_2' => htmlentities($settings2),
            'settings_value' => '1'
        ]);
      }
    }
}
