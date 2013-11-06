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

<?php
$uri_append = $this->getUriAppend();
?>
<div class="transport_pagination">
	<span class="transport_pagination_back">
		<?php if ($this->page >1) {?>
			<?php echo CHtml::link('&laquo; back',Yii::app()->createUrl('/OphTrOperationbooking/transport/index?page='.($this->page-1).$uri_append),array('class'=>'pagination-link','rel'=>'back'))?>
		<?php } else {?>
			&laquo; back
		<?php }?>
	</span>
	&nbsp;
	<?php for ($i=1;$i<=$this->pages;$i++) {?>
		<?php if ($i == $this->page) {?>
			<span class="transport_pagination_selected">&nbsp;<?php echo $i?> </span>
		<?php } else {?>
			<?php echo CHtml::link($i,Yii::app()->createUrl('/OphTrOperationbooking/transport/index?page='.$i.$uri_append),array('class'=>'pagination-link','rel'=>$i)) ?>
		<?php }?>
		&nbsp;
	<?php }?>
	<span class="transport_pagination_next">
		<?php if ($this->page < $this->pages) {?>
			<?php echo CHtml::link('next &raquo;',Yii::app()->createUrl('/OphTrOperationbooking/transport/index?page='.($this->page+1).$uri_append),array('class'=>'pagination-link','rel'=>'next'))?>
		<?php } else {?>
			next &raquo;
		<?php }?>
	</span>
</div>
