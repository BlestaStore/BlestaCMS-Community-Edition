<?php
/**
 * Take Blesta to the next level and power your website & company blog with Blesta.
 *
 * @package Blesta
 * @subpackage Blesta.plugins
 * @copyright Copyright (c) 2018 Blesta.Store. All Rights Reserved.
 * @license Blesta.Store End User License Agreement
 * @author Blesta.Store <cms@blesta.store>
 * Hi
 */

class BlestaCmsPlugin extends Plugin
{
    /**
     * @var int The plugin id
     */
    private $pluginid = null;

    /**
     * @var string The plugin version
     */
    private static $version = '1.4.0';

    /**
     * @var array The plugin authors
     */
    private static $authors = [['name' => 'GosuHost for BlestaStore', 'url' => 'https://blesta.store']];

    /**
     * @var array The ACL plugin permissions
     */
    private static $permissions = [
        [
            'name'        => 'blesta_cms.page_title',
            'level'       => 'staff',
            'alias'       => 'blesta_cms.admin_main',
            'permissions' => [
                [
                    'name'   => 'blesta_cms.pages',
                    'alias'  => 'blesta_cms.admin_pages',
                    'action' => '*'
                ],
                [
                    'name'   => 'blesta_cms.posts',
                    'alias'  => 'blesta_cms.admin_posts',
                    'action' => '*'
                ],
                [
                    'name'   => 'blesta_cms.categories',
                    'alias'  => 'blesta_cms.admin_categories',
                    'action' => '*'
                ],
                [
                    'name'   => 'blesta_cms.comments',
                    'alias'  => 'blesta_cms.admin_comments',
                    'action' => '*'
                ],
                [
                    'name'   => 'blesta_cms.menu_items',
                    'alias'  => 'blesta_cms.admin_menu_items',
                    'action' => '*'
                ],
                [
                    'name'   => 'blesta_cms.settings',
                    'alias'  => 'blesta_cms.admin_settings',
                    'action' => '*'
                ]
            ]
        ]
    ];

    /**
     * Plugin constructor.
     */
    public function __construct()
    {
        // Load language file
        Language::loadLang('blesta_cms', null, dirname(__FILE__) . DS . 'language' . DS);

        // Load components
        Loader::loadComponents($this, ['Input', 'Record', 'Session']);

        // Load Plugin Model
        Loader::loadModels($this, ['BlestaCms.CmsPages', 'Settings', 'Permissions', 'PluginManager', 'Plugins']);

    }

    /**
     * Fetches the plugin name.
     *
     * @return string The plugin name
     */
    public function getName()
    {
        return Language::_('blesta_cms.name', true);
    }

    /**
     * Fetches the plugin version.
     *
     * @return string The plugin version
     */
    public function getVersion()
    {
        return self::$version;
    }

    /**
     * Fetches the plugin authors.
     *
     * @return array The plugin authors
     */
    public function getAuthors()
    {
        return self::$authors;
    }

