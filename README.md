## BlestaCMS Community Edition

Current version: **1.3.6**

#### About

The BlestaCMS Community Edition is the open-source 100% version of the BlestaCMS.
This plugin allows you to extend Blesta to make it your website. If you need support at any time you can post on the Blesta.Store community forums (Will need a client account) or ask the kind community at Blesta.com/forums.

If you would like to get support from the Blesta.store team you will require a paid license key. These come at a wide range of plans from monthly, yearly or one-time.
Grabbing a key also allows you to access a more improved BlestaCMS Premium version.

#### Extra Information:

Premium support: https://blesta.store/plugin/support/validate

Complimentary support: https://blesta.store/plugin/support/complimentary

Task board: https://atlantis.plutio.com/p/cms

#### How do I install?

To install the BlestaCMS CE version simply upload the files to a folder called "blesta_cms" in the plugins or download the zip from the blesta.store website. The zip from the website has the plugins folder already created so you can just unzip it straight.

After uploading the files go to Settings > Company > Plugins > Available > BlestaCMS.

You will need to remove the default "cms" plugin to ensure no issues arises.

The final bit you'll need to do is edit the template you are using for Blesta: `/app/views/client/(template_name)/structure.pdt`

Add the following code to the top:
```
<?php
    Loader::loadModels($this, array("BlestaCms.CmsPages"));
    $menu_items = $this->CmsPages->getMenuItemsWithChilds();
    $lang = $this->CmsPages->getCurrentLang();
    $default_lang = $this->CmsPages->getAllLang()[0]->uri;
?>
```

#### How to install the custom menu?

We're assuming you're using Bootstrap so find: `<ul class="nav navbar-nav">`

And where it has `<li><a href="url">name</a></li>` for the first dropdown list, put this above it:
```
<?php if(!empty($menu_items)){
    foreach ($menu_items as $item) {
?>
```

Then remove all the `<li></li>` code until you get the dropdown and keep one of the normal links and the dropdown.
Replase the dropdown first bits with:
```
<li>
    <a href="<?php if(isset($item->target) != 'newtab'){ ?>
//<?php echo $this->Html->safe(trim($system_company->hostname . $this->Html->safe(WEBDIR) . ($default_lang == $lang ? null : $lang . '/') . ($item->uri == '/' ? null : $item->uri)), ''); }else{ echo $item->uri; } ?>" <?php if(isset($item->target) === 'newtab'){ ?>target="_blank"<?php } ?>
        <?php if (!empty($item->childs)) { ?>aria-expanded="false" class="dropdown-toggle" data-toggle="dropdown"<?php } ?>>
        <?php echo $this->Html->ifSet($item->title[$lang], $item->title[$default_lang]); ?>
        <?php if (!empty($item->childs)) { ?><i class="fa fa-angle-down"></i><?php } ?>
    </a>
        <?php
            if (!empty($item->childs)) {
        ?>
        <ul class="dropdown-menu">
            <?php
                foreach ($item->childs as $child) {
            ?>
            <li>
                <a href="<?php if(isset($child->target) != 'newtab'){ ?>
//<?php echo $this->Html->safe(trim($system_company->hostname . $this->Html->safe(WEBDIR) . ($default_lang == $lang ? null : $lang . '/') . ($child->uri == '/' ? null : $child->uri)), ''); }else{ echo $child->uri; } ?>" <?php if(isset($child->target) === 'newtab'){ ?>target="_blank"<?php } ?>>
                    <?php echo $this->Html->ifSet($child->title[$lang], $child->title[$default_lang]); ?>
                </a>
            </li>
            <?php } ?>
        </ul>
    <?php
        }
      }
    ?>
</li>
```
Edit as required and then put a `<?php } ?>` below the end of the nav lists. So it looks like this:

