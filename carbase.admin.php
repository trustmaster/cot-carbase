<?php
/* ====================
Copyright (c) 2008, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.

[BEGIN_SED_EXTPLUGIN]
Code=carbase
Part=admin
File=carbase.admin
Hooks=tools
Tags=
Order=
[END_SED_EXTPLUGIN]
==================== */
defined('SED_CODE') || die('Wrong URL.');

require_once $cfg['plugins_dir'] . '/carbase/inc/functions.php';

$mod = sed_import('mod', 'G', 'ALP');
$act = sed_import('act', 'G', 'ALP');

if($mod == 'models')
{
	$plugin_body .= '<h4>Models</h4>';
	// Structure params
	$params['id']['type'] = 'INT';
	$params['id']['pkey'] = true;
	$params['name']['type'] = 'STX';
	$params['name']['lang'] = 'Name';
	// Call routine
	cb_admin($params, 'mod', 'mod_name');
}
elseif($mod == 'engines')
{
	$plugin_body .= '<h4>Engines</h4>';
	// Structure params
	$params['id']['type'] = 'INT';
	$params['id']['pkey'] = true;
	$params['name']['type'] = 'STX';
	$params['name']['lang'] = 'Name';
	// Call routine
	cb_admin($params, 'eng', 'eng_name');
}
else
{
	$plugin_body .= <<<END
<ul>
<li><a href="admin.php?m=tools&p=carbase&mod=models">Models</a></li>
<li><a href="admin.php?m=tools&p=carbase&mod=engines">Engines</a></li>
</ul>
END;
}
?>