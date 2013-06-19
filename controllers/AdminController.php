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

class AdminController extends ModuleAdminController {
	public function actionViewERODRules() {
		$this->render('erodrules');
	}

	public function actionCreatePostOpDrug() {
		if (empty($_POST['name'])) {
			throw new Exception("Missing name");
		}

		if ($drug = PostopDrug::model()->find(array('order'=>'display_order desc'))) {
			$display_order = $drug->display_order+1;
		} else {
			$display_order = 1;
		}

		$drug = new PostopDrug;
		$drug->name = @$_POST['name'];
		$drug->display_order = $display_order;

		if (!$drug->save()) {
			echo json_encode(array('errors'=>$drug->getErrors()));
			return;
		}

		// TODO: this is a hack for the Orbis demo and should be removed when full site/subspecialty functionality has been implemented
		$specialty = Specialty::model()->find('code=?',array('OPH'));
		foreach (Site::model()->findAll('institution_id=?',array(1)) as $site) {
			foreach (Subspecialty::model()->findAll('specialty_id=?',array($specialty->id)) as $subspecialty) {
				$ssd = new PostopSiteSubspecialtyDrug;
				$ssd->site_id = $site->id;
				$ssd->subspecialty_id = $subspecialty->id;
				$ssd->drug_id = $drug->id;
				if (!$ssd->save()) {
					echo json_encode(array('errors'=>$ssd->getErrors()));
				}
			}
		}

		echo json_encode(array('id'=>$drug->id,'errors'=>array()));
	}

	public function actionUpdatePostOpDrug() {
		if (!$drug = PostopDrug::model()->findByPk(@$_POST['id'])) {
			throw new Exception("Drug not found: ".@$_POST['id']);
		}

		$drug->name = @$_POST['name'];
		if (!$drug->save()) {
			echo json_encode(array('errors'=>$drug->getErrors()));
			return;
		}

		echo json_encode(array('errors'=>array()));
	}

	public function actionDeletePostOpDrug($id) {
		if ($drug = PostopDrug::model()->findByPk($id)) {
			$drug->deleted = 1;
			if ($drug->save()) {
				echo "1";
				return;
			}
		}
		echo "0";
	}

	public function actionSortPostOpDrugs() {
		if (!empty($_POST['order'])) {
			foreach ($_POST['order'] as $i => $id) {
				if ($drug = PostopDrug::model()->findByPk($id)) {
					$drug->display_order = $i+1;
					if (!$drug->save()) {
						throw new Exception("Unable to save drug: ".print_r($drug->getErrors(),true));
					}
				}
			}
		}
	}

	public function actionEditERODRule($id) {
		if (!$erod = OphTrOperationbooking_Operation_EROD_Rule::model()->findByPk($id)) {
			throw new Exception("EROD rule not found: $id");
		}

		$errors = array();

		if (!empty($_POST)) {
			$erod->subspecialty_id = $_POST['OphTrOperationbooking_Operation_EROD_Rule']['subspecialty_id'];
			if (!$erod->save()) {
				$errors = $erod->getErrors();
			} else {
				$firm_ids = array();
				foreach ($erod->items as $item) {
					$firm_ids[] = $item['item_id'];
				}

				foreach ($_POST['Firms'] as $firm_id) {
					if (!in_array($firm_id,$firm_ids)) {
						$item = new OphTrOperationbooking_Operation_EROD_Rule_Item;
						$item->erod_rule_id = $erod->id;
						$item->item_type = 'firm';
						$item->item_id = $firm_id;
						if (!$item->save()) {
							$errors = array_merge($errors,$item->getErrors());
						}
					}
				}

				foreach ($firm_ids as $firm_id) {
					if (!in_array($firm_id,$_POST['Firms'])) {
						if (!$item = OphTrOperationbooking_Operation_EROD_Rule_Item::model()->find('erod_rule_id=? and item_type=? and item_id=?',array($erod->id,'firm',$firm_id))) {
							throw new Exception("Rule item not found: [$erod->id][firm][$firm_id]");
						}
						if (!$item->delete()) {
							throw new Exception("Rule item delete failed: ".print_r($item->getErrors(),true));
						}
					}
				}

				if (empty($errors)) {
					$this->redirect(array('/OphTrOperationbooking/admin/viewERODRules'));
				}
			}
		}

		$this->render('/admin/editerodrule',array(
			'erod' => $erod,
			'errors' => $errors,
		));
	}

