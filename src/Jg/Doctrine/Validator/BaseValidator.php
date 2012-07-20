<?php
namespace Jg\Doctrine\Validator;

abstract class BaseValidator
{
  protected $record;
  
  function __construct(\Doctrine_Record $record) 
  {
    $this->record = $record;
  }
  
  abstract public function validate();
}
