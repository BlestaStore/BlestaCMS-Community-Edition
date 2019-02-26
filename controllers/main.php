<?php
/**
 * Client side controller.
 *
 * @package Blesta
 * @subpackage Blesta.plugins
 * @copyright Copyright (c) 2018 Blesta.Store. All Rights Reserved.
 * @license Blesta.Store End User License Agreement
 * @author Blesta.Store <cms@blesta.store>
 */
class Main extends BlestaCmsController
{
    public function preAction()
    {
        // Execute parent constructor
        parent::preAction();

        // Load models
        $this->uses(['PluginManager', 'BlestaCms.CmsPages', 'Settings']);

        // Load components
        Loader::loadComponents($this, ['Session']);

        // Redirect if the plugin is not installed
        if (!$this->PluginManager->isInstalled('blesta_cms', $this->company_id)) {
            $this->redirect($this->client_uri);
        }

        // Set base uri
        $this->base_uri            = WEBDIR;
        $this->view->base_uri      = $this->base_uri;
        $this->structure->base_uri = $this->base_uri;

        // Structure
        $this->structure->setDefaultView(APPDIR);
        $this->structure->setView(null, 'client' . DS . $this->layout);

        if ($this->Session->read('blesta_client_id')) {
            // Get and set client information to the view and structure
            $this->client = $this->Clients->get($this->Session->read('blesta_client_id'));
            $this->view->set('client', $this->client);
            $this->structure->set('client', $this->client);

            // Set menu to the view and structure
            $this->structure->set('menus_items', $this->CmsPages->getMenuItemsWithChilds());
            $this->view->set('menus_items', $this->CmsPages->getMenuItemsWithChilds());
            $this->set('menus_items', $this->CmsPages->getMenuItemsWithChilds());
        }

    }

    /**
     * Call the correct function type.
     */
    public function index()
    {

        // If is language, Set and redirect, otherwise dispatch page
        if (isset($this->get[0]) && $this->CmsPages->langExists($this->get[0])) {
            // Set current language
            $this->CmsPages->setCurrentLang($this->get[0]);

            // Rebuild array
            unset($this->get[0]);
            $this->get = array_values($this->get);
            $lang      = $this->CmsPages->getCurrentLang();
        } elseif (defined('CURRENTLANGUAGE') && $this->CmsPages->langExists(CURRENTLANGUAGE)) {
            // Set current language
            $this->CmsPages->setCurrentLang(CURRENTLANGUAGE);
            $lang = $this->CmsPages->getCurrentLang();
        } else {
            // Set default language
            $lang = $this->CmsPages->getAllLang()[0]->uri;
            $this->CmsPages->setCurrentLang($lang);

            // Redirect to the default page if the language not exists
            if (isset($this->get[0]) && strlen($this->get[0]) == 2) {
                $this->redirect($this->base_uri . $this->get[1]);
            }
        }

        // Start Caching
        if ($this->Settings->getSetting('BlestaCms.Caching')->value) {
            // Trick uri string to avoid collisions over companies and languages
            $this->uri_str = $this->uri_str . DS . $this->company_id . DS . $lang;

            // Cache the request
            $this->startCaching(Configure::get('Blesta.cache_length'));

            // Print the cached version if exists
            $cached = Cache::fetchCache($this->uri_str);
            if ($cached) {
                echo $cached;
                exit;
            }
        }

        // Load Content
        $content = isset($this->get[0]) ? $this->get[0] : null;
        switch ($content) {
            case 'blog':
                // Load blog
                $this->blog();
                return $this->view->setView('main_blog', 'default');
                break;
            case 'category':
                // Load category
                $this->category();
                return $this->view->setView('main_category', 'default');
                break;
            default:
                // Load Page
                $this->page();
                return $this->view->setView('main', 'default');
                break;
        }

        // Stop Caching
        if ($this->Settings->getSetting('BlestaCms.Caching')) {
            $this->stopCaching();
        }
    }


