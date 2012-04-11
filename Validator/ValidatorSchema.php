<?php 
namespace Jg\Validator;

class ValidatorSchema extends \sfValidatorSchema
{ 
  protected $bypassTrigger = false;
  
  public function setBypassTrigger($callable)
  {
    $this->bypassTrigger = $callable;
  }
  
  public function needValidation($values)
  {
    if (!$this->bypassTrigger) 
    {
      return TRUE;
    }
    
    return call_user_func($this->bypassTrigger, $values);
  }
  
  protected function doClean($values)
  {
    if (null === $values)
    {
      $values = array();
    }

    if (!is_array($values))
    {
      throw new InvalidArgumentException('You must pass an array parameter to the clean() method');
    }
    
    if (!$this->needValidation($values)) 
    {
      return $values;
    }

    $clean  = array();
    $unused = array_keys($this->fields);
    $errorSchema = new \sfValidatorErrorSchema($this);

    // check that post_max_size has not been reached
    if (isset($_SERVER['CONTENT_LENGTH']) && (int) $_SERVER['CONTENT_LENGTH'] > $this->getBytes(ini_get('post_max_size')))
    {
      $errorSchema->addError(new \sfValidatorError($this, 'post_max_size'));

      throw $errorSchema;
    }

    // pre validator
    try
    {
      $this->preClean($values);
    }
    catch (\sfValidatorErrorSchema $e)
    {
      $errorSchema->addErrors($e);
    }
    catch (\sfValidatorError $e)
    {
      $errorSchema->addError($e);
    }

    // validate given values
    foreach ($values as $name => $value)
    {
      // field exists in our schema?
      if (!array_key_exists($name, $this->fields))
      {
        if (!$this->options['allow_extra_fields'])
        {
          $errorSchema->addError(new \sfValidatorError($this, 'extra_fields', array('field' => $name)));
        }
        else if (!$this->options['filter_extra_fields'])
        {
          $clean[$name] = $value;
        }

        continue;
      }

      unset($unused[array_search($name, $unused, true)]);

      // validate value
      try
      {
        $clean[$name] = $this->fields[$name]->clean($value);
      }
      catch (\sfValidatorError $e)
      {
        $clean[$name] = null;

        $errorSchema->addError($e, (string) $name);
      }
    }

    // are non given values required?
    foreach ($unused as $name)
    {
      // validate value
      try
      {
        $clean[$name] = $this->fields[$name]->clean(null);
      }
      catch (\sfValidatorError $e)
      {
        $clean[$name] = null;

        $errorSchema->addError($e, (string) $name);
      }
    }
    
    if (count($errorSchema))
    {
      throw $errorSchema;
    }

    return $clean;
  }
  
  public function postCleanLoop($clean)
  { 
    return self::doPostCleanLoop($this, $clean);
  }
  
  static protected function doPostCleanLoop($validator, $clean)
  {
    if ($validator instanceof ValidatorSchema && !$validator->needValidation($clean)) 
    {
      return $clean;
    }
    
    $errorSchema = new \sfValidatorErrorSchema($validator);
    
    // post validator
    try
    {
      $clean = $validator->postClean($clean);
    }
    catch (\sfValidatorErrorSchema $e)
    {
      $errorSchema->addErrors($e);
    }
    catch (\sfValidatorError $e)
    {
      $errorSchema->addError($e);
    }
    
    foreach ($clean as $name => $value)
    {
      if($validator->fields[$name] instanceof \sfValidatorSchema) 
      {
        $clean[$name] = self::doPostCleanLoop($validator->fields[$name], $value);
      } 
    }
    
    if (count($errorSchema))
    {
      throw $errorSchema;
    }
    
    return $clean;
  }
}
