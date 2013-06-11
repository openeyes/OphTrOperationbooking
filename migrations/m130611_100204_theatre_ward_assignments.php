<?php

class m130611_100204_theatre_ward_assignments extends CDbMigration
{
	public function up()
	{
		$this->addColumn('ophtroperationbooking_operation_theatre','ward_id','int(10) unsigned NULL');
		$this->createIndex('ophtroperationbooking_operation_theatre_ward_id_fk','ophtroperationbooking_operation_theatre','ward_id');
		$this->addForeignKey('ophtroperationbooking_operation_theatre_ward_id_fk','ophtroperationbooking_operation_theatre','ward_id','ophtroperationbooking_operation_ward','id');

		foreach (Yii::app()->db->createCommand()->select("*")->from("ophtroperationbooking_operation_ward")->where("theatre_id is not null")->queryAll() as $ward) {
			$this->update('ophtroperationbooking_operation_theatre',array('ward_id'=>$ward['id']),"id = {$ward['theatre_id']}");
		}

		$this->dropForeignKey('ophtroperationbooking_operation_ward_thi_fk','ophtroperationbooking_operation_ward');
		$this->dropIndex('ophtroperationbooking_operation_ward_thi_fk','ophtroperationbooking_operation_ward');
		$this->dropColumn('ophtroperationbooking_operation_ward','theatre_id');
	}

	public function down()
	{
		$this->addColumn('ophtroperationbooking_operation_ward','theatre_id','int(10) unsigned NULL');
		$this->createIndex('ophtroperationbooking_operation_ward_thi_fk','ophtroperationbooking_operation_ward','theatre_id');
		$this->addForeignKey('ophtroperationbooking_operation_ward_thi_fk','ophtroperationbooking_operation_ward','theatre_id','ophtroperationbooking_operation_theatre','id');

		foreach (Yii::app()->db->createCommand()->select("*")->from("ophtroperationbooking_operation_theatre")->where("ward_id is not null")->queryAll() as $theatre) {
			$this->update('ophtroperationbooking_operation_ward',array('theatre_id'=>$theatre['id']),"id = {$theatre['ward_id']}");
		}

		$this->dropForeignKey('ophtroperationbooking_operation_theatre_ward_id_fk','ophtroperationbooking_operation_theatre');
		$this->dropIndex('ophtroperationbooking_operation_theatre_ward_id_fk','ophtroperationbooking_operation_theatre');
		$this->dropColumn('ophtroperationbooking_operation_theatre','ward_id');
	}
}
