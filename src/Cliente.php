<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../../vendor/autoload.php';
require '../config/db.php';

$app = new \Slim\App;

//Obtener todos los clientes
$app->get('/api/listarCliente', function(Request $request, Response $response){
    $consulta = "SELECT t1.CODCLIENTE,t1.NOMBRES,t1.FECHANAC, t2.DEPARTAMENTO,t2.PROVINCIA,t2.DISTRITO,t1.DIRECCION FROM TABCLIENTE t1
                LEFT JOIN GENUBIGEO t2 ON t1.UBIGEO = t2.CODUBIGEO ORDER BY t1.CODCLIENTE";
    try{
        // Instanciar la base de datos
        $db = new db();
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);
        $clientes = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        //Exportar resultado en JSON
        echo json_encode($clientes);

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//Obtener detalle de Cliente
$app->get('/api/detalleCliente/{id}', function(Request $request, Response $response){
    $id = $request->getAttribute('id');
    $consulta = "SELECT * FROM TABCLIENTE WHERE CODCLIENTE = '$id'";
    try{
        // Instanciar la base de datos
        $db = new db();
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);
        $cliente = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        echo json_encode($cliente);
        
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});



$app->run();