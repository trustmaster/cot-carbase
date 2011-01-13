<?php
/**
 * Common functions and routines
 * @author Trustmaster
 * @copyright Vladimir Sibirov 2008
 */

define('PLUGIN_NAME', 'carbase');

//=============================================================================
// Admin routines
//=============================================================================

/**
 * Builds a complete admin section
 *
 * @param array $params DB table fields
 * @param string $prefix Column name prefix
 * @param string $order Record order SQL
 */
function cb_admin($params, $prefix, $order)
{
	global $mod, $act, $plugin_body, $L;
	$id = sed_import('id', 'G', 'INT');
	if($act == 'add')
	{
		$sql_keys = '';
		$sql_vals = '';
		foreach($params as $key => $val)
		if(!$val['pkey'] && $val['type'] != 'FILE') {
			$sql_keys .= $prefix.'_'.$key.',';
			${$prefix.'_'.$key} = sed_import($prefix.'_'.$key, 'P', $val['type']);
			$sql_vals .= $val['type'] == 'INT' || $val['type'] == 'BOL' ? ((int)${$prefix.'_'.$key}).',' : "'".sed_sql_prep(${$prefix.'_'.$key})."'," ;
		}
		$sql_keys = mb_substr($sql_keys, 0, mb_strlen($sql_keys) - 1);
		$sql_vals = mb_substr($sql_vals, 0, mb_strlen($sql_vals) - 1);
		sed_sql_query("INSERT INTO sed_cars_$mod ($sql_keys) VALUES ($sql_vals)");
	}
	elseif($act == 'upd' && $id > 0)
	{
		$sql_upd = '';
		foreach($params as $key => $val)
		if(!$val['pkey'] && $val['type'] != 'FILE') {
			$sql_upd .= $prefix.'_'.$key.'=';
			${$prefix.'_'.$key} = sed_import($prefix.'_'.$key, 'P', $val['type']);
			$sql_upd .= $val['type'] == 'INT' || $val['type'] == 'BOL' ? ((int)${$prefix.'_'.$key}).',' : "'".sed_sql_prep(${$prefix.'_'.$key})."'," ;
		}
		$sql_upd = mb_substr($sql_upd, 0, mb_strlen($sql_upd) - 1);
		sed_sql_query("UPDATE sed_cars_$mod SET $sql_upd WHERE {$prefix}_id = $id");
	}
	elseif($act == 'del' && $id > 0)
	{
		sed_sql_query("DELETE FROM sed_cars_$mod WHERE {$prefix}_id = $id");
	}
	// Display
	$plugin_body .= '<table class="cells"><tr>';
	foreach($params as $key => $val)
	{
		if(!$val['pkey'])
			$plugin_body .= '<td class="coltop">'.$val['lang'].'</td>';
	}
	$plugin_body .= '<td class="coltop">'.$L['Update'].'</td><td class="coltop">'.$L['Delete'].'</td></tr>';
	$sql = sed_sql_query("SELECT * FROM sed_cars_$mod ORDER BY $order");
	while($row = sed_sql_fetcharray($sql))
	{
		$plugin_body .= '<form action="admin.php?m=tools&p=carbase&mod='.$mod.'&act=upd&id='.$row[$prefix.'_id'].'" method="post" enctype="multipart/form-data"><tr>';
		foreach($params as $key => $val)
		if(!$val['pkey']) {
			$plugin_body .= '<td>';
			if($val['dict'])
			{
				if($val['dict']['sql'])
				{
					$sqld = sed_sql_query($val['dict']['sql']);
					$plugin_body .= cb_rowset2selectbox($prefix.'_'.$key, $sqld, $val['dict']['key'], $val['dict']['disp'], $row[$prefix.'_'.$key]);
					sed_sql_freeresult($sqld);
				}
				else
				{
					$plugin_body .= cb_array2selectbox($prefix.'_'.$key, $val['dict'],  $row[$prefix.'_'.$key]);
				}
			}
			elseif($val['type'] == 'BOL')
			{
				$checked =  $row[$prefix.'_'.$key] ? ' checked="checked"' : '';
				$plugin_body .= '<input type="checkbox" name="'.$prefix.'_'.$key.'"'.$checked.' />';
			}
			elseif($val['type'] == 'FILE')
			{
				$plugin_body .= '<input type="file" name="'.$prefix.'_'.$key.'" /> <img src="plugins/carbase/photos/'.$row[$prefix.'_'.$key].'" alt="" />';
			}
			else
			{
				$plugin_body .= '<input type="text" name="'.$prefix.'_'.$key.'" value="'.sed_cc($row[$prefix.'_'.$key]).'" />';
			}
			$plugin_body .= '</td>';
		}
		$plugin_body .= '<td><input type="submit" value="'.$L['Update'].'" /></td>';
		$plugin_body .= '<td><a href="admin.php?m=tools&p=carbase&mod='.$mod.'&act=del&id='.$row[$prefix.'_id'].'" onclick="return confirm(\'Are you sure?\')">'.$L['Delete'].'</a></td>';
		$plugin_body .= '</tr></form>';
	}
	sed_sql_freeresult($sql);
	$plugin_body .= '</table>';
	$plugin_body .= '<h4>'.$L['Add'].'</h4>';
	$plugin_body .= '<form action="admin.php?m=tools&p=carbase&mod='.$mod.'&act=add" method="post">';
	foreach($params as $key => $val)
	if(!$val['pkey'] && $val['type'] != 'FILE') {
		$plugin_body .= $val['lang'].': ';
		if($val['dict'])
		{
			if($val['dict']['sql'])
			{
				$sqld = sed_sql_query($val['dict']['sql']);
				$plugin_body .= cb_rowset2selectbox($prefix.'_'.$key, $sqld, $val['dict']['key'], $val['dict']['disp']);
				sed_sql_freeresult($sqld);
			}
			else
			{
				$plugin_body .= cb_array2selectbox($prefix.'_'.$key, $val['dict']);
			}
		}
		elseif($val['type'] == 'BOL')
		{
			$plugin_body .= '<input type="checkbox" name="'.$prefix.'_'.$key.'" />';
		}
		else
		{
			$plugin_body .= '<input type="text" name="'.$prefix.'_'.$key.'" />';
		}
		$plugin_body .= ' ';
	}
	$plugin_body .= '<input type="submit" value="'.$L['Add'].'" /></form>';
	$plugin_body .= '<br /><a href="admin.php?m=tools&p=carbase&mod='.$mod.'">Back</a>';
	$plugin_body .= '<br /><a href="admin.php?m=tools&p=carbase">'.$L['Main'].'</a>';
}