```
<div class="nav-content">
    <div class="nav">
        <nav class="custom-nav" role="navigation" style="margin-top: 11px;margin-bottom:0;padding:0;float: right;"">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#cms-navbar" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="cms-navbar">
                <ul class="nav navbar-nav">
                  <?php if(!empty($menu_items)){
                      foreach ($menu_items as $item) {
                  ?>
                    <li>
                        <a href="<?php if(isset($item->target) != 'newtab'){ ?>
									//<?php echo $this->Html->safe(trim($system_company->hostname . $this->Html->safe(WEBDIR) . ($default_lang == $lang ? null : $lang . '/') . ($item->uri == '/' ? null : $item->uri)), ''); }else{ echo $item->uri; } ?>" <?php if(isset($item->target) === 'newtab'){ ?>target="_blank"<?php } ?>
                            <?php if (!empty($item->childs)) { ?>aria-expanded="false" class="dropdown-toggle" data-toggle="dropdown"<?php } ?>>
                            	<?php echo $this->Html->ifSet($item->title[$lang], $item->title[$default_lang]); ?>
								<?php if (!empty($item->childs)) { ?><i class="fa fa-angle-down"></i><?php } ?>
                        </a>
                            <?php
                                if (!empty($item->childs)) {
                            ?>
                            <ul class="dropdown-menu">
                                <?php
                                    foreach ($item->childs as $child) {
                                ?>
                                <li>
                                    <a href="<?php if(isset($child->target) != 'newtab'){ ?>
									//<?php echo $this->Html->safe(trim($system_company->hostname . $this->Html->safe(WEBDIR) . ($default_lang == $lang ? null : $lang . '/') . ($child->uri == '/' ? null : $child->uri)), ''); }else{ echo $child->uri; } ?>" <?php if(isset($child->target) === 'newtab'){ ?>target="_blank"<?php } ?>>
                                        <?php echo $this->Html->ifSet($child->title[$lang], $child->title[$default_lang]); ?>
                                    </a>
                                </li>
                                <?php } ?>
                            </ul>
                        <?php
                            }
                        ?>
                    </li>
                    <?php } } ?>
                </ul>
            </div>
        </nav>
    </div>
</div>
```

#### How to display the page title?

```
<?php
if (strpos($_SERVER['REQUEST_URI'], "blog") == true){
  echo '<h4>' . $page_title . '</h4>';   
}elseif (strpos($_SERVER['REQUEST_URI'], "category") == true){
    echo "<h3>Categories</h3>";   
}elseif( $page_title != '' ){
    echo '<h3>' . $page_title . '</h3>';
}else{
    echo '<h3>' . ($this->Html->ifSet($title) ? $this->Html->_($title, true) : $this->_("AppController.client_structure.default_title", true)) . '</h3>';
}
?>
```

#### How to display the page description?

```
<?php if (strpos($_SERVER['REQUEST_URI'], "category") == true){ ?>
  Blog
<?php }elseif (strpos($_SERVER['REQUEST_URI'], "blog") == true){ ?>
  <?php echo $categories[$category_id]; ?>
<?php }elseif (strpos($_SERVER['REQUEST_URI'], "order") == true){ ?>
  Order
<?php }else{ ?>
  <?php if(!isset($description) && empty($description)){echo "Billing Area"; }else{ echo $description; } ?>
<?php } ?>
```

#### How do I display more than 5 posts per category?

Open `/plugins/blesta_cms/controllers/main.php` and find the following:

```
$settings = array_merge(Configure::get("Blesta.pagination_client"), array(
		'total_results' => $total_results,
		'uri' => $this->base_uri . "blog/" . $page->uri . "/[p]/",
		'results_per_page' => 5,
		)
);
```

Simply change the value from 5 to a number of your choice.

#### How do I keep Blesta's pages centered and my custom pages full width?

Find:
```
<?php
if (!empty($active_nav['secondary'])) {
?>
```

and replace it all the way down to the end of the section with the following:

