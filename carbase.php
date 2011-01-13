<?php
/* ====================
Copyright (c) 2008, Vladimir Sibirov.
All rights reserved. Distributed under BSD License.

[BEGIN_SED_EXTPLUGIN]
Code=carbase
Part=main
File=carbase
Hooks=standalone
Tags=
Order=
[END_SED_EXTPLUGIN]
==================== */
defined('SED_CODE') || die('Wrong URL.');

require_once $cfg['plugins_dir'] . '/carbase/inc/functions.php';

$id = (int) sed_import('id', 'G', 'INT');
$act = sed_import('act', 'G', 'ALP');
$upd = sed_import('upd', 'P', 'BOL');
$ph_id = sed_import('pid', 'G', 'ALP');
$uid = sed_import('uid', 'G', 'INT');

if($id > 0)
{
    $isadmin = sed_auth('plug', 'carbase', 'A') || sed_auth('plug', 'carbase', 'W') && sed_sql_result(sed_sql_query("SELECT COUNT(*) FROM sed_cars_owned WHERE car_id = $id AND car_owner = {$usr['id']}"),0,0) > 0;
    $car = sed_sql_fetchassoc(sed_sql_query("SELECT * FROM sed_cars_owned WHERE car_id = $id"));
    if(!$car) sed_die();
    if($act == 'upd' && $isadmin)
    {   
       // Edit mode
       if ($upd)
       {
            // Apply changes
            $car['car_model'] = sed_import('model', 'P', 'INT');
            $car['car_built'] = sed_import('built', 'P', 'INT');
            $car['car_bought'] = sed_import('bought', 'P', 'INT');
            $car['car_engine'] = sed_import('engine', 'P', 'INT');
            $car['car_fuel'] = "'" . sed_import('fuel', 'P', 'ALP') . "'";
            $car['car_cylinders'] = sed_import('cylinders', 'P', 'INT');
            $car['car_volume'] = sed_import('volume', 'P', 'INT');
            $car['car_valves'] = sed_import('valves', 'P', 'INT');
            $car['car_power'] = sed_import('power', 'P', 'INT');
            $car['car_rpm'] = sed_import('rpm', 'P', 'INT');
            $car['car_gearbox'] = "'" . sed_import('gearbox', 'P', 'ALP') . "'";
            $car['car_gears'] = sed_import('gears', 'P', 'INT');
            $car['car_descr'] = "'" . sed_sql_prep(sed_import('descr', 'P', 'STX')) . "'";
            if(!empty($car['car_model']))
            {
                $car['car_added'] = NULL;
                $car['car_views'] = NULL;
                $car['car_id'] = NULL;
                $sql_upd = '';
                foreach ($car as $key => $val)
                    if (! empty($val))
                        $sql_upd .= "$key = $val,";
                $sql_upd = mb_substr($sql_upd, 0, - 1);
                sed_sql_query("UPDATE sed_cars_owned SET $sql_upd WHERE car_id = $id");
				// Update photo details
				$sql = sed_sql_query("SELECT ph_id, ph_name, ph_com FROM sed_cars_photos WHERE ph_car = $id");
				while($row = sed_sql_fetcharray($sql))
				{
					$ph_name = sed_sql_prep(sed_import('ph_name_'.$row['ph_id'], 'P', 'STX'));
					$ph_com = sed_sql_prep(sed_import('ph_com_'.$row['ph_id'], 'P', 'STX'));
					if($ph_name != $row['ph_name'] || $ph_com != $row['ph_com'])
						sed_sql_query("UPDATE sed_cars_photos SET ph_name = '$ph_name', ph_com = '$ph_com' WHERE ph_id = {$row['ph_id']}");
				}
				// Upload a photo
                if($_FILES['photo']['size'] > 0)
				{
					$ph_name = sed_sql_prep(sed_import('photo_name', 'P', 'STX'));
					$ph_descr = sed_sql_prep(sed_import('photo_descr', 'P', 'STX'));
					cb_photo_add($id, 'photo', $ph_name, $ph_descr);
				}
                // Show updated entry
                header('Location: ' . SED_ABSOLUTE_URL . sed_url('plug', "e=carbase&id=$id&act=upd", '', TRUE));
                exit();
            }
        }
        // Display edit form
		$t = new XTemplate(sed_skinfile('carbase.edit', TRUE));
        $cyl = '';
        foreach(array(2, 3, 4, 5, 6, 8) as $n)
        $cyl .= $n == $car['car_cylinders'] ? '<option value="'.$n.'" selected="selected">'.$n.'</option>' : '<option value="'.$n.'">'.$n.'</option>';
        $grs = '';
        foreach(array(4, 5, 6, 7) as $n)
        $grs .= $n == $car['car_gears'] ? '<option value="'.$n.'" selected="selected">'.$n.'</option>' : '<option value="'.$n.'">'.$n.'</option>';
		$vlv = '';
        foreach(array(6, 8, 12, 16, 20, 24, 30, 32) as $n)
        $vlv .= $n == $car['car_valves'] ? '<option value="'.$n.'" selected="selected">'.$n.'</option>' : '<option value="'.$n.'">'.$n.'</option>';
		$date = getdate();
		$blt = '';
		for($n = 1920; $n <= $date['year']; $n++)
        $blt .= $n == $car['car_built'] ? '<option value="'.$n.'" selected="selected">'.$n.'</option>' : '<option value="'.$n.'">'.$n.'</option>';
		$bgt = '';
		for($n = 1920; $n <= $date['year']; $n++)
        $bgt .= $n == $car['car_bought'] ? '<option value="'.$n.'" selected="selected">'.$n.'</option>' : '<option value="'.$n.'">'.$n.'</option>';
        $t->assign(array(
    	'CARBASE_ACTION' => sed_url('plug', 'e=carbase&id='.$id.'&act=upd'),
    	'CARBASE_MODEL' => cb_rowset2selectbox('model', sed_sql_query('SELECT * FROM sed_cars_models ORDER BY mod_name'), 'mod_id', 'mod_name', $car['car_model']),
    	'CARBASE_BUILT' => $blt,
    	'CARBASE_BOUGHT' => $bgt,
    	'CARBASE_ENGINE' => cb_rowset2selectbox('engine', sed_sql_query('SELECT * FROM sed_cars_engines ORDER BY eng_name'), 'eng_id', 'eng_name', $car['car_engine']),
    	'CARBASE_GASOLINE' => $car['car_fuel'] == 'gasoline' ? 'selected="selected"' : '',
        'CARBASE_DIESEL' => $car['car_fuel'] == 'diesel' ? 'selected="selected"' : '',
        'CARBASE_CYLINDERS' => $cyl,
        'CARBASE_VOLUME' => $car['car_volume'],
        'CARBASE_VALVES' => $vlv,
        'CARBASE_POWER' => $car['car_power'],
        'CARBASE_RPM' => $car['car_rpm'],
        'CARBASE_MANUAL' => $car['car_gearbox'] == 'man' ? 'selected="selected"' : '',
        'CARBASE_AUTO' => $car['car_gearbox'] == 'auto' ? 'selected="selected"' : '',
        'CARBASE_GEARS' => $grs,
        'CARBASE_DESCRIPTION' => sed_cc($car['car_descr']),
		'CARBASE_URL' => sed_url('plug', 'e=carbase&id='.$id)
        ));
		$sql = sed_sql_query("SELECT * FROM sed_cars_photos WHERE ph_car = $id ORDER BY ph_id");
        $num = sed_sql_numrows($sql);
        while($row = sed_sql_fetcharray($sql))
        {
            $t->assign(array(
            'CARBASE_PHOTO_URL' => $cfg['plugins_dir'] . '/carbase/photos/'.$row['ph_id'].'.'.$row['ph_ext'],
            'CARBASE_PHOTO_THUMB' => cb_photo_thumb($cfg['plugins_dir'] . '/carbase/photos/'.$row['ph_id'].'.'.$row['ph_ext']),
			'CARBASE_PHOTO_WIDTH' => $row['ph_width'],
			'CARBASE_PHOTO_HEIGHT' => $row['ph_height'],
			'CARBASE_PHOTO_SIZE' => $row['ph_size'],
			'CARBASE_PHOTO_NAME' => '<input type="text" name="ph_name_'.$row['ph_id'].'" value="'.$row['ph_name'].'" />',
			'CARBASE_PHOTO_DESCR' => '<textarea name="ph_com_'.$row['ph_id'].'" rows="3" cols="40">'.$row['ph_com'].'</textarea>'
            ));
            $t->assign('CARBASE_PHOTO_DEL', '[<a href="'.sed_url('plug', 'e=carbase&id='.$id.'&pid='.$row['ph_id']).'">x</a>]');
            $t->parse('MAIN.CARBASE_PHOTOS_ROW');
        }
    }
    elseif($act == 'del' && $isadmin)
    {
        // Remove a car
        // Remove all photos first
        $sql = sed_sql_query("SELECT ph_id FROM sed_cars_photos WHERE ph_car = $id");
        while($row = sed_sql_fetcharray($sql))
        cb_photo_remove($row['ph_id']);
        sed_sql_query("DELETE FROM sed_cars_owned WHERE car_id = $id");
        header('Location: ' . SED_ABSOLUTE_URL . sed_url('users', 'm=details&id='.$usr['id'], '', TRUE));
        exit;
    }
    else
    {
        if($ph_id > 0 && $isadmin)
        {
            // Remove a photo
            cb_photo_remove($ph_id);
            
        }
        // Display car details
        $t = new XTemplate(sed_skinfile('carbase.car', TRUE));
        $urr = sed_sql_fetcharray(sed_sql_query("SELECT user_name FROM sed_users WHERE user_id = {$car['car_owner']}"));
		$mdl = sed_sql_fetcharray(sed_sql_query("SELECT mod_name FROM sed_cars_models WHERE mod_id = {$car['car_model']}"));
		$eng = sed_sql_fetcharray(sed_sql_query("SELECT eng_name FROM sed_cars_engines WHERE eng_id = {$car['car_engine']}"));
        $t->assign(array(
        'CARBASE_ID' => $id,
    	'CARBASE_MODEL' => sed_cc($mdl['mod_name']),
        'CARBASE_OWNER' => sed_build_user($car['car_owner'], $urr['user_name']),
    	'CARBASE_BUILT' => $car['car_built'],
    	'CARBASE_BOUGHT' => $car['car_bought'],
    	'CARBASE_ENGINE' => sed_cc($eng['eng_name']),
    	'CARBASE_FUEL' => $car['car_fuel'] == 'gasoline' ? $L['Gasoline'] : $L['Diesel'],
        'CARBASE_CYLINDERS' => $car['car_cylinders'],
        'CARBASE_VOLUME' => empty($car['car_volume']) ? '' : $car['car_volume'].' '.$L['ccm'],
        'CARBASE_VALVES' => empty($car['car_valves']) ? '' : $car['car_valves'].' '.$L['Valves'],
        'CARBASE_POWER' => $car['car_power'],
        'CARBASE_POWER_PS' => round($car['car_power']*1.36),
        'CARBASE_RPM' => empty($car['car_rpm']) ? '' : $L['at'].' '.$car['car_rpm'].' '.$L['RPM'],
        'CARBASE_GEARBOX' => $car['car_gearbox'] == 'man' ? $L['Manual'] : $L['Automatic'],
        'CARBASE_GEARS' => $car['car_gears'],
        'CARBASE_DESCRIPTION' => sed_cc($car['car_descr']),
        'CARBASE_ADDED' => $car['car_added'],
        'CARBASE_VIEWS' => $car['car_views'] + 1
        ));
        $sql = sed_sql_query("SELECT * FROM sed_cars_photos WHERE ph_car = $id ORDER BY ph_id");
        $num = sed_sql_numrows($sql);
        $i = 1;
        while($row = sed_sql_fetcharray($sql))
        {
            $t->assign(array(
            'CARBASE_PHOTO_SPAN' => $i == $num  ? $cfg['plugin']['carbase']['phrow'] - ($i % $cfg['plugin']['carbase']['phrow']) + 1 : 1,
            'CARBASE_PHOTO_URL' => $cfg['plugins_dir'] . '/carbase/photos/'.$row['ph_id'].'.'.$row['ph_ext'],
            'CARBASE_PHOTO_THUMB' => cb_photo_thumb($cfg['plugins_dir'] . '/carbase/photos/'.$row['ph_id'].'.'.$row['ph_ext']),
			'CARBASE_PHOTO_WIDTH' => $row['ph_width'],
			'CARBASE_PHOTO_HEIGHT' => $row['ph_height'],
			'CARBASE_PHOTO_SIZE' => $row['ph_size'],
			'CARBASE_PHOTO_NAME' => $row['ph_name'],
			'CARBASE_PHOTO_DESCR' => $row['ph_com']
            ));
            if($isadmin) $t->assign('CARBASE_PHOTO_DEL', '[<a href="'.sed_url('plug', 'e=carbase&id='.$id.'&pid='.$row['ph_id']).'">x</a>]');
            //$t->parse('MAIN.CARBASE_PHOTOS_ROW.CARBASE_PHOTOS_CELL');
            //if($i == $num || $i % $cfg['plugin']['carbase']['phrow'] == 0)
            $t->parse('MAIN.CARBASE_PHOTOS_ROW');
            $i++;
        }
        if($isadmin)
		{
			$t->assign(array(
			'CARBASE_EDIT_URL' => sed_url('plug', 'e=carbase&id='.$id.'&act=upd'),
			'CARBASE_DELETE_URL' => sed_url('plug', 'e=carbase&id='.$id.'&act=del')
			));
			$t->parse('MAIN.CARBASE_ADMIN');
		}
        if($cfg['plugin']['carbase']['views']) sed_sql_query("UPDATE sed_cars_owned SET car_views = car_views + 1 WHERE car_id = $id");
    }
}
elseif($act == 'add')
{
    // Add new car
    if($upd)
    {
        $car['car_owner'] = $usr['id'];
        $car['car_model'] = sed_import('model', 'P', 'INT');
        $car['car_built'] = sed_import('built', 'P', 'INT');
        $car['car_bought'] = sed_import('bought', 'P', 'INT');
        $car['car_engine'] = sed_import('engine', 'P', 'INT');
        $car['car_fuel'] = "'".sed_import('fuel', 'P', 'ALP')."'";
        $car['car_cylinders'] = sed_import('cylinders', 'P', 'INT');
        $car['car_volume'] = sed_import('volume', 'P', 'INT');
        $car['car_valves'] = sed_import('valves', 'P', 'INT');
        $car['car_power'] = sed_import('power', 'P', 'INT');
        $car['car_rpm'] = sed_import('rpm', 'P', 'INT');
        $car['car_gearbox'] = "'".sed_import('gearbox', 'P', 'ALP')."'";
        $car['car_gears'] = sed_import('gears', 'P', 'INT');
        $car['car_descr'] = "'".sed_sql_prep(sed_import('descr', 'P', 'STX'))."'";
        $car['car_added'] = 'NOW()';
        if(!empty($car['car_model']))
        {
            $sql_keys = '';
            $sql_vals = '';
            foreach($car as $key => $val)
            if(!empty($val)){
            	$sql_keys .= "$key,";
            	$sql_vals .= "$val,";
            }
            $sql_keys = mb_substr($sql_keys, 0, -1);
            $sql_vals = mb_substr($sql_vals, 0, -1);
            sed_sql_query("INSERT INTO sed_cars_owned ($sql_keys) VALUES ($sql_vals)");
            $id = sed_sql_result(sed_sql_query('SELECT LAST_INSERT_ID()'), 0, 0);
            // Upload photo
            if($_FILES['photo']['size'] > 0)
			{
				$ph_name = sed_sql_prep(sed_import('photo_name', 'P', 'STX'));
				$ph_descr = sed_sql_prep(sed_import('photo_descr', 'P', 'STX'));
				cb_photo_add($id, 'photo', $ph_name, $ph_descr);
			}
            // Show added entry
            header('Location: ' . SED_ABSOLUTE_URL . sed_url('plug', "e=carbase&id=$id", '', TRUE));
            exit;
        }
    }
    $t = new XTemplate(sed_skinfile('carbase.edit', TRUE));
    $cyl = '';
    foreach(array(2, 3, 4, 5, 6, 8) as $n)
        $cyl .= $n == $car['car_cylinders'] ? '<option value="'.$n.'" selected="selected">'.$n.'</option>' : '<option value="'.$n.'">'.$n.'</option>';
    $grs = '';
    foreach(array(4, 5, 6, 7) as $n)
        $grs .= $n == $car['car_gears'] ? '<option value="'.$n.'" selected="selected">'.$n.'</option>' : '<option value="'.$n.'">'.$n.'</option>';
	$vlv = '';
	foreach(array(6, 8, 12, 16, 20, 24, 30, 32) as $n)
        $vlv .= $n == $car['car_valves'] ? '<option value="'.$n.'" selected="selected">'.$n.'</option>' : '<option value="'.$n.'">'.$n.'</option>';
	$date = getdate();
	$car['car_built'] = $date['year'];
	$car['car_bought'] = $date['year'];
	$blt = '';
	for($n = 1920; $n <= $date['year']; $n++)
        $blt .= $n == $car['car_built'] ? '<option value="'.$n.'" selected="selected">'.$n.'</option>' : '<option value="'.$n.'">'.$n.'</option>';
	$bgt = '';
	for($n = 1920; $n <= $date['year']; $n++)
        $bgt .= $n == $car['car_bought'] ? '<option value="'.$n.'" selected="selected">'.$n.'</option>' : '<option value="'.$n.'">'.$n.'</option>';
    $t->assign(array(
    'CARBASE_ACTION' => sed_url('plug', 'e=carbase&act=add'),
    'CARBASE_MODEL' => cb_rowset2selectbox('model', sed_sql_query('SELECT * FROM sed_cars_models ORDER BY mod_name'), 'mod_id', 'mod_name', $car['car_model']),
    'CARBASE_BUILT' => $blt,
    'CARBASE_BOUGHT' => $bgt,
    'CARBASE_ENGINE' => cb_rowset2selectbox('engine', sed_sql_query('SELECT * FROM sed_cars_engines ORDER BY eng_name'), 'eng_id', 'eng_name', $car['car_engine']),
    'CARBASE_GASOLINE' => '',
    'CARBASE_DIESEL' => '',
    'CARBASE_CYLINDERS' => $cyl,
    'CARBASE_VOLUME' => '',
    'CARBASE_VALVES' => $vlv,
    'CARBASE_POWER' => '',
    'CARBASE_RPM' => '',
    'CARBASE_MANUAL' => '',
    'CARBASE_AUTO' => '',
    'CARBASE_GEARS' => $grs,
    'CARBASE_DESCRIPTION' => ''
    ));
}
elseif($uid > 0)
{
    // TODO Display all cars owned by user?
}
else
{
    // TODO List all cars?
}

?>