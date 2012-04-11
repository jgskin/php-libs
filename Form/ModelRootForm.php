<?php

namespace Jg\Form;

abstract class ModelRootForm extends ModelChildForm
{
  protected $saveTrigger = false;
  
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    parent::bind($taintedValues, $taintedFiles);

    if (count($this->errorSchema) === 0)
    {
      $this->updateObject();
      
      try
      {
        $this->values = $this->validatorSchema->postCleanLoop($this->values);
      } catch (\sfValidatorErrorSchema $e)
      {
        $this->values = array();
        $this->errorSchema = $e;
      }
    }
  }
  
  protected function updateAction()
  {
    //do nothing
  }
  
  protected function canUpdateObject($values)
  {
    return true;
  }

}
