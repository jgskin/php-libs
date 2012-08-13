<?php
namespace Jg\Symfony\ModelValidator;

/**
* Base class to validate doctrine 1.2 models 
*
* @author Jessé Alves Galdino <galdino.jesse@gmail.com>
*/
abstract class BaseValidator
{
  /**
   * The model to be validated
   *
   * @var \sfDoctrineRecord
   **/
  protected $subject;

  /**
   * Available validators
   *
   *  Validators names must be configured without the 'validate' prefix
   *  eg: the configuration "array('base' => array('foo'))" will call the method "validateFoo"
   * @var array
   **/
  protected $validators = array();

  /**
   * enabled validation groups
   *
   * @var array
   **/
  protected $configuration = array();

  /**
   * Initialize the validators an it's configuration
   *
   * @param \sfDoctrineRecord $subject Model to be validated
   */
  public function __construct(\sfDoctrineRecord $subject)
  {
    $this->subject = $subject;
  }

  /**
   * enable a validator
   *
   * @param string $validator_name
   **/
  final public function enValidator($validator_name)
  {
    $this->checkValidator($validator_name);

    if (!in_array($validator_name, $this->configuration)) {
      //insert the validator if it doesnt exists
      $this->configuration[] = $validator_name;
    }
  }

  /**
   * Disable the validator
   *
   * @param string $validator_name
   */
  final public function disValidator($validator_name)
  {
    $this->checkValidator($validator_name);

    if (in_array($validator_name, $this->configuration)) {
      //disable the validator
      unset($this->configuration[]);
    }
  }

  /**
   * Replace the validators configuration
   *
   * @param array $configuration The new configuration
   */
  final public function configure(array $configuration)
  {
    foreach ($configuration as $validator_name) {
      $this->checkValidator($validator_name);
    }

    $this->configuration = $configuration;
  }

  /**
   * Check if a validator exists
   *
   * @param string $validator_name
   * @throws \Jg\Symfony\ModelValidator\ValidatorNotFoundException 
   *  if the validator was not declared or if the name is not a string
   */
  final protected function checkValidator($validator_name)
  {
    if (!is_string($validator_name)) {
      throw new ValidatorInvalidConfigException("The validator name must be a string");
    }

    if (!array_key_exists($validator_name, $this->validators)) {
      //validator does not exist, throw a exception
      throw new ValidatorInvalidConfigException("Validator $validator_name was not found");
    }
  }

  /**
   * Adds an error to the subject
   * 
   * @param string $key The error stack id
   * @param string $error_code The error to be outputed
   */
  final protected function addError($key, $error_code)
  {
    $this->subject->getErrorStack()->add($key, $error_code);
  }

  /**
   * Execute the validations
   *
   * @todo The validation method must be in the valitor class. Should it be changed?
   */
  final public function validate()
  {
    //array to hold the validations the were executed
    $executed = array();

    //execute the validations
    foreach ($this->configuration as $validator_name) {
      foreach ($this->validators[$validator_name] as $validation) {
        //execute the validation if it was not executed yet
        if (!in_array($validation, $executed)) {
          $validation_method = 'validate' . capitalize($validation);

          // The validation method must be in the valitor class. Should it be changed?
          //execute it
          $this->$validation_method();

          //mark as executed
          $executed[] = $validation;
        }
      }
    }
  }

}