```
<?php
if (!empty($active_nav['secondary'])) {
?>
<div class="container">
    <div class="row<?php echo (!$this->Html->ifSet($show_header, true) ? ' login' : '');?>">
      <div class="col-md-3">
          <div class="list-group">
              <?php
              foreach ($active_nav['secondary'] as $link => $value) {
              ?>
                  <a href="<?php $this->Html->_($link);?>" class="list-group-item borderless left-nav <?php echo ($value['active'] ? 'active' : '');?>">
                      <i class="<?php $this->Html->_($value['icon']);?>"></i>
                      <?php
                      $this->Html->_($value['name']);
                      ?>
                  </a>
              <?php
              }
              ?>
          </div>
      </div>
      <div class="col-md-9">
          <div class="row">
              <?php echo $content;?>
          </div>
      </div>
    </div>
  </div>
<?php
} else {
?>
<?php if(!isset($description) && empty($description)){ ?>
<div class="container">
  <div class="row <?php echo (!$this->Html->ifSet($show_header, true) ? ' login' : '');?>">
      <?php
        }
          echo $content;
        if(!isset($description) && empty($description)){
      ?>
    </div>
  </div>
  <?php
  }
}
?>
```

If you want to use the description on the pages use the following set-up:
```
<?php
if (!empty($active_nav['secondary'])) {
?>
<div class="container">
    <div class="row<?php echo (!$this->Html->ifSet($show_header, true) ? ' login' : '');?>">
      <div class="col-md-3">
          <div class="list-group">
              <?php
              foreach ($active_nav['secondary'] as $link => $value) {
              ?>
                  <a href="<?php $this->Html->_($link);?>" class="list-group-item borderless left-nav <?php echo ($value['active'] ? 'active' : '');?>">
                      <i class="<?php $this->Html->_($value['icon']);?>"></i>
                      <?php
                      $this->Html->_($value['name']);
                      ?>
                  </a>
              <?php
              }
              ?>
          </div>
      </div>
      <div class="col-md-9">
          <div class="row">
              <?php echo $content;?>
          </div>
      </div>
    </div>
  </div>
<?php
} else {
   if (strpos($_SERVER['REQUEST_URI'], "client") && $page_title != "Log In" && $page_title != "Reset Password" ||
            strpos($_SERVER['REQUEST_URI'], "order") || strpos($_SERVER['REQUEST_URI'], "plugin") || strpos($_SERVER['REQUEST_URI'], "blog") !== false){
?>
<div class="container" style="margin-top: 30px;">
        <div class="row <?php echo (!$this->Html->ifSet($show_header, true) ? ' login' : '');?>">
        <?php }else{ ?>
<div class="container-fluid">
        <div class="row <?php echo (!$this->Html->ifSet($show_header, true) ? ' login' : '');?>">
        <?php }
       echo $content;
   }
?>
        </div>
</div>
```

#### How to display Blesta's nav to logged in customers only?

