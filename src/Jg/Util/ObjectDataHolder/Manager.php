<?php
namespace Jg\Util\ObjectDataHolder;

class Manager {
  static protected $instance = NULL;
  protected $holders = array();
  
  private function __construct() {}
  
  public function __clone() {
    trigger_error('Clone is not allowed.', E_USER_ERROR);
  }
    
  static public function getInstance() {
    if (!isset(self::$instance)) {
      $class = __CLASS__;
      self::$instance = new $class;
    }
    
    return self::$instance;
  }
  
  public function getHolder($object) {
    if (!is_object($object)) {
      throw new UnexpectedValueException("Objects only");
    }
    
    $hash = spl_object_hash($object);
    if (!isset($this->holders[$hash])) {
      $this->holders[$hash] = new Holder();
    }
    
    return $this->holders[$hash];
  }
}