    /**
     * Show a page.
     */
    public function page()
    {
        // Get uri
        $uri = isset($this->get[0]) ? $this->get[0] : '/';

        // Load models
        $this->uses(['BlestaCms.CmsPages', 'PluginManager']);

        // Get current language
        $lang = $this->CmsPages->getCurrentLang();

        // Initialize h2o parser
        Loader::load(VENDORDIR . 'h2o' . DS . 'h2o.php');
        $parser_options_html = Configure::get('Blesta.parser_options');
        $parser_options_html['autoescape'] = false;

        // Get page
        $page = $this->CmsPages->getPageUri($uri, true);

        // Redirect to 404 error if page not exists or if is only for guests
        if (!$page || ($page->permissions == 'guests' && !empty($this->Session->read('blesta_client_id')) && $uri !== '/')) {
            $this->redirect($this->base_uri . '404');
        }

        // Show warning for private content for logged users
        if ($page->permissions == 'logged' && empty($this->Session->read('blesta_client_id'))) {
            $this->setMessage('error', [['result' => Language::_('blesta_cms.!error.logged_in', true)]], false, null, false);

            return null;
        }

        // Tags
        $plugins           = $this->PluginManager->getAll($this->company_id);
        $installed_plugins = [];

        foreach ($plugins as $plugin) {
            $installed_plugins[$plugin->dir] = $plugin;
        }

        $url  = rtrim($this->base_url, '/');
        $tags = [
            'base_url'   => $this->Html->safe($url),
            'blesta_url' => $this->Html->safe($url . WEBDIR),
            'client_url' => $this->Html->safe($url . $this->client_uri),
            'admin_url'  => $this->Html->safe($url . $this->admin_uri),
            'plugins'    => $installed_plugins
        ];

        // Parse content tags
        $page->content = H2o::parseString($page->content[$lang], $parser_options_html)->render($tags);

        // Redirect to the default language if the translation is empty
        if (empty($page->content) && $lang != $this->CmsPages->getAllLang()[0]->uri) {
            $this->redirect($this->base_uri . $uri);
        }
        $content = $page->content;

        // Set variables
        $this->set('lang', $lang);
        $this->set('page', $page);
        $this->set('title', $page->title[$lang]);
        $this->set('content', $content);
        $this->set('meta_tags', $page->meta_tags[$lang]);
        $this->set('description', $page->description[$lang]);

        // Set structure variables
        $this->structure->set('lang', $lang);
        $this->structure->set('page', $page);
        $this->structure->set('meta_tags', $page->meta_tags[$lang]);
        $this->structure->set('page_title', $page->title[$lang]);
        $this->structure->set('description', $page->description[$lang]);
    }

