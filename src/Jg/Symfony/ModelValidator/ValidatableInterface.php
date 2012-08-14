<?php
namespace Jg\Symfony\ModelValidator;

/**
 * Interface for classe that use the ModelValidator Functionality
 *
 * @author Jessé Alves Galdino <galdino.jesse@gmail.com>
 */
interface ValidatableInterface
{
  /**
   * Return the validator object
   * 
   * @return \Jg\Symfony\ModelValidator\BaseValidator
   */
  public function getValidator();
}