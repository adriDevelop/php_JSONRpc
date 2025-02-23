<?php

// Ahora vamos al mandangon bueno, aquí está la clase que tiene que manejar nuestra petición y perdonarme pero tengo que mirar seguramente alguno de los ejemplos que tengo por ahí realizado

// Lo primero, como siempre, el espacio de nombres
namespace rpc_repetido_video\controlador;

use Exception;

// Creamos la clase JSONControlador
class JSONControlador{

    // Está tiene una propiedad privada que es la ruta donde se encuentran los modelos, que la usaremos mas adelante
    private string $ruta_modelos = "rpc_repetido_video\\modelo\\";

    // Después, crearemos las funciones que nos harán falta para poder mandar la petición correctamente

    // La primera es la función que validara nuestra petición

    // ¿Qué hacemos aqui? Básicamente si nos acordamos de como era la petición vemos que existen dos valores que son jsonrpc y method y que jsonrpc tiene que tener el valor 2.0, si no existe ni jsonrpc ni method nos devolverá false, y si jsonrpc no es igual a 2.0 pues nos devolverá false tambien. Por el contrario, si todo está bien y existe, nos devolverá true y eso significará que está correctamente

    private function validaPeticion($peticion){
        return isset($peticion['jsonrpc'], $peticion['method']) && $peticion['jsonrpc'] == "2.0";
    }

    // La siguiente funcion es la que va a mandar la respuesta

    // ¿Qué vamos a hacer en esta función? Lo que haremos sera comprobar que los valores que vienen no son nulos, y si no son nulos, se rellenarán los datos con lo que nos venga en el parametro
    private function enviaPeticion($id, $resultado, $error){

        // Lo primero es crear respuesta que será un array con todos los valores de la petición, jsonrpc, id, result y method
        $respuesta['jsonrpc'] = "2.0";

        // Ahora comprobamos los valores de nuestros valores recibidos por parametro
        if ($resultado){
            $respuesta['result'] = $resultado;
        }

        if ($error){
            $respuesta['error'] = $error;
        }

        $respuesta['id'] = $id;

        // Y ya mandamos la peticion
        // Hay que aplicar una cabecera para que nuestro json se mande correctamente
        header('Content-header: application/json');

        // Y que se devuelva correctamente
        echo json_encode($respuesta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    // Con esta función finalizada, deberemos crear otra función que también usaremos más adelante, la cual es la que se encargará de separar el metodo del objeto porque en $peticion['params'] nos venia la clase seguida de un punto y del metodo que se va a ejecutar (Matematicas.suma)
    private function modeloMetodo($peticion):array{
        if (!strpos($peticion['method'], '.')){
            // Si no podemos separarlo por el punto, significa que se ha mandado un método incorrecto, así que mandaremos una respuesta en formato json usando la función que hemos usado antes de envia respuesta
            $this->enviaPeticion(null, null, ['code' => -32600, 'message' => 'Invalid method. Request "Class.method".']);
        }

        // Y si sí podemos separarlo, debemos de hacer un explode para devolver un array con los dos parámetros.
        return explode('.', $peticion['method']);
    }

    // Ahora, viene la función mandangona
    // En esta función vamos a manejar la petición. Recogeremos el cuerpo que nos viene dado en la peticion, despues cogeremos los parametros y llamaremos al método que nos pasen en la petición usando los parámetros también mandados por el cuerpo de la petición.
    public function manejaPeticion(){

        // Lo primero es coger el cuerpo de la peticion desde "php://input"
        $cuerpo = file_get_contents("php://input");

        // Ahora, debemos de coger la peticion
        // Aquí lo que hacemos es recoger del cuerpo un json, lo tenemos que "decodificar" que nos devolverá un array asociativo gracias a que pasamos el parametro 'true'
        $peticion = json_decode($cuerpo, true);

        // Aquí lo que he hecho ha sido coger el id del array de la petición. Es el id que viene en el cuerpo y si no viene ninguno, le asingo null directamente
        $id = $peticion['id'] ?? null;

        // Ahora, una vez tenemos la peticion, debemos comprobar que sea válida
        // Vamos a haer uso de la función validaPeticion creada anteriormenteque devuelve o true o false dependiendo si es válida o no.
        if (!$this->validaPeticion($peticion)){

            // Y devolvemos una nueva respuesta del servidor.
            $this->enviaPeticion($id, null, ['code' => -32603, 'message' => 'Invalid request']);
        }

        // Cuando lo hayamos comprobado, deberemos de comprobar que la clase y el metodo existen, entonces, vamos a usar la desestructuración para obtener tanto el modelo como el metodo
        [$modelo, $metodo] = $this->modeloMetodo($peticion);

        // Ahora, ya tendremos tanto el modelo dentro de la variable $modelo y como el método en la variable $metodo

        // Ahora, continuamos creando un objeto de ese modelo concatenandole la ruta que declaramos como privada al principio
        $objetoModelo = $this->ruta_modelos . $modelo;

        // Y ahora, mandangote
        // Dentro de un try{}catch() vamos a comprobar que exista la clase y que exista el metodo dentro de esa clase y usaremos un callback para llamar a la función que venga en el cuerpo de la petición
        try{

            if (class_exists($objetoModelo) && method_exists($objetoModelo, $metodo)){

                // Creamos un nuevo objetoModelo
                $objeto = new $objetoModelo();

                // Tenemos que coger los parametros que nos mandan en la petición, que se me ha pasado
                $params = $peticion['params'];

                // Ahora, debemos de llamar a la función que nos manden en el cuerpo usando un callback
                $resultado = call_user_func_array([$objeto, $metodo], $params);

                // Y ya pued devolvemos el resultado
                $this->enviaPeticion($id, $resultado, null);

            } else {

                // En el caso que no exista o el método pues devolvemos otra respuesta del servidor
                $this->enviaPeticion($id, null, ['code' => -32603, 'message' => 'Invalid request']);
            }

        }catch(Exception $e){

            // Y si nos da un error en el try catch, pues capturamos la excepción y devolvemos otra respuesta del servidor
            $this->enviaPeticion($id, null, ['code' => $e->getCode(), 'message' => 'Error server', 'data' => $e->getMessage()]);
        }
    }
}

// En un principio, debe de funcionar, vamos a probar porque seguro que va a cascar por todos lados.
// Debemos de crear el index.php donde llamemos a nuestra autocarga y a nuestro JSONControlador

?>