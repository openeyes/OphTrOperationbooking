<?php
/**
 * Created by PhpStorm.
 * User: msmith
 * Date: 22/03/2014
 * Time: 10:50
 */

class OphTrOperationbookingEventControllerTest  extends CDbTestCase
{
	static public function setupBeforeClass()
	{
		Yii::import('application.modules.OphTrOperationbooking.controllers.*');
	}

	public $fixtures = array(
			'patients' => 'Patient',
			'referral_types' => 'ReferralType',
			'referrals' => 'Referral'
	);

	public function getOphTrOperationbookingEventController($methods = null)
	{
		return $this->getMockBuilder('OphTrOperationbookingEventController')
				->setConstructorArgs(array('OphTrOperationbookingEventController', new BaseEventTypeModule('OphTrOperationbooking',null)))
				->setMethods($methods)
				->getMock();
	}

	public function testGetReferralChoices()
	{
		$test = $this->getOphTrOperationbookingEventController();
		$test->patient = $this->patients('patient1');

		$this->assertEquals(array($this->referrals('referral3'), $this->referrals('referral1')), $test->getReferralChoices());
	}

	public function testGetReferralChoices_forElement()
	{
		$test = $this->getOphTrOperationbookingEventController();
		$test->patient = $this->patients('patient1');

		$element = ComponentStubGenerator::generate('Element_OphTrOperationbooking_Operation', array('referral_id' => $this->referrals('referral4')->id, 'referral' => $this->referrals('referral4')));
		$referrals = $test->getReferralChoices($element);

		$this->assertEquals(array($this->referrals('referral3'), $this->referrals('referral1'), $this->referrals('referral4')), $referrals);
	}
}