<?php
namespace Jg\Doctrine\Validator;

class TestValidator extends BaseValidator
{
  public function validate()
  {
    //add error
    $this->record->getErrorStack()->add('test_validator', 'test validator error');
  }
}
