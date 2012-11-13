<?php
namespace Jg\Form\Field;

/**
* Classe para configuração dos valores ao renderizar os campos
*
* @author Jessé Alves Galdino <galdino.jesse@gmail.com>
*/
class FormFieldConfigurator
{
  /**
   * repositório de campos
   */
  protected $fields = array();

  /**
   * Formulário base para renderização dos campos
   *
   * @var sfForm
   **/
  protected $form;

  /**
   * @param sfForm Formulário base para renderização dos campos
   */
  public function __construct(\sfForm $form)
  {
    $this->form = $form;
  }

  /**
   * Recupera a entidade responsável pela renderização do campo
   *
   * @param string $name Nome do campo
   */
  public function get($name)
  {
    $widgetschema = $this->form->getWidgetSchema();
    $taintedvalues = $this->form->getTaintedValues();
    $errorschema = $this->form->getErrorSchema();

    if (!isset($this->fields[$name])) {
      if (!$widget = $widgetschema[$name]) {
        throw new \InvalidArgumentException(sprintf('Widget "%s" does not exist.', $name));
      }

      if ($this->form->isBound()) {
        $value = isset($taintedvalues[$name]) ? $taintedvalues[$name] : null;
      } else if ($default = $this->form->getDefault($name)) {
        $value = $default;
      } else {
        $value = $widget instanceof \sfWidgetFormSchema ? $widget->getDefaults() : $widget->getDefault();
      }

      if ($widget instanceof \sfWidgetFormSchema) {
        $class = 'Jg\Form\Field\FormFieldSchema';
        $form = $this->form->getEmbeddedForm($name);
      } else {
        $class = 'Jg\Form\Field\FormField';
        $form = $this->form;
      }

      $this->fields[$name] = new $class($widget, $this->form->getFormFieldSchema(),
        $name, $value, $errorschema[$name]);
      $this->fields[$name]->setForm($form);
    }

    return $this->fields[$name];
  }
}