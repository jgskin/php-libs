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
  
  /**
   * Create new forms by values received from POST
   *
   * Update the embedded forms creating new forms based in the form collection
   * updateForm method and the values received from POST
   *
   * @param \sfForm $parent the parent form
   * @param array $values the post values
   */
  static protected function updateFormsChain($parent, $values)
  {
    //get the embedded forms who received a post value
    $keysToUpdate = self::getFormKeysToUpdate($parent, $values);
    
    foreach ($keysToUpdate as $key) {
      //get form to update
      $form = $parent->getEmbeddedForm($key);

      //if the form is updatable, update it
      if (method_exists($form, 'updateForm')) {
        $form->updateForm($values[$key]);
      }

      //keep searching the embeddedforms for update cases
      self::updateFormsChain($form, $values[$key]);
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