    /**
     * Blog.
     */
    public function blog()
    {
        // Load models
        $this->uses(['BlestaCms.CmsPages', 'PluginManager']);

        // Get uri
        if (isset($this->get[1])) {
            $uri = $this->get[1];
        } else {
            // Get first category
            $category = $this->CmsPages->getAllCategories()[0];

            $this->redirect($this->base_uri . 'category/' . $category->id . '/');
        }

        // Get current language
        $lang = $this->CmsPages->getCurrentLang();

        // Initialize h2o parser
        Loader::load(VENDORDIR . 'h2o' . DS . 'h2o.php');
        $parser_options_html               = Configure::get('Blesta.parser_options');
        $parser_options_html['autoescape'] = false;

        // Get posts
        $post = $this->CmsPages->getPostUri($uri, true);

        // Redirect to 404 error if post not exists or if is only for guests
        if (!$post || ($post->permissions == 'guests' && !empty($this->Session->read('blesta_client_id')))) {
            $this->redirect($this->base_uri);
        }

        // Show warning for private content for logged users
        if ($post->permissions == 'logged' && empty($this->Session->read('blesta_client_id'))) {
            $this->setMessage('error', [['result' => Language::_('blesta_cms.!error.logged_in', true)]], false, null, false);

            return null;
        }

        // Tags
        $plugins           = $this->PluginManager->getAll($this->company_id);
        $installed_plugins = [];

        foreach ($plugins as $plugin) {
            $installed_plugins[$plugin->dir] = $plugin;
        }

        $url  = rtrim($this->base_url, '/');
        $tags = [
            'base_url'   => $this->Html->safe($url),
            'blesta_url' => $this->Html->safe($url . WEBDIR),
            'client_url' => $this->Html->safe($url . $this->client_uri),
            'admin_url'  => $this->Html->safe($url . $this->admin_uri),
            'plugins'    => $installed_plugins
        ];

        // Parse content tags
        $post->content = H2o::parseString($post->content[$lang], $parser_options_html)->render($tags);

        // Get all categories
        $categories = $this->CmsPages->getAllCategories();

        // Add comment
        if (!empty($this->post)) {
            $data            = $this->post;
            $data['post_id'] = $post->id;
            $result          = $this->CmsPages->addComment($data);

            // Parse result
            if ($result) {
                $this->flashMessage('message', Language::_('blesta_cms.success_comment', true), null, false);
                $this->redirect($this->base_uri . 'blog/' . $post->uri);
            } else {
                $this->setMessage('error', Language::_('blesta_cms.!error.empty', true), false, null, false);
            }
        }

        // Get page number
        if (isset($this->get[2])) {
            $page_number = $this->get[2];
        } else {
            $page_number = 1;
        }

        // Get comments
        $comments      = $this->CmsPages->getCommentsPost($post->id, $page_number, true);
        $total_results = count($this->CmsPages->getAllCommentsPost($page->id, true));
        $settings      = array_merge(Configure::get('Blesta.pagination_client'), [
            'total_results'    => $total_results,
            'uri'              => $this->base_uri . 'blog/' . $page->uri . '/[p]/',
            'results_per_page' => 5,
        ]);
        $this->helpers(['Pagination' => [[0 => $page_number], $settings]]);
        $this->Pagination->setSettings(Configure::get('Blesta.pagination_ajax'));

        // Set variables
        $this->set('lang', $lang);
        $this->set('post', $post);
        $this->set('title', $post->title[$lang]);
        $this->set('content', $post->content);
	      $this->set('uri', "FFF");
        $this->set('comments', $comments);
        $this->set('meta_tags', $post->meta_tags[$lang]);
        $this->set('date_added', $post->date_added);
        $this->set('categories', $categories);

        // Set structure variables
        $this->structure->set('lang', $lang);
        $this->structure->set('post', $post);
        $this->structure->set('meta_tags', $post->meta_tags[$lang]);
        $this->structure->set('page_title', $post->title[$lang]);
        $this->structure->set('description', $post->content);
    }

    public function category()
    {
        // Get uri
        if (isset($this->get[1])) {
            $uri = $this->get[1];
        } else {
            $this->redirect($this->base_uri . '404');
        }

        // Load models
        $this->uses(['BlestaCms.CmsPages', 'PluginManager']);

        // Get current language
        $lang = $this->CmsPages->getCurrentLang();

        // Initialize h2o parser
        Loader::load(VENDORDIR . 'h2o' . DS . 'h2o.php');
        $parser_options_html               = Configure::get('Blesta.parser_options');
        $parser_options_html['autoescape'] = false;

        // Get posts
        $posts = $this->CmsPages->getCategoryPosts($uri, true);

        // Redirect to 404 error if category is empty
        if (!$posts) {
          //  $this->redirect($this->base_uri);
        }

        // Tags
        $plugins           = $this->PluginManager->getAll($this->company_id);
        $installed_plugins = [];

        foreach ($plugins as $plugin) {
            $installed_plugins[$plugin->dir] = $plugin;
        }

        $url  = rtrim($this->base_url, '/');
        $tags = [
            'base_url'   => $this->Html->safe($url),
            'blesta_url' => $this->Html->safe($url . WEBDIR),
            'client_url' => $this->Html->safe($url . $this->client_uri),
            'admin_url'  => $this->Html->safe($url . $this->admin_uri),
            'plugins'    => $installed_plugins
        ];

        // Parse posts
        $entries = [];
        foreach ($posts as $post) {
            $post->content[$lang] = H2o::parseString($post->content[$lang])->render($tags);

            $entries[] = $post;
        }

        // Get all categories
        $categories = $this->CmsPages->getAllCategories();

        // Get current category
        $category = $this->CmsPages->getCategory($uri);

        // Set variables
        $this->set('lang', $lang);
        $this->set('entries', $entries);
        $this->set('categories', $categories);
        $this->set('logged_in', !empty($this->Session->read('blesta_client_id')));

        // Set structure variables
        $this->structure->set('lang', $lang);
        $this->structure->set('page_title', $category->title[$lang]);
        $this->structure->set('description', $category->title[$lang]);
    }
}
