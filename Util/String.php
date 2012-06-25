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

  static function blank($str)
  {
    $str = trim($str);
    return empty($str);
  }

  static function hasOnlyNumbers($str)
  {
    return preg_match('/^\d*$/', $str);
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
  
  static function validCPF($cpf) {

    if(!is_numeric($cpf)) {
      return false;
    }

    //VERIFICA
    if( ($cpf == '11111111111') || ($cpf == '22222222222') ||
        ($cpf == '33333333333') || ($cpf == '44444444444') ||
        ($cpf == '55555555555') || ($cpf == '66666666666') ||
        ($cpf == '77777777777') || ($cpf == '88888888888') ||
        ($cpf == '99999999999') || ($cpf == '00000000000') ) {
      return false;
    }

    //PEGA O DIGITO VERIFIACADOR
    $dv_informado = substr($cpf, 9,2);

    for($i=0; $i<=8; $i++) {
      $digito[$i] = substr($cpf, $i,1);
    }

    //CALCULA O VALOR DO 10º DIGITO DE VERIFICAÇÂO
    $posicao = 10;
    $soma = 0;

    for($i=0; $i<=8; $i++) {
      $soma = $soma + $digito[$i] * $posicao;
      $posicao = $posicao - 1;
    }

    $digito[9] = $soma % 11;

    if($digito[9] < 2) {
      $digito[9] = 0;
    } else {
      $digito[9] = 11 - $digito[9];
    }

    //CALCULA O VALOR DO 11º DIGITO DE VERIFICAÇÃO
    $posicao = 11;
    $soma = 0;

    for ($i=0; $i<=9; $i++) {
      $soma = $soma + $digito[$i] * $posicao;
      $posicao = $posicao - 1;
    }

    $digito[10] = $soma % 11;

    if ($digito[10] < 2) {
      $digito[10] = 0; 
    } else {
      $digito[10] = 11 - $digito[10];
    }

    //VERIFICA SE O DV CALCULADO É IGUAL AO INFORMADO
    $dv = $digito[9] * 10 + $digito[10];
    if ($dv != $dv_informado) {
      $status = false;
    } else {
      $status = true;
    }

    return $status;
  }
  
  //Desenvolvedor: Marcelo Bom Jardim
  //Email: suporte@onzehost.net
  //Site: www.onzehost.net
  static function validCNPJ($cnpj) {
    
    if (strlen($cnpj) <> 14)
      return false;
 
    $soma = 0;
       
    $soma += ($cnpj[0] * 5);
    $soma += ($cnpj[1] * 4);
    $soma += ($cnpj[2] * 3);
    $soma += ($cnpj[3] * 2);
    $soma += ($cnpj[4] * 9);
    $soma += ($cnpj[5] * 8);
    $soma += ($cnpj[6] * 7);
    $soma += ($cnpj[7] * 6);
    $soma += ($cnpj[8] * 5);
    $soma += ($cnpj[9] * 4);
    $soma += ($cnpj[10] * 3);
    $soma += ($cnpj[11] * 2);
 
    $d1 = $soma % 11;
    $d1 = $d1 < 2 ? 0 : 11 - $d1;
 
    $soma = 0;
    $soma += ($cnpj[0] * 6);
    $soma += ($cnpj[1] * 5);
    $soma += ($cnpj[2] * 4);
    $soma += ($cnpj[3] * 3);
    $soma += ($cnpj[4] * 2);
    $soma += ($cnpj[5] * 9);
    $soma += ($cnpj[6] * 8);
    $soma += ($cnpj[7] * 7);
    $soma += ($cnpj[8] * 6);
    $soma += ($cnpj[9] * 5);
    $soma += ($cnpj[10] * 4);
    $soma += ($cnpj[11] * 3);
    $soma += ($cnpj[12] * 2);
       
       
    $d2 = $soma % 11;
    $d2 = $d2 < 2 ? 0 : 11 - $d2;
       
    if ($cnpj[12] == $d1 && $cnpj[13] == $d2) {
      return true;
    } else {
      return false;
    }
  }
}
