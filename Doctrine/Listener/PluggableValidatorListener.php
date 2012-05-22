<?php
namespace Jg\Doctrine\Listener;

class PluggableValidatorListener extends \Doctrine_Record_Listener
{
  public function preValidate(\Doctrine_Event $event)
  {
    $invoker = $event->getInvoker();

    if (count($invoker->_validators)) {
      foreach ($invoker->_validators as $validator) {
        //o padrão é que as validações se encontrem na própria classe
        if (!is_array($validator)) {
          $validator = array($invoker, $validator);
        }

        if (!is_callable($validator)) {
          throw new \RuntimeException('Favor verificar as regras de validação');
        }

        call_user_func($validator);
      }
    }
  }
}