<?php
namespace App\Traits;



use Illuminate\Support\Facades\Crypt;

trait Encryptable{

    function getAttribute($key){
        $value = parent::getAttribute($key);

        if (in_array($key,$this->encryptable)){
            $value = Crypt::decrypt($value);

            return $value;
        }

        return $value;
    }

    function setAttribute($key,$value){

        if (in_array($key,$this->encryptable)){
            $value = Crypt::encrypt($value);
        }

        return parent::setAttribute($key,$value);
    }
}
