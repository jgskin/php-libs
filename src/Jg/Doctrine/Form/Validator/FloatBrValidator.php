<?php
namespace Jg\Doctrine\Form\Validator;

class FloatBrValidator extends \sfValidatorNumber 
{
  protected function doClean($value)
  {
    $value = preg_replace(array('/\.(\d{3})/', '/,(\d+)$/'), array('$1', '.$1'), $value);

    return parent::doClean($value);
  }
}