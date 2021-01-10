<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Pone a todos los jugdaores del juego a esperar
$app->get('/api/esperando/{juego_id}', function (Request $request, Response $response) {
    try {

        // Verifica que el juego exista en la base de datos
        $juego_id = $request->getAttribute('juego_id');
        $sql="SELECT hash FROM juego WHERE id = $juego_id";

        $db = new db();
        $db = $db->connect();
        $stmt = $db->query($sql);
        $juegos = $stmt->fetchAll(PDO::FETCH_OBJ);

        if ($juegos == null) {
            $db = null;
            return messageResponse($response, 'Juego no encontrado, verifica el id.', 404);
        }

        // Si el juego existe, va a buscar el hash para enviar el mensaje por ese canal
        $juego_hash = $juegos[0]->hash;
        $texto_espera = $juego[0]->texto_espera;


        $options = array(
      'cluster' => 'us2',
      'useTLS' => true
    );
        $pusher = new Pusher\Pusher(
            '2b7eb169341874de5e39',
            '60b82dc89b2ae1d61d27',
            '1135520',
            $options
        );

        $pusher->trigger($juego_hash, 'esperando', $texto_espera);

        return messageResponse($response, $juego_hash, 200);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 500);
    }
})->add($authenticate);



// Activa una actividad
$app->get('/api/activar/{id}', function (Request $request, Response $response) {
    try {
        $id = $request->getAttribute('id');

        $sql = "SELECT * FROM actividad WHERE id = $id";

        $db = new db();
        $db = $db->connect();
        $stmt = $db->query($sql);
        $actividades = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Si no hay registros, salgo con error
        if (count($actividades) == 0) {
            $db = null;
            return messageResponse($response, 'La actividad sleeccionada no existe.', 404);
        }

        // Si estoy acá es porque encontró la actividad
        $actividad = $actividades[0];

        // Necesita el hash del juego para saber por qué canal enviar el mensaje
        $juego_id = $actividad ->juego;
        $sql="SELECT hash FROM juego WHERE id = $juego_id";
        $stmt = $db->query($sql);
        $juegos = $stmt->fetchAll(PDO::FETCH_OBJ);

        // Si no hay juegos, salgo con error
        if (count($juegos) == 0) {
            $db = null;
            return messageResponse($response, 'La actividad no corresponde a ningún juego, verifica los datos y vuelve a intentar.', 404);
        }

        // Si enceuntra el juego, guarda el hash para utilizar como canal
        $juego_hash = $juegos[0]->hash;

        $sql="SELECT id FROM participante WHERE participa = 1 AND activo = 1 ORDER BY RAND() LIMIT 1";
        $stmt = $db->query($sql);
        $actores = $stmt->fetchAll(PDO::FETCH_OBJ);

        $actividad->actores = $actores;

        $options = array(
      'cluster' => 'us2',
      'useTLS' => true
    );
        $pusher = new Pusher\Pusher(
            '2b7eb169341874de5e39',
            '60b82dc89b2ae1d61d27',
            '1135520',
            $options
        );

        // Antes de enviar los detalles de la actividad, elimina los campos internos
        unset($actividad->id);
        unset($actividad->juego);
        unset($actividad->numero);

        $pusher->trigger($juego_hash, 'actividad', $actividad);
        $db=null;
        return dataResponse($response, $actividad, 200);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 500);
    }
});
