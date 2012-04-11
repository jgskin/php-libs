<?php 
namespace Jg\Form;

abstract class UpdatableForm extends ModelRootForm
{ 
  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    $this->fixEmbeddedForms($taintedValues);
    parent::bind($taintedValues, $taintedFiles);
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
