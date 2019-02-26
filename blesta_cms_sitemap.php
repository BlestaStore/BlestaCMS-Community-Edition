<?php
/**
 * Sitemap generator. (Requires Seo Tools).
 *
 * @package Blesta
 * @subpackage Blesta.plugins
 * @copyright Copyright (c) 2017 BlestaAddons. All Rights Reserved.
 * @license BlestaAddons End User License Agreement
 * @author BlestaAddons <admin@blesta-addons.com>
 */
class BlestaCmsSitemap extends SeoToolsModel
{
    /**
     * Initialize.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generates the sitemap.
     *
     * @param mixed $sitemap The sitemap
     */
    public function generateSiteMap($sitemap = null)
    {
        if ($sitemap) {
            // Load the plugin model to fetch pages from database
            Loader::loadModels($this, ['BlestaCms.CmsPages']);

            // Create base urls map
            $sitemap->page('blesta_cms', 'Blesta CMS');

            // Get current language and get languages
            $pages = $this->CmsPages->getAllPages(Configure::get('Blesta.company_id'), $show_public = true);
            $posts = $this->CmsPages->getAllPost(Configure::get('Blesta.company_id'), $show_public = true);

            foreach ($pages as $page) {
                // Add url to sitemap
                $sitemap->url($page->uri, // page link , that can be a fullurl to page
                    'now', // last modification time , optional
                    'weekly', // How frequently the page is likely to change , this is optional
                    .6 // The priority of this URL relative to other URLs on your site, optional
                );
            }
            unset($pages);

            foreach ($posts as $post) {
                // Add url to sitemap
                $sitemap->url($post->uri, // page link , that can be a fullurl to page
                    'now', // last modification time , optional
                    'weekly', // How frequently the page is likely to change , this is optional
                    .6 // The priority of this URL relative to other URLs on your site, optional
                );
            }
            unset($posts);
        }
    }
}
