<?php
namespace Jg\Util\ObjectDataHolder;

class Holder extends \ArrayObject {
  public function __construct($config = array()) {
    parent::__construct($config, \ArrayObject::ARRAY_AS_PROPS);
  }
}

