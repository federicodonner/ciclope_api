<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/api/juego', function (Request $request, Response $response) {
    try {
        // Si estoy acá es porque los campos del request están bien
        $sql = "SELECT * FROM juego";

        $db = new db();
        $db = $db->connect();
        $stmt = $db->query($sql);
        $juegos = $stmt->fetchAll(PDO::FETCH_OBJ);
        $respuesta->juegos = $juegos;
        $db=null;
        return dataResponse($response, $respuesta, 200);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 500);
    }
})->add($authenticate);


$app->get('/api/juego/{id}', function (Request $request, Response $response) {
    try {
        $id = $request->getAttribute('id');

        $sql = "SELECT * FROM juego WHERE id = $id";

        $db = new db();
        $db = $db->connect();
        $stmt = $db->query($sql);
        $juegos = $stmt->fetchAll(PDO::FETCH_OBJ);

        if ($juegos == null) {
            $db = null;
            return messageResponse($response, 'Juego no encontrado, verifica el identificador e intenta nuevamente.', 404);
        }

        $respuesta=$juegos[0];

        $sql = "SELECT * FROM actividad WHERE juego = $id ORDER BY numero";
        $stmt = $db->query($sql);
        $actividades = $stmt->fetchAll(PDO::FETCH_OBJ);

        $respuesta->actividades = $actividades;

        $sql = "SELECT * FROM participante WHERE juego = $id";
        $stmt = $db->query($sql);
        $participantes = $stmt->fetchAll(PDO::FETCH_OBJ);

        $respuesta->participantes = $participantes;

        $db=null;
        return dataResponse($response, $respuesta, 200);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 500);
    }
})->add($authenticate);



// Creación de juego nuevo
$app->post('/api/juego', function (Request $request, Response $response) {
    try {
        $nombre = $request->getParam('nombre');
        $texto_espera = $request->getParam('textoEspera');

        // Verify that the information is present
        if (!$nombre) {
            $db = null;
            return messageResponse($response, 'Debes escribir un nombre', 403);
        }

        // Si estoy acá es porque los campos del request están bien
        $sql = "INSERT INTO juego (nombre, hash, texto_espera) VALUES (:nombre, :hash, :texto_espera)";

        $db = new db();
        $db = $db->connect();

        $stmt = $db->prepare($sql);

        $juego_hash = str_replace(" ", "", $nombre)."_".random_str(5, "abcdefghijkmnpqrstuvwxyz");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':hash', $juego_hash);
        $stmt->bindParam(':texto_espera', $texto_espera);

        $stmt->execute();

        // Obtiene el id de la droga recién creada para devolverla
        $sql="SELECT * FROM juego WHERE id = LAST_INSERT_ID()";
        $stmt = $db->query($sql);
        $juegos = $stmt->fetchAll(PDO::FETCH_OBJ);

        $juego = $juegos[0];

        $db=null;
        return dataResponse($response, $juego, 201);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 500);
    }
})->add($authenticate);




// Verificar que exista el juego por hash
$app->get('/api/juegohash/{hash}', function (Request $request, Response $response) {
    try {
        // Obtiene el hash de la invocación
        $juego_hash = $request->getAttribute('hash');

        if (!$juego_hash) {
            $db = null;
            return messageResponse($response, 'Debes especificar un hash de juego.', 403);
        }

        $sql="SELECT * FROM juego WHERE hash = '$juego_hash'";

        $db = new db();
        $db = $db->connect();

        $stmt = $db->query($sql);
        $juegos = $stmt->fetchAll(PDO::FETCH_OBJ);

        if ($juegos == null) {
            $db = null;
            return messageResponse($response, 'Juego no encontrado, verifica el hash e intenta nuevamente.', 404);
        }

        $juego = $juegos[0];

        $db=null;
        return dataResponse($response, $juego, 200);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 500);
    }
});
