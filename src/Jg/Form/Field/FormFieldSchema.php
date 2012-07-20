<?php 
namespace Jg\Form\Field;

class FormFieldSchema extends \sfFormFieldSchema
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
  
  public function offsetGet($name)
  {
    if (!isset($this->fields[$name]))
    {
      if (null === $widget = $this->widget[$name])
      {
        throw new \InvalidArgumentException(sprintf('Widget "%s" does not exist.', $name));
      }

      $error = isset($this->error[$name]) ? $this->error[$name] : null;

      if ($widget instanceof \sfWidgetFormSchema)
      {
        $class = 'Jg\Form\Field\FormFieldSchema';

        if ($error && !$error instanceof \sfValidatorErrorSchema)
        {
          $error = new \sfValidatorErrorSchema($error->getValidator(), array($error));
        }
        
        $form = $this->form->getEmbeddedForm($name);
      }
      else
      {
        $class = 'Jg\Form\Field\FormField';
        $form = $this->form;
      }

      $this->fields[$name] = new $class($widget, $this, $name, isset($this->value[$name]) ? $this->value[$name] : null, $error);
      $this->fields[$name]->setForm($form);
    }

    return $this->fields[$name];
  }
}