Replace it with the following:
```
<?php if (strpos($_SERVER['REQUEST_URI'], "client") || strpos($_SERVER['REQUEST_URI'], "plugin") || strpos($_SERVER['REQUEST_URI'], "order") !== false){
	if (!$this->Html->ifSet($logged_in)) {
?>
<div class="clearfix"></div>
<div class="no-nav"></div>
<?php }else{ ?>
<div class="nav-content <?php if( strpos($_SERVER['REQUEST_URI'], "plugin") ){echo "navbar-plugins";} ?>" style="margin-top: 0px;">
	<div class="nav">
		<nav class="navbar navbar-default" role="navigation">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only"><?php $this->_("AppController.sreader.navigation");?></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>

			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<div class="container">
					<?php
					$active_nav = null;
					?>
					<ul class="nav navbar-nav">
						<?php
						foreach ($this->Html->ifSet($nav, array()) as $link => $value) {
							$attributes = array();
							$link_attributes = array();
							$dropdown = !empty($value['sub']);
							$active = false;

							if ($value['active']) {
								$active = true;
								$attributes['class'][] = "active";
								$active_nav = $value;
							}
							if ($dropdown) {
								$attributes['class'][] = "dropdown";
								$link_attributes['class'][] = "dropdown-toggle";
								$link_attributes['data-toggle'][] = "dropdown";

								// Set parent to active if child is
								if (!$active) {
									foreach ($this->Html->ifSet($value['sub'], array()) as $sub_link => $sub_value) {
										if ($sub_value['active']) {
											$attributes['class'][] = "active";
											break;
										}
									}
								}
							}
						?>
						<li<?php echo $this->Html->buildAttributes($attributes);?>>
							<a href="<?php $this->Html->_($link);?>"<?php echo $this->Html->buildAttributes($link_attributes);?>>
								<i class="<?php $this->Html->_($value['icon']);?>"></i>
								<?php
								$this->Html->_($value['name']);

								if ($dropdown) {
								?>
								<b class="caret"></b>
								<?php
								}
								?>
							</a>
							<?php
							if (!empty($value['sub'])) {
							?>
							<ul class="dropdown-menu">
								<?php
								foreach ($this->Html->ifSet($value['sub'], array()) as $sub_link => $sub_value) {
								?>
								<li>
									<a href="<?php $this->Html->_($sub_link);?>"><i class="<?php $this->Html->_($sub_value['icon']);?>"></i> <?php $this->Html->_($sub_value['name']);?></a>
								</li>
								<?php
								}
								?>
							</ul>
							<?php
							}
							?>
						</li>
						<?php
						}
						?>
					</ul>

					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<?php $this->Html->_($contact->first_name);?> <?php $this->Html->_($contact->last_name);?>
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li><a href="<?php echo $this->Html->safe($this->client_uri . "main/edit/");?>"><i class="fa fa-edit fa-fw"></i> <?php $this->_("AppController.client_structure.text_update_account");?></a></li>
								<li><a href="<?php echo $this->Html->safe(WEBDIR);?>members"><i class="fa fa-circle-o fa-fw"></i> <?php $this->_("AppController.client_structure.text_return_to_portal");?></a></li>
								<li class="divider"></li>
								<li><a href="<?php echo $this->Html->safe($this->client_uri . "logout/");?>"><i class="fa fa-sign-out fa-fw"></i> <?php $this->_("AppController.client_structure.text_logout");?></a></li>
							</ul>
						</li>
					</ul>
				</div>
			</div><!-- /#header .navbar-collapse -->
		</nav>
	</div>
</div>
<?php
		 }
	}
?>
```

#### How can I display the latest blog posts in my footer?

```
<?php
	$latest_posts = $this->CmsPages->getLatestPost();

	if (!empty($latest_posts)) {
		foreach ($latest_posts as $post) {
?>
	<ul>
		<li>
			<a href="<?php echo $this->Html->safe($this->base_uri . 'blog/' . $post->uri); ?>">
				<?php echo $this->Html->safe($post->title[$lang]); ?>
			</a>
		</li>
	</ul>
<?php
		}
	}
?>
```

#### How to display the Meta Tags on my theme?
```
<?php
  if(!empty($meta_tags['key'])){
    foreach ($meta_tags['key'] as $key => $value) {
?>
    <meta name="<?php echo $this->Html->safe($meta_tags['key'][$key]);?>" content="<?php echo $this->Html->safe($meta_tags['value'][$key]);?>">
<?php
    }
  }
?>
```

#### How can I tell Google and other search engines about my other languages?

In the `structure.pdt` file in the template you are using paste this in the `<head>` section of the layout:

```
foreach( $langs AS $language ){
echo '<link rel="alternate" hreflang="<?php echo $this->Html->_($language->uri); ?>" href="<?php echo $this->Html->safe($this->base_uri . ""); ?>/<?php echo $this->Html->_($language->uri);echo $this->Html->_($page->uri); ?>" />';
}
```


#### I'm a web designer how can I make a theme to work with the CMS out of the box?

We've been thinking about that whilst designing the CMS and if you create pages for the theme put them in the `/plugins/blesta_cms/config/` folder with the following set-up:

```
Configure::set('Blesta_cms.title', 'Page Title');		
Configure::set('Blesta_cms.uri', 'uri');		
Configure::set('Blesta_cms.description', 'If there's a description, else leave blank');		
Configure::set('Blesta_cms.index', '
Put the page center content here.
');
 ```
