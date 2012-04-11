<?php
namespace Jg\Validator;

class ModelValidator extends \sfValidatorSchema
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('form');
  }

  /*
   * Validação do modelo
   */
  protected function doClean($value)
  {
    $modelo = $this->getForm()->getObject();
    
    $modelo->isValid();

    if ($modelo->getErrorStack()->count())
    {
      $erros = array();
      foreach ($modelo->getErrorStack()->toArray() as $k => $error)
      {
        $erro = new \sfValidatorError($this, implode(', ', \Hcrm::translateErrorCodes($modelo, $error)));
        if ($this->getOption('throw_global_error'))
        {
          throw $erro;
        }

        $erros[$k] = $erro;
      }

      throw new \sfValidatorErrorSchema($this, $erros);
    }

    return $value;
  }
  
  public function getForm()
  {
    return $this->getOption('form');
  }

}