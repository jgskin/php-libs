<?php
namespace Jg\Util;

class String
{
  static public function unCamelize($str)
  {
    return trim(preg_replace('/([A-Z])/e', "'_'.strtolower('$1')", $str), '_');
  }
  
  static public function removeNamespace($str)
  {
    if (($pos = strrpos($str, '\\')) !== FALSE) {
      $str = substr($str, $pos);
    }
    
    return $str;
  }
  
  /*
  * Verifica se o valor está formatado como um inteiro válido
  */
  static public function validInt($value)
  {
    $clean = intval($value);

    if (strval($clean) != $value)
    {
      return false;
    }
    
    return true;
  }
}
