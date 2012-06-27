<?php
namespace Jg\Validator;

class ModelValidator extends \sfValidatorSchema
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('form');
  }

  protected function doClean($value)
  {
    $modelo = $this->getForm()->getObject();
    
    $modelo->isValid();

    if ($modelo->getErrorStack()->count())
    {
      $error_schema = array();

      foreach ($modelo->getErrorStack() as $k => $errors) {

        $validator_errors = array();

        foreach ($errors as $error) {
          $validator_error = new \sfValidatorError($this, $error);

          if ($this->getOption('throw_global_error')) {
            throw $validator_error;
          }
          
          $validator_errors[] = $validator_error;
        }

        if (count($validator_errors) > 1) {
          $error_schema[$k] = new \sfValidatorErrorSchema($this, $validator_errors);
        } else {
          $error_schema[$k] = reset($validator_errors);
        }

      }

      throw new \sfValidatorErrorSchema($this, $error_schema);
    }

    return $value;
  }
  
  public function getForm()
  {
    return $this->getOption('form');
  }

}