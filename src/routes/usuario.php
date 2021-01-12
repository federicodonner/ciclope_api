<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Devuevle un solo usuario
$app->get('/api/usuario', function (Request $request, Response $response) {
    // El id del usuario logueado viene del middleware authentication
    $usuario_id = $request->getAttribute('usuario_id');
    $sql = "SELECT * FROM usuario WHERE id = $usuario_id";

    try {
        $db = new db();
        $db = $db->connect();

        $stmt = $db->query($sql);
        $usuarios = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Add the users array inside an object
        if (!empty($usuarios)) {
            // Delete the password hash for the response
            unset($usuarios[0]->pass_hash);
            unset($usuarios[0]->pendiente_cambio_pass);

            $usuario = $usuarios[0];

            $db = null;
            return dataResponse($response, $usuario, 200);
        } else {
            return messageResponse($response, 'Usuario incorrecto', 401);
        }
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 500);
    }
})->add($authenticate);
