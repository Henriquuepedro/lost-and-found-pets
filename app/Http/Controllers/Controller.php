<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\LogHistory;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $statusOrder = array(
        // Status PagSeguro
        0 => 'Inválido',

        1 => 'Aguardando pagamento',
        2 => 'Em análise',
        3 => 'Pago',
        4 => 'Disponível',
        5 => 'Em disputa',
        6 => 'Devolvido',
        7 => 'Cancelado',

        // Status APP
        50 => 'Aguardando Envio',
        51 => 'Enviado',
        52 => 'Falha no envio',
        53 => 'Aguardando Entrega',
        54 => 'Entregue',
        55 => 'Cancelado',

        99 => 'Cancelado'
    );

    public function formataCep($value, $mostravazio = false){
        if($value == "" && $mostravazio) $cep = "Não Informado";
        elseif(strlen($value) != 8) return false;
        elseif(strlen($value) == 8) $cep = preg_replace("/([0-9]{2})([0-9]{3})([0-9]{3})/", "$1$2-$3", $value);
        return $cep;
    }

    public function formataValor($value, $lang = 'pt'){

        if($lang == 'pt') {
            if ($value == "") return $value;
            $valor = number_format($value, 2, ',', '.');
            return $valor;
        }
        elseif($lang == 'en') {

            $value = str_replace(".", "", $value);
            $value = str_replace(",", ".", $value);

            return (float)$value;
        }
    }

    public function calcParcelaJuros($valor_total,$parcelas,$juros=2.99){
        $result = 0;
        if($juros==0){
            $result = $valor_total/$parcelas;
        }else{
            $I =$juros/100.00;
            $result = $valor_total*$I*pow((1+$I),$parcelas)/(pow((1+$I),$parcelas)-1);
        }

        return number_format($result, 2, ',', '.');
    }

    public function removeAcento($str){
        $LetraProibi = Array(",",".","'","\"","&","|","!","#","$","¨","*","(",")","`","´","<",">",";","=","+","§","{","}","[","]","^","~","?","%");
        $special = Array('Á','È','ô','Ç','á','è','Ò','ç','Â','Ë','ò','â','ë','Ø','Ñ','À','Ð','ø','ñ','à','ð','Õ','Å','õ','Ý','å','Í','Ö','ý','Ã','í','ö','ã',
            'Î','Ä','î','Ú','ä','Ì','ú','Æ','ì','Û','æ','Ï','û','ï','Ù','®','É','ù','©','é','Ó','Ü','Þ','Ê','ó','ü','þ','ê','Ô','ß','‘','’','‚','“','”','„');
        $clearspc = Array('a','e','o','c','a','e','o','c','a','e','o','a','e','o','n','a','d','o','n','a','o','o','a','o','y','a','i','o','y','a','i','o','a',
            'i','a','i','u','a','i','u','a','i','u','a','i','u','i','u','','e','u','c','e','o','u','p','e','o','u','b','e','o','b','','','','','','');
        $newId = str_replace($special, $clearspc, $str);
        $newId = str_replace($LetraProibi, "", trim($newId));
        return $newId;
    }

    public function formataData($value, $lang = 'en')
    {
        if($lang == 'en') {
            if (strlen($value) != 10) return $value;
            $expDate = explode("/", $value);
            $dia = $expDate[0];
            $mes = $expDate[1];
            $ano = $expDate[2];
            return "{$ano}-{$mes}-{$dia}";
        }
        return false;
    }

    public function formatDateTime($value, $lang = 'pt'){

        if($lang == 'en') {
            if (strlen($value) != 16) return $value;
            $expDateTime = explode(" ", $value);

            $expDate = explode("/", $expDateTime[0]);
            $dia = $expDate[0];
            $mes = $expDate[1];
            $ano = $expDate[2];


            return "{$ano}-{$mes}-{$dia} {$expDateTime[1]}";
        }
        return false;
    }

    public function validateCPF($cpf)
    {

        // Extrair somente os números
        $cpf = preg_replace('/[^0-9]/is', '', $cpf);

        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }
        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf{$c} * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf{$c} != $d) {
                return false;
            }
        }
        return true;
    }

    public function validadeCard($number) {

        // Strip any non-digits (useful for credit card numbers with spaces and hyphens)
        $number=preg_replace('/\D/', '', $number);

        // Set the string length and parity
        $number_length=strlen($number);
        $parity=$number_length % 2;

        // Loop through each digit and do the maths
        $total=0;
        for ($i=0; $i<$number_length; $i++) {
            $digit=$number[$i];
            // Multiply alternate digits by two
            if ($i % 2 == $parity) {
                $digit*=2;
                // If the sum is two digits, add them together (in effect)
                if ($digit > 9) {
                    $digit-=9;
                }
            }
            // Total up the digits
            $total+=$digit;
        }

        // If the total mod 10 equals 0, the number is valid
        return ($total % 10 == 0) ? TRUE : FALSE;

    }

    public function formatPhone($value){
        if ($value == null) return '';
        elseif((strlen($value) < 10 || strlen($value) > 11) && strlen($value) != 0) $tel = $value;
        elseif(strlen($value) == 10) $tel = preg_replace("/([0-9]{2})([0-9]{4})([0-9]{4})/", "($1) $2-$3", $value);
        elseif(strlen($value) == 11) $tel = preg_replace("/([0-9]{2})([0-9]{5})([0-9]{4})/", "($1) $2-$3", $value);
        return $tel;
    }

    public function formatDoc($value){
        if(strlen($value) != 11 && strlen($value) != 14 && strlen($value) != 0) $identidade = $value;
        elseif(strlen($value) == 11) $identidade = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{2})/", "$1.$2.$3-$4", $value);
        elseif(strlen($value) == 14) $identidade = preg_replace("/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})/", "$1.$2.$3/$4-$5", $value);
        return $identidade;
    }

    public function createLog($name, $description, $type, $class, $method, $user_id)
    {
        $data = [
            'name' => $name,
            'description' => $description,
            'type' => $type,
            'class' => $class,
            'method' => $method,
            'user_id' => $user_id
        ];

        $log = new LogHistory();
        return $log->createLog($data);
    }
}
