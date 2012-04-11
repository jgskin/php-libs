<?php 
namespace Jg\Form\NoObject;

use \Jg\Validator\ValidatorSchema;

abstract class UpdatableForm extends \sfForm
{ 
  protected $formName = 'root';
  
  public function setup()
  {
    $this->validatorSchema = new ValidatorSchema();
    
    $this->errorSchema = new \sfValidatorErrorSchema($this->validatorSchema);
    
    $this->widgetSchema->setNameFormat($this->formName.'[%s]');

    parent::setup();
  }
  
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    $this->fixEmbeddedForms($taintedValues);

    parent::bind($taintedValues, $taintedFiles);

    if (count($this->errorSchema) === 0)
    {
      $this->updateObjects();
      
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
  
  protected function updateObjects()
  {
    foreach ($this->embeddedForms as $k => $form) 
    {
      if ($form instanceof \BaseFormDoctrine) 
      {
        $form->updateObject($this->values[$k]);
      }
    }
  }
  
  protected function fixEmbeddedForms($values)
  {
    $this->updateForm($values);
    $this->updateFields($values, $this, $this->widgetSchema, $this->validatorSchema, $this->defaults);
  }
  
  protected function updateForm($values)
  {
    self::updateFormsChain($this, $values);
  }
  
  static protected function updateFormsChain($parent, $values)
  {
    $keysToUpdate = self::getFormKeysToUpdate($parent, $values);
    
    foreach ($keysToUpdate as $key) 
    {
      $form = $parent->getEmbeddedForm($key);
      if (method_exists($form, 'updateForm')) 
      {
        $form->updateForm($values[$key]);
      }
      else 
      {
        self::updateFormsChain($form, $values[$key]);
      }
    }
  }
  
  static protected function getFormKeysToUpdate($parent, $values)
  {
    if (!is_array($values)) 
    {
      return array();
    }
    
    $embeddeds = $parent->getEmbeddedForms();
    $formKeys  = array_keys($embeddeds);
    $valuesKeys = array_keys($values);
    
    return array_intersect($valuesKeys, $formKeys);
  }
  
  protected function updateFields($values, $parentForm, &$widgetSchema, &$validatorSchema, &$default)
  {
    $keysToUpdate = self::getFormKeysToUpdate($parentForm, $values);

    foreach ($keysToUpdate as $key) 
    {
      $form = $parentForm->getEmbeddedForm($key);
      unset($form[self::$CSRFFieldName]);

      if (!isset($widgetSchema[$key])) 
      {
        $formSchema = $form->getWidgetSchema();
        $decorator = $formSchema->getFormFormatter()->getDecoratorFormat();

        $default[$key] = $form->getDefaults();
        $widgetSchema[$key] = new \sfWidgetFormSchemaDecorator($formSchema, $decorator);
        $validatorSchema[$key] = $form->getValidatorSchema();
      }

      self::updateFields($values[$key], $form, $widgetSchema[$key], $validatorSchema[$key], $default[$key]);
    }
  }
}
