<?php

class m140903_092121_rtt2 extends OEMigration
{
	public function safeUp()
	{
		$this->createOeTable(
			'ophtroperationbooking_anaesthetic_choice',
			array(
				'id' => 'pk',
				'name' => 'string not null',
				'display_order' => 'integer not null',
			)
		);

		$this->addColumn('et_ophtroperationbooking_operation', 'organising_admission_user_id', 'integer unsigned');
		$this->addColumn('et_ophtroperationbooking_operation_version', 'organising_admission_user_id', 'integer unsigned');

		$this->addColumn('et_ophtroperationbooking_operation', 'anaesthetist_preop_assessment', 'boolean');
		$this->addColumn('et_ophtroperationbooking_operation_version', 'anaesthetist_preop_assessment', 'boolean');

		$this->addColumn('et_ophtroperationbooking_operation', 'anaesthetic_choice_id', 'integer');
		$this->addColumn('et_ophtroperationbooking_operation_version', 'anaesthetic_choice_id', 'integer');

		$this->addForeignKey('et_ophtroperationbooking_operation_anaesthetic_choice_fk', 'et_ophtroperationbooking_operation', 'anaesthetic_choice_id', 'ophtroperationbooking_anaesthetic_choice', 'id');

		$this->addColumn('et_ophtroperationbooking_operation', 'stop_medication', 'boolean');
		$this->addColumn('et_ophtroperationbooking_operation_version', 'stop_medication', 'boolean');

		$this->addColumn('et_ophtroperationbooking_operation', 'stop_medication_details', 'string');
		$this->addColumn('et_ophtroperationbooking_operation_version', 'stop_medication_details', 'string');

		$this->initialiseData(__DIR__);
	}

	public function safeDown()
	{
		$this->dropColumn('et_ophtroperationbooking_operation', 'organising_admission_user_id');
		$this->dropColumn('et_ophtroperationbooking_operation_version', 'organising_admission_user_id');

		$this->dropColumn('et_ophtroperationbooking_operation', 'anaesthetist_preop_assessment');
		$this->dropColumn('et_ophtroperationbooking_operation_version', 'anaesthetist_preop_assessment');

		$this->dropForeignKey('et_ophtroperationbooking_operation_anaesthetic_choice_fk', 'et_ophtroperationbooking_operation');

		$this->dropColumn('et_ophtroperationbooking_operation', 'anaesthetic_choice_id');
		$this->dropColumn('et_ophtroperationbooking_operation_version', 'anaesthetic_choice_id');

		$this->dropColumn('et_ophtroperationbooking_operation', 'stop_medication');
		$this->dropColumn('et_ophtroperationbooking_operation_version', 'stop_medication');

		$this->dropColumn('et_ophtroperationbooking_operation', 'stop_medication_details');
		$this->dropColumn('et_ophtroperationbooking_operation_version', 'stop_medication_details');

		$this->dropOeTable('ophtroperationbooking_anaesthetic_choice');
	}
}
