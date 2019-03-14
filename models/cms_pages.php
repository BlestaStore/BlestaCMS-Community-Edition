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
class CmsPages extends AppModel
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

    /**
     * Add page.
     *
     * @param  array $vars An array containing the page details
     * @return bool  True if the page is added succesfully
     */
    public function addPage(array $vars)
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Save the page in the database
        if (!empty($vars['uri']) && !empty($vars['title']) && !empty($vars['access'])) {
            $this->Record->insert('blestacms_pages', [
                'uri'         => $vars['uri'],
                'company_id'  => $company_id,
                'title'       => serialize($vars['title']),
                'content'     => serialize($vars['content']),
                'meta_tags'   => serialize($vars['meta_tags']),
                'description' => serialize($vars['description']),
                'access'      => $vars['access'],
                'permissions' => $vars['permissions']
            ]);
            return true;
        }
        return false;
    }

    /**
     * Edit page.
     *
     * @param  int   $id   The page id.
     * @param  array $vars An array containing the page details
     * @return bool  True if the page is edited succesfully
     */
    public function editPage($id, array $vars)
    {
        // Edit page
        if (!empty($vars['content']) && !empty($vars['title']) && !empty($vars['access'])) {
            $this->Record->where('id', '=', $id)->update('blestacms_pages', [
                'uri'         => $vars['uri'],
                'title'       => serialize($vars['title']),
                'content'     => serialize($vars['content']),
                'meta_tags'   => serialize($vars['meta_tags']),
                'description' => serialize($vars['description']),
                'access'      => $vars['access'],
                'permissions' => $vars['permissions']
            ]);

            return true;
        }

        return false;
    }

    /**
     * Get a specific page.
     *
     * @param  int      $id          The page id.
     * @param  bool     $show_public Set if the page are public or hidden
     * @return stdClass An object containing all the page data
     */
    public function getPage($id)
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Get page
        $records = $this->Record->select()->from('blestacms_pages')->
            where('id', '=', $id)->
            where('company_id', '=', $company_id);

        // Unserialize the data
        $result              = $records->fetch();
        $result->title       = unserialize($result->title);
        $result->content     = unserialize($result->content);
        $result->meta_tags   = unserialize($result->meta_tags);
        $result->description = unserialize($result->description);

        return $result;
    }

    /**
     * Get a post by URI.
     *
     * @param  string   $uri The page uri
     * @return atdClass An object containing all the post fields
     */
    public function getPageUri($uri, $show_public = false)
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Get post
        $records = $this->Record->select()->from('blestacms_pages')
            ->where('uri', '=', $uri)
            ->where('company_id', '=', $company_id);

        // Get only
        if ($show_public) {
            $records->where('access', '=', 'public');
        }

        // Unserialize the data
        $result = $records->fetch();
        if ($result) {
            $result->title       = unserialize($result->title);
            $result->content     = unserialize($result->content);
            $result->meta_tags   = unserialize($result->meta_tags);
            $result->description = unserialize($result->description);

            return empty($result->uri) ? false : $result;
        }
    }

    /**
     * Get all the company pages.
     *
     * @param  bool  $show_public True, to show only the public pages
     * @return array An array containing all the pages
     */
    public function getAllPages($show_public = false)
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Get all pages
        $records = $this->Record->select()->from('blestacms_pages')->
            where('company_id', '=', $company_id);

        // Get only public pages
        if ($show_public) {
            $records->where('access', '=', 'public');
        }

        // Unserialize results
        $pages = $records->fetchAll();
        foreach ($pages as $key => $page) {
            $page->title       = unserialize($page->title);
            $page->content     = unserialize($page->content);
            $page->meta_tags   = unserialize($page->meta_tags);
            $page->description = unserialize($page->description);

            $pages[$key] = $page;
        }

        return $pages;
    }

    /**
     * Delete a page.
     *
     * @param  int  $id The page ID
     * @return bool True if the page has been deleted
     */
    public function deletePage($id)
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Delete page
        $this->Record->from('blestacms_pages')->
            where('id', '=', $id)->
            where('company_id', '=', $company_id)->
            delete();

        return true;
    }

    /**
     * Add a post.
     *
     * @param  array $vars An array containing the post data to add
     * @return bool  True if the post hass been added
     */
    public function addPost(array $vars)
    {
        // Generate add date
        $vars['date_added'] = date('Y-m-d H:i:s');

        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Add the post in the database
        if (!empty($vars['uri']) && !empty($vars['title']) && !empty($vars['content']) && !empty($vars['category_id'])) {
            $this->Record->insert('blestacms_blog_posts', [
                'uri'         => $vars['uri'],
                'company_id'  => $company_id,
                'category_id' => $vars['category_id'],
                'author'      => $this->Session->read('blesta_staff_id'),
                'title'       => serialize($vars['title']),
                'content'     => serialize($vars['content']),
                'meta_tags'   => serialize($vars['meta_tags']),
                'access'      => $vars['access'],
                'permissions' => $vars['permissions'],
                'date_added'  => $vars['date_added'],
                'image'       => $vars['image']
            ]);

            return true;
        }

        return false;
    }

    /**
     * Edit a post.
     *
     * @param  int   $id   The post id
     * @param  array $vars An array containing the post data to update
     * @return bool  True if the post has been edited
     */
    public function editPost($id, array $vars)
    {
        // Generate add date
        $vars['date_updated'] = date('Y-m-d H:i:s');

        // Edit the post in the database
        if (!empty($vars['title']) && !empty($vars['content'])) {
            $this->Record->where('id', '=', $id)->update('blestacms_blog_posts', [
                'uri'          => $vars['uri'],
                'category_id'  => $vars['category_id'],
                'title'        => serialize($vars['title']),
                'content'      => serialize($vars['content']),
                'meta_tags'    => serialize($vars['meta_tags']),
                'date_updated' => $vars['date_updated'],
                'access'       => $vars['access'],
                'permissions'  => $vars['permissions'],
                'image'        => $vars['image']
            ]);

            return true;
        }

        return false;
    }

    /**
     * Get a post by ID.
     *
     * @param  int      $id The post id
     * @return atdClass An object containing all the post fields
     */
    public function getPost($id)
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Get post
        $records = $this->Record->select()->from('blestacms_blog_posts')->where('id', '=', $id);

        // Unserialize the data
        $result            = $records->fetch();
        $result->author    = $this->Staff->get(empty($result->author) ? 1 : $result->author, $company_id);
        $result->title     = unserialize($result->title);
        $result->content   = unserialize($result->content);
        $result->meta_tags = unserialize($result->meta_tags);

        return $result;
    }

    /**
     * Get a post by URI.
     *
     * @param  string $uri         The post URI
     * @param  bool   $show_public True to show only if is public post
     * @return array  An array containing all the post fields
     */
    public function getPostUri($uri, $show_public = false)
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Get the post
        $records = $this->Record->select()->from('blestacms_blog_posts')->
            where('uri', '=', $uri)->
            where('company_id', '=', $company_id);

        // Show only public
        if ($show_public) {
            $records->where('access', '=', 'public');
        }


        // Unserialize the data
        $result            = $records->fetch();
        $result->author    = $this->Staff->get(empty($result->author) ? 1 : $result->author, $company_id);
        $result->title     = unserialize($result->title);
        $result->content   = unserialize($result->content);
        $result->meta_tags = unserialize($result->meta_tags);

        return empty($result->content) ? false : $result;
    }

    /**
     * Get all the posts of the company.
     *
     * @param  bool  $show_public True to show only the public posts
     * @return array An array containing all the posts
     */
    public function getAllPost($show_public = false)
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Get all posts from the database
        $records = $this->Record->select()->from('blestacms_blog_posts')->
            where('company_id', '=', $company_id);

        // Show only public
        if ($show_public) {
            $records->where('access', '=', 'public');
        }


        // Unserialize results
        $posts = $records->fetchAll();
        foreach ($posts as $key => $post) {
            $post->author    = $this->Staff->get(empty($post->author) ? 1 : $post->author, $company_id);
            $post->title     = unserialize($post->title);
            $post->content   = unserialize($post->content);
            $post->meta_tags = unserialize($post->meta_tags);

            $posts[$key] = $post;
        }

        return $posts;
    }

    /**
     * Get the latest posts of the company.
     *
     * @param  int   $posts       The quantity of posts to get
     * @param  bool  $show_public True to show only the public posts
     * @return array An array containing all the posts
     */
    public function getLatestPost($posts = 5, $show_public = true)
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Get all posts from the database
        $records = $this->Record->select()->from('blestacms_blog_posts')
            ->where('company_id', '=', $company_id)
            ->limit($posts)
            ->order(['id' => 'desc']);

        // Show only public
        if ($show_public) {
            $records->where('access', '=', 'public');
        }

        // Unserialize results
        $posts = $records->fetchAll();
        foreach ($posts as $key => $post) {
            $post->author    = $this->Staff->get(empty($post->author) ? 1 : $post->author, $company_id);
            $post->title     = unserialize($post->title);
            $post->content   = unserialize($post->content);
            $post->meta_tags = unserialize($post->meta_tags);

            $posts[$key] = $post;
        }

        return $posts;
    }

    /**
     * Delete a post.
     *
     * @param  int  $id The post id
     * @return bool True if the post has been deleted
     */
    public function deletePost($id)
    {
        // Delete all the comments
        $comments = $this->getPostComments($id);

        foreach ($comments as $comment) {
            $this->deleteComment($comment->id);
        }

        // Delete the post
        $this->Record->from('blestacms_blog_posts')->where('id', '=', $id)->delete();

        return true;
    }

    /**
     * Get all the comments of a post.
     *
     * @param  int   $id              The post id
     * @param  bool  $status_approved True to show only the approved comments
     * @return array An array containing all the post comments
     */
    public function getPostComments($id, $status_approved = false)
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Get the post comments
        $records = $this->Record->select()->from('blestacms_posts_comments')->
            where('company_id', '=', $company_id)->
            where('post_id', '=', $id);

        // Get only the approved comments
        if ($status_approved) {
            $records->where('status', '=', 'approved');
        }

        return $records->fetchAll();
    }

    /**
     * Add a category.
     *
     * @param  array $vars An array containing the category data to add
     * @return bool  True if the category hass been added
     */
    public function addCategory(array $vars)
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Add the post in the database
        if (!empty($vars['title'])) {
            $this->Record->insert('blestacms_blog_categories', [
                'company_id'  => $company_id,
                'title'       => serialize($vars['title']),
                'access'      => $vars['access']
            ]);
            return true;
        }
        return false;
    }

    /**
     * Edit category.
     *
     * @param  int   $id   The category id.
     * @param  array $vars An array containing the category details
     * @return bool  True if the category is edited succesfully
     */
    public function editCategory($id, array $vars)
    {
        // Edit the category in the database
        if (!empty($vars['title'])) {
            $this->Record->where('id', '=', $id)->update('blestacms_blog_categories', [
                'title'  => serialize($vars['title']),
                'access' => $vars['access']
            ]);
            return true;
        }
        return false;
    }

    /**
     * Get a category by ID.
     *
     * @param  int      $id The category id
     * @return atdClass An object containing all the category fields
     */
    public function getCategory($id)
    {
        // Get category
        $records = $this->Record->select()->from('blestacms_blog_categories')->where('id', '=', $id);

        // Unserialize the data
        $result          = $records->fetch();
        $result->title   = unserialize($result->title);

        return $result;
    }

    /**
     * Get all the blog categories.
     *
     * @param  bool  $show_public Set if the page are public or hidden
     * @return array An array containing all the blog categories
     */
    public function getAllCategories($show_public = false)
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Get categories from the database
        $records = $this->Record->select()->from('blestacms_blog_categories')->
            where('company_id', '=', $company_id);

        // Show public categories only
        if ($show_public) {
            $records->where('access', '=', 'public');
        }

        // Unserialize results
        $categories = $records->fetchAll();
        foreach ($categories as $key => $category) {
            $category->title = unserialize($category->title);

            $categories[$key] = $category;
        }

        return $categories;
    }

    /**
     * Get all the posts of a category.
     *
     * @param  int   $id          The category id
     * @param  bool  $show_public True to show only the public posts
     * @return array An array containing all the posts
     */
    public function getCategoryPosts($id, $show_public = false)
    {
        // Get all posts of the category
        $records = $this->Record->select()->from('blestacms_blog_posts')->where('category_id', '=', $id);

        // Show only public
        if ($show_public) {
            $records->where('access', '=', 'public');
        }

        // Parse results
        $posts   = $records->order(['id'=>'desc'])->fetchAll();
        $results = [];
        foreach ($posts as $post) {
            $results[$post->id] = $this->getPost($post->id);
        }

        return empty($posts) ? false : $results;
    }

    /**
     * Delete a category.
     *
     * @param  int  $id The category id
     * @return bool True if the category has been deleted
     */
    public function deleteCategory($id)
    {
        // Delete all the posts
        $posts = $this->getCategoryPosts($id);
        foreach ($posts as $post) {
            $this->deletePost($post->id);
        }

        // Delete the post
        $result = $this->Record->from('blestacms_blog_categories')->where('id', '=', $id)->delete();

        return true;
    }

    /**
     * Add a comment.
     *
     * @param  array $vars An array containing the comment data
     * @return bool  True if the comment has been posted succesfully
     */
    public function addComment(array $vars)
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Generate add date
        $vars['date_added'] = date('Y-m-d H:i:s');

        // Validate Email
        if (!filter_var($vars['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (!empty($vars['full_name']) && !empty($vars['email']) && !empty($vars['post_id']) && !empty($vars['content'])) {
            $this->Record->insert('blestacms_posts_comments', [
                'full_name'  => $vars['full_name'],
                'email'      => $vars['email'],
                'company_id' => $company_id,
                'post_id'    => $vars['post_id'],
                'content'    => $vars['content'],
                'date_added' => $vars['date_added']
            ]);

            return true;
        }

        return false;
    }

    /**
     * Delete a comment.
     *
     * @param  int  $id The comment id
     * @return bool True if the comment has been deleted
     */
    public function deleteComment($id)
    {
        // Delete comment
        $this->Record->from('blestacms_posts_comments')->where('id', '=', $id)->delete();

        return true;
    }

    /**
     * Get all the comments of a post. (with pagination).
     *
     * @param  int      $id              The post id
     * @param  int      $page            The page number
     * @param  bool     $status_approved True to show ony the approved comments
     * @return stdClass An object containing the post comments
     */
    public function getCommentsPost($id, $page, $status_approved = false)
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Get comments
        $records = $this->Record->select()->from('blestacms_posts_comments')->
            where('company_id', '=', $company_id)->
            where('post_id', '=', $id)->
            limit(5, (max(1, $page) - 1) * 5)->
            order(['date_added' => 'desc']);

        // Get only the approved comments
        if ($status_approved) {
            $records->where('status', '=', 'approved');
        }
        return $records->fetchAll();
    }

    /**
     * Get all the comments.
     *
     * @param  bool     $status_approved True to get only the approved comments
     * @return stdClass An object with all the comments
     */
    public function getAllComments($status_approved = false)
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Get comments
        $records = $this->Record->select()->from('blestacms_posts_comments')->
            where('company_id', '=', $company_id);

        // Get only the approved comments
        if ($status_approved) {
            $records->where('status', '=', 'approved');
        }
        return $records->fetchAll();
    }

    /**
     * Get all the comments of a post.
     *
     * @param  int      $id              The post id
     * @param  bool     $status_approved True to show ony the approved comments
     * @return stdClass An object containing the post comments
     */
    public function getAllCommentsPost($id, $status_approved = false)
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Get comments
        $records = $this->Record->select()->from('blestacms_posts_comments')->
            where('company_id', '=', $company_id)->
            where('post_id', '=', $id);

        // Get only the approved comments
        if ($status_approved) {
            $records->where('status', '=', 'approved');
        }
        return $records->fetchAll();
    }

    /**
     * Approve a comment.
     *
     * @param  int  $id The comment id
     * @return bool True if the comment has been approved
     */
    public function approveComment($id)
    {
        $this->Record->where('id', '=', $id)->update('blestacms_posts_comments', ['status' => 'approved']);
        return true;
    }

    /**
     * Add a menu item.
     *
     * @param  array $vars An array containing the menu item fields
     * @return bool  True if the menu item has been addded successfully
     */
    public function addMenuItem(array $vars)
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Add menu item
        if (!empty($vars['title']) && !empty($vars['uri'])) {
            $this->Record->insert('blestacms_menus', [
                'title'      => serialize($vars['title']),
                'uri'        => $vars['uri'],
                'company_id' => $company_id,
                'parent'     => $vars['parent'],
                'access'     => $vars['access'],
                'target'     => $vars['target']
            ]);
            return true;
        }
        return false;
    }

    /**
     * Edit a menu item.
     *
     * @param  int   $id   The id of the menu item to edit
     * @param  array $vars An array containing the menu item fields
     * @return bool  True if the menu item has been edited successfully
     */
    public function editMenuItem($id, array $vars)
    {
        // Edit menu item
        if (!empty($vars['title']) && !empty($vars['uri'])) {
            $this->Record->where('id', '=', $id)->update('blestacms_menus', [
                'title'  => serialize($vars['title']),
                'uri'    => $vars['uri'],
                'parent' => $vars['parent'],
                'access' => $vars['access'],
                'target'     => $vars['target']
            ]);
            return true;
        }
        return false;
    }

    /**
     * Delete a menu item.
     *
     * @param  int  $id The id of the menu item to delete
     * @return bool True if the menu item has been deleted successfully
     */
    public function deleteMenuItem($id)
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Delete menu item
        $this->Record->from('blestacms_menus')
            ->where('company_id', '=', $company_id)
            ->where('id', '=', $id)
            ->delete();
        return true;
    }

    /**
     * Get a menu item.
     *
     * @param  int      $id          The id of the menu item
     * @param  bool     $show_public True to get only if is public
     * @return stdClass An object containing the menu item fields
     */
    public function getMenuItem($id, $show_public = false)
    {
        // Get menu item from the database
        $records = $this->Record->select()->from('blestacms_menus')->
            where('id', '=', $id);

        // Show only if is public
        if ($show_public) {
            $records->where('access', '=', 'public');
        }

        // Unserialize result
        $result        = $records->fetch();
        $result->title = unserialize($result->title);
        return $result;
    }

    /**
     * Get all the menu items.
     *
     * @param  bool   $show_public True to get only the public items
     * @param  string $parent      The parent type to get. Values: all, parent, child
     * @return array  An array containing all the menu items
     */
    public function getAllMenuItems($show_public = false, $parent = 'all')
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Get menu items from the database
        $records = $this->Record->select()->from('blestacms_menus')->
            where('company_id', '=', $company_id);

        // Get only public menu items
        if ($show_public) {
            $records->where('access', '=', 'public');
        }

        // Parent selector
        if ($parent === 'parent') {
            $records->where('parent', '=', '');
        } elseif ($parent == 'child') {
            $records->where('parent', '<>', '');
        }

        // Unserialize results
        $items = $records->fetchAll();
        foreach ($items as $key => $item) {
            $item->title = unserialize($item->title);
            $items[$key] = $item;
        }

        return $items;
    }

    /**
     * Get all the menu items by parent.
     *
     * @param  bool   $show_public True to get only the public items
     * @param  string $parent_id   The parent id.
     * @return array  An array containing all the parent menu items
     */
    public function getAllMenuItemsByParent($show_public, $parent_id)
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Get menu items from the database
        $records = $this->Record->select()->from('blestacms_menus')->
            where('company_id', '=', $company_id);

        // Show only public parents
        if ($show_public) {
            $records->where('access', '=', 'public');
        }
        $records->where('parent', '=', $parent_id);

        // Unserialize results
        $items = $records->fetchAll();
        foreach ($items as $key => $item) {
            $item->title = unserialize($item->title);
            $items[$key] = $item;
        }
        return $items;
    }

    /**
     * Get the parents field.
     *
     * @param  bool   $show_public True to get only the public items
     * @param  string $parent      The parent type to get. Values: all, parent, child
     * @return array  An array containing all the menu items
     */
    public function getMenuParentsField($show_public = false, $parent = 'all')
    {
        // Get menu items
        $cate      = $this->getAllMenuItems($show_public, $parent);
        $array     = [];
        $array[''] = Language::_('blesta_cms.notchild', true);

        foreach ($cate as $key => $value) {
            $array[$cate[$key]->id] = reset($cate[$key]->title);
        }

        return $array;
    }

    /**
     * Get all the menu items with their respective childs.
     *
     * @return array An array with all the menu items
     */
    public function getMenuItemsWithChilds()
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        // Get all the menu items with childs
        $records = $this->getAllMenuItems(true, 'parent');

        foreach ($records as $key => $value) {
            $records[$key]->childs = $this->getAllMenuItemsByParent(true, $value->id);
        }

        return $records;
    }

    /**
     * Add a Language.
     *
     * @param  string $name The language name
     * @param  string $lang The language code
     * @return bool   True if the language has added set succesfully
     */
    public function addLang($name, $lang)
    {
        // Install Language
        $company_id = Configure::get('Blesta.company_id');

        if (!empty($name) && !empty($lang) && !$this->langExists($lang)) {
            $this->Record->insert('blestacms_languages', ['uri' => $lang, 'company_id' => $company_id, 'title' => $name]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Edit a Language.
     *
     * @param  string $name     The language name
     * @param  string $lang     The language code
     * @param  string $old_lang The old language code
     * @return bool   True if the language has edited set succesfully
     */
    public function editLang($name, $lang, $old_lang)
    {
        // Remove Language
        $company_id = Configure::get('Blesta.company_id');

        if (!empty($lang)) {
            $this->Record->where('uri', '=', $old_lang)->update('blestacms_languages', ['uri' => $lang, 'title' => $name]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Delete a Language.
     *
     * @param  string $name The language name
     * @param  string $lang The language code
     * @return bool   True if the language has deleted set succesfully
     */
    public function deleteLang($lang)
    {
        // Remove Language
        $company_id = Configure::get('Blesta.company_id');
        $langs      = $this->getAllLang();

        if (!empty($lang) && count($langs) > 1) {
            $this->Record->from('blestacms_languages')->where('uri', '=', $lang)->delete();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Set a Language.
     *
     * @param  string $lang The language code to set
     * @return bool   True if the language has been set succesfully
     */
    public function setCurrentLang($lang)
    {
        // Get all installed languages
        $langs = $this->getAllLang();

        // Change Language if is available
        if (isset($lang)) {
            foreach ($langs as $language) {
                if ($language->uri === $lang) {
                    $this->Session->write('blestacms_language', $lang);
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get all the installed languages in the database.
     *
     * @return array An array containing all the installed languages
     */
    public function getAllLang()
    {
        // Get company id
        $company_id = Configure::get('Blesta.company_id');

        $langs = $this->Record->select()->from('blestacms_languages')
            ->where('company_id', '=', $company_id)
            ->fetchAll();
        return $langs;
    }

    /**
     * Check if a lang exists.
     *
     * @param  string $lang_code The ISO 639-1 code of the language
     * @return bool   True if the lang exists
     */
    public function langExists($lang_code)
    {
        // Get all languages
        $langs = $this->getAllLang();
        foreach ($langs as $lang) {
            $langs_array[$lang->uri] = $lang->title;
        }
        return array_key_exists($lang_code, $langs_array);
    }

    /**
     * Get the current language.
     *
     * @return string The current language
     */
    public function getCurrentLang()
    {
        // Load session lang
        $lang = $this->Session->read('blestacms_language');

        if (empty($lang) || !$this->langExists($lang)) {
            // Detect user language
            $user_lang = explode('-', $_SERVER['HTTP_ACCEPT_LANGUAGE'], 2)[0];

            // Check if user lang exists
            if ($this->langExists($user_lang)) {
                $this->setCurrentLang($user_lang);
            } else {
                // User language not exists, Set the first language installed
                $lang = $this->Record->select()->from('blestacms_languages')->fetch()->uri;
                $this->setCurrentLang($lang);
            }
        }
        return $lang;
    }

    /**
     * Get the default language of the system.
     *
     * @return string The default language
     */
    public function getDefaultLang()
    {
        $lang = $this->Companies->getSetting(Configure::get('Blesta.company_id'), 'language');
        $lang = explode('_', $lang->value, 2)[0];
        return $lang;
    }

    /**
     * Get the display name of a lang from the ISO 639-1 format.
     *
     * @param  string $iso_code The ISO 639-1 code of the language
     * @return string The full name of the language
     */
    public function getDisplayLang($iso_code)
    {
        $language_codes = [
            'en' => 'English',
            'aa' => 'Afar',
            'ab' => 'Abkhazian',
            'af' => 'Afrikaans',
            'am' => 'Amharic',
            'ar' => 'Arabic',
            'as' => 'Assamese',
            'ay' => 'Aymara',
            'az' => 'Azerbaijani',
            'ba' => 'Bashkir',
            'be' => 'Byelorussian',
            'bg' => 'Bulgarian',
            'bh' => 'Bihari',
            'bi' => 'Bislama',
            'bn' => 'Bengali/Bangla',
            'bo' => 'Tibetan',
            'br' => 'Breton',
            'ca' => 'Catalan',
            'co' => 'Corsican',
            'cs' => 'Czech',
            'cy' => 'Welsh',
            'da' => 'Danish',
            'de' => 'German',
            'dz' => 'Bhutani',
            'el' => 'Greek',
            'eo' => 'Esperanto',
            'es' => 'Spanish',
            'et' => 'Estonian',
            'eu' => 'Basque',
            'fa' => 'Persian',
            'fi' => 'Finnish',
            'fj' => 'Fiji',
            'fo' => 'Faeroese',
            'fr' => 'French',
            'fy' => 'Frisian',
            'ga' => 'Irish',
            'gd' => 'Scots/Gaelic',
            'gl' => 'Galician',
            'gn' => 'Guarani',
            'gu' => 'Gujarati',
            'ha' => 'Hausa',
            'hi' => 'Hindi',
            'hr' => 'Croatian',
            'hu' => 'Hungarian',
            'hy' => 'Armenian',
            'ia' => 'Interlingua',
            'ie' => 'Interlingue',
            'ik' => 'Inupiak',
            'in' => 'Indonesian',
            'is' => 'Icelandic',
            'it' => 'Italian',
            'iw' => 'Hebrew',
            'ja' => 'Japanese',
            'ji' => 'Yiddish',
            'jw' => 'Javanese',
            'ka' => 'Georgian',
            'kk' => 'Kazakh',
            'kl' => 'Greenlandic',
            'km' => 'Cambodian',
            'kn' => 'Kannada',
            'ko' => 'Korean',
            'ks' => 'Kashmiri',
            'ku' => 'Kurdish',
            'ky' => 'Kirghiz',
            'la' => 'Latin',
            'ln' => 'Lingala',
            'lo' => 'Laothian',
            'lt' => 'Lithuanian',
            'lv' => 'Latvian/Lettish',
            'mg' => 'Malagasy',
            'mi' => 'Maori',
            'mk' => 'Macedonian',
            'ml' => 'Malayalam',
            'mn' => 'Mongolian',
            'mo' => 'Moldavian',
            'mr' => 'Marathi',
            'ms' => 'Malay',
            'mt' => 'Maltese',
            'my' => 'Burmese',
            'na' => 'Nauru',
            'ne' => 'Nepali',
            'nl' => 'Dutch',
            'no' => 'Norwegian',
            'oc' => 'Occitan',
            'om' => '(Afan)/Oromoor/Oriya',
            'pa' => 'Punjabi',
            'pl' => 'Polish',
            'ps' => 'Pashto/Pushto',
            'pt' => 'Portuguese',
            'qu' => 'Quechua',
            'rm' => 'Rhaeto-Romance',
            'rn' => 'Kirundi',
            'ro' => 'Romanian',
            'ru' => 'Russian',
            'rw' => 'Kinyarwanda',
            'sa' => 'Sanskrit',
            'sd' => 'Sindhi',
            'sg' => 'Sangro',
            'sh' => 'Serbo-Croatian',
            'si' => 'Singhalese',
            'sk' => 'Slovak',
            'sl' => 'Slovenian',
            'sm' => 'Samoan',
            'sn' => 'Shona',
            'so' => 'Somali',
            'sq' => 'Albanian',
            'sr' => 'Serbian',
            'ss' => 'Siswati',
            'st' => 'Sesotho',
            'su' => 'Sundanese',
            'sv' => 'Swedish',
            'sw' => 'Swahili',
            'ta' => 'Tamil',
            'te' => 'Tegulu',
            'tg' => 'Tajik',
            'th' => 'Thai',
            'ti' => 'Tigrinya',
            'tk' => 'Turkmen',
            'tl' => 'Tagalog',
            'tn' => 'Setswana',
            'to' => 'Tonga',
            'tr' => 'Turkish',
            'ts' => 'Tsonga',
            'tt' => 'Tatar',
            'tw' => 'Twi',
            'uk' => 'Ukrainian',
            'ur' => 'Urdu',
            'uz' => 'Uzbek',
            'vi' => 'Vietnamese',
            'vo' => 'Volapuk',
            'wo' => 'Wolof',
            'xh' => 'Xhosa',
            'yo' => 'Yoruba',
            'zh' => 'Chinese',
            'zu' => 'Zulu'
        ];

        return (empty($language_codes[$iso_code])) ? $iso_code : $language_codes[$iso_code];
    }
}
