<?php
namespace Jg\Symfony\Form\Validator;

class CallableValidator extends \sfValidatorSchema
{
  protected $_errors = array();

  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('callable');
  }

  protected function doClean($values)
  {
    $callable = $this->getOption('callable');
    
    $return = call_user_func($callable, $this, $values);
    
    if ($this->_errors) {
      throw new \sfValidatorErrorSchema($this, $this->_errors);
    }

    return $return;
  }
  
  public function addError($key, $content)
  {
    $error = new \sfValidatorError($this, $content);
    
    if ($this->getOption('throw_global_error')) {
      throw $error;
    }
    
    $this->_errors[$key] = $error;
  }

}
