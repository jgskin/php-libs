<?php 
namespace Jg\Form\Field;

class FormField extends \sfFormField
{
  protected $form;
  
  public function setForm(\sfForm $form)
  {
    $this->form = $form;
  }
  
  public function getForm()
  {
    return $this->form;
  }
  
  public function getObject()
  {
    return $this->form->getObject();
  }

  /**
   * Recupera o valor do campo formatado pelo widget configurado
   *
   * @return string
   */
  public function getFormatedValue()
  {
    // widget configurado
    $widget = $this->widget;

    if ($widget->hasOption('formatmethod')) {
      $method = $widget->getOption('formatmethod');

      // utiliza o método configurado
      return call_user_func($method, $this->value);
    }

    // nenhum método para formatação configurado, recupera o valor original
    return $this->value;
  }
}