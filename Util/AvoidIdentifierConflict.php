<?php
namespace Jg\Util;

class AvoidIdentifierConflict
{
  protected $identifiers = array();
  
  public function __construct(array $identifiers = array())
  {
    $this->identifiers = $identifiers;
  }

  public function getIdentifier($identifier)
  {
    $modified_identifier = $identifier;

    $incremmenter = 0;

    if (isset($this->identifiers[$identifier])) {

      $incremmenter = $this->identifiers[$identifier] + 1;

      $modified_identifier = sprintf('%s_%s', $identifier, $incremmenter);

    }

    $this->identifiers[$identifier] = $incremmenter;

    return $modified_identifier;
  }
} 
