<?php

// Voy a empezar por la autocarga para que despues no se haga tan pesado

// Será de la siguiente manera


// Lo primero el espacio de nombres
namespace rpc_repetido_video\util;

use Exception;

// Creamos la clase Autocarga+
class Autocarga{

    // Esta tiene dos métodos gestiona_autocarga()
    public static function gestiona_autocarga(){
        try{
            spl_autoload_register(self::class . "::autocarga");
        }catch(Exception $e){
            return $e->getMessage();
        }
    }

    // Ahora, tenemos el método autocarga
    public static function autocarga($clase){

        // Lo primero que hacemos el cambiar el valor de la clase ya que nos viene algo parecido a eso : "clase\objeto\noseque" y lo tenemos que cambiar por "clase/objeto/noseque" entonces haremos lo siguiente
        $clase = str_replace("\\", "/", $clase);

        // Una vez cambiado, debemos de crear una variable con la ruta donde deberá buscar la clase que nos vendrá dada por parametro
        $ruta = "/rpc_repetido_video";

        // Y cuando tengamos la ruta, deberemos de comprobar que existe el fichero en esa ruta
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/dwes.com.com/practicando_ejercicios/" . "{$clase}.php")){
            require_once($_SERVER['DOCUMENT_ROOT'] . "/dwes.com.com/practicando_ejercicios/" . "{$clase}.php");
        }else {

            // Si no existe lanzamos una excepción
            throw new Exception("No se encuentra el directorio " . $_SERVER['DOCUMENT_ROOT'] . "/dwes.com.com/practicando_ejercicios/" . "{$clase}.php");
        }
    }
}

// Funcionando
// Era un error de la ruta de la autocarga.

// VAMOS CHAVALXS QUE YA TERMINAMOOOOOS

?>