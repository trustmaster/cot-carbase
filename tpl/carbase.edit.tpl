<!-- BEGIN: MAIN -->
<form action="{CARBASE_ACTION}" method="post" enctype="multipart/form-data">
<input type="hidden" name="upd" value="1" />
<h4>{PHP.L.Car_description}</h4>
<table class="cells">
<tr>
<td><strong>{PHP.L.Model}</strong></td>
<td>{CARBASE_MODEL}</td>
</tr>
<tr>
<td><strong>{PHP.L.Built}</strong></td>
<td><select name="built">{CARBASE_BUILT}</select></td>
</tr>
<tr>
<td><strong>{PHP.L.Bought}</strong></td>
<td><select name="bought">{CARBASE_BOUGHT}</select></td>
</tr>
<tr>
<td><strong>{PHP.L.Engine}</strong></td>
<td>{CARBASE_ENGINE}</td>
</tr>
<tr>
<td><strong>{PHP.L.Fuel}</strong></td>
<td><select name="fuel"><option value="gasoline" {CARBASE_GASOLINE}>{PHP.L.Gasoline}</option>
<option value="diesel" {CARBASE_DIESEL}>{PHP.L.Diesel}</option></select></td>
</tr>
<tr>
<td><strong>{PHP.L.Cylinders}</strong></td>
<td><select name="cylinders">{CARBASE_CYLINDERS}</select></td>
</tr>
<tr>
<td><strong>{PHP.L.Volume}</strong></td>
<td><input type="text" name="volume" size="4" value="{CARBASE_VOLUME}" /> {PHP.L.ccm}</td>
</tr>
<tr>
<td><strong>{PHP.L.Valves}</strong></td>
<td><select name="valves">{CARBASE_VALVES}</select></td>
</tr>
<tr>
<td><strong>{PHP.L.Performance}</strong></td>
<td><input type="text" name="power" value="{CARBASE_POWER}" /> {PHP.L.kW}</td>
</tr>
<tr>
<td><strong>{PHP.L.RPM}</strong></td>
<td><input type="text" name="rpm" value="{CARBASE_RPM}" /></td>
</tr>
<tr>
<td><strong>{PHP.L.Transmission}</strong></td>
<td><select name="gearbox"><option value="man" {CARBASE_MANUAL}>{PHP.L.Manual}</option>
<option value="auto" {CARBASE_AUTO}>{PHP.L.Automatic}</option></select></td>
</tr>
<tr>
<td><strong>{PHP.L.Gears}</strong></td>
<td><select name="gears">{CARBASE_GEARS}</select></td>
</tr>
<tr>
<td><strong>{PHP.L.Description}</strong></td>
<td><textarea name="descr" rows="4" cols="40">{CARBASE_DESCRIPTION}</textarea></td>
</tr>
</table>
<h4>{PHP.L.Photos}</h4>
<table>
<!-- BEGIN: CARBASE_PHOTOS_ROW -->
<tr>
<td><a href="{CARBASE_PHOTO_URL}"><img src="{CARBASE_PHOTO_THUMB}" alt="" /></a></td>
<td>{CARBASE_PHOTO_NAME}<br />
{CARBASE_PHOTO_DESCR}<br /> {CARBASE_PHOTO_DEL}</td>
</tr>
<!-- END: CARBASE_PHOTOS_ROW -->
<tr><td colspan="2"><h4>{PHP.L.Add}</h4></td></tr>
<tr>
<td><strong>{PHP.L.Name}</strong></td>
<td><input type="text" name="photo_name" /></td>
</tr>
<tr>
<td><strong>{PHP.L.Description}</strong></td>
<td><textarea name="photo_descr" rows="3" cols="40"></textarea></td>
</tr>
<tr>
<td><strong>{PHP.L.File}</strong></td>
<td><input type="file" name="photo" /></td>
</tr>
</table>
<input type="submit" />
</form>
<br />
<a href="{CARBASE_URL}">{PHP.L.Return}</a>
<!-- END: MAIN -->