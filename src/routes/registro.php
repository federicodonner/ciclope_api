<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Registro de participantes para presentación
$app->post('/api/registro', function (Request $request, Response $response) {
    try {
        $nombre = $request->getParam('nombre');
        $participa = $request->getParam('participa');
        $juego_hash = $request->getParam('juegoHash');

        // Verify that the information is present
        if (!$nombre) {
            $db = null;
            return messageResponse($response, 'Debes escribir un nombre', 403);
        }

        if (!$juego_hash) {
            $db = null;
            return messageResponse($response, 'Debes especificar un juego', 403);
        }

        // Si tiene juego hash, verifica que exista
        $sql = "SELECT id FROM juego WHERE hash = '$juego_hash'";
        $db = new db();
        $db = $db->connect();
        $stmt = $db->query($sql);
        $juegos = $stmt->fetchAll(PDO::FETCH_OBJ);

        if ($juegos == null) {
            $db = null;
            return messageResponse($response, 'Juego no encontrado, verifica el identificador.', 404);
        }

        // Si encuentra el juego registra el id para asociarlo al participante
        $juego_id = $juegos[0]->id;

        if (!$participa) {
            $participa = 1;
        }

        // Si estoy acá es porque los campos del request están bien
        $sql = "INSERT INTO participante (nombre, participa, juego, activo) VALUES (:nombre, :participa, :juego, :activo)";

        $stmt = $db->prepare($sql);

        $true = true;
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':participa', $participa);
        $stmt->bindParam(':juego', $juego_id);
        $stmt->bindParam(':activo', $true);

        $stmt->execute();

        // Obtiene el id de la droga recién creada para devolverla
        $sql="SELECT * FROM participante WHERE id = LAST_INSERT_ID()";
        $stmt = $db->query($sql);
        $participantes = $stmt->fetchAll(PDO::FETCH_OBJ);

        $participante = $participantes[0];

        $db=null;
        return dataResponse($response, $participante, 201);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 500);
    }
});
