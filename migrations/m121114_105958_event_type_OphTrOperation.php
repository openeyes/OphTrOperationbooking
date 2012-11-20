<?php 
class m121114_105958_event_type_OphTrOperation extends CDbMigration
{
	public function up() {

		// --- EVENT TYPE ENTRIES ---

		// create an event_type entry for this event type name if one doesn't already exist
		if (!$this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'OphTrOperation'))->queryRow()) {
			$group = $this->dbConnection->createCommand()->select('id')->from('event_group')->where('name=:name',array(':name'=>'Treatment events'))->queryRow();
			$this->insert('event_type', array('class_name' => 'OphTrOperation', 'name' => 'Operation','event_group_id' => $group['id']));
		}
		// select the event_type id for this event type name
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'OphTrOperation'))->queryRow();

		// create an element_type entry for this element type name if one doesn't already exist
		if (!$this->dbConnection->createCommand()->select('id')->from('element_type')->where('class_name=:class_name and event_type_id=:eventTypeId', array(':class_name'=>'Element_OphTrOperation_Diagnosis',':eventTypeId'=>$event_type['id']))->queryRow()) {
			$this->insert('element_type', array('name' => 'Diagnosis','class_name' => 'Element_OphTrOperation_Diagnosis', 'event_type_id' => $event_type['id'], 'display_order' => 1));
		}
		// select the element_type_id for this element type name
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id=:eventTypeId and class_name=:class_name', array(':eventTypeId'=>$event_type['id'],':class_name'=>'Element_OphTrOperation_Diagnosis'))->queryRow();

		$this->insert('element_type_eye',array('element_type_id' => $element_type['id'], 'eye_id' => 1, 'display_order' => 3));
		$this->insert('element_type_eye',array('element_type_id' => $element_type['id'], 'eye_id' => 2, 'display_order' => 1));
		$this->insert('element_type_eye',array('element_type_id' => $element_type['id'], 'eye_id' => 3, 'display_order' => 2));

		// create an element_type entry for this element type name if one doesn't already exist
		if (!$this->dbConnection->createCommand()->select('id')->from('element_type')->where('class_name=:class_name and event_type_id=:eventTypeId', array(':class_name'=>'Element_OphTrOperation_Operation',':eventTypeId'=>$event_type['id']))->queryRow()) {
			$this->insert('element_type', array('name' => 'Operation','class_name' => 'Element_OphTrOperation_Operation', 'event_type_id' => $event_type['id'], 'display_order' => 1));
		}
		// select the element_type_id for this element type name
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id=:eventTypeId and class_name=:class_name', array(':eventTypeId'=>$event_type['id'],':class_name'=>'Element_OphTrOperation_Operation'))->queryRow();

		for ($i=1;$i<=5;$i++) {
			$this->insert('element_type_anaesthetic_type',array('element_type_id' => $element_type['id'], 'anaesthetic_type_id' => $i, 'display_order' => $i));
		}

		$this->insert('element_type_eye',array('element_type_id' => $element_type['id'], 'eye_id' => 1, 'display_order' => 3));
		$this->insert('element_type_eye',array('element_type_id' => $element_type['id'], 'eye_id' => 2, 'display_order' => 1));
		$this->insert('element_type_eye',array('element_type_id' => $element_type['id'], 'eye_id' => 3, 'display_order' => 2));

		$this->insert('element_type_priority',array('element_type_id' => $element_type['id'], 'priority_id' => 1, 'display_order' => 1));
		$this->insert('element_type_priority',array('element_type_id' => $element_type['id'], 'priority_id' => 2, 'display_order' => 2));

		// create an element_type entry for this element type name if one doesn't already exist
		if (!$this->dbConnection->createCommand()->select('id')->from('element_type')->where('class_name=:class_name and event_type_id=:eventTypeId', array(':class_name'=>'Element_OphTrOperation_ScheduleOperation',':eventTypeId'=>$event_type['id']))->queryRow()) {
			$this->insert('element_type', array('name' => 'Schedule operation','class_name' => 'Element_OphTrOperation_ScheduleOperation', 'event_type_id' => $event_type['id'], 'display_order' => 1));
		}
		// select the element_type_id for this element type name
		$element_type = $this->dbConnection->createCommand()->select('id')->from('element_type')->where('event_type_id=:eventTypeId and class_name=:class_name', array(':eventTypeId'=>$event_type['id'],':class_name'=>'Element_OphTrOperation_ScheduleOperation'))->queryRow();

		// create the table for this element type: et_modulename_elementtypename
		$this->createTable('et_ophtroperation_diagnosis', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'eye_id' => 'int(10) unsigned NOT NULL DEFAULT 1', // Eyes
				'disorder_id' => 'int(10) unsigned NOT NULL', // Diagnosis
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperation_diagnosis_lmui_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperation_diagnosis_cui_fk` (`created_user_id`)',
				'KEY `et_ophtroperation_diagnosis_ev_fk` (`event_id`)',
				'KEY `et_ophtroperation_diagnosis_eye_id_fk` (`eye_id`)',
				'CONSTRAINT `et_ophtroperation_diagnosis_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperation_diagnosis_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperation_diagnosis_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `et_ophtroperation_diagnosis_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		// element lookup table et_ophtroperation_operation_priority
		$this->createTable('ophtroperation_operation_priority', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(128) COLLATE utf8_bin NOT NULL',
				'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ophtroperation_operation_priority_lmui_fk` (`last_modified_user_id`)',
				'KEY `ophtroperation_operation_priority_cui_fk` (`created_user_id`)',
				'CONSTRAINT `ophtroperation_operation_priority_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_operation_priority_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->insert('ophtroperation_operation_priority',array('name'=>'Routine','display_order'=>1));
		$this->insert('ophtroperation_operation_priority',array('name'=>'Urgent','display_order'=>2));

		$this->createTable('ophtroperation_operation_status', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(64) COLLATE utf8_bin',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ophtroperation_operation_status_lmui_fk` (`last_modified_user_id`)',
				'KEY `ophtroperation_operation_status_cui_fk` (`created_user_id`)',
				'CONSTRAINT `ophtroperation_operation_status_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_operation_status_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->insert('ophtroperation_operation_status',array('id'=>1,'name'=>'Pending'));
		$this->insert('ophtroperation_operation_status',array('id'=>2,'name'=>'Scheduled'));
		$this->insert('ophtroperation_operation_status',array('id'=>3,'name'=>'Needs rescheduling'));
		$this->insert('ophtroperation_operation_status',array('id'=>4,'name'=>'Rescheduled'));
		$this->insert('ophtroperation_operation_status',array('id'=>5,'name'=>'Cancelled'));

		$this->createTable('ophtroperation_operation_cancellation_reason', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'text' => "varchar(255) COLLATE utf8_bin NOT NULL DEFAULT ''",
				'parent_id' => 'int(10) unsigned DEFAULT NULL',
				'list_no' => 'tinyint(2) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ophtroperation_operation_cancellation_reason_lmui_fk` (`last_modified_user_id`)',
				'KEY `ophtroperation_operation_cancellation_reason_cui_fk` (`created_user_id`)',
				'CONSTRAINT `ophtroperation_operation_cancellation_reason_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_operation_cancellation_reason_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		// create the table for this element type: et_modulename_elementtypename
		$this->createTable('et_ophtroperation_operation', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'eye_id' => 'int(10) unsigned NOT NULL DEFAULT 1', // Eyes
				'consultant_required' => 'tinyint(1) unsigned NOT NULL DEFAULT 0', // Consultant required
				'anaesthetic_type_id' => 'int(10) unsigned NOT NULL DEFAULT 1', // Anaesthetic type
				'overnight_stay' => 'tinyint(1) unsigned NOT NULL DEFAULT 0', // Post operative stay
				'site_id' => 'int(10) unsigned NOT NULL DEFAULT 1', // Site
				'priority_id' => 'int(10) unsigned NOT NULL DEFAULT 1', // Priority
				'decision_date' => 'date DEFAULT NULL', // Decision date
				'comments' => 'text DEFAULT \'\'', // Add comments
				'total_duration' => 'smallint(5) unsigned NOT NULL',
				'status_id' => 'int(10) unsigned NOT NULL',
				'anaesthetist_required' => "tinyint(1) unsigned DEFAULT '0'",
				'cancellation_date' => 'datetime DEFAULT NULL',
				'cancellation_user_id' => 'int(10) unsigned NULL',
				'cancellation_reason_id' => 'int(10) unsigned NULL',
				'cancellation_comment' => 'varchar(200) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperation_operation_lmui_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperation_operation_cui_fk` (`created_user_id`)',
				'KEY `et_ophtroperation_operation_ev_fk` (`event_id`)',
				'KEY `et_ophtroperation_operation_eye_id_fk` (`eye_id`)',
				'KEY `et_ophtroperation_operation_anaesthetic_type_id_fk` (`anaesthetic_type_id`)',
				'KEY `et_ophtroperation_operation_site_id_fk` (`site_id`)',
				'KEY `et_ophtroperation_operation_priority_fk` (`priority_id`)',
				'KEY `et_ophtroperation_operation_cancellation_reason_id_fk` (`cancellation_reason_id`)',
				'KEY `et_ophtroperation_operation_status_id_fk` (`status_id`)',
				'KEY `et_ophtroperation_operation_cancellation_user_id_fk` (`cancellation_user_id`)',
				'CONSTRAINT `et_ophtroperation_operation_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperation_operation_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperation_operation_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `et_ophtroperation_operation_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)',
				'CONSTRAINT `et_ophtroperation_operation_anaesthetic_type_id_fk` FOREIGN KEY (`anaesthetic_type_id`) REFERENCES `anaesthetic_type` (`id`)',
				'CONSTRAINT `et_ophtroperation_operation_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
				'CONSTRAINT `et_ophtroperation_operation_priority_fk` FOREIGN KEY (`priority_id`) REFERENCES `ophtroperation_operation_priority` (`id`)',
				'CONSTRAINT `et_ophtroperation_operation_cancellation_reason_id_fk` FOREIGN KEY (`cancellation_reason_id`) REFERENCES `ophtroperation_operation_cancellation_reason` (`id`)',
				'CONSTRAINT `et_ophtroperation_operation_status_is_fk` FOREIGN KEY (`status_id`) REFERENCES `ophtroperation_operation_status` (`id`)',
				'CONSTRAINT `et_ophtroperation_operation_cancellation_user_id_fk` FOREIGN KEY (`cancellation_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('ophtroperation_operation_date_letter_sent', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_id' => 'int(10) unsigned NOT NULL',
				'date_invitation_letter_sent' => 'datetime DEFAULT NULL',
				'date_1st_reminder_letter_sent' => 'datetime DEFAULT NULL',
				'date_2nd_reminder_letter_sent' => 'datetime DEFAULT NULL',
				'date_gp_letter_sent' => 'datetime DEFAULT NULL',
				'date_scheduling_letter_sent' => 'datetime DEFAULT NULL',
				'last_modified_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'last_modified_date' => "datetime NOT NULL DEFAULT '1901-01-01 00:00:00'",
				'created_user_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'created_date' => "datetime NOT NULL DEFAULT '1901-01-01 00:00:00'",
				'PRIMARY KEY (`id`)',
				'KEY `element_id` (`element_id`)',
				'KEY `ophtroperation_operation_dls_last_modified_user_id_fk` (`last_modified_user_id`)',
				'KEY `ophtroperation_operation_dls_created_user_id_fk` (`created_user_id`)',
				'CONSTRAINT `ophtroperation_operation_dls_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_operation_dls_element_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophtroperation_operation` (`id`)',
				'CONSTRAINT `ophtroperation_operation_dls_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)'
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('ophtroperation_operation_theatre', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(255) COLLATE utf8_bin DEFAULT NULL',
				'site_id' => 'int(10) unsigned NOT NULL',
				'code' => 'varchar(4) COLLATE utf8_bin NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ophtroperation_operation_theatre_site_id_fk` (`site_id`)',
				'KEY `ophtroperation_operation_theatre_lmui_fk` (`last_modified_user_id`)',
				'KEY `ophtroperation_operation_theatre_cui_fk` (`created_user_id`)',
				'CONSTRAINT `ophtroperation_operation_theatre_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
				'CONSTRAINT `ophtroperation_operation_theatre_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_operation_theatre_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('ophtroperation_operation_ward', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'site_id' => 'int(10) unsigned NOT NULL',
				'name' => 'varchar(255) COLLATE utf8_bin NOT NULL',
				'restriction' => 'tinyint(1) DEFAULT NULL',
				'code' => 'varchar(10) COLLATE utf8_bin NOT NULL',
				'theatre_id' => 'int(10) unsigned NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ophtroperation_operation_ward_site_id_fk` (`site_id`)',
				'KEY `ophtroperation_operation_ward_lmui_fk` (`last_modified_user_id`)',
				'KEY `ophtroperation_operation_ward_cui_fk` (`created_user_id`)',
				'KEY `ophtroperation_operation_ward_thi_fk` (`theatre_id`)',
				'CONSTRAINT `ophtroperation_operation_ward_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)',
				'CONSTRAINT `ophtroperation_operation_ward_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_operation_ward_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_operation_ward_thi_fk` FOREIGN KEY (`theatre_id`) REFERENCES `ophtroperation_operation_theatre` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('ophtroperation_operation_sequence_interval', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(32) COLLATE utf8_bin NOT NULL',
				'display_order' => 'int(10) unsigned NOT NULL DEFAULT 0',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ophtroperation_operation_sequencei_lmui_fk` (`last_modified_user_id`)',
				'KEY `ophtroperation_operation_sequencei_cui_fk` (`created_user_id`)',
				'CONSTRAINT `ophtroperation_operation_sequencei_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_operation_sequencei_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->insert('ophtroperation_operation_sequence_interval',array('id'=>1,'name'=>'Once'));
		$this->insert('ophtroperation_operation_sequence_interval',array('id'=>2,'name'=>'1 week'));
		$this->insert('ophtroperation_operation_sequence_interval',array('id'=>3,'name'=>'2 weeks'));
		$this->insert('ophtroperation_operation_sequence_interval',array('id'=>4,'name'=>'3 weeks'));
		$this->insert('ophtroperation_operation_sequence_interval',array('id'=>5,'name'=>'4 weeks'));
		$this->insert('ophtroperation_operation_sequence_interval',array('id'=>6,'name'=>'Monthly'));

		$this->createTable('ophtroperation_operation_sequence', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'firm_id' => 'int(10) unsigned NULL',
				'theatre_id' => 'int(10) unsigned NOT NULL',
				'start_date' => 'date NOT NULL',
				'start_time' => 'time NOT NULL',
				'end_time' => 'time NOT NULL',
				'end_date' => 'date DEFAULT NULL',
				'interval_id' => 'int(10) unsigned NOT NULL',
				'weekday' => 'tinyint(1) DEFAULT NULL',
				'week_selection' => 'tinyint(1) DEFAULT NULL',
				'consultant' => "tinyint(1) unsigned NOT NULL DEFAULT '1'",
				'paediatric' => "tinyint(1) unsigned NOT NULL DEFAULT '0'",
				'anaesthetist' => "tinyint(1) unsigned NOT NULL DEFAULT '0'",
				'general_anaesthetic' => "tinyint(1) unsigned NOT NULL DEFAULT '0'",
				'last_generate_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ophtroperation_operation_sequence_firm_id_fk` (`firm_id`)',
				'KEY `ophtroperation_operation_sequence_theatre_id_fk` (`theatre_id`)',
				'KEY `ophtroperation_operation_sequence_interval_id_fk` (`interval_id`)',
				'KEY `ophtroperation_operation_sequence_lmui_fk` (`last_modified_user_id`)',
				'KEY `ophtroperation_operation_sequence_cui_fk` (`created_user_id`)',
				'CONSTRAINT `ophtroperation_operation_sequence_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
				'CONSTRAINT `ophtroperation_operation_sequence_theatre_id_fk` FOREIGN KEY (`theatre_id`) REFERENCES `ophtroperation_operation_theatre` (`id`)',
				'CONSTRAINT `ophtroperation_operation_sequence_interval_id_fk` FOREIGN KEY (`interval_id`) REFERENCES `ophtroperation_operation_sequence_interval` (`id`)',
				'CONSTRAINT `ophtroperation_operation_sequence_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_operation_sequence_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('ophtroperation_operation_session', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'sequence_id' => 'int(10) unsigned NOT NULL',
				'firm_id' => 'int(10) unsigned NULL',
				'date' => 'date NOT NULL',
				'start_time' => 'time NOT NULL',
				'end_time' => 'time NOT NULL',
				'comments' => 'text COLLATE utf8_bin',
				'available' => "tinyint(1) unsigned NOT NULL DEFAULT 1",
				'consultant' => "tinyint(1) unsigned NOT NULL DEFAULT '1'",
				'paediatric' => "tinyint(1) unsigned NOT NULL DEFAULT '0'",
				'anaesthetist' => "tinyint(1) unsigned NOT NULL DEFAULT '0'",
				'general_anaesthetic' => "tinyint(1) unsigned NOT NULL DEFAULT '0'",
				'theatre_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ophtroperation_operation_session_sequence_id_fk` (`sequence_id`)',
				'KEY `ophtroperation_operation_session_firm_id_fk` (`firm_id`)',
				'KEY `ophtroperation_operation_session_theatre_id_fk` (`theatre_id`)',
				'KEY `ophtroperation_operation_session_lmui_fk` (`last_modified_user_id`)',
				'KEY `ophtroperation_operation_session_cui_fk` (`created_user_id`)',
				'CONSTRAINT `ophtroperation_operation_session_sequence_id_fk` FOREIGN KEY (`sequence_id`) REFERENCES `ophtroperation_operation_sequence` (`id`)',
				'CONSTRAINT `ophtroperation_operation_session_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
				'CONSTRAINT `ophtroperation_operation_session_theatre_id_fk` FOREIGN KEY (`theatre_id`) REFERENCES `ophtroperation_operation_theatre` (`id`)',
				'CONSTRAINT `ophtroperation_operation_session_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_operation_session_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('ophtroperation_operation_erod', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_id' => "int(10) unsigned NOT NULL DEFAULT '1'",
				'session_id' => 'int(10) unsigned NOT NULL',
				'session_date' => 'date NOT NULL',
				'session_start_time' => 'time NOT NULL',
				'session_end_time' => 'time NOT NULL',
				'firm_id' => 'int(10) unsigned NOT NULL',
				'consultant' => 'tinyint(1) unsigned NOT NULL',
				'paediatric' => 'tinyint(1) unsigned NOT NULL',
				'anaesthetist' => 'tinyint(1) unsigned NOT NULL',
				'general_anaesthetic' => 'tinyint(1) unsigned NOT NULL',
				'session_duration' => 'int(10) unsigned NOT NULL',
				'total_operations_time' => 'int(10) unsigned NOT NULL',
				'available_time' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ophtroperation_operation_erod_lmui_fk` (`last_modified_user_id`)',
				'KEY `ophtroperation_operation_erod_cui_fk` (`created_user_id`)',
				'KEY `ophtroperation_operation_erod_element_id_fk` (`element_id`)',
				'KEY `ophtroperation_operation_erod_session_id_fk` (`session_id`)',
				'KEY `ophtroperation_operation_erod_firm_id_fk` (`firm_id`)',
				'CONSTRAINT `ophtroperation_operation_erod_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_operation_erod_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_operation_erod_element_id_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophtroperation_operation` (`id`)',
				'CONSTRAINT `ophtroperation_operation_erod_session_id_fk` FOREIGN KEY (`session_id`) REFERENCES `ophtroperation_operation_session` (`id`)',
				'CONSTRAINT `ophtroperation_operation_erod_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('ophtroperation_operation_erod_rule', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'subspecialty_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ophtroperation_operation_erod_rule_sid_fk` (`subspecialty_id`)',
				'KEY `ophtroperation_operation_erod_rule_lmui_fk` (`last_modified_user_id`)',
				'KEY `ophtroperation_operation_erod_rule_cui_fk` (`created_user_id`)',
				'CONSTRAINT `ophtroperation_operation_erod_rule_sid_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`)',
				'CONSTRAINT `ophtroperation_operation_erod_rule_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_operation_erod_rule_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('ophtroperation_operation_erod_rule_item', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'erod_rule_id' => 'int(10) unsigned NOT NULL',
				'item_type' => 'varchar(64) COLLATE utf8_bin NOT NULL',
				'item_id' => 'int(10) unsigned NOT NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ophtroperation_operation_erod_rule_item_eri_fk` (`erod_rule_id`)',
				'KEY `ophtroperation_operation_erod_rule_item_lmui_fk` (`last_modified_user_id`)',
				'KEY `ophtroperation_operation_erod_rule_item_cui_fk` (`created_user_id`)',
				'CONSTRAINT `ophtroperation_operation_erod_rule_item_eri_fk` FOREIGN KEY (`erod_rule_id`) REFERENCES `ophtroperation_operation_erod_rule` (`id`)',
				'CONSTRAINT `ophtroperation_operation_erod_rule_item_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_operation_erod_rule_item_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('ophtroperation_operation_booking', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_id' => 'int(10) unsigned NOT NULL',
				'session_id' => 'int(10) unsigned NULL',
				'display_order' => 'int(10) unsigned NOT NULL DEFAULT 0',
				'ward_id' => 'int(10) unsigned NOT NULL DEFAULT 0',
				'admission_time' => 'time NOT NULL',
				'confirmed' => 'tinyint(1) unsigned NOT NULL DEFAULT 0',
				'session_date' => 'date NOT NULL',
				'session_start_time' => 'time NOT NULL',
				'session_end_time' => 'time NOT NULL',
				'session_theatre_id' => 'int(10) unsigned NOT NULL',
				'cancellation_date' => 'datetime DEFAULT NULL',
				'cancellation_reason_id' => 'int(10) unsigned NULL',
				'cancellation_comment' => 'varchar(200) COLLATE utf8_bin NOT NULL',
				'cancellation_user_id' => 'int(10) unsigned NULL',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperation_operation_booking_lmui_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperation_operation_booking_cui_fk` (`created_user_id`)',
				'KEY `et_ophtroperation_operation_booking_ele_fk` (`element_id`)',
				'KEY `et_ophtroperation_operation_booking_wid_fk` (`ward_id`)',
				'KEY `et_ophtroperation_operation_booking_sti_fk` (`session_theatre_id`)',
				'KEY `et_ophtroperation_operation_booking_cri_fk` (`cancellation_reason_id`)',
				'KEY `et_ophtroperation_operation_booking_ses_fk` (`session_id`)',
				'KEY `et_ophtroperation_operation_booking_caui_fk` (`cancellation_user_id`)',
				'CONSTRAINT `et_ophtroperation_operation_booking_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperation_operation_booking_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperation_operation_booking_ele_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophtroperation_operation` (`id`)',
				'CONSTRAINT `et_ophtroperation_operation_booking_wid_fk` FOREIGN KEY (`ward_id`) REFERENCES `ophtroperation_operation_ward` (`id`)',
				'CONSTRAINT `et_ophtroperation_operation_booking_sti_fk` FOREIGN KEY (`session_theatre_id`) REFERENCES `ophtroperation_operation_theatre` (`id`)',
				'CONSTRAINT `et_ophtroperation_operation_booking_cri_fk` FOREIGN KEY (`cancellation_reason_id`) REFERENCES `ophtroperation_operation_cancellation_reason` (`id`)',
				'CONSTRAINT `et_ophtroperation_operation_booking_ses_fk` FOREIGN KEY (`session_id`) REFERENCES `ophtroperation_operation_session` (`id`)',
				'CONSTRAINT `et_ophtroperation_operation_booking_caui_fk` FOREIGN KEY (`cancellation_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->createTable('ophtroperation_operation_procedures_procedures', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'element_id' => 'int(10) unsigned NOT NULL',
				'proc_id' => 'int(10) unsigned NOT NULL',
				'display_order' => 'int(10) unsigned NOT NULL DEFAULT 0',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ophtroperation_operation_procedures_procedures_lmui_fk` (`last_modified_user_id`)',
				'KEY `ophtroperation_operation_procedures_procedures_cui_fk` (`created_user_id`)',
				'KEY `ophtroperation_operation_procedures_procedures_ele_fk` (`element_id`)',
				'KEY `ophtroperation_operation_procedures_procedures_lku_fk` (`proc_id`)',
				'CONSTRAINT `ophtroperation_operation_procedures_procedures_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_operation_procedures_procedures_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_operation_procedures_procedures_ele_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophtroperation_operation` (`id`)',
				'CONSTRAINT `ophtroperation_operation_procedures_procedures_lku_fk` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		// element lookup table ophtroperation_scheduleope_schedule_options
		$this->createTable('ophtroperation_scheduleope_schedule_options', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'name' => 'varchar(128) COLLATE utf8_bin NOT NULL',
				'display_order' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `ophtroperation_scheduleope_schedule_options_lmui_fk` (`last_modified_user_id`)',
				'KEY `ophtroperation_scheduleope_schedule_options_cui_fk` (`created_user_id`)',
				'CONSTRAINT `ophtroperation_scheduleope_schedule_options_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `ophtroperation_scheduleope_schedule_options_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');

		$this->insert('ophtroperation_scheduleope_schedule_options',array('name'=>'As soon as possible','display_order'=>1));

		// create the table for this element type: et_modulename_elementtypename
		$this->createTable('et_ophtroperation_scheduleope', array(
				'id' => 'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'event_id' => 'int(10) unsigned NOT NULL',
				'schedule_options_id' => 'int(10) unsigned NOT NULL DEFAULT 1', // Schedule options
				'last_modified_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'last_modified_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'created_user_id' => 'int(10) unsigned NOT NULL DEFAULT 1',
				'created_date' => 'datetime NOT NULL DEFAULT \'1901-01-01 00:00:00\'',
				'PRIMARY KEY (`id`)',
				'KEY `et_ophtroperation_scheduleope_lmui_fk` (`last_modified_user_id`)',
				'KEY `et_ophtroperation_scheduleope_cui_fk` (`created_user_id`)',
				'KEY `et_ophtroperation_scheduleope_ev_fk` (`event_id`)',
				'KEY `et_ophtroperation_scheduleope_schedule_options_fk` (`schedule_options_id`)',
				'CONSTRAINT `et_ophtroperation_scheduleope_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperation_scheduleope_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)',
				'CONSTRAINT `et_ophtroperation_scheduleope_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`)',
				'CONSTRAINT `et_ophtroperation_scheduleope_schedule_options_fk` FOREIGN KEY (`schedule_options_id`) REFERENCES `ophtroperation_scheduleope_schedule_options` (`id`)',
			), 'ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin');
	}

	public function down() {
		$this->dropTable('et_ophtroperation_diagnosis');
		$this->dropTable('ophtroperation_operation_procedures_procedures');
		$this->dropTable('ophtroperation_operation_erod_rule_item');
		$this->dropTable('ophtroperation_operation_erod_rule');
		$this->dropTable('ophtroperation_operation_booking');
		$this->dropTable('ophtroperation_operation_erod');
		$this->dropTable('ophtroperation_operation_session');
		$this->dropTable('ophtroperation_operation_sequence');
		$this->dropTable('ophtroperation_operation_sequence_interval');
		$this->dropTable('ophtroperation_operation_ward');
		$this->dropTable('ophtroperation_operation_theatre');
		$this->dropTable('ophtroperation_operation_date_letter_sent');
		$this->dropTable('et_ophtroperation_operation');
		$this->dropTable('ophtroperation_operation_status');
		$this->dropTable('ophtroperation_operation_cancellation_reason');
		$this->dropTable('ophtroperation_operation_priority');
		$this->dropTable('et_ophtroperation_scheduleope');
		$this->dropTable('ophtroperation_scheduleope_schedule_options');

		// --- delete event entries ---
		$event_type = $this->dbConnection->createCommand()->select('id')->from('event_type')->where('class_name=:class_name', array(':class_name'=>'OphTrOperation'))->queryRow();

/*
		foreach ($this->dbConnection->createCommand()->select('id')->from('event')->where('event_type_id=:event_type_id', array(':event_type_id'=>$event_type['id']))->queryAll() as $row) {
			$this->delete('audit', 'event_id='.$row['id']);
			$this->delete('event', 'id='.$row['id']);
		}
*/

		$element_operation = Yii::app()->db->createCommand("select * from element_type where event_type_id={$event_type['id']} and class_name = 'Element_OphTrOperation_Operation'")->queryRow();
		$element_diagnosis = Yii::app()->db->createCommand("select * from element_type where event_type_id={$event_type['id']} and class_name = 'Element_OphTrOperation_Diagnosis'")->queryRow();
		$element_schedule = Yii::app()->db->createCommand("select * from element_type where event_type_id={$event_type['id']} and class_name = 'Element_OphTrOperation_ScheduleOperation'")->queryRow();

		foreach (array($element_operation['id'],$element_diagnosis['id']) as $element_type_id) {
			$this->delete('element_type_anaesthetic_type','element_type_id='.$element_type_id);
			$this->delete('element_type_eye','element_type_id='.$element_type_id);
			$this->delete('element_type_priority','element_type_id='.$element_type_id);
		}

		// --- delete entries from element_type ---
		$this->delete('element_type', 'id='.$element_operation['id']);
		$this->delete('element_type', 'id='.$element_diagnosis['id']);
		$this->delete('element_type', 'id='.$element_schedule['id']);
	}
}
?>