	public function actionAddERODRule() {
		$errors = array();

		$erod = new OphTrOperationbooking_Operation_EROD_Rule;

		if (!empty($_POST)) {
			$erod->subspecialty_id = $_POST['OphTrOperationbooking_Operation_EROD_Rule']['subspecialty_id'];
			if (!$erod->save()) {
				$errors = $erod->getErrors();
			} else {
				$firm_ids = array();
				foreach ($erod->items as $item) {
					$firm_ids[] = $item['item_id'];
				}

				foreach ($_POST['Firms'] as $firm_id) {
					if (!in_array($firm_id,$firm_ids)) {
						$item = new OphTrOperationbooking_Operation_EROD_Rule_Item;
						$item->erod_rule_id = $erod->id;
						$item->item_type = 'firm';
						$item->item_id = $firm_id;
						if (!$item->save()) {
							$errors = array_merge($errors,$item->getErrors());
						}
					}
				}

				foreach ($firm_ids as $firm_id) {
					if (!in_array($firm_id,$_POST['Firms'])) {
						if (!$item = OphTrOperationbooking_Operation_EROD_Rule_Item::model()->find('erod_rule_id=? and item_type=? and item_id=?',array($erod->id,'firm',$firm_id))) {
							throw new Exception("Rule item not found: [$erod->id][firm][$firm_id]");
						}
						if (!$item->delete()) {
							throw new Exception("Rule item delete failed: ".print_r($item->getErrors(),true));
						}
					}
				}

				if (empty($errors)) {
					$this->redirect(array('/OphTrOperationbooking/admin/viewERODRules'));
				}
			}
		}

		$this->render('/admin/adderodrule',array(
			'erod' => $erod,
			'errors' => $errors,
		));
	}

	public function actionDeleteERODRules() {
		if (!empty($_POST['erod'])) {
			foreach ($_POST['erod'] as $erod_id) {
				if ($_erod = OphTrOperationbooking_Operation_EROD_Rule::model()->findByPk($erod_id)) {
					foreach ($_erod->items as $item) {
						if (!$item->delete()) {
							throw new Exception("Unable to delete rule item: ".print_r($item->getErrors(),true));
						}
					}
					if (!$_erod->delete()) {
						throw new Exception("Unable to delete erod rule: ".print_r($_erod->getErrors(),true));
					}
				}
			}
		}

		echo "1";
	}

	public function actionViewLetterContactRules() {
		$this->jsVars['OE_rule_model'] = 'LetterContactRule';

		$this->render('lettercontactrules',array(
			'data' => OphTrOperationbooking_Letter_Contact_Rule::model()->findAllAsTree(),
		));
	}

	public function actionTestLetterContactRules() {
		$site_id = @$_POST['lcr_site_id'];
		$subspecialty_id = @$_POST['lcr_subspecialty_id'];
		$theatre_id = @$_POST['lcr_theatre_id'];
		$firm_id = @$_POST['lcr_firm_id'];

		$criteria = new CDbCriteria;
		$criteria->addCondition('parent_rule_id is null');
		$criteria->order = 'rule_order asc';

		$rule_ids = array();

		foreach (OphTrOperationbooking_Letter_Contact_Rule::model()->findAll($criteria) as $rule) {
			if ($rule->applies($site_id,$subspecialty_id,$theatre_id,$firm_id)) {
				$final = $rule->parse($site_id,$subspecialty_id,$theatre_id,$firm_id);
				echo json_encode(array($final->id));
				return;
			}
		}

		echo json_encode(array());
	}

