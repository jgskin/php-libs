<?php
namespace Jg\Test\Symfony\ModelValidator;

use \Jg\Symfony\ModelValidator\BaseValidator;

/**
* Fake Validator class
*
* @author Jessé Alves Galdino <galdino.jesse@gmail.com>
*/
class FakeValidator extends BaseValidator
{
  protected $validators = array(
    'base' => array('validate1', 'validate2'),
    'validator2' => array('validate3'),
    );

  protected $configuration = array('base');

  public $validations_count = 0;

  protected function plusOne()
  {
    $this->validations_count++;
  }

  protected function validateValidate1()
  {
    $this->plusOne();
  }

  public function validateValidate2()
  {
    $this->plusOne();
  }

  protected function validateValidate3()
  {
    $this->plusOne();
  }
}

/**
* Fake model class
*
* @author Jessé Alves Galdino <galdino.jesse@gmail.com>
*/
class FakeModel extends \sfDoctrineRecord
{
}

/**
* Test para base validator class
*
* @author Jessé Alves Galdino <galdino.jesse@gmail.com>
*/
class BaseValidatorTest extends \PHPUnit_Framework_TestCase
{
  public function testValidate()
  {
    //default validators test
    $validator = new FakeValidator(new FakeModel);

    $validator->validate();

    $this->assertEquals(2, $validator->validations_count, "Default Validators error");

    //add validator test
    $validator = new FakeValidator(new FakeModel);

    $validator->enValidator('validator2');

    $validator->validate();

    $this->assertEquals(3, $validator->validations_count, "Enable validators error");
  }
}