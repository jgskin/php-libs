<?php
namespace Jg\Doctrine\Behavior;

use \Jg\Doctrine\Listener\PluggableValidatorListener;
use \Jg\Doctrine\Validator\BaseValidator;
use \Jg\Util\ObjectDataHolder\Manager;

class PluggableValidatorBehavior extends \Doctrine_Template
{ 
  public function setTableDefinition() {
    $this->addListener(new PluggableValidatorListener());
  }
}