//=========================================================================
// LAYOUT
//=========================================================================

/**
 * Turns SQL rowset into array for use in dropdowns
 *
 * @param resource $rowset SQL result rowset
 * @param string $key_field Key filed name
 * @param string $display_field Displayed field name
 * @return array
 */
function cb_rowset2array($rowset, $key_field, $display_field)
{
	$res = array();
	while($row = sed_sql_fetcharray($rowset))
		$res[$row[$key_field]] = $row[$display_field];
	return $res;
}

/**
 * Build dropdown input based on resultset from other table
 *
 * @param string $sb_name Select input name
 * @param resource $rowset SQL result rowset
 * @param string $key_field Key filed name
 * @param string $display_field Displayed field name
 * @param mixed $selected Selected key
 * @param bool $empty Add empty value as default
 * @return string
 */
function cb_rowset2selectbox($sb_name, $rowset, $key_field, $display_field, $selected = 0, $empty = false)
{
	$res = '<select name="'.$sb_name.'">';
	if($empty) $res .= empty($selected) || $selected == 0 ? '<option value="" selected="selected">--</option>' : '<option value="" selected="selected">--</option>';
	while($row = sed_sql_fetcharray($rowset))
	{
		$sel = $row[$key_field] == $selected ? ' selected="selected"' : '';
		$res .= '<option value="'.$row[$key_field].'"'.$sel.'>'.sed_cc($row[$display_field]).'</option>';
	}
	$res .= '</select>';
	return $res;
}

/**
 * Build dropdown out of array
 *
 * @param string $sb_name Select input name
 * @param array $arr Data array
 * @param mixed $selected Selected key
 * @return string
 */
