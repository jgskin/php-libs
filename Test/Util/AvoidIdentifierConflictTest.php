<?php
namespace Jg\Test\Util;

use \Jg\Util\AvoidIdentifierConflict;

class AvoidIdentifierConflictTest extends \PHPUnit_Framework_TestCase
{
  public function testNameConflict()
  {
    //create the identifier
    $identifier = 'identifier';

    //instantiate a identifier conflic avoider with the previously identifier
    $avoider = new AvoidIdentifierConflict(array($identifier => 0));
    
    //insert the same identifier and check the return value
    $this->assertEquals($avoider->getIdentifier($identifier), sprintf('%s_%s', $identifier, 1));

    //insert the same identifier, again, and test, again
    $this->assertEquals($avoider->getIdentifier($identifier), sprintf('%s_%s', $identifier, 2)); 
  }
}