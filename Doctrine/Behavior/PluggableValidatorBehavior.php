<?php
namespace Jg\Doctrine\Behavior;

use \Jg\Doctrine\Listener\PluggableValidatorListener;
use \Jg\Doctrine\Validator\BaseValidator;
use \Jg\Util\ObjectDataHolder\Manager;

class PluggableValidatorBehavior extends \Doctrine_Template
{ 
  public function getHolder()
  {
    $manager = Manager::getInstance();
    return $manager->getHolder($this->getInvoker());
  }
  
  public function getValidators()
  {
    $invoker_holder = $this->getHolder(); 
    return isset($invoker_holder->pluggable_validators) ? $invoker_holder->pluggable_validators : array();
  }
  
  public function addValidator(BaseValidator $validator)
  {
    $invoker_holder = $this->getHolder(); 
    
    if (!isset($invoker_holder->pluggable_validators)) {
      $invoker_holder->pluggable_validators = array();
    }
    
    $invoker_holder->pluggable_validators[$validator::IDENTIFIER] = $validator;
  }
  
  public function removeValidator($validator_identifier)
  {
    $invoker_holder = $this->getHolder();
    if (isset($invoker_holder->pluggable_validators[$validator_identifier])) {
      unset($invoker_holder->pluggable_validators[$validator_identifier]);
    }
  }
  
  public function setTableDefinition()
  {
    $this->addListener(new PluggableValidatorListener());
  }
  
}
