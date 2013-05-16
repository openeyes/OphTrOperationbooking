<?php

class m130515_100455_point_operation_at_latest_booking extends CDbMigration
{
	public function up()
	{
		/*
		$this->addColumn('et_ophtroperationbooking_operation','latest_booking_id','int(10) unsigned NULL');
		$this->createIndex('et_ophtroperationbooking_operation_latest_booking_id_fk','et_ophtroperationbooking_operation','latest_booking_id');
		$this->addForeignKey('et_ophtroperationbooking_operation_latest_booking_id_fk','et_ophtroperationbooking_operation','latest_booking_id','ophtroperationbooking_operation_booking','id');
*/
		foreach (Yii::app()->db->createCommand()->select("*")->from("et_ophtroperationbooking_operation")->queryAll() as $eo) {
			if ($booking = Yii::app()->db->createCommand()->select("*")->from("ophtroperationbooking_operation_booking")->where("element_id = :elementId",array(':elementId'=>$eo['id']))->order('id desc')->queryRow()) {
				$this->update('et_ophtroperationbooking_operation',array('latest_booking_id'=>$booking['id']),"id={$eo['id']}");
			}
		}
	}

	public function down()
	{
		$this->dropForeignKey('et_ophtroperationbooking_operation_latest_booking_id_fk','et_ophtroperationbooking_operation');
		$this->dropIndex('et_ophtroperationbooking_operation_latest_booking_id_fk','et_ophtroperationbooking_operation');
		$this->dropColumn('et_ophtroperationbooking_operation','latest_booking_id');
	}
}