    /**
     * Perform the install logic of the plugin.
     *
     * @param string $plugin_id The version of the product
     */
    public function install($plugin_id)
    {
        // Set Plugin ID
        $this->pluginid = $plugin_id;

        try {
            // Perform installation

            // Pages Table
            $this->Record->setField('id', ['type' => 'int', 'size' => 10, 'unsigned' => true, 'auto_increment' => true])
                    ->setField('uri', ['type' => 'varchar', 'size' => 255])
                    ->setField('company_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])
                    ->setField('title', ['type' => 'text'])
                    ->setField('content', ['type' => 'longtext'])
                    ->setField('description', ['type' => 'text'])
                    ->setField('meta_tags', ['type' => 'text', 'is_null' => true, 'default' => null])
                    ->setField('access', ['type' => 'enum', 'size' => "'public','hidden'", 'default' => 'public'])
                    ->setField('permissions', ['type' => 'enum', 'size' => "'all','logged','guests'", 'default' => 'all'])
                    ->setKey(['id', 'uri', 'company_id'], 'primary')
                    ->create('blestacms_pages', true);

            // Blog Posts Table
            $this->Record->setField('id', ['type' => 'int', 'size' => 10, 'unsigned' => true, 'auto_increment' => true])
                    ->setField('uri', ['type' => 'varchar', 'size' => 255])
                    ->setField('company_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])
                    ->setField('category_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])
                    ->setField('author', ['type' => 'int', 'size' => 10, 'unsigned' => true])
                    ->setField('title', ['type' => 'text'])
                    ->setField('content', ['type' => 'longtext'])
                    ->setField('meta_tags', ['type' => 'text', 'is_null' => true, 'default' => null])
                    ->setField('access', ['type' => 'enum', 'size' => "'public','hidden'", 'default' => 'public'])
                    ->setField('permissions', ['type' => 'enum', 'size' => "'all','logged','guests'", 'default' => 'all'])
                    ->setField('date_added', ['type' => 'datetime'])
                    ->setField('date_updated', ['type' => 'datetime', 'is_null' => true, 'default' => null])
                    ->setField('image', ['type' => 'text'])
                    ->setKey(['id', 'uri', 'company_id'], 'primary')
                    ->create('blestacms_blog_posts', true);

            // Blog Categories Table
            $this->Record->setField('id', ['type' => 'int', 'size' => 10, 'unsigned' => true, 'auto_increment' => true])
                    ->setField('company_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])
                    ->setField('title', ['type' => 'text'])
                    ->setField('access', ['type' => 'enum', 'size' => "'public','hidden'", 'default' => 'public'])
                    ->setKey(['id'], 'primary')
                    ->create('blestacms_blog_categories', true);

            // Post Comments Table
            $this->Record->setField('id', ['type' => 'int', 'size' => 10, 'unsigned' => true, 'auto_increment' => true])
                    ->setField('full_name', ['type' => 'varchar', 'size' => 255])
                    ->setField('email', ['type' => 'varchar', 'size' => 255])
                    ->setField('company_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])
                    ->setField('post_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])
                    ->setField('content', ['type' => 'text'])
                    ->setField('status', ['type' => 'enum', 'size' => "'approved','awaiting'", 'default' => 'awaiting'])
                    ->setField('date_added', ['type' => 'datetime'])
                    ->setKey(['id'], 'primary')
                    ->create('blestacms_posts_comments', true);

            // Menus Table
            $this->Record->setField('id', ['type' => 'int', 'size' => 10, 'unsigned' => true, 'auto_increment' => true])
                    ->setField('company_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])
                    ->setField('title', ['type' => 'text'])
                    ->setField('uri', ['type' => 'text'])
                    ->setField('parent', ['type' => 'varchar', 'size' => 255])
                    ->setField('access', ['type' => 'enum', 'size' => "'public','hidden'", 'default' => 'public'])
                    ->setField('target', ['type' => 'enum', 'size' => "'-','newtab'", 'default' => '-'])
                    ->setKey(['id'], 'primary')
                    ->create('blestacms_menus', true);

            // Languages Table
            $this->Record->setField('id', ['type' => 'int', 'size' => 10, 'unsigned' => true, 'auto_increment' => true])
                    ->setField('uri', ['type' => 'varchar', 'size' => 255])
                    ->setField('company_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])
                    ->setField('title', ['type' => 'varchar', 'size' => 255])
                    ->setKey(['id', 'uri', 'company_id'], 'primary')
                    ->create('blestacms_languages', true);

            // Install default language
            Loader::loadModels($this, [
                    'Companies'
                ]);
            $lang = $this->Companies->getSetting(Configure::get('Blesta.company_id'), 'language');
            $lang = explode('_', $lang->value, 2)[0];
            $this->Record->insert('blestacms_languages', ['uri' => $lang, 'company_id' => Configure::get('Blesta.company_id'), 'title' => $this->CmsPages->getDisplayLang($lang)]);

            // Settings Table
            $this->Record->setField('settings_id', ['type' => 'int', 'size' => 10, 'unsigned' => true, 'auto_increment' => true])
                      ->setField('settings_key', ['type' => 'text'])
                      ->setField('settings_value', ['type' => 'text'])
                      ->setField('settings_description', ['type' => 'varchar', 'size' => 255])
                      ->setField('settings_1', ['type' => 'varchar', 'size' => 255])
                      ->setField('settings_2', ['type' => 'varchar', 'size' => 255])
                      ->setField('company_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])
                      ->setKey(['settings_id'], 'primary')
                      ->create('blestacms_settings', true);

              //Insert Settings Data
              $this->Record->insert('blestacms_settings', [
                      'settings_key'           => 'recaptcha',
                      'settings_value'         => '',
                      'settings_1'             => '',
                      'settings_2'             => '',
                      'settings_description'   => 'Google Recaptcha',
                      'company_id'             => Configure::get('Blesta.company_id'),
              ]);
		
            $this->Record->query("ALTER TABLE blestacms_pages CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->Record->query("ALTER TABLE blestacms_blog_posts CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->Record->query("ALTER TABLE blestacms_blog_categories CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->Record->query("ALTER TABLE blestacms_posts_comments CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->Record->query("ALTER TABLE blestacms_menus CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->Record->query("ALTER TABLE blestacms_languages CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->Record->query("ALTER TABLE blestacms_settings CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // Load all the configuration files and insert the sample data in to the database.
            $config_files = array_diff(scandir(dirname(__FILE__) . DS . 'config' . DS), ['.', '..']);

            foreach ($config_files as $file) {
                $file = trim($file, '.php');
                Configure::load($file, dirname(__FILE__) . DS . 'config' . DS);

                $this->Record->insert('blestacms_pages', [
                        'uri'         => Configure::get('Blesta_cms.uri'),
                        'company_id'  => Configure::get('Blesta.company_id'),
                        'title'       => serialize([$lang => Configure::get('Blesta_cms.title')]),
                        'content'     => serialize([$lang => Configure::get('Blesta_cms.index')]),
                        'description' => serialize([$lang => Configure::get('Blesta_cms.description')]),
                        'meta_tags'   => serialize([$lang => Configure::get('Blesta_cms.meta_tags')]),
                        'access'      => 'public'
                    ]);
            }

            // Add Plugin Settings
            $this->Settings->setSetting('BlestaCms.Caching', false);

            // Add ACL permissions.
            foreach (self::$permissions as $set) {
                $group_id = $this->Permissions->addGroup([
                        'plugin_id' => $plugin_id,
                        'name'      => Language::_($set['name'], true),
                        'level'     => $set['level'],
                        'alias'     => $set['alias']
                    ]);

                foreach ($set['permissions'] as $permission) {
                    $this->Permissions->add([
                            'group_id'  => $group_id,
                            'plugin_id' => $plugin_id,
                            'name'      => Language::_($permission['name'], true),
                            'alias'     => $permission['alias'],
                            'action'    => $permission['action']
                        ]);
                }

                // Automate Route Change from Portal to BlestaCMS
                $blesta_routes = file_get_contents("config" . DS . "routes.php");
                $blesta_routes = str_replace("'cms'", "'blesta_cms'", $blesta_routes);
                $blesta_routes = str_replace("'/cms/main/index/$1'", "'/blesta_cms/main/index/$1'", $blesta_routes);

                file_put_contents("config" . DS . "routes.php", $blesta_routes);
            }
        } catch (Exception $e) {
            $this->Input->setErrors(['db' => ['create' => $e->getMessage()]]);
        }
    }

    /**
     * Perform the install logic of the plugin.
     *
     * @param string $plugin_id     The version of the product
     * @param bool   $last_instance True if is the last instance in the system
     */
    public function uninstall($plugin_id, $last_instance)
    {
        if ($last_instance) {
            // Remove plugin database
            try {
                $this->Record->drop('blestacms_pages');
                $this->Record->drop('blestacms_blog_posts');
                $this->Record->drop('blestacms_blog_categories');
                $this->Record->drop('blestacms_posts_comments');
                $this->Record->drop('blestacms_menus');
                $this->Record->drop('blestacms_languages');

                // Automate Route Change from BlestaCMS to Portal
                $blesta_routes = file_get_contents("config" . DS . "routes.php");
                $blesta_routes = str_replace("'blesta_cms'", "'cms'", $blesta_routes);
                $blesta_routes = str_replace("'/blesta_cms/main/index/$1'", "'/cms/main/index/$1'", $blesta_routes);

                file_put_contents("config" . DS . "routes.php", $blesta_routes);
            } catch (Exception $e) {
                $this->Input->setErrors(['db' => ['drop' => $e->getMessage()]]);
            }

            // Remove plugin settings
            $this->Settings->unsetSetting('BlestaCms.Caching');

            // Remove permission groups
            foreach (self::$permissions as $set) {
                $group = $this->Permissions->getGroupByAlias($set['alias'], $plugin_id);

                if ($group) {
                    $this->Permissions->deleteGroup($group->id);
                }
            }
        }
    }

    /**
     * Perform the upgrade logic of the plugin.
     *
     * @param string $current_version The installed version of the product
     * @param int    $plugin_id       The plugin ID
     */
    public function upgrade($current_version, $plugin_id)
    {
        // Load settings
        Configure::load('blesta_cms', dirname(__FILE__) . DS . 'config' . DS);

        // Upgrade if possible
        if (version_compare($this->getVersion(), $current_version, '>')) {
            // Upgrade to 1.2.0
            if (version_compare($current_version, '1.2.3', '>')) {
                // Add ACL permissions.
                foreach (self::$permissions as $set) {
                    $group_id = $this->Permissions->addGroup([
                        'plugin_id' => $plugin_id,
                        'name'      => Language::_($set['name'], true),
                        'level'     => $set['level'],
                        'alias'     => $set['alias']
                    ]);

                    foreach ($set['permissions'] as $permission) {
                        $this->Permissions->add([
                            'group_id'  => $group_id,
                            'plugin_id' => $plugin_id,
                            'name'      => Language::_($permission['name'], true),
                            'alias'     => $permission['alias'],
                            'action'    => $permission['action']
                        ]);
                    }
                }
            }
            if (version_compare($current_version, '1.2.5', '<')) {
                // Alter Blog table to add support for image URLs.
                $this->Record->query(
                  "ALTER TABLE `blestacms_blog_posts` ADD `image` TEXT( 255 ) AFTER `date_updated`;"
              );
            }
            if(version_compare($current_version, '1.2.8', '<')){
              // Add Settings table.
              $this->Record->setField('settings_id', ['type' => 'int', 'size' => 10, 'unsigned' => true, 'auto_increment' => true])
                      ->setField('settings_key', ['type' => 'text'])
                      ->setField('settings_value', ['type' => 'text'])
                      ->setField('settings_description', ['type' => 'varchar', 'size' => 255])
                      ->setField('settings_1', ['type' => 'varchar', 'size' => 255])
                      ->setField('settings_2', ['type' => 'varchar', 'size' => 255])
                      ->setField('company_id', ['type' => 'int', 'size' => 10, 'unsigned' => true])
                      ->setKey(['settings_id'], 'primary')
                      ->create('blestacms_settings', true);

              //Insert Settings Data
              $this->Record->insert('blestacms_settings', [
                      'settings_key'           => 'recaptcha',
                      'settings_value'         => '',
                      'settings_1'             => '',
                      'settings_2'             => '',
                      'settings_description'   => 'Google Recaptcha',
                      'company_id'             => Configure::get('Blesta.company_id'),
              ]);
            }
            if (version_compare($current_version, '1.3.6', '<')) {
		$this->Record->query("ALTER TABLE `blestacms_menus` ADD `target` ENUM('-','newtab') default '-' AFTER `access`;"
              );
            }
	    if (version_compare($current_version, '1.4.0', '<')) {
		// Upgrade tables to support Emojis in Blesta 5.0.0
		$this->Record->query("ALTER TABLE blestacms_pages CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
		$this->Record->query("ALTER TABLE blestacms_blog_posts CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
		$this->Record->query("ALTER TABLE blestacms_blog_categories CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
		$this->Record->query("ALTER TABLE blestacms_posts_comments CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
		$this->Record->query("ALTER TABLE blestacms_menus CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
		$this->Record->query("ALTER TABLE blestacms_languages CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
		$this->Record->query("ALTER TABLE blestacms_settings CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"); 
	    }
        }
    }

    /**
     * Set the actions of the plugin.
     *
     * @return array An array containing the links of the plugin actions
     */
    public function getActions()
    {
        return [
            [
                'action'  => 'nav_primary_staff',
                'uri'     => 'plugin/blesta_cms/admin_main/',
                'name'    => Language::_('blesta_cms.nav.name', true),
                'options' => [
                    'sub' => [
                        [
                            'uri'  => 'plugin/blesta_cms/admin_pages/',
                            'name' => Language::_('blesta_cms.pages', true)
                        ],
                        [
                            'uri'  => 'plugin/blesta_cms/admin_posts/',
                            'name' => Language::_('blesta_cms.posts', true)
                        ],
                        [
                            'uri'  => 'plugin/blesta_cms/admin_categories/',
                            'name' => Language::_('blesta_cms.categories', true)
                        ],
                        [
                            'uri'  => 'plugin/blesta_cms/admin_menu_items/',
                            'name' => Language::_('blesta_cms.menu_items', true)
                        ],
                        [
                            'uri'  => 'plugin/blesta_cms/admin_comments/',
                            'name' => Language::_('blesta_cms.comments', true)
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     *
     * Sets the events of the plugin.
     * @return array an array containing the trigger of events
     */
    public function getEvents()
    {
        return [
           [
                  'event' => 'Appcontroller.structure',
                  'callback' => [
                      'this',
                      'run'
                  ]
            ]
       ];
    }

    public function run($event)
    {
        $result = $event->getReturnValue();
        $params = $event->getParams();

        $portal = $params['portal'];
        $controller= $params['controller'];

        if ($portal == 'client') {
            // Get uri
            $uri = substr($_SERVER['REQUEST_URI'], 1) ? substr($_SERVER['REQUEST_URI'], 1) : '/';

            // Load models
            Loader::loadModels($this, ['BlestaCms.CmsPages', 'PluginManager']);

            // Get current language
            $lang = $this->CmsPages->getCurrentLang();

            // Get page
            $page = $this->CmsPages->getPageUri($uri, true);
            $page_title = $page->title[$lang];

            foreach ($page->meta_tags[$lang]['key'] as $key => $value) {
                $message = '<meta name="' . $page->meta_tags[$lang]['key'][$key] . '" content="' . $page->meta_tags[$lang]['value'][$key] . '">';
                array_push($result['head'], $message);
            }
        }

        $event->setReturnValue($result);
        return;
    }

    public function delete_directory($dirname)
    {
        if (is_dir($dirname)) {
            $dir_handle = opendir($dirname);
        }
        if (!$dir_handle) {
            return false;
        }
        while ($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file)) {
                    unlink($dirname."/".$file);
                } else {
                    delete_directory($dirname.'/'.$file);
                }
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }

    public function debug($a)
    {
        echo '<pre>';
        print_r($a);
        echo '</pre>';
    }
}