function cb_array2selectbox($sb_name, $arr, $selected = 0)
{
	$res = '<select name="'.$sb_name.'">';
	foreach($arr as $key => $val)
	{
		$sel = $key == $selected ? ' selected="selected"' : '';
		$res .= '<option value="'.$key.'"'.$sel.'>'.sed_cc($val).'</option>';
	}
	$res .= '</select>';
	return $res;
}

//=========================================================================
// PHOTO MANIPULATION
//=========================================================================

/**
 * Uploads a photo if correct
 *
 * @param int $item_id Item ID
 * @param string $var_name Param name
 * @param string $name Photo title
 * @param string $descr Photo description
 * @return bool
 */
function cb_photo_add($item_id, $var_name, $name, $descr)
{
	global $img_width, $img_height, $cfg;
	$extp = @strrpos($_FILES[$var_name]['name'], '.') + 1;
	$ext = strtolower(@substr($_FILES[$var_name]['name'], $extp, strlen($_FILES[$var_name]['name']) - $extp));
	$img = (int) in_array($ext, array('gif', 'jpg', 'jpeg', 'png'));
	if($img)
	{
	    sed_sql_query("INSERT INTO sed_cars_photos (ph_car, ph_ext, ph_name, ph_com) VALUES ($item_id, '$ext', '$name', '$descr')");
	    $ph_id = sed_sql_result(sed_sql_query('SELECT LAST_INSERT_ID()'), 0, 0);
		$path = $cfg['plugins_dir'] . '/'.PLUGIN_NAME.'/photos/'.$ph_id.'.'.$ext;
		move_uploaded_file($_FILES[$var_name]['tmp_name'], $path);
		if(cb_photo_makethumb($path))
		{
			$size = round(filesize($path)/1024);
			sed_sql_query("UPDATE sed_cars_photos SET ph_width = $img_width, ph_height = $img_height, ph_size = $size WHERE ph_id = $ph_id");
			return true;
		}
		else
		{
		    sed_sql_query("DELETE FROM sed_cars_photos WHERE ph_id = $ph_id");
			@unlink($path);
			return false;
		}
	}
	return false;
}

/**
 * Removes a photo
 * 
 * @param int $id Photo ID
 * @return bool
 */
function cb_photo_remove($id)
{
	global $cfg;
    $ext = @sed_sql_result(sed_sql_query("SELECT ph_ext FROM sed_cars_photos WHERE ph_id = $id"), 0, 0);
	$path = $cfg['plugins_dir'] . '/'.PLUGIN_NAME.'/photos/'.$id.'.'.$ext;
	if(file_exists($path))
	{
	    sed_sql_query("DELETE FROM sed_cars_photos WHERE ph_id = $id");
		@unlink(cb_photo_thumb($path));
		@unlink($path);
		return true;
	}
	return false;
}

