<?php

class m130627_132947_database_settings extends CDbMigration
{
	public function up()
	{
		$config_key_id = Yii::app()->db->createCommand()->select("id")->from("config_key")->where("name=:name",array(":name"=>"menu"))->queryScalar();

		$this->insert('config',array(
			'config_key_id' => $config_key_id,
			'module_name' => 'OphTrOperationbooking',
			'value' => serialize(array(
				'theatre_diaries' => array(
					'title' => 'Theatre Diaries',
					'uri' => 'OphTrOperationbooking/theatreDiary/index',
					'position' => 10,
				),
				'partial_bookings' => array(
					'title' => 'Partial bookings waiting list',
					'uri' => 'OphTrOperationbooking/waitingList/index',
					'position' => 20,
				),
			)),
		));

		$config_key_id = Yii::app()->db->createCommand()->select("id")->from("config_key")->where("name=:name",array(":name"=>"admin_menu"))->queryScalar();

		$this->insert('config',array(
			'config_key_id' => $config_key_id,
			'module_name' => 'OphTrOperationbooking',
			'value' => serialize(array(
				'Sequences' => '/OphTrOperationbooking/admin/viewSequences',
				'Sessions' => '/OphTrOperationbooking/admin/viewSessions',
				'Wards' => '/OphTrOperationbooking/admin/viewWards',
				'Theatres' => '/OphTrOperationbooking/admin/viewTheatres',
				'Scheduling options' => '/OphTrOperationbooking/admin/viewSchedulingOptions',
				'EROD rules' => '/OphTrOperationbooking/admin/viewERODRules',
				'Letter contact rules' => '/OphTrOperationbooking/admin/viewLetterContactRules',
				'Letter warning rules' => '/OphTrOperationbooking/admin/viewLetterWarningRules',
				'Operation name rules' => '/OphTrOperationbooking/admin/viewOperationNameRules',
				'Waiting list contact rules' => '/OphTrOperationbooking/admin/viewWaitingListContactRules',
			)),
		));

		$this->insert('config_key',array(
			'config_group_id' => 2,
			'module_name' => 'OphTrOperationbooking',
			'name' => 'urgent_booking_notify_hours',
			'label' => 'Urgent booking notification window (hours)',
			'config_type_id' => 2,
			'default_value' => '24',
			'display_order' => 10,
		));

		$this->insert('config_key',array(
			'config_group_id' => 2,
			'module_name' => 'OphTrOperationbooking',
			'name' => 'urgent_booking_notify_email',
			'label' => 'Urgent booking notification email addresses',
			'config_type_id' => 6,
			'default_value' => serialize(array()),
			'display_order' => 20,
		));

		$this->insert('config_key',array(
			'config_group_id' => 2,
			'module_name' => 'OphTrOperationbooking',
			'name' => 'urgent_booking_notify_email_from',
			'label' => 'Urgent booking notification from address',
			'config_type_id' => 3,
			'default_value' => '',
			'display_order' => 30,
		));

		$this->insert('config_key',array(
			'config_group_id' => 2,
			'module_name' => 'OphTrOperationbooking',
			'name' => 'transport_exclude_sites',
			'label' => 'Exclude sites from transport list',
			'config_type_id' => 7,
			'default_value' => serialize(array()),
			'display_order' => 40,
			'metadata1' => 'Site',
			'metadata2' => 'name',
		));

		$this->insert('config_key',array(
			'config_group_id' => 2,
			'module_name' => 'OphTrOperationbooking',
			'name' => 'transport_exclude_theatres',
			'label' => 'Exclude theatres from transport list',
			'config_type_id' => 7,
			'default_value' => serialize(array()),
			'display_order' => 50,
			'metadata1' => 'OphTrOperationbooking_Operation_Theatre',
			'metadata2' => 'name',
		));
	}

	public function down()
	{
		$this->delete('config',"module_name = 'OphTrOperationbooking'");
		$this->delete('config_key',"module_name = 'OphTrOperationbooking'");
	}
}
