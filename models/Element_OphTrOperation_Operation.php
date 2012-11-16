	<?php /**
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

	/**
	 * This is the model class for table "et_ophtroperation_operation".
	 *
	 * The followings are the available columns in table:
	 * @property string $id
	 * @property integer $event_id
	 * @property integer $eye_id
	 * @property integer $consultant_required
	 * @property integer $anaesthetic_type_id
	 * @property integer $overnight_stay
	 * @property integer $site_id
	 * @property integer $priority_id
	 * @property string $decision_date
	 * @property string $comments
	 *
	 * The followings are the available model relations:
	 *
	 * @property ElementType $element_type
	 * @property EventType $eventType
	 * @property Event $event
	 * @property User $user
	 * @property User $usermodified
	 * @property Eye $eye
	 * @property OphTrOperation_Operation_Operations $procedures
	 * @property AnaestheticType $anaesthetic_type
	 * @property Site $site
	 * @property Element_OphTrOperation_Operation_Priority $priority
	 */

	class Element_OphTrOperation_Operation extends BaseEventTypeElement
	{
		public $service;

		/**
		 * Returns the static model of the specified AR class.
		 * @return the static model class
		 */
		public static function model($className = __CLASS__)
		{
			return parent::model($className);
		}

		/**
		 * @return string the associated database table name
		 */
		public function tableName()
		{
			return 'et_ophtroperation_operation';
		}

		/**
		 * @return array validation rules for model attributes.
		 */
		public function rules()
		{
			// NOTE: you should only define rules for those attributes that
			// will receive user inputs.
			return array(
				array('event_id, eye_id, consultant_required, anaesthetic_type_id, overnight_stay, site_id, priority_id, decision_date, comments, anaesthetist_required, total_duration, status_id, cancellation_date, cancellation_reason_id, cancellation_comment', 'safe'),
				array('eye_id, consultant_required, anaesthetic_type_id, overnight_stay, site_id, priority_id, decision_date', 'required'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, event_id, eye_id, consultant_required, anaesthetic_type_id, overnight_stay, site_id, priority_id, decision_date, comments, ', 'safe', 'on' => 'search'),
		);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'element_type' => array(self::HAS_ONE, 'ElementType', 'id','on' => "element_type.class_name='".get_class($this)."'"),
			'eventType' => array(self::BELONGS_TO, 'EventType', 'event_type_id'),
			'event' => array(self::BELONGS_TO, 'Event', 'event_id'),
			'user' => array(self::BELONGS_TO, 'User', 'created_user_id'),
			'usermodified' => array(self::BELONGS_TO, 'User', 'last_modified_user_id'),
			'eye' => array(self::BELONGS_TO, 'Eye', 'eye_id'),
			'procedures' => array(self::HAS_MANY, 'OphTrOperation_Operation_Operations', 'element_id'),
			'anaesthetic_type' => array(self::BELONGS_TO, 'AnaestheticType', 'anaesthetic_type_id'),
			'site' => array(self::BELONGS_TO, 'Site', 'site_id'),
			'priority' => array(self::BELONGS_TO, 'OphTrOperation_Operation_Priority', 'priority_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'event_id' => 'Event',
'eye_id' => 'Eyes',
'procedures' => 'Operations',
'consultant_required' => 'Consultant required',
'anaesthetic_type_id' => 'Anaesthetic type',
'overnight_stay' => 'Post operative stay',
'site_id' => 'Site',
'priority_id' => 'Priority',
'decision_date' => 'Decision date',
'comments' => 'Add comments',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('event_id', $this->event_id, true);

$criteria->compare('eye_id', $this->eye_id);
$criteria->compare('procedures', $this->procedures);
$criteria->compare('consultant_required', $this->consultant_required);
$criteria->compare('anaesthetic_type_id', $this->anaesthetic_type_id);
$criteria->compare('overnight_stay', $this->overnight_stay);
$criteria->compare('site_id', $this->site_id);
$criteria->compare('priority_id', $this->priority_id);
$criteria->compare('decision_date', $this->decision_date);
$criteria->compare('comments', $this->comments);
		
		return new CActiveDataProvider(get_class($this), array(
				'criteria' => $criteria,
			));
	}

	/**
	 * Set default values for forms on create
	 */
	public function setDefaultOptions()
	{
		if (Yii::app()->getController()->getAction()->id == 'create') {
			$this->eye_id = 1;
			$this->anaesthetic_type_id = 1;
			$this->site_id = 1;
			$this->priority_id = 1;
		}
	}


	public function getproc_defaults() {
		$ids = array();
		foreach (OphTrOperation_Operation_Defaults::model()->findAll() as $item) {
			$ids[] = $item->value_id;
		}
		return $ids;
	}

	protected function beforeSave()
	{
		return parent::beforeSave();
	}

	protected function afterSave()
	{
		if (!empty($_POST['MultiSelect_procedures'])) {

			$existing_ids = array();

			foreach (OphTrOperation_Operation_Operations::model()->findAll('element_id = :elementId', array(':elementId' => $this->id)) as $item) {
				$existing_ids[] = $item->proc_id;
			}

			foreach ($_POST['MultiSelect_procedures'] as $id) {
				if (!in_array($id,$existing_ids)) {
					$item = new OphTrOperation_Operation_Operations;
					$item->element_id = $this->id;
					$item->proc_id = $id;

					if (!$item->save()) {
						throw new Exception('Unable to save MultiSelect item: '.print_r($item->getErrors(),true));
					}
				}
			}

			foreach ($existing_ids as $id) {
				if (!in_array($id,$_POST['MultiSelect_procedures'])) {
					$item = OphTrOperation_Operation_Operations::model()->find('element_id = :elementId and proc_id = :lookupfieldId',array(':elementId' => $this->id, ':lookupfieldId' => $id));
					if (!$item->delete()) {
						throw new Exception('Unable to delete MultiSelect item: '.print_r($item->getErrors(),true));
					}
				}
			}
		}

		return parent::afterSave();
	}

	protected function beforeValidate()
	{
		return parent::beforeValidate();
	}
}
?>
