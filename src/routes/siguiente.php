<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Devuelve los detalles del juego en base al hash
$app->get('/api/esperando', function (Request $request, Response $response) {
    $options = array(
      'cluster' => 'us2',
      'useTLS' => true
    );
    $pusher = new Pusher\Pusher(
        '57e880a09cb967a7829c',
        '46852057512240da4efb',
        '1109555',
        $options
    );

    $data['pregunta'] = $pregunta;
    $pusher->trigger('actividades', 'esperando', $data);

    return messageResponse($response, 'funciona', 200);
});

// Devuelve los detalles del juego en base al hash
$app->get('/api/actividad/{numero}', function (Request $request, Response $response) {
    try {
        $actividad_numero = $request->getAttribute('numero');

        $sql = "SELECT * FROM actividad WHERE actividad_numero = $actividad_numero";

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

        $sql="SELECT id FROM participante WHERE participa = 1 ORDER BY RAND() LIMIT 3";
        $stmt = $db->query($sql);
        $actores = $stmt->fetchAll(PDO::FETCH_OBJ);

        $actividad->actores = $actores;

        $options = array(
      'cluster' => 'us2',
      'useTLS' => true
    );
        $pusher = new Pusher\Pusher(
            '57e880a09cb967a7829c',
            '46852057512240da4efb',
            '1109555',
            $options
        );

        $pusher->trigger('actividades', 'actividad', $actividad);
        $db=null;
        return messageResponse($response, 'funciona', 200);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 500);
    }
});
