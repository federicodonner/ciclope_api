<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Registro de participantes para presentación
$app->delete('/api/ciclope_participante/{id}', function (Request $request, Response $response) {
    try {
        $id = $request->getAttribute('id');

        // Verifica que el juego especificado exista
        $sql = "SELECT id FROM ciclope_participante WHERE id = $id";
        $db = new db();
        $db = $db->connect();
        $stmt = $db->query($sql);
        $participantes = $stmt->fetchAll(PDO::FETCH_OBJ);

        if ($participantes == null) {
            $db = null;
            return messageResponse($response, 'Participante no encontrado, verifica el identificador.', 404);
        }

        // Si estoy acá es porque los campos del request están bien
        $sql = "DELETE FROM ciclope_participante WHERE id = $id";

        $stmt = $db->prepare($sql);
        $stmt->execute();

        // Devuelve el id de la actividad eliminada
        $participante->id=$id;
        $db=null;
        return dataResponse($response, $participante, 200);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 500);
    }
})->add($authenticate);

// Registro de participantes para presentación
$app->put('/api/ciclope_participante/{id}', function (Request $request, Response $response) {
    try {
        $id = $request->getAttribute('id');

        // Verifica que el juego especificado exista
        $sql = "SELECT * FROM ciclope_participante WHERE id = $id";
        $db = new db();
        $db = $db->connect();
        $stmt = $db->query($sql);
        $participantes = $stmt->fetchAll(PDO::FETCH_OBJ);

        if ($participantes == null) {
            $db = null;
            return messageResponse($response, 'Participante no encontrado, verifica el identificador.', 404);
        }

        $participante = $participantes[0];
        $participa = ($participante->participa == 1 ? 0 : 1);

        // Si estoy acá es porque los campos del request están bien
        $sql = "UPDATE ciclope_participante SET participa = $participa WHERE id = $id";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $participante->participa = $participa;
        // Devuelve el id de la actividad eliminada;
        $db=null;
        return dataResponse($response, $participante, 200);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 500);
    }
})->add($authenticate);
