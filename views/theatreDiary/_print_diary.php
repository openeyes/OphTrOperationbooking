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
<?php
foreach ($diary as $site_name => $theatres) {
	foreach ($theatres as $theatre_name => $dates) {?>
		<h3 class="theatre"><strong><?php echo $theatre_name?> (<?php echo $site_name?>)</strong></h3>
		<?php foreach ($dates as $date => $sessions) {
			foreach ($sessions as $session_id => $session) {?>
				<div id="diaryTemplate">
					<div id="d_title">OPERATION LIST FORM</div>
					<table class="d_overview">
						<tbody>
							<tr>
								<td>THEATRE NO:</td>
								<td colspan="2"><?php echo(htmlspecialchars($theatre_name, ENT_QUOTES))?></td>
							</tr>
							<tr>
								<td>SESSION:</td>
								<td><?php echo $session['start_time']?> - <?php echo $session['end_time']?></td>
								<td>NHS</td>
							</tr>
						</tbody>
					</table>
					<table class="d_overview">
						<tbody>
							<tr>
								<td>SURGICAL FIRM:<?php echo htmlspecialchars($session['firm_name'], ENT_QUOTES)?></td>
								<td>ANAESTHETIST:</td>
								<td>&nbsp;</td>
								<td>DATE:</td>
								<td><?php echo Helper::convertDate2NHS($date)?></td>
							</tr>
							<tr>
								<td>COMMENTS: <?php echo htmlspecialchars($session['comments'])?></td>
							</tr>
						</tbody>
					</table>
					<table class="d_data">
						<tbody>
							<tr>
								<th>HOSPT NO</th>
								<th>PATIENT</th>
								<th>AGE</th>
								<th>WARD</th>
								<th>GA or LA</th>
								<th>PRIORITY</th>
								<th>PROCEDURES AND COMMENTS</th>
								<th>ADMISSION TIME</th>
							</tr>
							<?php foreach ($session['bookings'] as $booking) {?>
								<tr>
									<td><?php echo $booking['hos_num']?></td>
									<td><?php echo $booking['patient']?></td>
									<td><?php echo $booking['age']?></td>
									<td><?php echo htmlspecialchars($booking['ward'])?></td>
									<td><?php echo htmlspecialchars($booking['anaesthetic_type'])?></td>
									<td><?php echo $booking['priority']?></td>
									<td style="max-width: 500px; word-wrap:break-word; overflow: hidden;">
									<?php echo !empty($booking['procedures']) ? '['.$booking['eye'].'] '.htmlspecialchars($booking['procedures']) : 'No procedures'?><br />
									<?php echo htmlspecialchars($booking['comments'])?>
									<td><?php echo $booking['admission_time']?></td>
								</tr>
							<?php }?>
						</tbody>
					</table>
				</div>
				<div style="page-break-after:always"></div>
			<?php }
		}
	}
}
