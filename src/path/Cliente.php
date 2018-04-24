<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../../vendor/autoload.php';
require '../config/db.php';

$app = new \Slim\App;

//Obtener todos los clientes
$app->get('/api/listarCliente', function(Request $request, Response $response){
    $consulta = "SELECT t1.CODCLIENTE AS ID,t1.NOMBRES,t1.FECHANAC, t2.DEPARTAMENTO,t2.PROVINCIA,t2.DISTRITO,t1.DIRECCION FROM TABCLIENTE t1
                LEFT JOIN GENUBIGEO t2 ON t1.UBIGEO = t2.CODUBIGEO ORDER BY t1.CODCLIENTE";
    try{
        // Instanciar la base de datos
        $db = new db();

        // Conexión
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);
        $clientes = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        //Exportar y mostrar en formato JSON
        echo json_encode($clientes);

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//Obtener un solo cliente
$app->get('/api/detalleCliente/{id}', function(Request $request, Response $response){

    $id = $request->getAttribute('id');

    $consulta = "SELECT * FROM TABCLIENTE WHERE CODCLIENTE = '$id'";
    try{
        // Instanciar la base de datos
        $db = new db();

        // Conexión
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);
        $cliente = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        //Exportar y mostrar en formato JSON
        echo json_encode($cliente);
        
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

// Agregar Cliente
$app->post('/api/Cliente/Insertar', function(Request $request, Response $response){
    $nombres = $request->getParam('nombres');
    $fechanac = $request->getParam('fechanac');
    $ubigeo = $request->getParam('ubigeo');
    $direccion = $request->getParam('direccion');
    
    $query = "INSERT INTO TABCLIENTE (NOMBRES, FECHANAC, UBIGEO, DIRECCION) VALUES
    (:nombres, STR_TO_DATE(:fechanac, '%d/%m/%Y'), :ubigeo, :direccion)";
    try{
        // Instanciar la base de datos
        $db = new db();

        // Conexión
        $db = $db->conectar();
        $stmt = $db->prepare($query);
        $stmt->bindParam(':nombres', $nombres);
        $stmt->bindParam(':fechanac', $fechanac);
        $stmt->bindParam(':ubigeo', $ubigeo);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->execute();
        echo 'Cliente Insertado Exitosamente...';
    } catch(PDOException $e){
        echo $e->getMessage();
    }
});

// Actualizar Cliente
$app->put('/api/Cliente/Modificar/{codcliente}', function(Request $request, Response $response){
    $codcliente = $request->getAttribute('codcliente');
    $nombres = $request->getParam('nombres');
    $fechanac = $request->getParam('fechanac');
    $ubigeo = $request->getParam('ubigeo');
    $direccion = $request->getParam('direccion');

    $query = "UPDATE TABCLIENTE SET
               NOMBRES         = :nombres,
               FECHANAC 	   = STR_TO_DATE(:fechanac, '%d/%m/%Y'),
               UBIGEO          = :ubigeo,
               DIRECCION       = :direccion
           WHERE CODCLIENTE    = $codcliente";

    try{
        // Instanciar la base de datos
        $db = new db();

        // Conexion
        $db = $db->conectar();
        $stmt = $db->prepare($query);
        $stmt->bindParam(':nombres', $nombres);
        $stmt->bindParam(':fechanac', $fechanac);
        $stmt->bindParam(':ubigeo', $ubigeo);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->execute();
        echo "Cliente Actualizado Exitosamente...";
    } catch(PDOException $e){
        echo $e->getMessage();
    }
});


// Borrar cliente
$app->delete('/api/Cliente/Eliminar/{codcliente}', function(Request $request, Response $response){
    $codcliente = $request->getAttribute('codcliente');
    $sql = "DELETE FROM TABCLIENTE WHERE CODCLIENTE = $codcliente";
    try{
        // Instanciar la base de datos
        $db = new db();

        // Conexion
        $db = $db->conectar();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $db = null;
        echo "Cliente Eliminado Exitosamente...";
    } catch(PDOException $e){
        echo $e->getMessage();
    }
});

$app->run();