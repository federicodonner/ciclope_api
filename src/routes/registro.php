<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Registro de participantes para presentación
$app->post('/api/registro', function (Request $request, Response $response) {
    try {
        $nombre = $request->getParam('nombre');
        $participa = $request->getParam('participa');

        // Verify that the information is present
        if (!$nombre) {
            $db = null;
            return messageResponse($response, 'Debes escribir un nombre', 403);
        }

        if (!$participa) {
            $participa = 0;
        }

        // Si estoy acá es porque los campos del request están bien
        $sql = "INSERT INTO participante (nombre, participa) VALUES (:nombre, :participa)";

        $db = new db();
        $db = $db->connect();

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':participa', $participa);

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
