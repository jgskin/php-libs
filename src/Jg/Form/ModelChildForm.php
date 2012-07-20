<?php 
namespace Jg\Form;

use \Jg\Validator\ValidatorSchema;
use \Jg\Form\Field\FormFieldSchema;
use \Jg\Form\Field\FormField;

abstract class ModelChildForm extends \BaseFormDoctrine
{
  protected $saveTrigger = 'save_it';
  protected $collectionsToClear = array();
  protected $isModel = false;
  
  public function setup()
  {
    $this->validatorSchema = new ValidatorSchema();
    $this->validatorSchema->setBypassTrigger(array($this, 'needValidation'));
    
    $this->errorSchema = new \sfValidatorErrorSchema($this->validatorSchema);
    
    $formName = \Jg\Util\String::unCamelize(trim($this->getModelName(), '\\'));
    
    $this->widgetSchema->setNameFormat($formName.'[%s]');

    $this->setupInheritance();

    //model validation
    $this->validatorSchema->setPostValidator(new \Jg\Validator\ModelValidator(null, array('form' => $this)));
    
    $this->configureSaveTrigger();
    
    parent::setup();
  }
  
  public function getModelName()
  {
    $modelName = \Jg\Util\String::removeNamespace(get_called_class());
    $modelName = str_replace('Form', '', $modelName);
    return $modelName;
  }

  protected function doUpdateObject($values)
  {
    foreach (array_keys($values) as $key) {
      if ($this->object->hasReference($key)) {
        unset($values[$key]);
      }
    }

    parent::doUpdateObject($values);
  }
  
  public function updateObject($values = null)
  {
    if (null === $values)
    {
      $values = $this->values;
    }

    if ($this->canUpdateObject($values)) 
    {
      $values = $this->processValues($values);

      $this->doUpdateObject($values);
      
      $this->updateAction();
      $this->clearChildrenEntities();

      // embedded forms
      $this->updateObjectEmbeddedForms($values);
    }

    return $this->getObject();
  }
  
  public function isModel()
  {
    return $this->isModel;
  }
  
  public function setIsModel($value)
  {
    $this->isModel = (boolean) $value;
    
    if ($this->saveTrigger) {
      $this->defaults[$this->saveTrigger] = false;
    }
  }
  
  protected function updateAction()
  {
    $this->object->parentUpdate();
  }
  
  public function needValidation($values)
  {
    return $this->canUpdateObject($values);
  }
  
  protected function canUpdateObject($values)
  {
    return $this->saveTrigger && isset($values[$this->saveTrigger]) && $values[$this->saveTrigger];
  }
  
  protected function configureSaveTrigger()
  {
    if ($this->saveTrigger) 
    {
      $this->widgetSchema[$this->saveTrigger] = new \sfWidgetFormInputCheckbox(array('label' => $this->getSaveTriggerLabel()));
      $this->validatorSchema[$this->saveTrigger] = new \sfValidatorBoolean(array('required' => false));
      $this->defaults[$this->saveTrigger] = $this->object->isNew() ? false : true;
    }
  }
  
  protected function clearChildrenEntities()
  {
    if ($this->collectionsToClear) 
    {
      foreach ($this->collectionsToClear as $collectionName) 
      {
        $this->object->$collectionName->clear();
      }
    }
  }
  
  protected function getSaveTriggerLabel()
  {
    return $this->object->__toString();
  }
}