// A slightly modified sed_createthumb to track invalid images
function cb_createthumb($img_big, $img_small, $small_x, $small_y, $keepratio, $extension, $filen, $fsize, $textcolor, $textsize, $bgcolor, $bordersize, $jpegquality, $dim_priority="Width")
{
	global $img_width, $img_height;
	if (!function_exists('gd_info'))
	{ return false; }
	global $cfg;
	$gd_supported = array('jpg', 'jpeg', 'png', 'gif');
	switch($extension)
	{
		case 'gif':
			$source = imagecreatefromgif($img_big);
			break;
		case 'png':
			$source = imagecreatefrompng($img_big);
			break;
		default:
			$source = imagecreatefromjpeg($img_big);
			break;
	}	
	if(!$source) return false;
	
	$big_x = imagesx($source);
	$big_y = imagesy($source);
	$img_width = $big_x;
	$img_height = $big_y;
	if (!$keepratio)
	{
		$thumb_x = $small_x;
		$thumb_y = $small_y;
	}
	elseif ($dim_priority=="Width")
	{
		$thumb_x = $small_x;
		$thumb_y = floor($big_y * ($small_x / $big_x));
	}
	else
	{
		$thumb_x = floor($big_x * ($small_y / $big_y));
		$thumb_y = $small_y;
	}
	if ($textsize==0)
	{
		if ($cfg['th_amode']=='GD1')
		{ $new = imagecreate($thumb_x+$bordersize*2, $thumb_y+$bordersize*2); }
		else
		{ $new = imagecreatetruecolor($thumb_x+$bordersize*2, $thumb_y+$bordersize*2); }
		$background_color = imagecolorallocate ($new, $bgcolor[0], $bgcolor[1] ,$bgcolor[2]);
		imagefilledrectangle ($new, 0,0, $thumb_x+$bordersize*2, $thumb_y+$bordersize*2, $background_color);
		if ($cfg['th_amode']=='GD1')
		{ imagecopyresized($new, $source, $bordersize, $bordersize, 0, 0, $thumb_x, $thumb_y, $big_x, $big_y); }
		else
		{ imagecopyresampled($new, $source, $bordersize, $bordersize, 0, 0, $thumb_x, $thumb_y, $big_x, $big_y); }
	}
	else
	{
		if ($cfg['th_amode']=='GD1')
		{ $new = imagecreate($thumb_x+$bordersize*2, $thumb_y+$bordersize*2+$textsize*3.5+6); }
		else
		{ $new = imagecreatetruecolor($thumb_x+$bordersize*2, $thumb_y+$bordersize*2+$textsize*3.5+6); }
		$background_color = imagecolorallocate($new, $bgcolor[0], $bgcolor[1] ,$bgcolor[2]);
		imagefilledrectangle ($new, 0,0, $thumb_x+$bordersize*2, $thumb_y+$bordersize*2+$textsize*4+14, $background_color);
		$text_color = imagecolorallocate($new, $textcolor[0],$textcolor[1],$textcolor[2]);
		if ($cfg['th_amode']=='GD1')
		{ imagecopyresized($new, $source, $bordersize, $bordersize, 0, 0, $thumb_x, $thumb_y, $big_x, $big_y); }
		else
		{ imagecopyresampled($new, $source, $bordersize, $bordersize, 0, 0, $thumb_x, $thumb_y, $big_x, $big_y); }
		imagestring ($new, $textsize, $bordersize, $thumb_y+$bordersize+$textsize+1, $big_x."x".$big_y." ".$fsize."kb", $text_color);
	}
	switch($extension)
	{
		case 'gif':
			imagegif($new, $img_small);
			break;
		case 'png':
			imagepng($new, $img_small);
			break;
		default:
			imagejpeg($new, $img_small, $jpegquality);
			break;
	}
	imagedestroy($new);
	imagedestroy($source);
	return true;
}

/**
 * Adds an image thumbnail
 *
 * @param string $path Source image path
 * @return bool
 */
function cb_photo_makethumb($path)
{
	global $cfg;
	$extp = strrpos($path, '.');
	$len = strlen($path);
	$ext = strtolower(substr($path, $extp + 1, $len - $extp - 1));
	$fname = substr($path, strrpos($path, '/') + 1, $len - strrpos($path, '/') - $extp + 1);
	$thumb_path = substr($path, 0, $extp).'.thumb.'.$ext;
	@unlink($thumb_path);
	$th_colortext = array(hexdec(substr($cfg['th_colortext'],0,2)), hexdec(substr($cfg['th_colortext'],2,2)), hexdec(substr($cfg['th_colortext'],4,2)));
	$th_colorbg = array(hexdec(substr($cfg['th_colorbg'],0,2)), hexdec(substr($cfg['th_colorbg'],2,2)), hexdec(substr($cfg['th_colorbg'],4,2)));
	if(cb_createthumb($path, $thumb_path, $cfg['plugin'][PLUGIN_NAME]['th_x'], $cfg['plugin'][PLUGIN_NAME]['th_y'], true, $ext, $fname, round(filesize($path)/1024), $th_colortext, 0, $th_colorbg, 0, $cfg['th_jpeg_quality'], $cfg['th_dimpriority']))
		return true;
	else
		return false;
}

/**
 * Returns image thumbnail path
 *
 * @param string $path Original image
 * @return string
 */
function cb_photo_thumb($path)
{
	$extp = strrpos($path, '.');
	$len = strlen($path);
	$ext = strtolower(substr($path, $extp + 1, $len - $extp - 1));
	return substr($path, 0, $extp).'.thumb.'.$ext;
}
?>