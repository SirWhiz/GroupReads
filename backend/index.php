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

    /* --- DEVOLVER TOTAL DE LIBROS --- */
    $app->get('/totallibros',function() use($app,$db){
        $consulta = "SELECT COUNT(isbn) as total FROM libros";
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

    /* --- DEVOLVER TOTAL DE AUTORES --- */
    $app->get('/totalautores',function() use($app,$db){
        $consulta = "SELECT COUNT(id) as total FROM autores";
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

    /* --- DEVOLVER AUTORES CON FILTRO --- */
    $app->get('/autoresfiltro/:filtro',function($filtro) use($app,$db){
        $consulta = "SELECT * FROM autores WHERE nombre_ape LIKE '%".$filtro."%'";
        $query = $db->query($consulta);

        if($query->num_rows==0){
            $result = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'No se han encontrado usuarios con ese criterio'
            );
        }else{
            $autoresFiltro = array();
            while($autor = $query->fetch_assoc()){
                $autoresFiltro[] = $autor;
            }

            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$autoresFiltro
            );
        }

        echo json_encode($result);
    });

    /* --- DEVOLVER SOLICITUDES PENDIENTES DE AMISTAD --- */
    $app->get('/solicitudespen/:id',function($id) use($app,$db){
        $consulta = "SELECT COUNT(*) as total FROM amigos WHERE idAmigo=".$id." AND pendiente=1";
        $query = $db->query($consulta);

        if($query){
            $fila = $query->fetch_assoc();
            $total = $fila['total'];
            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$total
            );
        }else{
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>$consulta
            );
        }
        echo json_encode($result);
    });

    /* --- DEVOLVER LOS USUARIOS QUE NO SON AMIGOS DE UN USUARIO --- */
    $app->get('/noamigos/:id',function($id) use($app,$db){
        $consulta = "SELECT * FROM usuarios WHERE usuarios.id NOT IN (SELECT  id FROM usuarios INNER JOIN amigos ON usuarios.id=amigos.idUsuario WHERE idAmigo=".$id." AND idUsuario<>".$id." UNION SELECT  id FROM usuarios INNER JOIN amigos ON usuarios.id=amigos.idAmigo WHERE idUsuario=".$id." AND idAmigo<>".$id.") AND id<>1 AND id<>".$id;
        $query = $db->query($consulta);
        
        if(!$query){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>$consulta
            );
        }

        if($query->num_rows==0){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'No se han encontrado usuarios'
            );
        }else{
            $usuarios = array();
            while($usuario = $query->fetch_assoc()){
                $usuarios[] = $usuario;
            }
            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$usuarios
            );
        }
        echo json_encode($result);
    });

    /* --- DEVOLVER LOS USUARIOS QUE NO SON AMIGOS DE UN USUARIO FILTRADOS --- */
    $app->get('/noamigosfiltro/:id/:filtro',function($id,$filtro) use($app,$db){
        $consulta = "SELECT * FROM usuarios WHERE usuarios.id NOT IN (SELECT  id FROM usuarios INNER JOIN amigos ON usuarios.id=amigos.idUsuario WHERE idAmigo=".$id." AND idUsuario<>".$id." UNION SELECT  id FROM usuarios INNER JOIN amigos ON usuarios.id=amigos.idAmigo WHERE idUsuario=".$id." AND idAmigo<>".$id.") AND id<>1 AND id<>".$id." AND nombre LIKE '%".$filtro."%'";
        $query = $db->query($consulta);
        
        if(!$query){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>$consulta
            );
        }

        if($query->num_rows==0){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'No se han encontrado usuarios'
            );
        }else{
            $usuarios = array();
            while($usuario = $query->fetch_assoc()){
                $usuarios[] = $usuario;
            }
            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$usuarios
            );
        }
        echo json_encode($result);
    });

    /* --- DEVOLVER USUARIOS PENDIENTES DE ACEPTAR LA SOLICITUD --- */
    $app->get('/pendientes/:id',function($id) use($app,$db){
        $consulta = "SELECT  id,correo,nombre,apellidos,nick,fecha,foto FROM usuarios INNER JOIN amigos ON usuarios.id=amigos.idAmigo WHERE idUsuario=".$id." AND idAmigo<>".$id." AND pendiente=1 ";
        $query = $db->query($consulta);
        
        if(!$query){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>$consulta
            );
        }

        if($query->num_rows==0){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'No se han encontrado usuarios pendientes'
            );
        }else{
            $pendientes = array();
            while($pendiente = $query->fetch_assoc()){
                $pendientes[] = $pendiente;
            }
            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$pendientes
            );
        }
        echo json_encode($result);
    });

    /* --- DEVOLVER USUARIOS QUE HAN ENVIADO PETICIONES DE AMISTAD AL USUARIO --- */
    $app->get('/peticiones/:id',function($id) use($app,$db){
        $consulta = "SELECT  id,correo,nombre,apellidos,nick,fecha,foto FROM usuarios INNER JOIN amigos ON usuarios.id=amigos.idUsuario WHERE idUsuario<>".$id." AND idAmigo=".$id." AND pendiente=1 ";
        $query = $db->query($consulta);
        
        if(!$query){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>$consulta
            );
        }else{
            if($query->num_rows==0){
                $result = array(
                    'status'=>'error',
                    'code'=>404,
                    'message'=>'No se han encontrado peticiones pendientes'
                );
            }else{
                $pendientes = array();
                while($pendiente = $query->fetch_assoc()){
                    $pendientes[] = $pendiente;
                }
                $result = array(
                    'status'=>'success',
                    'code'=>200,
                    'data'=>$pendientes
                );
            }
        }
        echo json_encode($result);
    });

    /* --- DEVOLVER LOS AMIGOS DE UN USUARIO --- */
    $app->get('/amigos/:id',function($id) use($app,$db){
        $consulta = "SELECT nombre,apellidos,correo,fecha,foto,id,nick,pais,pwd,tipo FROM usuarios INNER JOIN amigos ON usuarios.id=amigos.idUsuario WHERE idAmigo=".$id." AND idUsuario<>".$id." AND pendiente=0 UNION SELECT nombre,apellidos,correo,fecha,foto,id,nick,pais,pwd,tipo FROM usuarios INNER JOIN amigos ON usuarios.id=amigos.idAmigo WHERE idUsuario=".$id." AND idAmigo<>".$id." AND pendiente=0";
        $query = $db->query($consulta);
        
        if(!$query){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>$consulta
            );
        }

        if($query->num_rows==0){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'No se han encontrado amigos'
            );
        }else{
            $amigos = array();
            while($amigo = $query->fetch_assoc()){
                $amigos[] = $amigo;
            }
            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$amigos
            );
        }
        echo json_encode($result);
    });

    /* --- DEVOLVER LOS AMIGOS FILTRADOS DE UN USUARIO --- */
    $app->get('/amigosfiltro/:id/:filtro',function($id,$filtro) use($app,$db){
        $consulta = "SELECT nombre,apellidos,correo,fecha,foto,id,nick,pais,pwd,tipo FROM usuarios INNER JOIN amigos ON usuarios.id=amigos.idUsuario WHERE idAmigo=".$id." AND idUsuario<>".$id." AND pendiente=0 AND nombre LIKE '%".$filtro."%' UNION SELECT nombre,apellidos,correo,fecha,foto,id,nick,pais,pwd,tipo FROM usuarios INNER JOIN amigos ON usuarios.id=amigos.idAmigo WHERE idUsuario=".$id." AND idAmigo<>".$id." AND pendiente=0 AND nombre LIKE '%".$filtro."%'";
        $query = $db->query($consulta);
        
        if(!$query){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>$consulta
            );
        }

        if($query->num_rows==0){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'No se han encontrado amigos'
            );
        }else{
            $amigos = array();
            while($amigo = $query->fetch_assoc()){
                $amigos[] = $amigo;
            }
            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$amigos
            );
        }
        echo json_encode($result);
    });

    /* --- BORRAR AMIGO --- */
    $app->get('/deleteamigo/:id/:idamigo',function($id,$idamigo) use($app,$db){
        $consulta = "DELETE FROM amigos WHERE idUsuario=".$id." AND idAmigo=".$idamigo." OR idAmigo=".$id." AND idUsuario=".$idamigo." AND pendiente=0";
        $query = $db->query($consulta);
        if(!$query){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Error al borrar amigo'
            );
        }else{
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Amigo borrado correctamente'
            );
        }

        echo json_encode($result);
    });

    /* --- HACER PETICION DE AMISTAD --- */
    $app->get('/peticionamigo/:id/:idamigo',function($id,$idamigo) use($app,$db){
        $consulta = "INSERT INTO amigos VALUES(".$id.",".$idamigo.",DEFAULT)";
        $query = $db->query($consulta);
        if(!$query){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Error al hacer la peticion'
            );
        }else{
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Peticion correcta'
            );
        }

        echo json_encode($result);
    });

    /* --- BORRAR PETICIÓN DE AMISTAD --- */
    $app->get('/deletepeticion/:id/:idamigo',function($id,$idamigo) use($app,$db){
        $consulta = "DELETE FROM amigos WHERE idUsuario=".$id." AND idAmigo=".$idamigo." AND pendiente=1";
        $query = $db->query($consulta);
        if(!$query){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Error al hacer la peticion'
            );
        }else{
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Peticion correcta'
            );
        }

        echo json_encode($result);
    });

    /* --- BORRAR SOLICITUD DE AMISTAD --- */
    $app->get('/deletesolicitud/:id/:idamigo',function($id,$idamigo) use($app,$db){
        $consulta = "DELETE FROM amigos WHERE idUsuario=".$idamigo." AND idAmigo=".$id." AND pendiente=1";
        $query = $db->query($consulta);
        if(!$query){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Error al hacer la peticion'
            );
        }else{
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Peticion correcta'
            );
        }

        echo json_encode($result);
    });

    /* --- ACEPTAR SOLICITUD DE AMISTAD --- */
    $app->get('/aceptarsolicitud/:id/:idamigo',function($id,$idamigo) use($app,$db){
        $consulta = "UPDATE amigos SET pendiente=0 WHERE idUsuario=".$idamigo." AND idAmigo=".$id;
        $query = $db->query($consulta);
        if(!$query){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Error al hacer la peticion'
            );
        }else{
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Peticion correcta'
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

    /* --- AÑADIR NUEVO GENERO --- */
    $app->post('/nuevogenero',function() use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);

        $consulta = "INSERT INTO generos VALUES (DEFAULT,'".$data['nombre']."');";
        $query=$db->query($consulta);

        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Genero creado correctamente',
                'id'=>$db->insert_id
            );
        }else{
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Error creando el genero'
            );
        }

        echo json_encode($result);
    });

    /* --- EDITAR UN GENERO --- */
    $app->post('/editargenero',function() use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);

        $consulta = "UPDATE generos SET nombre='".$data['nombre']."' WHERE id=".$data['id'];
        $query = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>'Error al modificar el genero'
        );

        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>$consulta
            );  
        }

        echo json_encode($result);
    });

    /* --- BORRAR UN GÉNERO --- */
    $app->get('/deletegenero/:id',function($id) use($app,$db){
        $consulta = "DELETE FROM generos WHERE id=".$id;
        $query = $db->query($consulta);

        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Genero borrado correctamente'
            );
        }else{
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Error al borrar el genero'
            );
        }

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

    /* --- DEVOLVER UN AUTOR CONCRETO --- */
    $app->get('/autor/:id',function($id) use($app,$db){
        $consulta = "SELECT * FROM autores WHERE id=".$id;
        $query = $db->query($consulta);

        if($query){
            if($query->num_rows == 1){
                $autor = $query->fetch_assoc();
                $result = array(
                    'status'=>'success',
                    'code'=>200,
                    'data'=>$autor
                );
            }else{
                $result = array(
                    'status'=>'error',
                    'code'=>404,
                    'message'=>'No se ha encontrado el autor'
                );
            }
        }else{
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'No se ha encontrado el autor'
            );
        }
        

        echo json_encode($result);
    });

    /* --- AÑADIR NUEVO AUTOR --- */
    $app->post('/nuevoautor',function() use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);

        if(empty($data['foto'])){
            $data['foto'] = NULL;
        }

        $consulta = "INSERT INTO autores VALUES (DEFAULT,".
            "'{$data['nombre_ape']}',".
            "'{$data['fecha']}',".
            "'{$data['pais']}',".
            "'{$data['foto']}');";
        
        $query = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>'Error al registrar autor'
        );

        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Autor registrado correctamente'
            );
        }

        echo json_encode($result);
    });

    /* --- ACTUALIZAR DATOS GENERALES DE UN AUTOR --- */
    $app->post('/updateautor',function() use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);

        $consulta = "UPDATE autores SET nombre_ape=".
                    "'{$data['nombre_ape']}',fecha_nacimiento=".
                    "'{$data['fecha_nacimiento']}',nacionalidad=".
                    "'{$data['nacionalidad']}' WHERE id=".$data['id'];

        $result = array(
            'status'=>'error',
            'code'=>404,
            'consulta'=>$consulta
        );

        $insert = $db->query($consulta);
        if($insert){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Autor actualizado correctamente'
            );
        }

        echo json_encode($result);
    });

    /* --- ACTUALIZAR IMAGEN DE UN AUTOR --- */
    $app->post('/actualizarfotoautor/:id',function($id) use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);

        $consulta = "UPDATE autores SET foto='".$data['foto']."' WHERE id=".$id;
        $query = $db->query($consulta);

        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Imagen actualizada correctamente'
            );
        }else{
            $result = array(
                'status'=>'error',
                'code'=>404,
                'consulta'=>$consulta
            );
        }

        echo json_encode($result);
    });

    /* --- BORRAR IMAGEN DE UN AUTOR --- */
    $app->post('/deletefotoautor',function() use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);

        unlink('../src/app/imgautores/'.$data['foto']);
        $consulta = "UPDATE autores SET foto=NULL WHERE id=".$data['id'];
        $query = $db->query($consulta);

        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Imagen borrada correctamente'
            );
        }else{
            $result = array(
                'status'=>'error',
                'code'=>404,
                'consulta'=>$consulta
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

    /* --- BORRAR UN AUTOR --- */
    $app->get('/deleteautor/:id',function($id) use($app,$db){

        $consulta = "SELECT * FROM autores_libros WHERE idAutor=".$id;
        $query = $db->query($consulta);
        while($libro = $query->fetch_assoc()){
            $consulta = "DELETE FROM libros WHERE isbn=".$libro['isbnLibro'].";";
            $db->query($consulta);
        }

        $consulta = "DELETE FROM autores WHERE id=".$id;
        $query = $db->query($consulta);

        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Autor borrado correctamente'
            );
        }else{
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Error al borrar el autor'
            );
        }

        echo json_encode($result);
    });

    /* --- COMPROBAR SI EXISTE UN ISBN --- */
    $app->get('/checkisbn/:isbn',function($isbn) use($app,$db){
        $consulta = "SELECT * FROM libros WHERE isbn=".$isbn;
        $query = $db->query($consulta);

        if($query->num_rows==0){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'No existe un libro con ese isbn'
            );
        }else{
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Ya existe un libro con ese isbn'
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

    /* --- DEVOLVER EL AUTOR/AUTORES DE UN LIBRO --- */
    $app->get('/autores/:isbn',function($isbn) use($app,$db){
        $consulta = "SELECT * FROM autores_libros WHERE isbnLibro=".$isbn;
        $query = $db->query($consulta);

        $autores = array();
        while($autor = $query->fetch_assoc()){
            $autores[] = $autor;
        }

        $result = array(
            'status'=>'success',
            'code'=>200,
            'data'=>$autores
        );

        echo json_encode($result);
    });

    /* --- BORRAR UN LIBRO --- */
    $app->post('/deletelibro',function() use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);
        $isbn = $data['isbn'];

        $consulta = "DELETE FROM autores_libros WHERE isbnLibro=".$isbn;
        $db->query($consulta);
        $consulta = "DELETE FROM libros WHERE isbn=".$isbn;
        $query = $db->query($consulta);

        if($query){
            if(!empty($data['portada'])){
                unlink('../src/app/portadas/'.$data['portada']);
            }

            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Libro borrado correctamente'
            );
        }else{
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Error al borrar el libro'
            );  
        }

        echo json_encode($result);
    });

    /* --- BORRAR IMAGEN DE UN LIBRO --- */
    $app->get('/deleteportada/:isbn',function($isbn) use($app,$db){
        $consulta = "SELECT * FROM libros WHERE isbn=".$isbn;
        $resultado = $db->query($consulta);
        $datos = $resultado->fetch_assoc();
        $portada = $datos['portada'];
        
        $consulta = "UPDATE libros SET portada=NULL WHERE isbn=".$isbn;
        $query = $db->query($consulta);

        if($query){
            unlink('../src/app/portadas/'.$portada);
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Portada borrada correctamente'
            );
        }else{
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Error al borrar la portada'
            );
        }

        echo json_encode($result);
    });

    /* --- ACTUALIZAR IMAGEN DE UN LIBRO --- */
    $app->post('/actualizarportada/:isbn',function($isbn) use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);

        $consulta = "UPDATE libros SET portada='".$data['portada']."' WHERE isbn='".$isbn."'";

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>'No se ha podido actualiza el libro'
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

    /* --- ACTUALIZAR AUTORES DE UN LIBRO --- */
    $app->post('/actualizarautores/:isbn',function($isbn) use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);

        $consulta = "DELETE FROM autores_libros WHERE isbnLibro=".$isbn;
        $db->query($consulta);

        foreach ($data as $autor) {
            $consulta = "INSERT INTO autores_libros VALUES(".$autor.",'".$isbn."');";
            $query = $db->query($consulta);
        }

        $result = array(
            'status'=>'success',
            'code'=>200,
            'message'=>'Autores actualizados correctamente'
        );

        echo json_encode($result);
    });

    /* --- ACTUALIZAR DATOS GENERALES DE UN LIBRO --- */
    $app->post('/actualizarlibro/:isbn',function($isbn) use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);

        $consulta = "UPDATE libros SET isbn=".
                    "{$data['isbn']},titulo=".
                    "'{$data['titulo']}',paginas=".
                    "'{$data['paginas']}',idGenero=".
                    "'{$data['idGenero']}' WHERE isbn=".$isbn;

        $result = array(
            'status'=>'error',
            'code'=>404,
            'consulta'=>$consulta
        );

        $insert = $db->query($consulta);
        if($insert){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Libro actualizado correctamente'
            );
        }

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

    /* --- SUBIR IMAGEN DE UN AUTOR --- */
    $app->post('/upload-autor',function() use($app,$db){
        $result = array(
            'status' => 'error',
            'code' => 404,
            'message' => 'El archivo no ha podido subirse'
        );

        if(isset($_FILES['uploads'])){
            $piramideUploader = new PiramideUploader();

            $upload = $piramideUploader->upload('image','uploads','../src/app/imgautores',array('image/jpeg','image/png'));
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