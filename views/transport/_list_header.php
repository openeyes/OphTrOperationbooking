<?php /* DEPRECATED */ ?>
<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<div class="transport_pagination"><span class="transport_pagination_back"> &laquo; back &nbsp; </span><span class="transport_pagination_selected">&nbsp;1 </span><span class="transport_pagination_next"> &nbsp; next &raquo;</span></div>
<div id="no_gp_warning" class="alertBox" style="display: none;">One or more patients has no GP practice, please correct in PAS before printing GP letter.</div>
<div id="transportList" class="grid-view-waitinglist">
	<table>
		<thead>
			<tr>
				<th>Hospital number</th>
				<th>Patient</th>
				<th>TCI date</th>
				<th>Admission time</th>
				<th>Site</th>
				<th>Ward</th>
				<th>Method</th>
				<th>Firm</th>
				<th>Subspecialty</th>
				<th>DTA</th>
				<th>Priority</th>
				<th><input style="margin-top: 0.4em;" type="checkbox" id="transport_checkall" value="" /></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="12">
					<img src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" class="loader" /> loading data ...
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div class="transport_pagination"><span class="transport_pagination_back"> &laquo; back &nbsp; </span><span class="transport_pagination_selected">&nbsp;1 </span><span class="transport_pagination_next"> &nbsp; next &raquo;</span></div>
<script type="text/javascript">
	$(document).ready(function() { transport_load_tcis(); });
</script>
