<?php

class m131202_102025_table_versioning extends OEMigration
{
	public function up()
	{
		$this->execute("
CREATE TABLE `et_ophtroperationbooking_diagnosis_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` int(10) unsigned NOT NULL,
	`eye_id` int(10) unsigned NOT NULL DEFAULT '1',
	`disorder_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_et_ophtroperationbooking_diagnosis_lmui_fk` (`last_modified_user_id`),
	KEY `acv_et_ophtroperationbooking_diagnosis_cui_fk` (`created_user_id`),
	KEY `acv_et_ophtroperationbooking_diagnosis_ev_fk` (`event_id`),
	KEY `acv_et_ophtroperationbooking_diagnosis_eye_id_fk` (`eye_id`),
	CONSTRAINT `acv_et_ophtroperationbooking_diagnosis_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_diagnosis_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_diagnosis_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_diagnosis_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('et_ophtroperationbooking_diagnosis_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','et_ophtroperationbooking_diagnosis_version');

		$this->createIndex('et_ophtroperationbooking_diagnosis_aid_fk','et_ophtroperationbooking_diagnosis_version','id');

		$this->addColumn('et_ophtroperationbooking_diagnosis_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('et_ophtroperationbooking_diagnosis_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','et_ophtroperationbooking_diagnosis_version','version_id');
		$this->alterColumn('et_ophtroperationbooking_diagnosis_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `et_ophtroperationbooking_operation_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` int(10) unsigned NOT NULL,
	`eye_id` int(10) unsigned NOT NULL DEFAULT '1',
	`consultant_required` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`anaesthetic_type_id` int(10) unsigned NOT NULL DEFAULT '1',
	`overnight_stay` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`site_id` int(10) unsigned NOT NULL DEFAULT '1',
	`priority_id` int(10) unsigned NOT NULL DEFAULT '1',
	`decision_date` date DEFAULT NULL,
	`comments` text,
	`total_duration` smallint(5) unsigned NOT NULL,
	`status_id` int(10) unsigned NOT NULL,
	`anaesthetist_required` tinyint(1) unsigned DEFAULT '0',
	`operation_cancellation_date` datetime DEFAULT NULL,
	`cancellation_user_id` int(10) unsigned DEFAULT NULL,
	`cancellation_reason_id` int(10) unsigned DEFAULT NULL,
	`cancellation_comment` varchar(200) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`latest_booking_id` int(10) unsigned DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_et_ophtroperationbooking_operation_lmui_fk` (`last_modified_user_id`),
	KEY `acv_et_ophtroperationbooking_operation_cui_fk` (`created_user_id`),
	KEY `acv_et_ophtroperationbooking_operation_ev_fk` (`event_id`),
	KEY `acv_et_ophtroperationbooking_operation_eye_id_fk` (`eye_id`),
	KEY `acv_et_ophtroperationbooking_operation_anaesthetic_type_id_fk` (`anaesthetic_type_id`),
	KEY `acv_et_ophtroperationbooking_operation_site_id_fk` (`site_id`),
	KEY `acv_et_ophtroperationbooking_operation_priority_fk` (`priority_id`),
	KEY `acv_et_ophtroperationbooking_operation_cancellation_reason_id_fk` (`cancellation_reason_id`),
	KEY `acv_et_ophtroperationbooking_operation_status_id_fk` (`status_id`),
	KEY `acv_et_ophtroperationbooking_operation_cancellation_user_id_fk` (`cancellation_user_id`),
	KEY `acv_et_ophtroperationbooking_operation_latest_booking_id_fk` (`latest_booking_id`),
	CONSTRAINT `acv_et_ophtroperationbooking_operation_latest_booking_id_fk` FOREIGN KEY (`latest_booking_id`) REFERENCES `ophtroperationbooking_operation_booking` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_operation_anaesthetic_type_id_fk` FOREIGN KEY (`anaesthetic_type_id`) REFERENCES `anaesthetic_type` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_operation_cancellation_reason_id_fk` FOREIGN KEY (`cancellation_reason_id`) REFERENCES `ophtroperationbooking_operation_cancellation_reason` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_operation_cancellation_user_id_fk` FOREIGN KEY (`cancellation_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_operation_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_operation_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_operation_eye_id_fk` FOREIGN KEY (`eye_id`) REFERENCES `eye` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_operation_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_operation_priority_fk` FOREIGN KEY (`priority_id`) REFERENCES `ophtroperationbooking_operation_priority` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_operation_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_operation_status_is_fk` FOREIGN KEY (`status_id`) REFERENCES `ophtroperationbooking_operation_status` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('et_ophtroperationbooking_operation_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','et_ophtroperationbooking_operation_version');

		$this->createIndex('et_ophtroperationbooking_operation_aid_fk','et_ophtroperationbooking_operation_version','id');

		$this->addColumn('et_ophtroperationbooking_operation_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('et_ophtroperationbooking_operation_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','et_ophtroperationbooking_operation_version','version_id');
		$this->alterColumn('et_ophtroperationbooking_operation_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `et_ophtroperationbooking_scheduleope_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`event_id` int(10) unsigned NOT NULL,
	`schedule_options_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_et_ophtroperationbooking_scheduleope_lmui_fk` (`last_modified_user_id`),
	KEY `acv_et_ophtroperationbooking_scheduleope_cui_fk` (`created_user_id`),
	KEY `acv_et_ophtroperationbooking_scheduleope_ev_fk` (`event_id`),
	KEY `acv_et_ophtroperationbooking_scheduleope_schedule_options_fk` (`schedule_options_id`),
	CONSTRAINT `acv_et_ophtroperationbooking_scheduleope_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_scheduleope_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_scheduleope_ev_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_scheduleope_schedule_options_fk` FOREIGN KEY (`schedule_options_id`) REFERENCES `ophtroperationbooking_scheduleope_schedule_options` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('et_ophtroperationbooking_scheduleope_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','et_ophtroperationbooking_scheduleope_version');

		$this->createIndex('et_ophtroperationbooking_scheduleope_aid_fk','et_ophtroperationbooking_scheduleope_version','id');

		$this->addColumn('et_ophtroperationbooking_scheduleope_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('et_ophtroperationbooking_scheduleope_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','et_ophtroperationbooking_scheduleope_version','version_id');
		$this->alterColumn('et_ophtroperationbooking_scheduleope_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophtroperationbooking_admission_letter_warning_rule_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`rule_type_id` int(10) unsigned NOT NULL,
	`parent_rule_id` int(10) unsigned DEFAULT NULL,
	`rule_order` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`site_id` int(10) unsigned DEFAULT NULL,
	`theatre_id` int(10) unsigned DEFAULT NULL,
	`subspecialty_id` int(10) unsigned DEFAULT NULL,
	`is_child` tinyint(1) unsigned DEFAULT NULL,
	`show_warning` tinyint(1) unsigned NOT NULL DEFAULT '1',
	`warning_text` text NOT NULL,
	`emphasis` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`strong` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`firm_id` int(10) unsigned DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_ophtroperationbooking_admission_lwr_rti_fk` (`rule_type_id`),
	KEY `acv_ophtroperationbooking_admission_lwr_lmui_fk` (`last_modified_user_id`),
	KEY `acv_ophtroperationbooking_admission_lwr_cui_fk` (`created_user_id`),
	KEY `acv_ophtroperationbooking_admission_lwr_pri_fk` (`parent_rule_id`),
	KEY `acv_ophtroperationbooking_admission_lwr_ti_fk` (`theatre_id`),
	KEY `acv_ophtroperationbooking_admission_lwr_si_fk` (`subspecialty_id`),
	KEY `acv_ophtroperationbooking_admission_lwr_site_fk` (`site_id`),
	KEY `acv_ophtroperationbooking_alw_rule_fidfk` (`firm_id`),
	CONSTRAINT `acv_ophtroperationbooking_alw_rule_fidfk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_admission_lwr_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_admission_lwr_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_admission_lwr_pri_fk` FOREIGN KEY (`parent_rule_id`) REFERENCES `ophtroperationbooking_admission_letter_warning_rule` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_admission_lwr_rti_fk` FOREIGN KEY (`rule_type_id`) REFERENCES `ophtroperationbooking_admission_letter_warning_rule_type` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_admission_lwr_site_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_admission_lwr_si_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_admission_lwr_ti_fk` FOREIGN KEY (`theatre_id`) REFERENCES `ophtroperationbooking_operation_theatre` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophtroperationbooking_admission_letter_warning_rule_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophtroperationbooking_admission_letter_warning_rule_version');

		$this->createIndex('ophtroperationbooking_admission_letter_warning_rule_aid_fk','ophtroperationbooking_admission_letter_warning_rule_version','id');

		$this->addColumn('ophtroperationbooking_admission_letter_warning_rule_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophtroperationbooking_admission_letter_warning_rule_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophtroperationbooking_admission_letter_warning_rule_version','version_id');
		$this->alterColumn('ophtroperationbooking_admission_letter_warning_rule_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophtroperationbooking_admission_letter_warning_rule_type_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_ophtroperationbooking_admission_letter_wrt_lmui_fk` (`last_modified_user_id`),
	KEY `acv_ophtroperationbooking_admission_letter_wrt_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_ophtroperationbooking_admission_letter_wrt_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_admission_letter_wrt_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophtroperationbooking_admission_letter_warning_rule_type_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophtroperationbooking_admission_letter_warning_rule_type_version');

		$this->createIndex('ophtroperationbooking_admission_letter_warning_rule_type_aid_fk','ophtroperationbooking_admission_letter_warning_rule_type_version','id');
		$this->addForeignKey('ophtroperationbooking_admission_letter_warning_rule_type_aid_fk','ophtroperationbooking_admission_letter_warning_rule_type_version','id','ophtroperationbooking_admission_letter_warning_rule_type','id');

		$this->addColumn('ophtroperationbooking_admission_letter_warning_rule_type_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophtroperationbooking_admission_letter_warning_rule_type_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophtroperationbooking_admission_letter_warning_rule_type_version','version_id');
		$this->alterColumn('ophtroperationbooking_admission_letter_warning_rule_type_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophtroperationbooking_letter_contact_rule_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`parent_rule_id` int(10) unsigned DEFAULT NULL,
	`rule_order` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`site_id` int(10) unsigned DEFAULT NULL,
	`subspecialty_id` int(10) unsigned DEFAULT NULL,
	`theatre_id` int(10) unsigned DEFAULT NULL,
	`firm_id` int(10) unsigned DEFAULT NULL,
	`refuse_telephone` varchar(64) NOT NULL,
	`health_telephone` varchar(64) NOT NULL,
	`refuse_title` varchar(64) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`is_child` tinyint(1) unsigned DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `acv_ophtroperationbooking_letter_contact_rule_pi_fk` (`parent_rule_id`),
	KEY `acv_ophtroperationbooking_letter_contact_rule_site_id_fk` (`site_id`),
	KEY `acv_ophtroperationbooking_letter_contact_rule_subspecialty_id_fk` (`subspecialty_id`),
	KEY `acv_ophtroperationbooking_letter_contact_rule_theatre_id_fk` (`theatre_id`),
	KEY `acv_ophtroperationbooking_letter_contact_rule_firm_id_fk` (`firm_id`),
	KEY `acv_ophtroperationbooking_letter_contact_rule_lmui_fk` (`last_modified_user_id`),
	KEY `acv_ophtroperationbooking_letter_contact_rule_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_ophtroperationbooking_letter_contact_rule_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_letter_contact_rule_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_letter_contact_rule_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_letter_contact_rule_pi_fk` FOREIGN KEY (`parent_rule_id`) REFERENCES `ophtroperationbooking_letter_contact_rule` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_letter_contact_rule_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_letter_contact_rule_subspecialty_id_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_letter_contact_rule_theatre_id_fk` FOREIGN KEY (`theatre_id`) REFERENCES `ophtroperationbooking_operation_theatre` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophtroperationbooking_letter_contact_rule_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophtroperationbooking_letter_contact_rule_version');

		$this->createIndex('ophtroperationbooking_letter_contact_rule_aid_fk','ophtroperationbooking_letter_contact_rule_version','id');

		$this->addColumn('ophtroperationbooking_letter_contact_rule_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophtroperationbooking_letter_contact_rule_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophtroperationbooking_letter_contact_rule_version','version_id');
		$this->alterColumn('ophtroperationbooking_letter_contact_rule_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophtroperationbooking_operation_booking_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`element_id` int(10) unsigned NOT NULL,
	`session_id` int(10) unsigned DEFAULT NULL,
	`display_order` int(10) unsigned NOT NULL DEFAULT '0',
	`ward_id` int(10) unsigned NOT NULL DEFAULT '0',
	`admission_time` time NOT NULL,
	`confirmed` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`session_date` date NOT NULL,
	`session_start_time` time NOT NULL,
	`session_end_time` time NOT NULL,
	`session_theatre_id` int(10) unsigned NOT NULL,
	`transport_arranged` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`transport_arranged_date` date DEFAULT NULL,
	`booking_cancellation_date` datetime DEFAULT NULL,
	`cancellation_reason_id` int(10) unsigned DEFAULT NULL,
	`cancellation_comment` varchar(200) NOT NULL,
	`cancellation_user_id` int(10) unsigned DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_et_ophtroperationbooking_operation_booking_lmui_fk` (`last_modified_user_id`),
	KEY `acv_et_ophtroperationbooking_operation_booking_cui_fk` (`created_user_id`),
	KEY `acv_et_ophtroperationbooking_operation_booking_ele_fk` (`element_id`),
	KEY `acv_et_ophtroperationbooking_operation_booking_wid_fk` (`ward_id`),
	KEY `acv_et_ophtroperationbooking_operation_booking_sti_fk` (`session_theatre_id`),
	KEY `acv_et_ophtroperationbooking_operation_booking_cri_fk` (`cancellation_reason_id`),
	KEY `acv_et_ophtroperationbooking_operation_booking_ses_fk` (`session_id`),
	KEY `acv_et_ophtroperationbooking_operation_booking_caui_fk` (`cancellation_user_id`),
	CONSTRAINT `acv_et_ophtroperationbooking_operation_booking_caui_fk` FOREIGN KEY (`cancellation_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_operation_booking_cri_fk` FOREIGN KEY (`cancellation_reason_id`) REFERENCES `ophtroperationbooking_operation_cancellation_reason` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_operation_booking_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_operation_booking_ele_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophtroperationbooking_operation` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_operation_booking_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_operation_booking_ses_fk` FOREIGN KEY (`session_id`) REFERENCES `ophtroperationbooking_operation_session` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_operation_booking_sti_fk` FOREIGN KEY (`session_theatre_id`) REFERENCES `ophtroperationbooking_operation_theatre` (`id`),
	CONSTRAINT `acv_et_ophtroperationbooking_operation_booking_wid_fk` FOREIGN KEY (`ward_id`) REFERENCES `ophtroperationbooking_operation_ward` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophtroperationbooking_operation_booking_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophtroperationbooking_operation_booking_version');

		$this->createIndex('ophtroperationbooking_operation_booking_aid_fk','ophtroperationbooking_operation_booking_version','id');

		$this->addColumn('ophtroperationbooking_operation_booking_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophtroperationbooking_operation_booking_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophtroperationbooking_operation_booking_version','version_id');
		$this->alterColumn('ophtroperationbooking_operation_booking_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophtroperationbooking_operation_cancellation_reason_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`text` varchar(255) NOT NULL DEFAULT '',
	`parent_id` int(10) unsigned DEFAULT NULL,
	`list_no` tinyint(2) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_ophtroperationbooking_operation_cancellation_reason_lmui_fk` (`last_modified_user_id`),
	KEY `acv_ophtroperationbooking_operation_cancellation_reason_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_cancellation_reason_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_cancellation_reason_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophtroperationbooking_operation_cancellation_reason_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophtroperationbooking_operation_cancellation_reason_version');

		$this->createIndex('ophtroperationbooking_operation_cancellation_reason_aid_fk','ophtroperationbooking_operation_cancellation_reason_version','id');
		$this->addForeignKey('ophtroperationbooking_operation_cancellation_reason_aid_fk','ophtroperationbooking_operation_cancellation_reason_version','id','ophtroperationbooking_operation_cancellation_reason','id');

		$this->addColumn('ophtroperationbooking_operation_cancellation_reason_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophtroperationbooking_operation_cancellation_reason_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophtroperationbooking_operation_cancellation_reason_version','version_id');
		$this->alterColumn('ophtroperationbooking_operation_cancellation_reason_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophtroperationbooking_operation_date_letter_sent_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`element_id` int(10) unsigned NOT NULL,
	`date_invitation_letter_sent` datetime DEFAULT NULL,
	`date_1st_reminder_letter_sent` datetime DEFAULT NULL,
	`date_2nd_reminder_letter_sent` datetime DEFAULT NULL,
	`date_gp_letter_sent` datetime DEFAULT NULL,
	`date_scheduling_letter_sent` datetime DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_element_id` (`element_id`),
	KEY `acv_ophtroperationbooking_operation_dls_last_modified_user_id_fk` (`last_modified_user_id`),
	KEY `acv_ophtroperationbooking_operation_dls_created_user_id_fk` (`created_user_id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_dls_created_user_id_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_dls_element_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophtroperationbooking_operation` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_dls_last_modified_user_id_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophtroperationbooking_operation_date_letter_sent_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophtroperationbooking_operation_date_letter_sent_version');

		$this->createIndex('ophtroperationbooking_operation_date_letter_sent_aid_fk','ophtroperationbooking_operation_date_letter_sent_version','id');

		$this->addColumn('ophtroperationbooking_operation_date_letter_sent_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophtroperationbooking_operation_date_letter_sent_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophtroperationbooking_operation_date_letter_sent_version','version_id');
		$this->alterColumn('ophtroperationbooking_operation_date_letter_sent_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophtroperationbooking_operation_erod_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`element_id` int(10) unsigned NOT NULL DEFAULT '1',
	`session_id` int(10) unsigned NOT NULL,
	`session_date` date NOT NULL,
	`session_start_time` time NOT NULL,
	`session_end_time` time NOT NULL,
	`firm_id` int(10) unsigned NOT NULL,
	`consultant` tinyint(1) unsigned NOT NULL,
	`paediatric` tinyint(1) unsigned NOT NULL,
	`anaesthetist` tinyint(1) unsigned NOT NULL,
	`general_anaesthetic` tinyint(1) unsigned NOT NULL,
	`session_duration` int(10) unsigned NOT NULL,
	`total_operations_time` int(10) unsigned NOT NULL,
	`available_time` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_ophtroperationbooking_operation_erod_lmui_fk` (`last_modified_user_id`),
	KEY `acv_ophtroperationbooking_operation_erod_cui_fk` (`created_user_id`),
	KEY `acv_ophtroperationbooking_operation_erod_element_id_fk` (`element_id`),
	KEY `acv_ophtroperationbooking_operation_erod_session_id_fk` (`session_id`),
	KEY `acv_ophtroperationbooking_operation_erod_firm_id_fk` (`firm_id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_erod_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_erod_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_erod_element_id_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophtroperationbooking_operation` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_erod_session_id_fk` FOREIGN KEY (`session_id`) REFERENCES `ophtroperationbooking_operation_session` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_erod_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophtroperationbooking_operation_erod_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophtroperationbooking_operation_erod_version');

		$this->createIndex('ophtroperationbooking_operation_erod_aid_fk','ophtroperationbooking_operation_erod_version','id');

		$this->addColumn('ophtroperationbooking_operation_erod_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophtroperationbooking_operation_erod_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophtroperationbooking_operation_erod_version','version_id');
		$this->alterColumn('ophtroperationbooking_operation_erod_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophtroperationbooking_operation_erod_rule_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`subspecialty_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_ophtroperationbooking_operation_erod_rule_sid_fk` (`subspecialty_id`),
	KEY `acv_ophtroperationbooking_operation_erod_rule_lmui_fk` (`last_modified_user_id`),
	KEY `acv_ophtroperationbooking_operation_erod_rule_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_erod_rule_sid_fk` FOREIGN KEY (`subspecialty_id`) REFERENCES `subspecialty` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_erod_rule_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_erod_rule_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophtroperationbooking_operation_erod_rule_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophtroperationbooking_operation_erod_rule_version');

		$this->createIndex('ophtroperationbooking_operation_erod_rule_aid_fk','ophtroperationbooking_operation_erod_rule_version','id');

		$this->addColumn('ophtroperationbooking_operation_erod_rule_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophtroperationbooking_operation_erod_rule_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophtroperationbooking_operation_erod_rule_version','version_id');
		$this->alterColumn('ophtroperationbooking_operation_erod_rule_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophtroperationbooking_operation_erod_rule_item_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`erod_rule_id` int(10) unsigned NOT NULL,
	`item_type` varchar(64) NOT NULL,
	`item_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_ophtroperationbooking_operation_erod_rule_item_eri_fk` (`erod_rule_id`),
	KEY `acv_ophtroperationbooking_operation_erod_rule_item_lmui_fk` (`last_modified_user_id`),
	KEY `acv_ophtroperationbooking_operation_erod_rule_item_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_erod_rule_item_eri_fk` FOREIGN KEY (`erod_rule_id`) REFERENCES `ophtroperationbooking_operation_erod_rule` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_erod_rule_item_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_erod_rule_item_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophtroperationbooking_operation_erod_rule_item_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophtroperationbooking_operation_erod_rule_item_version');

		$this->createIndex('ophtroperationbooking_operation_erod_rule_item_aid_fk','ophtroperationbooking_operation_erod_rule_item_version','id');

		$this->addColumn('ophtroperationbooking_operation_erod_rule_item_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophtroperationbooking_operation_erod_rule_item_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophtroperationbooking_operation_erod_rule_item_version','version_id');
		$this->alterColumn('ophtroperationbooking_operation_erod_rule_item_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophtroperationbooking_operation_name_rule_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`theatre_id` int(10) unsigned DEFAULT NULL,
	`name` varchar(64) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1900-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_ophtroperationbooking_operation_name_rt_id_fk` (`theatre_id`),
	KEY `acv_ophtroperationbooking_operation_name_r_lmui_fk` (`last_modified_user_id`),
	KEY `acv_ophtroperationbooking_operation_name_r_cid_fk` (`created_user_id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_name_rt_id_fk` FOREIGN KEY (`theatre_id`) REFERENCES `ophtroperationbooking_operation_theatre` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_name_r_cid_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_name_r_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophtroperationbooking_operation_name_rule_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophtroperationbooking_operation_name_rule_version');

		$this->createIndex('ophtroperationbooking_operation_name_rule_aid_fk','ophtroperationbooking_operation_name_rule_version','id');

		$this->addColumn('ophtroperationbooking_operation_name_rule_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophtroperationbooking_operation_name_rule_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophtroperationbooking_operation_name_rule_version','version_id');
		$this->alterColumn('ophtroperationbooking_operation_name_rule_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophtroperationbooking_operation_priority_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(128) NOT NULL,
	`display_order` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_ophtroperationbooking_operation_priority_lmui_fk` (`last_modified_user_id`),
	KEY `acv_ophtroperationbooking_operation_priority_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_priority_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_priority_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophtroperationbooking_operation_priority_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophtroperationbooking_operation_priority_version');

		$this->createIndex('ophtroperationbooking_operation_priority_aid_fk','ophtroperationbooking_operation_priority_version','id');
		$this->addForeignKey('ophtroperationbooking_operation_priority_aid_fk','ophtroperationbooking_operation_priority_version','id','ophtroperationbooking_operation_priority','id');

		$this->addColumn('ophtroperationbooking_operation_priority_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophtroperationbooking_operation_priority_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophtroperationbooking_operation_priority_version','version_id');
		$this->alterColumn('ophtroperationbooking_operation_priority_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophtroperationbooking_operation_procedures_procedures_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`element_id` int(10) unsigned NOT NULL,
	`proc_id` int(10) unsigned NOT NULL,
	`display_order` int(10) unsigned NOT NULL DEFAULT '0',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_roperationbooking_operation_procedures_procedures_lmui_fk` (`last_modified_user_id`),
	KEY `acv_ophtroperationbooking_operation_procedures_procedures_cui_fk` (`created_user_id`),
	KEY `acv_ophtroperationbooking_operation_procedures_procedures_ele_fk` (`element_id`),
	KEY `acv_ophtroperationbooking_operation_procedures_procedures_lku_fk` (`proc_id`),
	CONSTRAINT `acv_roperationbooking_operation_procedures_procedures_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_procedures_procedures_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_procedures_procedures_ele_fk` FOREIGN KEY (`element_id`) REFERENCES `et_ophtroperationbooking_operation` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_procedures_procedures_lku_fk` FOREIGN KEY (`proc_id`) REFERENCES `proc` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophtroperationbooking_operation_procedures_procedures_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophtroperationbooking_operation_procedures_procedures_version');

		$this->createIndex('ophtroperationbooking_operation_procedures_procedures_aid_fk','ophtroperationbooking_operation_procedures_procedures_version','id');

		$this->addColumn('ophtroperationbooking_operation_procedures_procedures_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophtroperationbooking_operation_procedures_procedures_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophtroperationbooking_operation_procedures_procedures_version','version_id');
		$this->alterColumn('ophtroperationbooking_operation_procedures_procedures_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophtroperationbooking_operation_sequence_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`firm_id` int(10) unsigned DEFAULT NULL,
	`theatre_id` int(10) unsigned NOT NULL,
	`start_date` date NOT NULL,
	`start_time` time NOT NULL,
	`end_time` time NOT NULL,
	`end_date` date DEFAULT NULL,
	`interval_id` int(10) unsigned NOT NULL,
	`weekday` tinyint(1) DEFAULT NULL,
	`week_selection` tinyint(1) DEFAULT NULL,
	`consultant` tinyint(1) unsigned NOT NULL DEFAULT '1',
	`paediatric` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`anaesthetist` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`general_anaesthetic` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`last_generate_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `acv_ophtroperationbooking_operation_sequence_firm_id_fk` (`firm_id`),
	KEY `acv_ophtroperationbooking_operation_sequence_theatre_id_fk` (`theatre_id`),
	KEY `acv_ophtroperationbooking_operation_sequence_interval_id_fk` (`interval_id`),
	KEY `acv_ophtroperationbooking_operation_sequence_lmui_fk` (`last_modified_user_id`),
	KEY `acv_ophtroperationbooking_operation_sequence_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_sequence_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_sequence_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_sequence_interval_id_fk` FOREIGN KEY (`interval_id`) REFERENCES `ophtroperationbooking_operation_sequence_interval` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_sequence_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_sequence_theatre_id_fk` FOREIGN KEY (`theatre_id`) REFERENCES `ophtroperationbooking_operation_theatre` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophtroperationbooking_operation_sequence_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophtroperationbooking_operation_sequence_version');

		$this->createIndex('ophtroperationbooking_operation_sequence_aid_fk','ophtroperationbooking_operation_sequence_version','id');

		$this->addColumn('ophtroperationbooking_operation_sequence_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophtroperationbooking_operation_sequence_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophtroperationbooking_operation_sequence_version','version_id');
		$this->alterColumn('ophtroperationbooking_operation_sequence_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophtroperationbooking_operation_sequence_interval_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(32) NOT NULL,
	`display_order` int(10) unsigned NOT NULL DEFAULT '0',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_ophtroperationbooking_operation_sequencei_lmui_fk` (`last_modified_user_id`),
	KEY `acv_ophtroperationbooking_operation_sequencei_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_sequencei_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_sequencei_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophtroperationbooking_operation_sequence_interval_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophtroperationbooking_operation_sequence_interval_version');

		$this->createIndex('ophtroperationbooking_operation_sequence_interval_aid_fk','ophtroperationbooking_operation_sequence_interval_version','id');

		$this->addColumn('ophtroperationbooking_operation_sequence_interval_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophtroperationbooking_operation_sequence_interval_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophtroperationbooking_operation_sequence_interval_version','version_id');
		$this->alterColumn('ophtroperationbooking_operation_sequence_interval_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophtroperationbooking_operation_session_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`sequence_id` int(10) unsigned NOT NULL,
	`firm_id` int(10) unsigned DEFAULT NULL,
	`date` date NOT NULL,
	`start_time` time NOT NULL,
	`end_time` time NOT NULL,
	`comments` text,
	`available` tinyint(1) unsigned NOT NULL DEFAULT '1',
	`consultant` tinyint(1) unsigned NOT NULL DEFAULT '1',
	`paediatric` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`anaesthetist` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`general_anaesthetic` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`theatre_id` int(10) unsigned NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `acv_ophtroperationbooking_operation_session_sequence_id_fk` (`sequence_id`),
	KEY `acv_ophtroperationbooking_operation_session_firm_id_fk` (`firm_id`),
	KEY `acv_ophtroperationbooking_operation_session_theatre_id_fk` (`theatre_id`),
	KEY `acv_ophtroperationbooking_operation_session_lmui_fk` (`last_modified_user_id`),
	KEY `acv_ophtroperationbooking_operation_session_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_session_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_session_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_session_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_session_sequence_id_fk` FOREIGN KEY (`sequence_id`) REFERENCES `ophtroperationbooking_operation_sequence` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_session_theatre_id_fk` FOREIGN KEY (`theatre_id`) REFERENCES `ophtroperationbooking_operation_theatre` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophtroperationbooking_operation_session_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophtroperationbooking_operation_session_version');

		$this->createIndex('ophtroperationbooking_operation_session_aid_fk','ophtroperationbooking_operation_session_version','id');

		$this->addColumn('ophtroperationbooking_operation_session_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophtroperationbooking_operation_session_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophtroperationbooking_operation_session_version','version_id');
		$this->alterColumn('ophtroperationbooking_operation_session_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophtroperationbooking_operation_status_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) DEFAULT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_ophtroperationbooking_operation_status_lmui_fk` (`last_modified_user_id`),
	KEY `acv_ophtroperationbooking_operation_status_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_status_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_status_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophtroperationbooking_operation_status_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophtroperationbooking_operation_status_version');

		$this->createIndex('ophtroperationbooking_operation_status_aid_fk','ophtroperationbooking_operation_status_version','id');

		$this->addColumn('ophtroperationbooking_operation_status_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophtroperationbooking_operation_status_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophtroperationbooking_operation_status_version','version_id');
		$this->alterColumn('ophtroperationbooking_operation_status_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophtroperationbooking_operation_theatre_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) DEFAULT NULL,
	`site_id` int(10) unsigned NOT NULL,
	`code` varchar(4) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`display_order` int(10) NOT NULL DEFAULT '1',
	`ward_id` int(10) unsigned DEFAULT NULL,
	`deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `acv_ophtroperationbooking_operation_theatre_site_id_fk` (`site_id`),
	KEY `acv_ophtroperationbooking_operation_theatre_lmui_fk` (`last_modified_user_id`),
	KEY `acv_ophtroperationbooking_operation_theatre_cui_fk` (`created_user_id`),
	KEY `acv_ophtroperationbooking_operation_theatre_ward_id_fk` (`ward_id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_theatre_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_theatre_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_theatre_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_theatre_ward_id_fk` FOREIGN KEY (`ward_id`) REFERENCES `ophtroperationbooking_operation_ward` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophtroperationbooking_operation_theatre_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophtroperationbooking_operation_theatre_version');

		$this->createIndex('ophtroperationbooking_operation_theatre_aid_fk','ophtroperationbooking_operation_theatre_version','id');
		$this->addForeignKey('ophtroperationbooking_operation_theatre_aid_fk','ophtroperationbooking_operation_theatre_version','id','ophtroperationbooking_operation_theatre','id');

		$this->addColumn('ophtroperationbooking_operation_theatre_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophtroperationbooking_operation_theatre_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophtroperationbooking_operation_theatre_version','version_id');
		$this->alterColumn('ophtroperationbooking_operation_theatre_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophtroperationbooking_operation_ward_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`site_id` int(10) unsigned NOT NULL,
	`name` varchar(255) NOT NULL,
	`long_name` varchar(255) NOT NULL,
	`directions` varchar(255) NOT NULL,
	`restriction` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`code` varchar(10) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_ophtroperationbooking_operation_ward_site_id_fk` (`site_id`),
	KEY `acv_ophtroperationbooking_operation_ward_lmui_fk` (`last_modified_user_id`),
	KEY `acv_ophtroperationbooking_operation_ward_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_ward_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_ward_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_operation_ward_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophtroperationbooking_operation_ward_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophtroperationbooking_operation_ward_version');

		$this->createIndex('ophtroperationbooking_operation_ward_aid_fk','ophtroperationbooking_operation_ward_version','id');
		$this->addForeignKey('ophtroperationbooking_operation_ward_aid_fk','ophtroperationbooking_operation_ward_version','id','ophtroperationbooking_operation_ward','id');

		$this->addColumn('ophtroperationbooking_operation_ward_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophtroperationbooking_operation_ward_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophtroperationbooking_operation_ward_version','version_id');
		$this->alterColumn('ophtroperationbooking_operation_ward_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophtroperationbooking_scheduleope_schedule_options_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(128) NOT NULL,
	`display_order` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_ophtroperationbooking_scheduleope_schedule_options_lmui_fk` (`last_modified_user_id`),
	KEY `acv_ophtroperationbooking_scheduleope_schedule_options_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_ophtroperationbooking_scheduleope_schedule_options_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_scheduleope_schedule_options_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophtroperationbooking_scheduleope_schedule_options_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophtroperationbooking_scheduleope_schedule_options_version');

		$this->createIndex('ophtroperationbooking_scheduleope_schedule_options_aid_fk','ophtroperationbooking_scheduleope_schedule_options_version','id');
		$this->addForeignKey('ophtroperationbooking_scheduleope_schedule_options_aid_fk','ophtroperationbooking_scheduleope_schedule_options_version','id','ophtroperationbooking_scheduleope_schedule_options','id');

		$this->addColumn('ophtroperationbooking_scheduleope_schedule_options_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophtroperationbooking_scheduleope_schedule_options_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophtroperationbooking_scheduleope_schedule_options_version','version_id');
		$this->alterColumn('ophtroperationbooking_scheduleope_schedule_options_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->execute("
CREATE TABLE `ophtroperationbooking_waiting_list_contact_rule_version` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`parent_rule_id` int(10) unsigned DEFAULT NULL,
	`rule_order` int(10) unsigned NOT NULL DEFAULT '0',
	`site_id` int(10) unsigned DEFAULT NULL,
	`service_id` int(10) unsigned DEFAULT NULL,
	`firm_id` int(10) unsigned DEFAULT NULL,
	`is_child` tinyint(1) unsigned DEFAULT NULL,
	`name` varchar(64) NOT NULL,
	`telephone` varchar(64) NOT NULL,
	`last_modified_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`last_modified_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	`created_user_id` int(10) unsigned NOT NULL DEFAULT '1',
	`created_date` datetime NOT NULL DEFAULT '1901-01-01 00:00:00',
	PRIMARY KEY (`id`),
	KEY `acv_ophtroperationbooking_waiting_list_cr_parent_rule_id_fk` (`parent_rule_id`),
	KEY `acv_ophtroperationbooking_waiting_list_cr_site_id_fk` (`site_id`),
	KEY `acv_ophtroperationbooking_waiting_list_cr_service_id_fk` (`service_id`),
	KEY `acv_ophtroperationbooking_waiting_list_cr_firm_id_fk` (`firm_id`),
	KEY `acv_ophtroperationbooking_waiting_list_cr_lmui_fk` (`last_modified_user_id`),
	KEY `acv_ophtroperationbooking_waiting_list_cr_cui_fk` (`created_user_id`),
	CONSTRAINT `acv_ophtroperationbooking_waiting_list_cr_parent_rule_id_fk` FOREIGN KEY (`parent_rule_id`) REFERENCES `ophtroperationbooking_waiting_list_contact_rule` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_waiting_list_cr_site_id_fk` FOREIGN KEY (`site_id`) REFERENCES `site` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_waiting_list_cr_service_id_fk` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_waiting_list_cr_firm_id_fk` FOREIGN KEY (`firm_id`) REFERENCES `firm` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_waiting_list_cr_lmui_fk` FOREIGN KEY (`last_modified_user_id`) REFERENCES `user` (`id`),
	CONSTRAINT `acv_ophtroperationbooking_waiting_list_cr_cui_fk` FOREIGN KEY (`created_user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		$this->alterColumn('ophtroperationbooking_waiting_list_contact_rule_version','id','int(10) unsigned NOT NULL');
		$this->dropPrimaryKey('id','ophtroperationbooking_waiting_list_contact_rule_version');

		$this->createIndex('ophtroperationbooking_waiting_list_contact_rule_aid_fk','ophtroperationbooking_waiting_list_contact_rule_version','id');

		$this->addColumn('ophtroperationbooking_waiting_list_contact_rule_version','version_date',"datetime not null default '1900-01-01 00:00:00'");

		$this->addColumn('ophtroperationbooking_waiting_list_contact_rule_version','version_id','int(10) unsigned NOT NULL');
		$this->addPrimaryKey('version_id','ophtroperationbooking_waiting_list_contact_rule_version','version_id');
		$this->alterColumn('ophtroperationbooking_waiting_list_contact_rule_version','version_id','int(10) unsigned NOT NULL AUTO_INCREMENT');

		$this->addColumn('ophtroperationbooking_admission_letter_warning_rule_type','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_admission_letter_warning_rule_type_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_cancellation_reason','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_cancellation_reason_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_priority','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_priority_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_sequence_interval','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_sequence_interval_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_ward','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_operation_ward_version','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_scheduleope_schedule_options','deleted','tinyint(1) unsigned not null');
		$this->addColumn('ophtroperationbooking_scheduleope_schedule_options_version','deleted','tinyint(1) unsigned not null');
	}

	public function down()
	{
		$this->dropColumn('et_ophtroperationbooking_diagnosis','deleted');
		$this->dropColumn('et_ophtroperationbooking_operation','deleted');
		$this->dropColumn('et_ophtroperationbooking_scheduleope','deleted');

		$this->dropColumn('ophtroperationbooking_admission_letter_warning_rule_type','deleted');
		$this->dropColumn('ophtroperationbooking_operation_cancellation_reason','deleted');
		$this->dropColumn('ophtroperationbooking_operation_priority','deleted');
		$this->dropColumn('ophtroperationbooking_operation_ward','deleted');
		$this->dropColumn('ophtroperationbooking_scheduleope_schedule_options','deleted');

		$this->dropTable('et_ophtroperationbooking_diagnosis_version');
		$this->dropTable('et_ophtroperationbooking_operation_version');
		$this->dropTable('et_ophtroperationbooking_scheduleope_version');
		$this->dropTable('ophtroperationbooking_admission_letter_warning_rule_version');
		$this->dropTable('ophtroperationbooking_admission_letter_warning_rule_type_version');
		$this->dropTable('ophtroperationbooking_letter_contact_rule_version');
		$this->dropTable('ophtroperationbooking_operation_booking_version');
		$this->dropTable('ophtroperationbooking_operation_cancellation_reason_version');
		$this->dropTable('ophtroperationbooking_operation_date_letter_sent_version');
		$this->dropTable('ophtroperationbooking_operation_erod_version');
		$this->dropTable('ophtroperationbooking_operation_erod_rule_version');
		$this->dropTable('ophtroperationbooking_operation_erod_rule_item_version');
		$this->dropTable('ophtroperationbooking_operation_name_rule_version');
		$this->dropTable('ophtroperationbooking_operation_priority_version');
		$this->dropTable('ophtroperationbooking_operation_procedures_procedures_version');
		$this->dropTable('ophtroperationbooking_operation_sequence_version');
		$this->dropTable('ophtroperationbooking_operation_sequence_interval_version');
		$this->dropTable('ophtroperationbooking_operation_session_version');
		$this->dropTable('ophtroperationbooking_operation_status_version');
		$this->dropTable('ophtroperationbooking_operation_theatre_version');
		$this->dropTable('ophtroperationbooking_operation_ward_version');
		$this->dropTable('ophtroperationbooking_scheduleope_schedule_options_version');
		$this->dropTable('ophtroperationbooking_waiting_list_contact_rule_version');
	}
}
