<?php 
namespace Jg\Form\Field;

class FormField extends \sfFormField
{
  /**
   * Formulário o qual configurou o widget
   */
  protected $form;
  
  /**
   * Insere o formulário
   */
  public function setForm(\sfForm $form)
  {
    $this->form = $form;
  }
  
  /**
   * Recupera o formulário
   */
  public function getForm()
  {
    return $this->form;
  }
  
  /**
   * Recupera o objeto atrelado ao formulário
   */
  public function getObject()
  {
    return $this->form->getObject();
  }

  /**
   * Recupera uma versão iteravel da renderização do widget
   */
  public function getIterator($attributes = array())
  {
    // verifica a configuração do método iterador
    if (!is_callable($this->widget->getOption('iterator'))) {
      throw new Exception('Iterator not configured'); // alterar a exception
    }

    // formatação do nome do campo caso o mesmo pertença a um widget superior
    $name = 
      $this->parent 
      ? 
      $this->parent->getWidget()->generateName($this->name)
      : 
      $this->name;
    
    // executa o método iterador
    return call_user_func(
      $this->widget->getOption('iterator'),
      $name,
      $this->value,
      $attributes,
      $this->error
      );
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