	public function actionEditLetterContactRule($id) {
		if (!$rule = OphTrOperationbooking_Letter_Contact_Rule::model()->findByPk($id)) {
			throw new Exception("Letter contact rule not found: $id");
		}

		$errors = array();

		if (!empty($_POST)) {
			$rule->attributes = $_POST['OphTrOperationbooking_Letter_Contact_Rule'];

			if (!$rule->save()) {
				$errors = $rule->getErrors();
			} else {
				$this->redirect(array('/OphTrOperationbooking/admin/viewLetterContactRules'));
			}
		}

		$this->jsVars['OE_rule_model'] = 'LetterContactRule';

		$this->render('editlettercontactrule',array(
			'rule' => $rule,
			'errors' => $errors,
		));
	}

	public function actionDeleteLetterContactRule($id) {
		if (!$rule = OphTrOperationbooking_Letter_Contact_Rule::model()->findByPk($id)) {
			throw new Exception("Letter contact rule not found: $id");
		}

		$errors = array();

		if (!empty($_POST)) {
			if (@$_POST['delete']) {
				if (!$rule->delete()) {
					$errors = $rule->getErrors();
				} else {
					$this->redirect(array('/OphTrOperationbooking/admin/viewLetterContactRules'));
				}
			}
		}

		$this->jsVars['OE_rule_model'] = 'LetterContactRule';

		$this->render('deletelettercontactrule',array(
			'rule' => $rule,
			'errors' => $errors,
			'data' => OphTrOperationbooking_Letter_Contact_Rule::model()->findAllAsTree($rule,true,'textPlain'),
		));
	}

	public function actionAddLetterContactRule() {
		$rule = new OphTrOperationbooking_Letter_Contact_Rule;

		$errors = array();

		if (!empty($_POST)) {
			$rule->attributes = $_POST['OphTrOperationbooking_Letter_Contact_Rule'];

			if (!$rule->save()) {
				$errors = $rule->getErrors();
			} else {
				$this->redirect(array('/OphTrOperationbooking/admin/viewLetterContactRules'));
			}
		} else {
			if (isset($_GET['parent_rule_id'])) {
				$rule->parent_rule_id = $_GET['parent_rule_id'];
			}
		}

		$this->jsVars['OE_rule_model'] = 'LetterContactRule';

		$this->render('editlettercontactrule',array(
			'rule' => $rule,
			'errors' => $errors,
		));
	}

	public function actionViewLetterWarningRules() {
		$this->jsVars['OE_rule_model'] = 'LetterWarningRule';

		$this->render('letterwarningrules',array(
			'data' => OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findAllAsTree(),
		));
	}

	public function actionTestLetterWarningRules() {
		$site_id = @$_POST['lcr_site_id'];
		$subspecialty_id = @$_POST['lcr_subspecialty_id'];
		$theatre_id = @$_POST['lcr_theatre_id'];
		$firm_id = @$_POST['lcr_firm_id'];
		$is_child = @$_POST['lcr_is_child'];

		$criteria = new CDbCriteria;
		$criteria->addCondition('parent_rule_id is null');
		$criteria->addCondition('rule_type_id = :rule_type_id');
		$criteria->params[':rule_type_id'] = @$_POST['lcr_rule_type_id'];
		$criteria->order = 'rule_order asc';

		$rule_ids = array();

		foreach (OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findAll($criteria) as $rule) {
			if ($rule->applies($site_id, $is_child, $theatre_id, $subspecialty_id, $firm_id)) {
				$final = $rule->parse($site_id, $is_child, $theatre_id, $subspecialty_id, $firm_id);
				echo json_encode(array($final->id));
				return;
			}
		}

		echo json_encode(array());
	}

	public function actionEditLetterWarningRule($id) {
		if (!$rule = OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findByPk($id)) {
			throw new Exception("Letter warning rule not found: $id");
		}

		$errors = array();

		if (!empty($_POST)) {
			$rule->attributes = $_POST['OphTrOperationbooking_Admission_Letter_Warning_Rule'];

			if (!$rule->save()) {
				$errors = $rule->getErrors();
			} else {
				$this->redirect(array('/OphTrOperationbooking/admin/viewLetterWarningRules'));
			}
		}

		$this->jsVars['OE_rule_model'] = 'LetterWarningRule';

		$this->render('editletterwarningrule',array(
			'rule' => $rule,
			'errors' => $errors,
		));
	}

