<!-- BEGIN: MAIN -->
<h4>{PHP.L.Car_description}</h4>
<table class="cells">
<tr>
<td class="coltop">{PHP.L.Model}</td>
<td class="coltop">{CARBASE_MODEL}</td>
</tr>
<tr>
<td><strong>{PHP.L.Owner}</strong></td>
<td>{CARBASE_OWNER}</td>
</tr>
<tr>
<td><strong>{PHP.L.Built}</strong></td>
<td>{CARBASE_BUILT}</td>
</tr>
<tr>
<td><strong>{PHP.L.Bought}</strong></td>
<td>{CARBASE_BOUGHT}</td>
</tr>
<tr>
<td><strong>{PHP.L.Engine}</strong></td>
<td>{CARBASE_ENGINE} {CARBASE_FUEL} {CARBASE_CYLINDERS}-{PHP.L.cylinder}
{CARBASE_VOLUME} {CARBASE_VALVES}</td>
</tr>
<tr>
<td><strong>{PHP.L.Performance}</strong></td>
<td>{CARBASE_POWER} {PHP.L.kW} ({CARBASE_POWER_PS} {PHP.L.PS}) {CARBASE_RPM}</td>
</tr>
<tr>
<td><strong>{PHP.L.Transmission}</strong></td>
<td>{CARBASE_GEARS}x {CARBASE_GEARBOX}</td>
</tr>
<tr>
<td><strong>{PHP.L.Description}</strong></td>
<td>{CARBASE_DESCRIPTION}</td>
</tr>
<tr>
<td><strong>{PHP.L.Added}</strong></td>
<td>{CARBASE_ADDED}</td>
</tr>
<tr>
<td><strong>{PHP.L.Views}</strong></td>
<td>{CARBASE_VIEWS}</td>
</tr>
</table>

<h4>{PHP.L.Photos}</h4>
<table>
<!-- BEGIN: CARBASE_PHOTOS_ROW -->
<tr>
<td><a href="{CARBASE_PHOTO_URL}"><img src="{CARBASE_PHOTO_THUMB}" alt="" /></a></td>
<td><h4>{CARBASE_PHOTO_NAME}</h4>
{CARBASE_PHOTO_DESCR}<br />
<em>{CARBASE_PHOTO_WIDTH}x{CARBASE_PHOTO_HEIGHT} {CARBASE_PHOTO_SIZE}KB</em> {CARBASE_PHOTO_DEL}</td>
</tr>
<!-- END: CARBASE_PHOTOS_ROW -->
</table>
<!-- BEGIN: CARBASE_ADMIN -->
<a href="{CARBASE_EDIT_URL}">{PHP.L.Edit}</a>
<a href="{CARBASE_DELETE_URL}" onclick="return confirm('{PHP.L.Delete_car}')">{PHP.L.Delete}</a>
<!-- END: CARBASE_ADMIN -->
<!-- END: MAIN -->