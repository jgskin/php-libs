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
}