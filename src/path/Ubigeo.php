<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../../vendor/autoload.php';
require '../config/db.php';

$app = new \Slim\App;

//Obtener todos los DEPARTAMENTOS
$app->get('/api/listarDepartamento', function(Request $request, Response $response){
    $consulta = "SELECT DISTINCT(SUBSTRING(CODUBIGEO, 1, 2)) AS CODDEPA, DEPARTAMENTO FROM GENUBIGEO";
    try{
        $db = new db();
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);
        $departamentos = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        //Exportar y mostrar en formato JSON
        echo json_encode($departamentos, JSON_UNESCAPED_UNICODE);

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});


$app->run();