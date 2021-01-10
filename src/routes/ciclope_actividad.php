<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Creación de actividad nueva
$app->post('/api/ciclope_actividad', function (Request $request, Response $response) {
    try {
        $juego = $request->getParam('juego');
        $numero = $request->getParam('numero');
        $texto_activo = $request->getParam('textoActivo');
        $texto_inactivo = $request->getParam('textoInactivo');
        $uploadedFiles = $request->getUploadedFiles();

        // Verify that the information is present
        if (!$juego) {
            $db = null;
            return messageResponse($response, 'Debes especificar un juego', 403);
        }

        // Verifica que el juego especificado exista
        $sql = "SELECT id FROM ciclope_juego WHERE id = $juego";
        $db = new db();
        $db = $db->connect();
        $stmt = $db->query($sql);
        $juegos = $stmt->fetchAll(PDO::FETCH_OBJ);

        if ($juegos == null) {
            $db = null;
            return messageResponse($response, 'Juego no encontrado, verifica el identificador.', 404);
        }


        if (!$numero) {
            $db = null;
            return messageResponse($response, 'Debes especificar un numero de actividad', 403);
        }

        // Si viene una imagen la procesa y la sube
        if (count($uploadedFiles) != 0) {
            $directory = $this->get('upload_directory');
            $uploadedFile = $uploadedFiles['imagenActividad'];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
                $basename = bin2hex(random_bytes(8));
                $filename = sprintf('%s.%0.8s', $basename, $extension);
                $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
            }
        } else {
            $db = null;
            return messageResponse($response, 'Debes subir una imagen para la actividad.', 403);
        }

        // Si estoy acá es porque los campos del request están bien
        $sql = "INSERT INTO ciclope_actividad (juego, numero, imagen_activo, texto_activo, texto_inactivo) VALUES (:juego, :numero, :imagen_activo, :texto_activo, :texto_inactivo)";

        $db = new db();
        $db = $db->connect();

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':juego', $juego);
        $stmt->bindParam(':numero', $numero);
        $stmt->bindParam(':imagen_activo', $filename);
        $stmt->bindParam(':texto_activo', $texto_activo);
        $stmt->bindParam(':texto_inactivo', $texto_inactivo);
        $stmt->execute();

        // Obtiene el id de la droga recién creada para devolverla
        $sql="SELECT * FROM ciclope_actividad WHERE id = LAST_INSERT_ID()";
        $stmt = $db->query($sql);
        $actividades = $stmt->fetchAll(PDO::FETCH_OBJ);

        $actividad = $actividades[0];

        $db=null;
        return dataResponse($response, $actividad, 201);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 500);
    }
})->add($authenticate);


// Edición de actividad
$app->put('/api/ciclope_actividad/{id}', function (Request $request, Response $response) {
    try {
        $id = $request->getAttribute('id');
        $juego = $request->getParam('juego');
        $numero = $request->getParam('numero');
        $texto_activo = $request->getParam('textoActivo');
        $texto_inactivo = $request->getParam('textoInactivo');


        $sql = "SELECT * FROM ciclope_actividad WHERE id = $id";
        $db = new db();
        $db = $db->connect();
        $stmt = $db->query($sql);
        $actividades = $stmt->fetchAll(PDO::FETCH_OBJ);

        if ($actividades == null) {
            $db = null;
            return messageResponse($response, 'Actividad no encontrada, verifica el id.', 404);
        }

        $actividad_anterior = $actividades[0];

        if ($juego) {
            $juego_ingresar = $juego;
        } else {
            $juego_ingresar = $actividad_anterior->juego;
        }

        if ($numero) {
            $numero_ingresar = $numero;
        } else {
            $numero_ingresar = $actividad_anterior->numero;
        }

        if ($texto_activo) {
            $texto_activo_ingresar = $texto_activo;
        } else {
            $texto_activo_ingresar = $actividad_anterior->texto_activo;
        }

        if ($texto_inactivo) {
            $texto_inactivo_ingresar = $texto_inactivo;
        } else {
            $texto_inactivo_ingresar = $actividad_anterior->texto_inactivo;
        }



        // Si estoy acá es porque los campos del request están bien
        $sql = "UPDATE ciclope_actividad SET juego = :juego, numero = :numero, texto_activo = :texto_activo, texto_inactivo = :texto_inactivo WHERE id = $id";



        $stmt = $db->prepare($sql);

        $stmt->bindParam(':juego', $juego_ingresar);
        $stmt->bindParam(':numero', $numero_ingresar);
        $stmt->bindParam(':texto_activo', $texto_activo_ingresar);
        $stmt->bindParam(':texto_inactivo', $texto_inactivo_ingresar);
        $stmt->execute();

        $sql = "SELECT * FROM ciclope_actividad WHERE id = $id";
        $stmt = $db->query($sql);
        $actividades = $stmt->fetchAll(PDO::FETCH_OBJ);

        $actividad_devolver = $actividades[0];


        $db=null;
        return dataResponse($response, $actividad_devolver, 201);
    } catch (PDOException $e) {
        $db = null;
        return messageResponse($response, $e->getMessage(), 500);
    }
})->add($authenticate);