	public function actionAddLetterWarningRule() {
		$rule = new OphTrOperationbooking_Admission_Letter_Warning_Rule;

		$errors = array();

		if (!empty($_POST)) {
			$rule->attributes = $_POST['OphTrOperationbooking_Admission_Letter_Warning_Rule'];

			if (!$rule->save()) {
				$errors = $rule->getErrors();
			} else {
				$this->redirect(array('/OphTrOperationbooking/admin/viewLetterWarningRules'));
			}
		} else {
			if (isset($_GET['parent_rule_id'])) {
				$rule->parent_rule_id = $_GET['parent_rule_id'];
			}
		}

		$this->jsVars['OE_rule_model'] = 'LetterWarningRule';

		$this->render('editletterwarningrule',array(
			'rule' => $rule,
			'errors' => $errors,
		));
	}

	public function actionDeleteLetterWarningRule($id) {
		if (!$rule = OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findByPk($id)) {
			throw new Exception("Letter warning rule not found: $id");
		}

		$errors = array();

		if (!empty($_POST)) {
			if (@$_POST['delete']) {
				if (!$rule->delete()) {
					$errors = $rule->getErrors();
				} else {
					$this->redirect(array('/OphTrOperationbooking/admin/viewLetterWarningRules'));
				}
			}
		}

		$this->jsVars['OE_rule_model'] = 'LetterWarningRule';

		$this->render('deleteletterwarningrule',array(
			'rule' => $rule,
			'errors' => $errors,
			'data' => OphTrOperationbooking_Admission_Letter_Warning_Rule::model()->findAllAsTree($rule,true,'textPlain'),
		));
	}

	public function actionViewWaitingListContactRules() {
		$this->jsVars['OE_rule_model'] = 'WaitingListContactRule';

		$this->render('waitinglistcontactrules',array(
			'data' => OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAllAsTree(),
		));
	}

	public function actionTestWaitingListContactRules() {
		$site_id = @$_POST['lcr_site_id'];
		$service_id = @$_POST['lcr_service_id'];
		$firm_id = @$_POST['lcr_firm_id'];
		$is_child = @$_POST['lcr_is_child'];

		$criteria = new CDbCriteria;
		$criteria->addCondition('parent_rule_id is null');
		$criteria->order = 'rule_order asc';

		$rule_ids = array();

		foreach (OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAll($criteria) as $rule) {
			if ($rule->applies($site_id, $service_id, $firm_id, $is_child)) {
				$final = $rule->parse($site_id, $service_id, $firm_id, $is_child);
				echo json_encode(array($final->id));
				return;
			}
		}

		echo json_encode(array());
	}

	public function actionEditWaitingListContactRule($id) {
		if (!$rule = OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findByPk($id)) {
			throw new Exception("Waiting list contact rule not found: $id");
		}

		$errors = array();

		if (!empty($_POST)) {
			$rule->attributes = $_POST['OphTrOperationbooking_Waiting_List_Contact_Rule'];

			if (!$rule->save()) {
				$errors = $rule->getErrors();
			} else {
				$this->redirect(array('/OphTrOperationbooking/admin/viewWaitingListContactRules'));
			}
		}

		$this->jsVars['OE_rule_model'] = 'WaitingListContactRule';

		$this->render('editwaitinglistcontactrule',array(
			'rule' => $rule,
			'errors' => $errors,
		));
	}

	public function actionDeleteWaitingListContactRule($id) {
		if (!$rule = OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findByPk($id)) {
			throw new Exception("Waiting list contact rule not found: $id");
		}

		$errors = array();

		if (!empty($_POST)) {
			if (@$_POST['delete']) {
				if (!$rule->delete()) {
					$errors = $rule->getErrors();
				} else {
					$this->redirect(array('/OphTrOperationbooking/admin/viewWaitingListContactRules'));
				}
			}
		}

		$this->jsVars['OE_rule_model'] = 'WaitingListContactRule';

		$this->render('deletewaitinglistcontactrule',array(
			'rule' => $rule,
			'errors' => $errors,
			'data' => OphTrOperationbooking_Waiting_List_Contact_Rule::model()->findAllAsTree($rule,true,'textPlain'),
		));
	}
}
