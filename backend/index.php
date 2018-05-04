<?php
    require_once 'vendor/autoload.php';
    require_once('piramide-uploader/PiramideUploader.php');

    $app = new \Slim\Slim();
    $db = new mysqli('localhost','root','','groupreads');
    $db->set_charset("utf8");

    /* --- CONFIGURAR CABECERAS --- */
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");
    $method = $_SERVER['REQUEST_METHOD'];
    if($method == "OPTIONS") {
        die();
    }

    /* --- CREAR UN NUEVO USUARIO --- */
    $app->post('/nuevousuario',function() use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);

        if(!isset($data['correo'])){
            $data['correo']=null;
        }

        if(!isset($data['nombre'])){
            $data['nombre']=null;
        }

        if(!isset($data['apellidos'])){
            $data['apellidos']=null;
        }

        if(!isset($data['nick'])){
            $data['nick']=null;
        }

        if(!isset($data['fecha'])){
            $data['fecha']=null;
        }

        if(!isset($data['pwd'])){
            $data['pwd']=null;
        }else{
            $data['pwd'] = password_hash($data['pwd'], PASSWORD_BCRYPT);
        }

        if(!isset($data['pais'])){
            $data['pais']=null;
        }

        if(!isset($data['foto'])){
            $data['foto']=null;
        }else if($data['foto']==''){
            $data['foto']=null;
        }

        $consulta = "INSERT INTO usuarios VALUES (DEFAULT,".
                    "'{$data['correo']}',".
                    "'{$data['nombre']}',".
                    "'{$data['apellidos']}',".
                    "'{$data['nick']}',".
                    "'{$data['fecha']}',".
                    "'{$data['pwd']}',".
                    "'{$data['foto']}',".
                    "'{$data['pais']}','n');";

        $insert = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>'Error al registrar usuario'
        );

        if($insert){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Usuario registrado correctamente'
            );
        }

        echo json_encode($result);
    });

    /* --- SUBIR IMAGEN DE UN USUARIO --- */
    $app->post('/upload-file',function() use($app,$db){
        $result = array(
            'status' => 'error',
            'code' => 404,
            'message' => 'El archivo no ha podido subirse'
        );

        if(isset($_FILES['uploads'])){
            $piramideUploader = new PiramideUploader();

            $upload = $piramideUploader->upload('image','uploads','../src/app/upload',array('image/jpeg','image/png'));
            $file = $piramideUploader->getInfoFile();
            $file_name = $file['complete_name'];

            if(isset($upload) && $upload['uploaded']==false){
                $result = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El archivo no ha podido subirse'
                );
            }else{
                $result = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El archivo se ha subido',
                    'filename' => $file_name
                );
            }
        }

        echo json_encode($result);
    });

    /* --- COMPROBAR LOGIN DE USUARIO --- */
    $app->post('/loginusuario',function() use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);
        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>'No se ha econtrado el usuario'
        );

        $correo = $data['correo'];
        $pwd = $data['pwd'];

        $consulta = "SELECT * FROM usuarios WHERE correo='".$correo."'";
        $query = $db->query($consulta);

        if($query){
            $datos = $query->fetch_assoc();
            if(password_verify($pwd,$datos['pwd'])){
                $result = array(
                    'status'=>'success',
                    'code'=>200,
                    'data'=>$datos
                );
            }else if(sha1($pwd)==$datos['pwd']){
                $result = array(
                    'status'=>'success',
                    'code'=>200,
                    'data'=>$datos
                );
            }
        }

        echo json_encode($result);
    });

    /* --- BUSCAR UN USUARIO CONCRETO --- */
    $app->get('/usuarios/:correo',function($correo) use($app,$db){
        $consulta = "SELECT * FROM usuarios WHERE correo='".$correo."';";
        $query = $db->query($consulta);
        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>'No se ha econtrado el usuario'
        );

        if($query->num_rows == 1){
            $usuario = $query->fetch_assoc();

            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$usuario
            );
        }

        echo json_encode($result);

    });

    /* --- ACTUALIZAR DATOS GENERALES DE UN USUARIO --- */
    $app->post('/actualizar/:correo',function($correo) use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);

        $consulta = "UPDATE usuarios SET nombre=".
                    "'{$data['nombre']}',apellidos=".
                    "'{$data['apellidos']}',nick=".
                    "'{$data['nick']}',fecha=".
                    "'{$data['fecha']}',pais=".
                    "'{$data['pais']}' WHERE correo=".
                    "'{$data['correo']}';";

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>'No se ha podido actualiza el usuario'
        );

        $insert = $db->query($consulta);
        if($insert){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Usuario actualizado correctamente'
            );
        }

        echo json_encode($result);
    });

    /* --- ACTUALIZAR CONTRASEÑA DE UN USUARIO --- */
    $app->post('/actualizarpwd/:correo',function($correo) use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);

        $data['pwd'] = password_hash($data['pwd'], PASSWORD_BCRYPT);
        $consulta = "UPDATE usuarios SET pwd='".$data['pwd']."' WHERE correo='".$correo."'";
        
        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>'No se ha podido actualiza el usuario'
        );

        $insert = $db->query($consulta);
        if($insert){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Usuario actualizado correctamente'
            );
        }

        echo json_encode($result);

    });

    /* --- ACTUALIZAR IMAGEN DE UN USUARIO --- */
    $app->post('/actualizarimg/:correo',function($correo) use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);

        $consulta = "UPDATE usuarios SET foto='".$data['foto']."' WHERE correo='".$correo."'";

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>'No se ha podido actualiza el usuario'
        );

        $insert = $db->query($consulta);
        if($insert){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Imagen actualizada correctamente'
            );
        }

        echo json_encode($result);
    });

    /* --- BORRAR LA IMAGEN DE UN USUARIO --- */
    $app->post('/borrarimg',function() use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);
        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>'No se ha podido borrar la foto'
        );

        $consulta = "UPDATE usuarios SET foto=NULL WHERE correo='".$data['correo']."'";
        $query = $db->query($consulta);
        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Foto borrada correctamente'
            );
            unlink('../src/app/upload/'.$data['foto']);
        }

        echo json_encode($result);
    });

    /* --- DEVOLVER TOTAL DE USUARIOS --- */
    $app->get('/totalusuarios',function() use($app,$db){
        $consulta = "SELECT COUNT(*) as total FROM usuarios WHERE tipo<>'a'";
        $query = $db->query($consulta);
        $datos = $query->fetch_assoc();
        $total = $datos['total'];

        $result = array(
            'status'=>'success',
            'code'=>200,
            'data'=>$total
        );
        echo json_encode($result);
    });

    /* --- DEVOLVER TODOS LOS USUARIOS --- */
    $app->get('/usuarios',function() use($app,$db){
        $consulta = "SELECT * FROM usuarios";
        $query = $db->query($consulta);

        if($query->num_rows > 1){
            $usuarios = array();
            while($usuario = $query->fetch_assoc()){
                $usuarios[] = $usuario;
            }

            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$usuarios
            );
        }else{
            $result = array(
                'status'=>'success',
                'code'=>201,
                'data'=>'No hay usuarios a parte del administrador'
            );
        }

        echo json_encode($result);
    });

    /* --- DEVOLVER USUARIOS CON FILTRO --- */
    $app->get('/usuariosfiltro/:filtro',function($filtro) use($app,$db){
        $consulta = "SELECT * FROM usuarios WHERE nombre LIKE '%".$filtro."%'";
        $query = $db->query($consulta);

        if($query->num_rows==0){
            $result = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No se han encontrado usuarios con ese criterio'
            );
        }else{
            $usuariosFiltro = array();
            while($usuario = $query->fetch_assoc()){
                $usuariosFiltro[] = $usuario;
            }

            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$usuariosFiltro
            );
        }

        echo json_encode($result);
    });

    /* --- HACER UN USUARIO COLABORADOR --- */
    $app->get('/colaborador/:id',function($id) use($app,$db){
        $consulta = "UPDATE usuarios SET tipo='c' WHERE id=".$id;
        $query = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>'Error al modificar el usuario'
        );

        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Usuario modificado correctamente'
            );
        }

        echo json_encode($result);
    });

    /* --- QUITAR A UN USUARIO DE COLABORADOR --- */
    $app->get('/normal/:id',function($id) use($app,$db){
        $consulta = "UPDATE usuarios SET tipo='n' WHERE id=".$id;
        $query = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>'Error al modificar el usuario'
        );

        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Usuario modificado correctamente'
            );
        }

        echo json_encode($result);
    });

    /* --- DEVOLVER TODOS LOS PAISES --- */
    $app->get('/paises',function() use($app,$db){
        $consulta = "SELECT id,nicename FROM paises";
        $query = $db->query($consulta);

        $paises = array();
        while($pais = $query->fetch_assoc()){
            $paises[] = $pais;
        }

        $result = array(
            'status'=>'success',
            'code'=>200,
            'data'=>$paises
        );

        echo json_encode($result);
    });

    /* --- DEVOLVER TODOS LOS GÉNEROS --- */
    $app->get('/generos',function() use($app,$db){
        $consulta = "SELECT * FROM generos";
        $query = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>'No hay generos'
        );

        if($query->num_rows==0){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'No hay generos'
            );
        }else{
            $generos = array();
            while($genero = $query->fetch_assoc()){
                $generos[] = $genero;
            }

            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$generos
            );
        }
        echo json_encode($result);
    });

    /* --- DEVOLVER TODOS LOS AUTORES --- */
    $app->get('/autores',function() use($app,$db){
        $consulta = "SELECT * FROM autores";
        $query = $db->query($consulta);

        if($query->num_rows==0){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'No hay autores'
            );
        }else{
            $autores = array();
            while($autor = $query->fetch_assoc()){
                $autores[] = $autor;
            }

            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$autores
            );
        }
        echo json_encode($result);
    });

    /* --- CREAR NUEVO LIBRO --- */
    $app->post('/nuevolibro',function() use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);

        if(empty($data['portada'])){
            $data['portada'] = NULL;
        }

        $now = new DateTime();
        $consulta = "INSERT INTO libros VALUES (".
            "'{$data['isbn']}',".
            "'{$data['titulo']}',".
            "'{$data['paginas']}',".
            "'{$data['fechaAlta']}',".
            "'{$data['genero']}',".
            "'{$data['portada']}');";
        
        $query = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>'Error al registrar libro'
        );

        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Libro registrado correctamente'
            );
        }

        echo json_encode($result);
    });

    /* --- OBTENER UN LIBRO CONCRETO --- */
    $app->get('/libro/:isbn',function($isbn) use($app,$db){
        $consulta = "SELECT * FROM libros WHERE isbn=".$isbn;
        $query = $db->query($consulta);

        if($query->num_rows>0){
            $libro = $query->fetch_assoc();
            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$libro
            );
        }else{
            $result = array(
                'status'=>'erro',
                'code'=>404,
                'message'=>'No se ha encontrado ningun libro con ese isbn'
            ); 
        }

        echo json_encode($result);
    });

    /* --- OBTENER TODOS LOS LIBROS --- */
    $app->get('/libros',function() use($app,$db){
        $consulta = "SELECT * FROM libros";

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>'No hay libros'
        );

        $query = $db->query($consulta);
        if($query->num_rows == 0){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'No hay libros'
            );
        }else{
            $libros = array();
            while($libro = $query->fetch_assoc()){
                $libros[] = $libro;
            }
            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$libros
            );
        }

        echo json_encode($result);
    });

    /* --- DEVOLVER LIBROS CON FILTRO --- */
    $app->get('/librosfiltro/:filtro',function($filtro) use($app,$db){
        $consulta = "SELECT * FROM libros WHERE titulo LIKE '%".$filtro."%'";
        $query = $db->query($consulta);

        if($query->num_rows==0){
            $result = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No se han encontrado libros con ese criterio'
            );
        }else{
            $librosFiltro = array();
            while($libro = $query->fetch_assoc()){
                $librosFiltro[] = $libro;
            }

            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$librosFiltro
            );
        }

        echo json_encode($result);
    });

    /* --- GUARDAR LOS AUTORES DE UN LIBRO --- */
    $app->post('/autoreslibros/:isbn',function($isbn) use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);

        foreach ($data as $autor) {
            $consulta = "INSERT INTO autores_libros VALUES(".$autor.",'".$isbn."');";
            $query = $db->query($consulta);
        }

        $result = array(
            'status'=>'success',
            'code'=>200,
            'message'=>'Autor asignado correctamente'
        );

        echo json_encode($result);
    });

    /* --- SUBIR IMAGEN DE UN LIBRO --- */
    $app->post('/upload-cover',function() use($app,$db){
        $result = array(
            'status' => 'error',
            'code' => 404,
            'message' => 'El archivo no ha podido subirse'
        );

        if(isset($_FILES['uploads'])){
            $piramideUploader = new PiramideUploader();

            $upload = $piramideUploader->upload('image','uploads','../src/app/portadas',array('image/jpeg','image/png'));
            $file = $piramideUploader->getInfoFile();
            $file_name = $file['complete_name'];

            if(isset($upload) && $upload['uploaded']==false){
                $result = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El archivo no ha podido subirse'
                );
            }else{
                $result = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El archivo se ha subido',
                    'filename' => $file_name
                );
            }
        }

        echo json_encode($result);
    });

    $app->run();