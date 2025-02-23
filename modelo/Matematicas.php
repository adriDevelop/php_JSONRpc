<?php

// Vamos a trabajar con Matematicas porque es un ejemplo simple

// Lo primero es el espacio de nombres
namespace rpc_repetido_video\modelo;

// Creamos la clase Matematicas
class Matematicas{

    // Está contiene tres métodos

    // Método que suma dos valores que recibimos en la peticion['params']
    public function suma($a, $b){
        return $a + $b;
    }

    // Método que resta dos valores que recibimos en la peticion['params']
    public function resta($a, $b){
        return $a - $b;
    }

    // Método que multiplica dos valores que recibimos en la peticion['params']
    public function multiplica($a, $b){
        return $a * $b;
    }
}

?>