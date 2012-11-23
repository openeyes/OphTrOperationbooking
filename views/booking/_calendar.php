<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

$thisMonth = date('Y-m-d', $date);
$lastMonth = mktime(0,0,0, date('m', $date)-1, 1, date('Y', $date));
$nextMonth = mktime(0,0,0, date('m', $date)+1, 1, date('Y', $date));
$nextYear = mktime(0,0,0, date('m'), 1, date('Y')+1);
?>
<div id="dates" class="clearfix">
	<div id="current_month" class="column"><?php echo date('F Y', $date)?></div>
	<div class="column" id="month_back">
		<span class="button">
			<button type="submit" class="classy blue venti" name="yt1" id="previous_month"><?php echo CHtml::link('<span class="button-span button-span-blue">&#x25C0;&nbsp;&nbsp;previous month</span>',array('booking/'.($operation->booking?'re':'').'schedule/'.$operation->event_id.'?firm_id='.$firm->id.'&date='.date('Ym',$lastMonth)))?></button>
		</span>
	</div>
	<div class="column" id="month_forward">
		<?php if ($nextMonth > $nextYear) {
			echo '<button type="submit" class="classy blue inactive" name="yt1" id="next_month"><span class="button-span button-span-inactive">next month&nbsp;&nbsp;&#x25B6;</span></button>';
		} else {?>
			<span class="button">
				<button type="submit" class="classy blue venti" name="yt1" id="next_month"><?php echo CHtml::link('<span class="button-span button-span-blue">next month&nbsp;&nbsp;&#x25B6;</span>',array('booking/'.($operation->booking?'re':'').'schedule/'.$operation->event_id.'?firm_id='.$firm->id.'&date='.date('Ym',$nextMonth)))?></button>
			</span>
		<?php }?>
	</div>
</div>
<table id="calendar">
	<tbody>
		<?php
		foreach ($sessions as $weekday => $list) {?>
			<tr>
				<th><?php echo $weekday?></th>

				<?php foreach ($list as $date => $session) {?>
					<?php if ($session['status'] == 'blank') {?>
						<td></td>
					<?php }else{?>
						<td class="<?php echo $session['status']?><?php if ($date == $selectedDate) {?> selected_date<?php }?>">
							<?php echo date('j', strtotime($date))?>
						</td>
					<?php }?>
				<?php }?>
			</tr>
		<?php }?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="8">
				<div id="key">
				<span>Key:</span>
					<div class="container" id="day"><div class="color_box"></div><div class="label">Day of the week</div></div>
					<div class="container" id="available"><div class="color_box"></div><div class="label">Slots Available</div></div>
					<div class="container" id="limited"><div class="color_box"></div><div class="label">Limited Slots</div></div>
					<div class="container" id="full"><div class="color_box"></div><div class="label">Full</div></div>
					<div class="container" id="closed"><div class="color_box"></div><div class="label">Theatre Closed</div></div>
					<div class="container" id="selected_date"><div class="color_box"></div><div class="label">Selected Date</div></div>
					<div class="container" id="outside_rtt"><div class="color_box"></div><div class="label">Outside RTT</div></div>
				</div>
			</td>
		</tr>
	</tfoot>
</table>
<script type="text/javascript">
	$('#calendar table td').click(function() {
		var day = $(this).text().match(/[0-9]+/);
		if (day == null) return false;

		if (window.location.href.match(/day=/)) {
			var href = window.location.href.replace(/day=[0-9]+/,'day='+day);
		} else if (window.location.href.match(/\?/)) {
			var href = window.location.href + '&day='+day;
		} else {
			var href = window.location.href + '?day='+day;
		}
		href = href.replace(/(&|\?)session_id=[0-9]+/,'');
		window.location.href = href;
		return false;
	});
</script>
