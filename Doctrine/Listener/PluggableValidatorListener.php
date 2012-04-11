<?php
namespace Jg\Doctrine\Listener;

class PluggableValidatorListener extends \Doctrine_Record_Listener
{
  public function preValidate(\Doctrine_Event $event)
  {
    $invoker = $event->getInvoker();
    
    if ($validators = $invoker->getValidators()) {
      foreach ($validators as $validator) {
        $validator->validate();
      }
    }
  }
}