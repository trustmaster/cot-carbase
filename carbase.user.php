<?php
/* ====================
Copyright (c) 2008, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.

[BEGIN_SED_EXTPLUGIN]
Code=carbase
Part=user
File=carbase.user
Hooks=users.details.tags
Tags=users.details.tpl:{CARBASE_ADMIN},{CARBASE_TOP_OWNED},{CARBASE_TOP_BOUGHT},{CARBASE_TOP_MODEL},{CARBASE_ROW_URL},{CARBASE_ROW_IMG},{CARBASE_ROW_THUMB},{CARBASE_ROW_BOUGHT},{CARBASE_ROW_MODEL}
Order=
[END_SED_EXTPLUGIN]
==================== */
if (!defined('SED_CODE')) { die('Wrong URL.'); }

require_once $cfg['plugins_dir'] . '/carbase/inc/functions.php';
require_once sed_langfile('carbase');

$t->assign(array(
'CARBASE_TOP_OWNED' => $L['Owned_cars'],
'CARBASE_TOP_BOUGHT' => $L['Bought'],
'CARBASE_TOP_MODEL' => $L['Model']
));
$sql = sed_sql_query("SELECT * FROM sed_cars_owned WHERE car_owner = $id");
while($car = sed_sql_fetcharray($sql))
{
    $ph = @sed_sql_fetcharray(sed_sql_query("SELECT * FROM sed_cars_photos WHERE ph_car = {$car['car_id']} LIMIT 1"));
	$mdl = sed_sql_fetcharray(sed_sql_query("SELECT mod_name FROM sed_cars_models WHERE mod_id = {$car['car_model']}"));
    $t->assign(array(
    'CARBASE_ROW_URL' => sed_url('plug', 'e=carbase&id='.$car['car_id']),
    'CARBASE_ROW_IMG' => $cfg['plugins_dir'] . '/carbase/photos/'.$ph['ph_id'].'.'.$ph['ph_ext'],
    'CARBASE_ROW_THUMB' => cb_photo_thumb($cfg['plugins_dir'] . '/carbase/photos/'.$ph['ph_id'].'.'.$ph['ph_ext']),
    'CARBASE_ROW_BOUGHT' => $car['car_bought'],
    'CARBASE_ROW_MODEL' => $car['car_built'].' '.sed_cc($mdl['mod_name']),
    'CARBASE_ROW_ADMIN' => $is == $usr['id'] ? '<a href="'.sed_url('plug', 'e=carbase&id='.$car['car_id'].'&act=upd').'">'.$L['Edit'].'</a>' : ''
    ));
    $t->parse('MAIN.CARBASE_ROW');
}
if($id == $usr['id']) $t->assign('CARBASE_ADMIN', '<a href="'.sed_url('plug', 'e=carbase&act=add').'">'.$L['Add_car'].'</a>');
?>