<?php
/**
 * Welcome page.
 *
 * @package Blesta
 * @subpackage Blesta.plugins
 * @copyright Copyright (c) 2018 Blesta.Store. All Rights Reserved.
 * @license Blesta.Store End User License Agreement
 * @author Blesta.Store <cms@blesta.store>
 */
Configure::set('Blesta_cms.title', 'Welcome');
Configure::set('Blesta_cms.uri', '/');
Configure::set('Blesta_cms.description', 'Welcome Page');
Configure::set('Blesta_cms.meta_tags', ['key' => ['keywords'], 'value' => ['welcome, page']]);
Configure::set('Blesta_cms.index', '
<div class="col-md-12">
	<div class="alert alert-info" role="alert">
		<p>Congratulations on installing the BlestaCMS. You can edit the content in the admin area.</p>
	</div>
</div>
');
