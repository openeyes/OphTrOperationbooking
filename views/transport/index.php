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
?>
<h2>Transport</h2>

<div class="fullWidth fullBox clearfix">
	<div id="waitinglist_display">
		<h3>TCIs for today onwards.</h3>
		<button type="submit" class="classy blue venti btn_transport_download" style="margin-right: 10px; margin-top: 20px; margin-bottom: 20px; float: right; z-index: 1"><span class="button-span button-span-blue">Download CSV</span></button>
		<button type="submit" class="classy blue tall btn_transport_print" style="margin-right: 10px; margin-top: 20px; margin-bottom: 20px; float: right; z-index: 1"><span class="button-span button-span-blue">Print list</span></button>
		<button type="submit" class="classy blue tall btn_transport_confirm" style="margin-right: 10px; margin-top: 20px; margin-bottom: 20px; float: right; z-index: 1"><span class="button-span button-span-blue">Confirm</span></button>

		<div id="searchResults" class="whiteBox">
			<form id="transport_form" method="post" action="<?php echo Yii::app()->createUrl('/OphTrOperation/transport/index')?>">
				<label for="transport_date_from">
					From:
				</label>
				<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
					'name' => 'transport_date_from',
					'id' => 'transport_date_from',
					'options' => array(
						'showAnim'=>'fold',
						'dateFormat'=>Helper::NHS_DATE_FORMAT_JS
					),
					'value' => @$_REQUEST['date_from'],
					'htmlOptions' => array('style' => "width: 95px;"),
				))?>
				<label for="transport_date_to">
					To:
				</label>
				<?php $this->widget('zii.widgets.jui.CJuiDatePicker', array(
					'name' => 'transport_date_to',
					'id' => 'transport_date_to',
					'options' => array(
						'showAnim'=>'fold',
						'dateFormat'=>Helper::NHS_DATE_FORMAT_JS
					),
					'value' => @$_REQUEST['date_to'],
					'htmlOptions' => array('style' => "width: 95px;"),
				))?>
				<button type="submit" class="classy blue mini btn_transport_filter"><span class="button-span button-span-blue">Filter</span></button>
				<button type="submit" class="classy blue mini btn_transport_viewall"><span class="button-span button-span-blue">View all</span></button>
				<img src="<?php echo Yii::app()->createUrl('img/ajax-loader.gif')?>" class="loader" style="display: none;" />
				<div style="height: 0.4em;"></div>
				<label>
					Include:
				</label>
				&nbsp;
				<input type="checkbox" name="include_bookings" id="include_bookings" class="filter" value="1"<?php if (@$_REQUEST['include_bookings']){?> checked="checked"<?php }?> /> Bookings
				<input type="checkbox" name="include_reschedules" id="include_reschedules" class="filter" value="1"<?php if (@$_REQUEST['include_reschedules']){?> checked="checked"<?php }?> /> Reschedules
				<input type="checkbox" name="include_cancellations" id="include_cancellations" class="filter" value="1"<?php if (@$_REQUEST['include_cancellations']){?> checked="checked"<?php }?> /> Cancellations
			</form>
			<form id="csvform" method="post" action="<?php echo Yii::app()->createUrl('/OphTrOperation/transport/downloadcsv')?>">
				<input type="hidden" name="date_from" value="<?php echo @$_REQUEST['date_from']?>" />
				<input type="hidden" name="date_to" value="<?php echo @$_REQUEST['date_to']?>" />
				<input type="hidden" name="include_bookings" value="<?php echo (@$_REQUEST['include_bookings'] ? 1 : 0)?>" />
				<input type="hidden" name="include_reschedules" value="<?php echo (@$_REQUEST['include_reschedules'] ? 1 : 0)?>" />
				<input type="hidden" name="include_cancellations" value="<?php echo (@$_REQUEST['include_cancellations'] ? 1 : 0)?>" />
			</form>
			<?php echo $this->renderPartial('/transport/_pagination')?>
			<?php echo $this->renderPartial('/transport/_list',array('bookings' => $bookings))?>
			<?php echo $this->renderPartial('/transport/_pagination')?>
		</div>
		<button type="submit" class="classy blue venti btn_transport_download" style="margin-right: 10px; margin-top: 20px; margin-bottom: 20px; float: right;"><span class="button-span button-span-blue">Download CSV</span></button>
		<button type="submit" class="classy blue tall btn_transport_print" style="margin-right: 10px; margin-top: 20px; margin-bottom: 20px; float: right;"><span class="button-span button-span-blue">Print list</span></button>
		<button type="submit" class="classy blue tall btn_transport_confirm" style="margin-right: 10px; margin-top: 20px; margin-bottom: 20px; float: right;"><span class="button-span button-span-blue">Confirm</span></button>
	</div>
</div>
