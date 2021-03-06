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
        // Instanciar la base de datos
        $db = new db();

        // Conexión
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);
        $departamentos = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        //Exportar y mostrar en formato JSON
        //echo json_encode($depatamentos);
        echo json_encode($departamentos, JSON_UNESCAPED_UNICODE);

    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//Obtener un solo PROVINCIA
$app->get('/api/listarProvincia/{departamento}', function(Request $request, Response $response){

    $departamento = $request->getAttribute('departamento');

    $consulta = "SELECT DISTINCT(SUBSTRING(CODUBIGEO, 3, 2)) AS CODPROV, PROVINCIA FROM GENUBIGEO WHERE DEPARTAMENTO = '$departamento'";
    try{
        // Instanciar la base de datos
        $db = new db();

        // Conexión
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);
        $provincia = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        //Exportar y mostrar en formato JSON
        echo json_encode($provincia, JSON_UNESCAPED_UNICODE);
        
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

//Obtener un solo DISTRITO
$app->get('/api/listarDistrito/{provincia}', function(Request $request, Response $response){

    $provincia = $request->getAttribute('provincia');

    $consulta = "SELECT  DISTINCT(SUBSTRING(CODUBIGEO, 5, 2)) AS CODDIST, DISTRITO FROM GENUBIGEO WHERE PROVINCIA = '$provincia'";
    try{
        // Instanciar la base de datos
        $db = new db();

        // Conexión
        $db = $db->conectar();
        $ejecutar = $db->query($consulta);
        $provincia = $ejecutar->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        //Exportar y mostrar en formato JSON
        echo json_encode($provincia, JSON_UNESCAPED_UNICODE);
        
    } catch(PDOException $e){
        echo '{"error": {"text": '.$e->getMessage().'}';
    }
});

$app->run();