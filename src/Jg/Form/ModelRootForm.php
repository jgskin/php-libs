<?php

namespace Jg\Form;

abstract class ModelRootForm extends ModelChildForm
{
  protected $saveTrigger = false;
  
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    parent::bind($taintedValues, $taintedFiles);

    if (count($this->errorSchema) === 0)
    {
      $this->updateObject();
      
      try
      {
        $this->values = $this->validatorSchema->postCleanLoop($this->values);
      } catch (\sfValidatorErrorSchema $e)
      {
        $this->values = array();
        $this->errorSchema = $e;
      }
    }
  }
  
  public function offsetGet($name)
  {
    if (!isset($this->formFields[$name]))
    {
      if (!$widget = $this->widgetSchema[$name])
      {
        throw new \InvalidArgumentException(sprintf('Widget "%s" does not exist.', $name));
      }

      if ($this->isBound)
      {
        $value = isset($this->taintedValues[$name]) ? $this->taintedValues[$name] : null;
      }
      else if (isset($this->defaults[$name]))
      {
        $value = $this->defaults[$name];
      }
      else
      {
        $value = $widget instanceof \sfWidgetFormSchema ? $widget->getDefaults() : $widget->getDefault();
      }

      if ($widget instanceof \sfWidgetFormSchema)
      {
        $class = 'Jg\Form\Field\FormFieldSchema';
        $form = $this->getEmbeddedForm($name);
      }
      else
      {
        $class = 'Jg\Form\Field\FormField';
        $form = $this;
      }

      $this->formFields[$name] = new $class($widget, $this->getFormFieldSchema(), $name, $value, $this->errorSchema[$name]);
      $this->formFields[$name]->setForm($form);
    }

    return $this->formFields[$name];
  }
  
  protected function updateAction()
  {
    //do nothing
  }
  
  protected function canUpdateObject($values)
  {
    return true;
  }

}
