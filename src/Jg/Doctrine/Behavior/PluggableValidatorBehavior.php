<?php
namespace Jg\Doctrine\Behavior;

use \Jg\Doctrine\Listener\PluggableValidatorListener;
use \Jg\Doctrine\Validator\BaseValidator;
use \Jg\Util\ObjectDataHolder\Manager;

class PluggableValidatorBehavior extends \Doctrine_Template
{ 
  public function removeValidator($name) {
    if (!array_key_exists($name, $this->_invoker->_validators)) {
      throw new RuntimeException("O Validador $name não existe");
    }

    unset($this->_invoker->_validators[$name]);
  }

  public function setTableDefinition() {
    $this->addListener(new PluggableValidatorListener());
  }
}
