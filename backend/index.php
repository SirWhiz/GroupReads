<?php
    require_once 'vendor/autoload.php';
    require_once('piramide-uploader/PiramideUploader.php');

    $app = new \Slim\Slim();
    $db = new mysqli('localhost','root','Alvaroadcarry123','groupreads');
    $db2 = new mysqli('localhost','root','Alvaroadcarry123','groupreads');
    $db->set_charset("utf8");
    $db2->set_charset("utf8");

    /* --- CONFIGURAR CABECERAS --- */
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");
    $method = $_SERVER['REQUEST_METHOD'];
    if($method == "OPTIONS") {
        die();
    }

    /* --- Comprobar si hay conexion --- */
    $app->get('/checkconn',function() use($app,$db){
        $conn = new mysqli('localhost','root','Alvaroadcarry','groupreads');

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        echo "Bien";
    });

    /* --- ENVIAR CORREO CONTRASEÑA OLVIDADA --- */
    $app->get('/forgotpwd/:mail',function($mail) use($app,$db){
        
        /*Generar contraseña aleatoria*/
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 10; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        $copia = $randomString;
        $randomString = password_hash($randomString, PASSWORD_BCRYPT);
        $consulta = "UPDATE usuarios SET pwd='".$randomString."' WHERE correo='".$mail."'";
        $query = $db->query($consulta);

        if($query){
            $texto = "Hola, para acceder a tu cuenta usa esta contraseña: <b>".$copia."</b> recuerda que debes cambiarla para mayor seguridad";
            $destinatario = $mail;
            $asunto = "Recuperación de contraseña";
            $header='MIME-Version: 1.0'."\r\n";
            $header.= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
            $header.= 'From: Password <info@groupreads.com>' . "\r\n";
            $exito = mail($destinatario,$asunto,$texto,$header);

            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Contraseña modificada correctamente'
            );
        }else{
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Error al modificar la contraseña'
            );
        }

        echo json_encode($result);
    });

    /* --- REINSTALAR APLICACIÓN --- */
    $app->get('/restart',function() use($app,$db){
        $consulta = "DELETE FROM amigos";
        $db->query($consulta);

        $consulta = "DELETE FROM usuarios WHERE correo<>'admin@admin.com'";
        $db->query($consulta);

        $consulta = "DELETE FROM autores";
        $db->query($consulta);

        $consulta = "DELETE FROM libros";
        $db->query($consulta);

        $consulta = "DELETE FROM clubes";
        $db->query($consulta);

        $consulta = "DELETE FROM libros_para_votar";
        $db->query($consulta);

        $consulta = "DELETE FROM generos";
        $db->query($consulta);

        $result = array(
            'status'=>'success',
            'code'=>200,
            'message'=>'Datos borrados correctamente'
        );

        echo json_encode($result);
    });

    /* --- COMPROBAR SI SE PUEDE INSTALAR --- */
    $app->get('/checkinstall',function() use($app,$db){

        $result = $db->query("SHOW TABLES LIKE 'usuarios'");
        if ($result->num_rows == 1) {
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Ya existe la estructura'
            );
        }
        else {
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'No existe la estructura'
            );
        }

        echo json_encode($result);

    });

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

    /* --- CONVERTIR USUARIO A PROPIETARIO DE CLUB --- */
    $app->get('/convertuser/:idusuario/:idclub',function($idusuario,$idclub) use($app,$db){
        $consulta = "UPDATE clubes SET idCreador=".$idusuario." WHERE id=".$idclub;

        $query = $db->query($consulta);
        if($query){
            $result = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Usuario convertido correctamente'
            );
        }else{
            $result = array(
                'status' => 'error',
                'code' => 404,
                'message' => $consulta
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

    /* --- COMPROBAR SI UN USUARIO ESTÁ EN ALGÚN CLUB --- */
    $app->get('/checkuserclub/:id', function($id) use($app,$db){
        $consulta = "SELECT * FROM usuarios_clubes WHERE pendiente=0 AND idUsuario=".$id;
        $query = $db->query($consulta);

        if($query->num_rows==0){
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'El usuario no esta en ningun club'
            );
        }else{
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'El usuario pertenece a algun club'
            );
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

    /* --- DEVOLVER TOTALES PARA EL ADMIN --- */
    $app->get('/totalesadmin',function() use($app,$db){
        $consulta = "SELECT COUNT(*) as usuarios FROM usuarios WHERE tipo<>'a'";
        $query = $db->query($consulta);
        $fila = $query->fetch_assoc();
        $usuarios = $fila['usuarios'];

        $consulta = "SELECT COUNT(*) as libros FROM libros";
        $query = $db->query($consulta);
        $fila = $query->fetch_assoc();
        $libros = $fila['libros'];

        $consulta = "SELECT COUNT(*) as autores FROM autores";
        $query = $db->query($consulta);
        $fila = $query->fetch_assoc();
        $autores = $fila['autores'];        

        $data = array(
            'usuarios'=>$usuarios,
            'libros'=>$libros,
            'autores'=>$autores
        );

        $result = array(
            'status'=>'success',
            'code'=>200,
            'data'=>$data
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

    /* --- OBTENER LOS MENSAJES DEL CHAT --- */
    $app->get('/getmessages/:id/:idamigo',function($id,$idamigo) use($app,$db){
        $consulta = "SELECT * FROM mensajes WHERE de=".$id." AND para=".$idamigo." OR de=".$idamigo." AND para=".$id;
        $query = $db->query($consulta);

        if($query){
            $mensajes = array();
            while($fila = $query->fetch_assoc()){
                $mensajes[] = $fila;
            }
            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$mensajes
            );
        }else{
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Error'
            );
        }

        echo json_encode($result);
    });

    /* --- ENVIAR MENSAJE CHAT --- */
    $app->post('/sendmessage',function() use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);

        $consulta = "INSERT INTO mensajes VALUES(DEFAULT,".$data['de'].",".$data['para'].",'".$data['texto']."')";
        $query=$db->query($consulta);

        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Mensaje enviado correctamente',
            );
        }else{
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Error al enviar el mensaje'
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
            "'{$data['fecha_nacimiento']}',".
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
                'message'=>'Autor registrado correctamente',
                'query'=>$consulta
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
    $app->get('/deleteautor/:id',function($id) use($app,$db,$db2){

        $consulta = "SELECT * FROM autores_libros WHERE id=".$id;
        $query = $db->query($consulta);
        while($libro = $query->fetch_assoc()){
            $consulta = "DELETE FROM libros WHERE isbn=".$libro['isbnLibro'].";";
            $db2->query($consulta);
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

    /* --- OBTENER SUGERENCIAS DE AMIGOS --- */
    $app->get('/sugerencias/:id',function($id) use($app,$db){
        $consulta = "SELECT * FROM usuarios WHERE usuarios.id NOT IN (SELECT  id FROM usuarios INNER JOIN amigos ON usuarios.id=amigos.idUsuario WHERE idAmigo=".$id." AND idUsuario<>".$id." UNION SELECT  id FROM usuarios INNER JOIN amigos ON usuarios.id=amigos.idAmigo WHERE idUsuario=".$id." AND idAmigo<>".$id.") AND id<>1 AND id<>".$id." LIMIT 4";
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

    /* --- CREAR UN NUEVO CLUB --- */
    $app->post('/addclub',function() use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);

        $consulta = "INSERT INTO clubes VALUES (DEFAULT,".
            "'{$data['nombreClub']}',".
            "{$data['idCreador']},".
            "{$data['idGenero']},".
            "'{$data['privacidad']}',".
            "'{$data['descripcion']}');";
        $query = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>$consulta
        );

        if($query){
            $idclub = $db->insert_id;
            $consulta = "INSERT INTO usuarios_clubes VALUES(".$idclub.",".$data['idCreador'].",DEFAULT)";
            $db->query($consulta);

            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Club registrado correctamente'
            );
        }

        echo json_encode($result);
    });

    /* --- COMPROBAR SI HAY UNA VOTACIÓN EN MARCHA --- */
    $app->get('/checkvotacion/:idclub',function($idclub) use($app,$db){
        $consulta = "SELECT * FROM libros_para_votar WHERE idClub=".$idclub;
        $query = $db->query($consulta);

        if($query){
            if($query->num_rows>0){
                $result = array(
                    'status'=>'error',
                    'code'=>200,
                    'message'=>'Hay una votacion en curso'
                );
            }else{
                $result = array(
                    'status'=>'error',
                    'code'=>404,
                    'message'=>'No hay votacion en curso'
                );    
            }
        }else{
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>$consulta
            );
        }

        echo json_encode($result);
    });

    /* --- DEVOLVER LOS LIBROS QUE SE ESTÁN VOTANDO --- */
    $app->get('/getvotingbooks/:idclub',function($idclub) use($app,$db,$db2){
        $consulta = "SELECT isbn,titulo,paginas,portada FROM libros INNER JOIN libros_para_votar ON libros_para_votar.isbn1 = libros.isbn WHERE libros_para_votar.idClub=".$idclub." UNION
            SELECT isbn,titulo,paginas,portada FROM libros INNER JOIN libros_para_votar ON libros_para_votar.isbn2 = libros.isbn WHERE libros_para_votar.idClub=".$idclub." UNION
            SELECT isbn,titulo,paginas,portada FROM libros INNER JOIN libros_para_votar ON libros_para_votar.isbn3 = libros.isbn WHERE libros_para_votar.idClub=".$idclub;
        $query = $db->query($consulta);

        if($query){
            $resultado = array();
            while($fila = $query->fetch_assoc()){
                $consulta = "SELECT COUNT(*) as votos FROM votaciones WHERE idClub=".$idclub." AND voto=".$fila['isbn'];
                $queryVotos = $db2->query($consulta);
                $total = $queryVotos->fetch_assoc();
                $votos = $total['votos'];
                $opcion = array(
                    'isbn'=>$fila['isbn'],
                    'titulo'=>$fila['titulo'],
                    'portada'=>$fila['portada'],
                    'paginas'=>$fila['paginas'],
                    'votos'=>$votos
                );
                $resultado[] = $opcion;
            }
            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$resultado
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

    /* --- ASIGNAR LOS LIBROS QUE VA A VOTAR UN CLUB --- */
    $app->get('/setvotacion/:idclub/:isbn1/:isbn2/:isbn3',function($idclub,$isbn1,$isbn2,$isbn3) use($app,$db){

        $consulta = "DELETE FROM libros_para_votar WHERE idClub=".$idclub;
        $db->query($consulta);

        $consulta = "DELETE FROM votaciones WHERE idClub=".$idclub;
        $db->query($consulta);

        $consulta = "INSERT INTO libros_para_votar VALUES (DEFAULT,".$idclub.",".$isbn1.",".$isbn2.",".$isbn3.",0);";

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>$consulta
        );

        $insert = $db->query($consulta);
        if($insert){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Club actualizado correctamente'
            );
        }

        echo json_encode($result);
    });

    /* --- COMPROBAR SI UN USUARIO HA VOTADO UN LIBRO --- */
    $app->get('/checkuservote/:id',function($id) use($app,$db){

        $consulta = "SELECT * FROM votaciones WHERE idUsuario=".$id;
        $insert = $db->query($consulta);

        if($insert){
            if($insert->num_rows==0){
                $result = array(
                    'status'=>'success',
                    'code'=>201,
                    'message'=>'El usuario no ha votado'
                );    
            }else{
                $result = array(
                    'status'=>'success',
                    'code'=>200,
                    'message'=>'El usuario ha votado'
                );
            }            
        }else{
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Error con la consulta'
            );
        }

        echo json_encode($result);
    });

    /* --- OBTENER EL LIBRO QUE LIBRO HA VOTADO UN USUARIO --- */
    $app->get('/checkvotedbook/:id',function($id) use($app,$db){

        $consulta = "SELECT isbn,titulo,portada,paginas FROM libros INNER JOIN votaciones ON libros.isbn=votaciones.voto WHERE idUsuario=".$id;
        $insert = $db->query($consulta);

        if($insert){
            $libro = $insert->fetch_assoc();
            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$libro
            );           
        }else{
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Error con la consulta'
            );
        }

        echo json_encode($result);
    });

    /* --- VOTAR UN LIBRO --- */
    $app->get('/votar/:id/:idclub/:isbn',function($id,$idclub,$isbn) use($app,$db){

        $consulta = "INSERT INTO votaciones VALUES(DEFAULT,".$idclub.",".$id.",".$isbn.")";

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>$consulta
        );

        $insert = $db->query($consulta);
        if($insert){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Voto correcto'
            );
        }

        echo json_encode($result);
    });

    /* --- QUITAR UN VOTO --- */
    $app->get('/removevote/:id/:idclub/:isbn',function($id,$idclub,$isbn) use($app,$db){

        $consulta = "DELETE FROM votaciones WHERE idClub=".$idclub." AND idUsuario=".$id." AND voto=".$isbn;

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>$consulta
        );

        $insert = $db->query($consulta);
        if($insert){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Voto eliminado correctamente'
            );
        }

        echo json_encode($result);
    });

    /* --- ACABAR UNA VOTACION --- */
    $app->get('/endvote/:idclub/:isbn',function($idclub,$isbn) use($app,$db){

        $consulta = "DELETE FROM votaciones WHERE idClub=".$idclub;
        $db->query($consulta);

        $consulta = "DELETE FROM libros_para_votar WHERE idClub=".$idclub;
        $db->query($consulta);

        $fechaAprox = date('Y-m-d', strtotime('+15 days'));
        $consulta = "INSERT INTO libros_clubes VALUES (DEFAULT,".$idclub.",".$isbn.",0,'".$fechaAprox."');";
        $insert = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>$consulta
        );

        if($insert){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Libro asignado correctamente'
            );
        }

        echo json_encode($result);
    });

    /* --- OBTENER EL CLUB DE UN USUARIO --- */
    $app->get('/getclub/:id',function($id) use($app,$db){
        $consulta = "SELECT id,nombreClub,idCreador,idGenero,privacidad,descripcion FROM clubes INNER JOIN usuarios_clubes ON clubes.id=usuarios_clubes.idClub WHERE pendiente=0 AND idUsuario=".$id;
        $query = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>$consulta
        );

        if($query){
            if($query->num_rows==0){
                $result = array(
                    'status'=>'error',
                    'code'=>404,
                    'message'=>$consulta
                );
            }else{
                $data = $query->fetch_assoc();
                $result = array(
                    'status'=>'success',
                    'code'=>200,
                    'data'=>$data
                ); 
            }           
        }

        echo json_encode($result);
    });

    /* --- OBTENER LOS CLUBES DISPONIBLES --- */
    $app->get('/freeclubs',function() use($app,$db){
        $consulta = "SELECT clubes.id,nombreClub,idCreador,idGenero,generos.nombre,privacidad,descripcion FROM clubes INNER JOIN generos WHERE generos.id=clubes.idGenero";
        $query = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>$consulta
        );

        if($query){
            $clubes = array();
            while ($club = $query->fetch_assoc()) {
                $clubes[] = $club;
            }
            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$clubes
            );            
        }

        echo json_encode($result);
    });

    /* --- OBTENER LOS CLUBES SOLICITADOS --- */
    $app->get('/requestedclubs/:id',function($id) use($app,$db){
        $consulta = "SELECT clubes.id,nombreClub,idCreador,idGenero,generos.nombre,privacidad,descripcion FROM usuarios_clubes INNER JOIN clubes ON clubes.id=usuarios_clubes.idClub INNER JOIN generos ON generos.id=clubes.idGenero WHERE usuarios_clubes.idUsuario=".$id." AND usuarios_clubes.pendiente=1";
        $query = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>$consulta
        );

        if($query){
            if($query->num_rows==0){
                $result = array(
                    'status'=>'success',
                    'code'=>400,
                    'message'=>'No ha solicitado ningun club'
                );
            }else{
                $clubes = array();
                while ($club = $query->fetch_assoc()) {
                    $clubes[] = $club;
                }
                $result = array(
                    'status'=>'success',
                    'code'=>200,
                    'data'=>$clubes
                );
            }            
        }

        echo json_encode($result);
    });

    /* --- BORRAR SOLICITUD A UN CLUB --- */
    $app->get('/deleteclubreq/:id/:idclub',function($id,$idclub) use($app,$db){
        $consulta = "DELETE FROM usuarios_clubes WHERE idClub=".$idclub." AND idUsuario=".$id." AND pendiente=1";
        $query = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>$consulta
        );

        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Solicitud borrada correctamente'
            );            
        }

        echo json_encode($result);
    });

    /* --- ACEPTAR SOLICITUD A UN CLUB --- */
       $app->get('/confirmclubreq/:id/:idclub',function($id,$idclub) use($app,$db){
        $consulta = "UPDATE usuarios_clubes SET pendiente=0 WHERE idClub=".$idclub." AND idUsuario=".$id;
        $query = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>$consulta
        );

        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Solicitud aceptada correctamente'
            );            
        }

        echo json_encode($result);
    });

    /* --- EDITAR DATOS DE UN CLUB --- */
    $app->post('/editclub',function() use($app,$db){
        $json = $app->request->post('json');
        $data = json_decode($json,true);

        $consulta = "UPDATE clubes SET nombreClub=".
                    "'{$data['nombreClub']}',idGenero=".
                    "{$data['idGenero']},privacidad=".
                    "'{$data['privacidad']}',descripcion=".
                    "'{$data['descripcion']}' WHERE id=".
                    "{$data['id']};";

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>$consulta
        );

        $insert = $db->query($consulta);
        if($insert){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Club actualizado correctamente'
            );
        }

        echo json_encode($result);
    });

    /* --- BORRAR UN CLUB --- */
    $app->get('/deleteclub/:id',function($id) use($app,$db){
        $consulta = "DELETE FROM clubes WHERE id=".$id;
        $query = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>$consulta
        );

        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Club borrado correctamente'
            );            
        }

        echo json_encode($result);
    });

    /* --- UNIRSE A UN CLUB --- */
    $app->get('/joinclub/:id/:idclub',function($id,$idclub) use($app,$db){
        $consulta = "INSERT INTO usuarios_clubes VALUES(".$idclub.",".$id.",DEFAULT)";
        $query = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>$consulta
        );

        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Usuario apuntado correctamente'
            );            
        }

        echo json_encode($result);
    });

    /* --- SOLICITAR UN CLUB --- */
    $app->get('/requestclub/:id/:idclub',function($id,$idclub) use($app,$db){
        $consulta = "INSERT INTO usuarios_clubes VALUES(".$idclub.",".$id.",1)";
        $query = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>$consulta
        );

        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Usuario apuntado correctamente'
            );            
        }

        echo json_encode($result);
    });

    /* --- ABANDONAR UN CLUB --- */
    $app->get('/dejarclub/:id/:idclub',function($id,$idclub) use($app,$db){
        $consulta = "DELETE FROM usuarios_clubes WHERE idClub=".$idclub." AND idUsuario=".$id;
        $query = $db->query($consulta);

        $consulta = "DELETE FROM votaciones WHERE idUsuario=".$id;
        $query = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>$consulta
        );

        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Usuario borrado correctamente'
            );            
        }

        echo json_encode($result);
    });

    /* --- OBTENER LOS MIEMBROS DE UN CLUB --- */
    $app->get('/miembros/:idclub',function($idclub) use($app,$db){
        $consulta = "SELECT * FROM usuarios INNER JOIN usuarios_clubes ON usuarios.id=usuarios_clubes.idUsuario WHERE idClub=".$idclub." AND pendiente=0";
        $query = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>$consulta
        );

        if($query){
            $miembros = array();
            while($miembro = $query->fetch_assoc()){
                $miembros[] = $miembro;
            }
            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$miembros
            );            
        }

        echo json_encode($result);
    });

    /* --- EXPULSAR A UN MIEMBRO DE UN CLUB --- */
    $app->get('/kickmember/:id/:idclub',function($id,$idclub) use($app,$db){
        $consulta = "DELETE FROM usuarios_clubes WHERE idClub=".$idclub." AND idUsuario=".$id;
        $query = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>$consulta
        );

        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Usuario expulsado correctamente'
            );            
        }

        echo json_encode($result);
    });

    /* --- OBTENER LAS PETICIONES DE UN CLUB --- */
    $app->get('/getrequests/:idclub',function($idclub) use($app,$db){
        $consulta = "SELECT id,correo,nombre,apellidos,nick,fecha,pwd,foto,pais,tipo FROM usuarios INNER JOIN usuarios_clubes ON usuarios.id=usuarios_clubes.idUsuario WHERE idClub=".$idclub." AND pendiente=1";
        $query = $db->query($consulta);

        $result = array(
            'status'=>'error',
            'code'=>404,
            'message'=>$consulta
        );

        if($query){
            $peticiones = array();
            while($peticion = $query->fetch_assoc()){
                $peticiones[] = $peticion;
            }
            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$peticiones
            );            
        }

        echo json_encode($result);
    });

    /* --- COMPROBAR SI UN CLUB SE ESTÁ LEYENDO UN LIBRO --- */
    $app->get('/checkbook/:idclub',function($idclub) use($app,$db){
        $consulta = "SELECT * FROM libros_clubes WHERE finalizado=0 AND idClub=".$idclub;
        $query = $db->query($consulta);

        if($query->num_rows>0){
            $fila = $query->fetch_assoc();
            $consulta = "SELECT * FROM libros WHERE isbn=".$fila['isbnLibro'];
            $resultado = $db->query($consulta);
            $filaLibro = $resultado->fetch_assoc();
            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$filaLibro
            );
        }else{
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'El club no se está leyendo un libro'
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

    /* --- OBTENER LOS COMENTARIOS DE UN LIBRO DE UN CLUB --- */
    $app->get('/getcomments/:idclub/:isbn',function($idclub,$isbn) use($app,$db,$db2){
        $consulta = "SELECT * FROM comentarios_libros WHERE idClub=".$idclub." AND isbnLibro=".$isbn;
        $query = $db->query($consulta);

        if($query){
            $comentarios = array();
            while($fila = $query->fetch_assoc()){
                $id = $fila['idComentario'];
                $idClub = $fila['idClub'];
                $texto = $fila['comentario'];

                $consulta = "SELECT * FROM usuarios WHERE id=".$fila['idUsuario'];
                $resultado = $db2->query($consulta);
                $fila = $resultado->fetch_assoc();
                $nombreUsuario = $fila['nombre'];
                $foto = $fila['foto'];

                $comentario = array(
                    'id'=>$id,
                    'idclub'=>$idclub,
                    'nombreUsuario'=>$nombreUsuario,
                    'foto'=>$foto,
                    'comentario'=>$texto
                );
                $comentarios[] = $comentario;
            }
            $result = array(
                'status'=>'success',
                'code'=>200,
                'data'=>$comentarios
            );

        }else{
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Error al realizar la consulta'
            );
        }

        echo json_encode($result);
    });

    /* --- COMENTAR UN LIBRO --- */
    $app->get('/comment/:id/:idclub/:isbn/:comentario',function($id,$idclub,$isbn,$comentario) use($app,$db){
        $consulta = "INSERT INTO comentarios_libros VALUES(DEFAULT,".$idclub.",".$id.",".$isbn.",'".$comentario."')";
        $query = $db->query($consulta);

        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Comentario correcto'
            );
        }else{
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Error al comentar'
            ); 
        }

        echo json_encode($result);
    });

    /* --- FINALIZAR UN LIBRO --- */
    $app->get('/finishbook/:idclub/:isbn',function($idclub,$isbn) use($app,$db){
        $consulta = "UPDATE libros_clubes SET finalizado=1 WHERE idClub=".$idclub." AND isbnLibro=".$isbn;
        $query = $db->query($consulta);

        if($query){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Libro finalizado correctamente'
            );
        }else{
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Error al finalizar el libro'
            ); 
        }

        echo json_encode($result);
    });

    /* --- OBTENER TODOS LOS LIBROS --- */
    $app->get('/libros',function() use($app,$db){
        $consulta = "SELECT * FROM libros ORDER BY fechaAlta DESC";

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

    /* --- OBTENER TODOS LOS LIBROS CON EL NOMBRE DEL GENERO --- */
    $app->get('/librosgenero',function() use($app,$db){
        $consulta = "SELECT isbn,titulo,paginas,fechaAlta,idGenero,portada,generos.nombre as nombre_genero FROM libros INNER JOIN generos ON libros.idGenero=generos.id";

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

    /* --- OBTENER TODOS LOS LIBROS CON EL NOMBRE DEL GENERO FILTRADOS --- */
    $app->get('/librosgenerofilter/:filtro',function($filtro) use($app,$db){
        $consulta = "SELECT isbn,titulo,paginas,fechaAlta,idGenero,portada,generos.nombre as nombre_genero FROM libros INNER JOIN generos ON libros.idGenero=generos.id WHERE libros.idGenero=".$filtro;

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

    /* --- OBTENER LOS LIBROS QUE HA LEIDO UN CLUB --- */
    $app->get('/finishedbooks/:idclub',function($idclub) use($app,$db){
        $consulta = "SELECT DISTINCT isbn,titulo,paginas,portada,generos.nombre as nombre_genero FROM libros INNER JOIN libros_clubes ON libros.isbn=libros_clubes.isbnLibro INNER JOIN generos ON libros.idGenero=generos.id WHERE libros_clubes.finalizado=1 AND idClub=".$idclub;

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

    /* --- OBTENER LIBROS RECOMENDADOS PARA UN CLUB --- */
    $app->get('/recommendedbooks/:idclub',function($idclub) use($app,$db){
        $consulta = "SELECT idGenero FROM clubes WHERE id=".$idclub;
        $query = $db->query($consulta);
        $fila = $query->fetch_assoc();
        $generoPreferido = $fila['idGenero'];

        $librosLeidos = array();
        $consulta = "SELECT isbn,titulo,paginas,portada,generos.nombre as nombre_genero FROM libros INNER JOIN generos ON libros.idGenero=generos.id INNER JOIN libros_clubes ON libros.isbn=libros_clubes.isbnLibro WHERE libros_clubes.idClub=".$idclub;
        $query = $db->query($consulta);
        while($libro = $query->fetch_assoc()){
            $librosLeidos[] = $libro;
        }

        $consulta = "SELECT isbn,titulo,paginas,portada,generos.nombre as nombre_genero FROM libros INNER JOIN generos ON libros.idGenero=generos.id WHERE libros.idGenero=".$generoPreferido." LIMIT 2";
        $query = $db->query($consulta);

        $librosRecomendados = array();
        while($libro = $query->fetch_assoc()){
            if(!in_array($libro, $librosLeidos)){
                $librosRecomendados[] = $libro;
            }
        }

        $consulta = "SELECT id FROM autores_libros WHERE isbnLibro=(SELECT isbnLibro FROM libros_clubes WHERE finalizado=1 AND idClub=".$idclub." LIMIT 1)";
        $query = $db->query($consulta);
        $fila = $query->fetch_assoc();
        $ultimoAutor = $fila['id'];

        $consulta = "SELECT isbn,titulo,paginas,portada,generos.nombre as nombre_genero FROM libros INNER JOIN autores_libros ON libros.isbn=isbnLibro INNER JOIN generos ON libros.idGenero=generos.id WHERE autores_libros.id=".$ultimoAutor." LIMIT 1";
        $query = $db->query($consulta);
        while($libro = $query->fetch_assoc()){
            if(!in_array($libro, $librosRecomendados) AND !in_array($libro, $librosLeidos)){
                $librosRecomendados[] = $libro;
            }
        }

        $consulta = "SELECT isbn,titulo,paginas,portada,generos.nombre as nombre_genero FROM libros INNER JOIN generos ON libros.idGenero=generos.id WHERE isbn NOT IN (SELECT isbnLibro FROM libros_clubes WHERE finalizado=1 AND idClub=".$idclub.") LIMIT 3";
        $query = $db->query($consulta);
        while($libro = $query->fetch_assoc()){
            if(!in_array($libro, $librosRecomendados) AND !in_array($libro, $librosLeidos)){
                $librosRecomendados[] = $libro;
            }
        }

        $result = array(
            'status'=>'success',
            'code'=>200,
            'data'=>$librosRecomendados,
        );

        echo json_encode($result);
    });

    /* --- OBTENER LOS 5 ÚLTIMOS LIBROS --- */
    $app->get('/librostop',function() use($app,$db){
        $consulta = "SELECT * FROM libros ORDER BY fechaAlta DESC LIMIT 5";

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

    /* --- DEVOLVER TODOS LOS DATOS DEL AUTOR/AUTORES DE UN LIBRO --- */
    $app->get('/fullautores/:isbn',function($isbn) use($app,$db){
        $consulta = "SELECT autores.id,nombre_ape,fecha_nacimiento,paises.nombre as pais,foto FROM autores INNER JOIN paises ON autores.nacionalidad=paises.id INNER JOIN autores_libros ON autores.id=autores_libros.id WHERE autores_libros.isbnLibro=".$isbn;
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

    /* --- INSTALACION --- */
    $app->get('/install/:pwd',function($pwd) use($app,$db){

        $pwdEncriptada = password_hash($pwd, PASSWORD_BCRYPT);

        $consulta = "
            CREATE TABLE paises(
                id smallint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
                iso char(2) NOT NULL,
                nombre varchar(80) NOT NULL,
                nicename varchar(80) NOT NULL,
                iso3 char(3) DEFAULT NULL,
                numcode smallint(6) DEFAULT NULL,
                phonecode int(5) NOT NULL
            );

            CREATE TABLE usuarios(
                id smallint unsigned AUTO_INCREMENT PRIMARY KEY,
                correo varchar(100) UNIQUE NOT NULL,
                nombre varchar(60) NOT NULL,
                apellidos varchar(100) NOT NULL,
                nick varchar(40) NOT NULL,
                fecha date NOT NULL,
                pwd varchar(255) NOT NULL,
                foto varchar(100) NULL,
                pais smallint unsigned NOT NULL,
                tipo ENUM('n','a','c') NOT NULL,
                FOREIGN KEY (pais) REFERENCES paises(id)
            );

            CREATE TABLE amigos(
                idUsuario smallint unsigned,
                idAmigo smallint unsigned,
                pendiente boolean DEFAULT 1,
                PRIMARY KEY (idUsuario,idAmigo),
                FOREIGN KEY (idUsuario) REFERENCES usuarios(id),
                FOREIGN KEY (idAmigo) REFERENCES usuarios(id)
            );

            CREATE TABLE mensajes(
                id smallint unsigned AUTO_INCREMENT PRIMARY KEY,
                de smallint unsigned NOT NULL,
                para smallint unsigned NOT NULL,
                texto text NOT NULL,
                FOREIGN KEY (de) REFERENCES usuarios(id),
                FOREIGN KEY (para) REFERENCES usuarios(id)
            );

            CREATE TABLE generos(
                id smallint unsigned AUTO_INCREMENT PRIMARY KEY,
                nombre varchar(60) NOT NULL
            );

            CREATE TABLE clubes(
                id smallint unsigned AUTO_INCREMENT PRIMARY KEY,
                nombreClub varchar(60) NOT NULL,
                idCreador smallint unsigned,
                idGenero smallint unsigned NULL,
                privacidad ENUM('a','c') NOT NULL,
                descripcion text NOT NULL,
                FOREIGN KEY (idGenero) REFERENCES generos(id) ON DELETE CASCADE,
                FOREIGN KEY (idCreador) REFERENCES usuarios(id) ON DELETE CASCADE
            );

            CREATE TABLE usuarios_clubes(
                idClub smallint unsigned,
                idUsuario smallint unsigned,
                pendiente boolean DEFAULT 0 NOT NULL,
                PRIMARY KEY(idClub,idUsuario),
                FOREIGN KEY (idClub) REFERENCES clubes(id) ON DELETE CASCADE,
                FOREIGN KEY (idUsuario) REFERENCES usuarios(id) ON DELETE CASCADE
            );

            CREATE TABLE libros(
                isbn varchar(15) PRIMARY KEY,
                titulo varchar(80) NOT NULL,
                paginas smallint NOT NULL,
                fechaAlta date NOT NULL,
                idGenero smallint unsigned NOT NULL,
                portada varchar(100) NULL,
                FOREIGN KEY (idGenero) REFERENCES generos(id) ON DELETE CASCADE
            );

            CREATE TABLE autores(
                id smallint unsigned AUTO_INCREMENT PRIMARY KEY,
                nombre_ape varchar(150) NOT NULL,
                fecha_nacimiento date NOT NULL,
                nacionalidad smallint unsigned NOT NULL,
                foto varchar(100) NULL,
                FOREIGN KEY (nacionalidad) REFERENCES paises(id) ON DELETE CASCADE
            );

            CREATE TABLE autores_libros(
                id smallint unsigned NOT NULL,
                isbnLibro varchar(15) NOT NULL,
                PRIMARY KEY (id,isbnLibro),
                FOREIGN KEY (id) REFERENCES autores(id) ON DELETE CASCADE,
                FOREIGN KEY (isbnLibro) REFERENCES libros(isbn) ON DELETE CASCADE ON UPDATE CASCADE
            );

            CREATE TABLE libros_clubes(
                id smallint unsigned AUTO_INCREMENT PRIMARY KEY,
                idClub smallint unsigned NOT NULL,
                isbnLibro varchar(15) NOT NULL,
                finalizado boolean NOT NULL DEFAULT 0,
                fecha_fin date NOT NULL,
                FOREIGN KEY (idClub) REFERENCES clubes(id) ON DELETE CASCADE,
                FOREIGN KEY (isbnLibro) REFERENCES libros(isbn) ON DELETE CASCADE
            );

            CREATE TABLE comentarios_libros(
                idComentario smallint unsigned AUTO_INCREMENT PRIMARY KEY,
                idClub smallint unsigned NOT NULL,
                idUsuario smallint unsigned NOT NULL,
                isbnLibro varchar(15) NOT NULL,
                comentario text NOT NULL,
                FOREIGN KEY (idClub) REFERENCES clubes(id) ON DELETE CASCADE,
                FOREIGN KEY (idUsuario) REFERENCES usuarios(id) ON DELETE CASCADE,
                FOREIGN KEY (isbnLibro) REFERENCES libros(isbn) ON DELETE CASCADE
            );

            CREATE TABLE libros_para_votar(
                id smallint unsigned AUTO_INCREMENT PRIMARY KEY,
                idClub smallint unsigned NOT NULL,
                isbn1 varchar(15) NOT NULL,
                isbn2 varchar(15) NOT NULL,
                isbn3 varchar(15) NOT NULL,
                finalizado boolean DEFAULT 0 NOT NULL,
                FOREIGN KEY (idClub) REFERENCES clubes(id) ON DELETE CASCADE,
                FOREIGN KEY (isbn1) REFERENCES libros(isbn) ON DELETE CASCADE,
                FOREIGN KEY (isbn2) REFERENCES libros(isbn) ON DELETE CASCADE,
                FOREIGN KEY (isbn3) REFERENCES libros(isbn) ON DELETE CASCADE
            );

            CREATE TABLE votaciones(
                id smallint unsigned AUTO_INCREMENT PRIMARY KEY,
                idClub smallint unsigned NOT NULL,
                idUsuario smallint unsigned NOT NULL,
                voto varchar(15) NOT NULL,
                FOREIGN KEY (idClub) REFERENCES clubes(id) ON DELETE CASCADE,
                FOREIGN KEY (idUsuario) REFERENCES usuarios(id) ON DELETE CASCADE
            );

            INSERT INTO paises (id,iso,nombre,nicename,iso3,numcode,phonecode) VALUES
                (1, 'AF', 'AFGHANISTAN', 'Afghanistan', 'AFG', 4, 93),
                (2, 'AL', 'ALBANIA', 'Albania', 'ALB', 8, 355),
                (3, 'DZ', 'ALGERIA', 'Algeria', 'DZA', 12, 213),
                (4, 'AS', 'AMERICAN SAMOA', 'American Samoa', 'ASM', 16, 1684),
                (5, 'AD', 'ANDORRA', 'Andorra', 'AND', 20, 376),
                (6, 'AO', 'ANGOLA', 'Angola', 'AGO', 24, 244),
                (7, 'AI', 'ANGUILLA', 'Anguilla', 'AIA', 660, 1264),
                (8, 'AQ', 'ANTARCTICA', 'Antarctica', NULL, NULL, 0),
                (9, 'AG', 'ANTIGUA AND BARBUDA', 'Antigua and Barbuda', 'ATG', 28, 1268),
                (10, 'AR', 'ARGENTINA', 'Argentina', 'ARG', 32, 54),
                (11, 'AM', 'ARMENIA', 'Armenia', 'ARM', 51, 374),
                (12, 'AW', 'ARUBA', 'Aruba', 'ABW', 533, 297),
                (13, 'AU', 'AUSTRALIA', 'Australia', 'AUS', 36, 61),
                (14, 'AT', 'AUSTRIA', 'Austria', 'AUT', 40, 43),
                (15, 'AZ', 'AZERBAIJAN', 'Azerbaijan', 'AZE', 31, 994),
                (16, 'BS', 'BAHAMAS', 'Bahamas', 'BHS', 44, 1242),
                (17, 'BH', 'BAHRAIN', 'Bahrain', 'BHR', 48, 973),
                (18, 'BD', 'BANGLADESH', 'Bangladesh', 'BGD', 50, 880),
                (19, 'BB', 'BARBADOS', 'Barbados', 'BRB', 52, 1246),
                (20, 'BY', 'BELARUS', 'Belarus', 'BLR', 112, 375),
                (21, 'BE', 'BELGIUM', 'Belgium', 'BEL', 56, 32),
                (22, 'BZ', 'BELIZE', 'Belize', 'BLZ', 84, 501),
                (23, 'BJ', 'BENIN', 'Benin', 'BEN', 204, 229),
                (24, 'BM', 'BERMUDA', 'Bermuda', 'BMU', 60, 1441),
                (25, 'BT', 'BHUTAN', 'Bhutan', 'BTN', 64, 975),
                (26, 'BO', 'BOLIVIA', 'Bolivia', 'BOL', 68, 591),
                (27, 'BA', 'BOSNIA AND HERZEGOVINA', 'Bosnia and Herzegovina', 'BIH', 70, 387),
                (28, 'BW', 'BOTSWANA', 'Botswana', 'BWA', 72, 267),
                (29, 'BV', 'BOUVET ISLAND', 'Bouvet Island', NULL, NULL, 0),
                (30, 'BR', 'BRAZIL', 'Brazil', 'BRA', 76, 55),
                (31, 'IO', 'BRITISH INDIAN OCEAN TERRITORY', 'British Indian Ocean Territory', NULL, NULL, 246),
                (32, 'BN', 'BRUNEI DARUSSALAM', 'Brunei Darussalam', 'BRN', 96, 673),
                (33, 'BG', 'BULGARIA', 'Bulgaria', 'BGR', 100, 359),
                (34, 'BF', 'BURKINA FASO', 'Burkina Faso', 'BFA', 854, 226),
                (35, 'BI', 'BURUNDI', 'Burundi', 'BDI', 108, 257),
                (36, 'KH', 'CAMBODIA', 'Cambodia', 'KHM', 116, 855),
                (37, 'CM', 'CAMEROON', 'Cameroon', 'CMR', 120, 237),
                (38, 'CA', 'CANADA', 'Canada', 'CAN', 124, 1),
                (39, 'CV', 'CAPE VERDE', 'Cape Verde', 'CPV', 132, 238),
                (40, 'KY', 'CAYMAN ISLANDS', 'Cayman Islands', 'CYM', 136, 1345),
                (41, 'CF', 'CENTRAL AFRICAN REPUBLIC', 'Central African Republic', 'CAF', 140, 236),
                (42, 'TD', 'CHAD', 'Chad', 'TCD', 148, 235),
                (43, 'CL', 'CHILE', 'Chile', 'CHL', 152, 56),
                (44, 'CN', 'CHINA', 'China', 'CHN', 156, 86),
                (45, 'CX', 'CHRISTMAS ISLAND', 'Christmas Island', NULL, NULL, 61),
                (46, 'CC', 'COCOS (KEELING) ISLANDS', 'Cocos (Keeling) Islands', NULL, NULL, 672),
                (47, 'CO', 'COLOMBIA', 'Colombia', 'COL', 170, 57),
                (48, 'KM', 'COMOROS', 'Comoros', 'COM', 174, 269),
                (49, 'CG', 'CONGO', 'Congo', 'COG', 178, 242),
                (50, 'CD', 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'Congo, the Democratic Republic of the', 'COD', 180, 242),
                (51, 'CK', 'COOK ISLANDS', 'Cook Islands', 'COK', 184, 682),
                (52, 'CR', 'COSTA RICA', 'Costa Rica', 'CRI', 188, 506),
                (53, 'CI', 'COTE D''IVOIRE', 'Cote D''Ivoire', 'CIV', 384, 225),
                (54, 'HR', 'CROATIA', 'Croatia', 'HRV', 191, 385),
                (55, 'CU', 'CUBA', 'Cuba', 'CUB', 192, 53),
                (56, 'CY', 'CYPRUS', 'Cyprus', 'CYP', 196, 357),
                (57, 'CZ', 'CZECH REPUBLIC', 'Czech Republic', 'CZE', 203, 420),
                (58, 'DK', 'DENMARK', 'Denmark', 'DNK', 208, 45),
                (59, 'DJ', 'DJIBOUTI', 'Djibouti', 'DJI', 262, 253),
                (60, 'DM', 'DOMINICA', 'Dominica', 'DMA', 212, 1767),
                (61, 'DO', 'DOMINICAN REPUBLIC', 'Dominican Republic', 'DOM', 214, 1809),
                (62, 'EC', 'ECUADOR', 'Ecuador', 'ECU', 218, 593),
                (63, 'EG', 'EGYPT', 'Egypt', 'EGY', 818, 20),
                (64, 'SV', 'EL SALVADOR', 'El Salvador', 'SLV', 222, 503),
                (65, 'GQ', 'EQUATORIAL GUINEA', 'Equatorial Guinea', 'GNQ', 226, 240),
                (66, 'ER', 'ERITREA', 'Eritrea', 'ERI', 232, 291),
                (67, 'EE', 'ESTONIA', 'Estonia', 'EST', 233, 372),
                (68, 'ET', 'ETHIOPIA', 'Ethiopia', 'ETH', 231, 251),
                (69, 'FK', 'FALKLAND ISLANDS (MALVINAS)', 'Falkland Islands (Malvinas)', 'FLK', 238, 500),
                (70, 'FO', 'FAROE ISLANDS', 'Faroe Islands', 'FRO', 234, 298),
                (71, 'FJ', 'FIJI', 'Fiji', 'FJI', 242, 679),
                (72, 'FI', 'FINLAND', 'Finland', 'FIN', 246, 358),
                (73, 'FR', 'FRANCE', 'France', 'FRA', 250, 33),
                (74, 'GF', 'FRENCH GUIANA', 'French Guiana', 'GUF', 254, 594),
                (75, 'PF', 'FRENCH POLYNESIA', 'French Polynesia', 'PYF', 258, 689),
                (76, 'TF', 'FRENCH SOUTHERN TERRITORIES', 'French Southern Territories', NULL, NULL, 0),
                (77, 'GA', 'GABON', 'Gabon', 'GAB', 266, 241),
                (78, 'GM', 'GAMBIA', 'Gambia', 'GMB', 270, 220),
                (79, 'GE', 'GEORGIA', 'Georgia', 'GEO', 268, 995),
                (80, 'DE', 'GERMANY', 'Germany', 'DEU', 276, 49),
                (81, 'GH', 'GHANA', 'Ghana', 'GHA', 288, 233),
                (82, 'GI', 'GIBRALTAR', 'Gibraltar', 'GIB', 292, 350),
                (83, 'GR', 'GREECE', 'Greece', 'GRC', 300, 30),
                (84, 'GL', 'GREENLAND', 'Greenland', 'GRL', 304, 299),
                (85, 'GD', 'GRENADA', 'Grenada', 'GRD', 308, 1473),
                (86, 'GP', 'GUADELOUPE', 'Guadeloupe', 'GLP', 312, 590),
                (87, 'GU', 'GUAM', 'Guam', 'GUM', 316, 1671),
                (88, 'GT', 'GUATEMALA', 'Guatemala', 'GTM', 320, 502),
                (89, 'GN', 'GUINEA', 'Guinea', 'GIN', 324, 224),
                (90, 'GW', 'GUINEA-BISSAU', 'Guinea-Bissau', 'GNB', 624, 245),
                (91, 'GY', 'GUYANA', 'Guyana', 'GUY', 328, 592),
                (92, 'HT', 'HAITI', 'Haiti', 'HTI', 332, 509),
                (93, 'HM', 'HEARD ISLAND AND MCDONALD ISLANDS', 'Heard Island and Mcdonald Islands', NULL, NULL, 0),
                (94, 'VA', 'HOLY SEE (VATICAN CITY STATE)', 'Holy See (Vatican City State)', 'VAT', 336, 39),
                (95, 'HN', 'HONDURAS', 'Honduras', 'HND', 340, 504),
                (96, 'HK', 'HONG KONG', 'Hong Kong', 'HKG', 344, 852),
                (97, 'HU', 'HUNGARY', 'Hungary', 'HUN', 348, 36),
                (98, 'IS', 'ICELAND', 'Iceland', 'ISL', 352, 354),
                (99, 'IN', 'INDIA', 'India', 'IND', 356, 91),
                (100, 'ID', 'INDONESIA', 'Indonesia', 'IDN', 360, 62),
                (101, 'IR', 'IRAN, ISLAMIC REPUBLIC OF', 'Iran, Islamic Republic of', 'IRN', 364, 98),
                (102, 'IQ', 'IRAQ', 'Iraq', 'IRQ', 368, 964),
                (103, 'IE', 'IRELAND', 'Ireland', 'IRL', 372, 353),
                (104, 'IL', 'ISRAEL', 'Israel', 'ISR', 376, 972),
                (105, 'IT', 'ITALY', 'Italy', 'ITA', 380, 39),
                (106, 'JM', 'JAMAICA', 'Jamaica', 'JAM', 388, 1876),
                (107, 'JP', 'JAPAN', 'Japan', 'JPN', 392, 81),
                (108, 'JO', 'JORDAN', 'Jordan', 'JOR', 400, 962),
                (109, 'KZ', 'KAZAKHSTAN', 'Kazakhstan', 'KAZ', 398, 7),
                (110, 'KE', 'KENYA', 'Kenya', 'KEN', 404, 254),
                (111, 'KI', 'KIRIBATI', 'Kiribati', 'KIR', 296, 686),
                (112, 'KP', 'KOREA, DEMOCRATIC PEOPLE''S REPUBLIC OF', 'Korea, Democratic People''s Republic of', 'PRK', 408, 850),
                (113, 'KR', 'KOREA, REPUBLIC OF', 'Korea, Republic of', 'KOR', 410, 82),
                (114, 'KW', 'KUWAIT', 'Kuwait', 'KWT', 414, 965),
                (115, 'KG', 'KYRGYZSTAN', 'Kyrgyzstan', 'KGZ', 417, 996),
                (116, 'LA', 'LAO PEOPLE''S DEMOCRATIC REPUBLIC', 'Lao People''s Democratic Republic', 'LAO', 418, 856),
                (117, 'LV', 'LATVIA', 'Latvia', 'LVA', 428, 371),
                (118, 'LB', 'LEBANON', 'Lebanon', 'LBN', 422, 961),
                (119, 'LS', 'LESOTHO', 'Lesotho', 'LSO', 426, 266),
                (120, 'LR', 'LIBERIA', 'Liberia', 'LBR', 430, 231),
                (121, 'LY', 'LIBYAN ARAB JAMAHIRIYA', 'Libyan Arab Jamahiriya', 'LBY', 434, 218),
                (122, 'LI', 'LIECHTENSTEIN', 'Liechtenstein', 'LIE', 438, 423),
                (123, 'LT', 'LITHUANIA', 'Lithuania', 'LTU', 440, 370),
                (124, 'LU', 'LUXEMBOURG', 'Luxembourg', 'LUX', 442, 352),
                (125, 'MO', 'MACAO', 'Macao', 'MAC', 446, 853),
                (126, 'MK', 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'Macedonia, the Former Yugoslav Republic of', 'MKD', 807, 389),
                (127, 'MG', 'MADAGASCAR', 'Madagascar', 'MDG', 450, 261),
                (128, 'MW', 'MALAWI', 'Malawi', 'MWI', 454, 265),
                (129, 'MY', 'MALAYSIA', 'Malaysia', 'MYS', 458, 60),
                (130, 'MV', 'MALDIVES', 'Maldives', 'MDV', 462, 960),
                (131, 'ML', 'MALI', 'Mali', 'MLI', 466, 223),
                (132, 'MT', 'MALTA', 'Malta', 'MLT', 470, 356),
                (133, 'MH', 'MARSHALL ISLANDS', 'Marshall Islands', 'MHL', 584, 692),
                (134, 'MQ', 'MARTINIQUE', 'Martinique', 'MTQ', 474, 596),
                (135, 'MR', 'MAURITANIA', 'Mauritania', 'MRT', 478, 222),
                (136, 'MU', 'MAURITIUS', 'Mauritius', 'MUS', 480, 230),
                (137, 'YT', 'MAYOTTE', 'Mayotte', NULL, NULL, 269),
                (138, 'MX', 'MEXICO', 'Mexico', 'MEX', 484, 52),
                (139, 'FM', 'MICRONESIA, FEDERATED STATES OF', 'Micronesia, Federated States of', 'FSM', 583, 691),
                (140, 'MD', 'MOLDOVA, REPUBLIC OF', 'Moldova, Republic of', 'MDA', 498, 373),
                (141, 'MC', 'MONACO', 'Monaco', 'MCO', 492, 377),
                (142, 'MN', 'MONGOLIA', 'Mongolia', 'MNG', 496, 976),
                (143, 'MS', 'MONTSERRAT', 'Montserrat', 'MSR', 500, 1664),
                (144, 'MA', 'MOROCCO', 'Morocco', 'MAR', 504, 212),
                (145, 'MZ', 'MOZAMBIQUE', 'Mozambique', 'MOZ', 508, 258),
                (146, 'MM', 'MYANMAR', 'Myanmar', 'MMR', 104, 95),
                (147, 'NA', 'NAMIBIA', 'Namibia', 'NAM', 516, 264),
                (148, 'NR', 'NAURU', 'Nauru', 'NRU', 520, 674),
                (149, 'NP', 'NEPAL', 'Nepal', 'NPL', 524, 977),
                (150, 'NL', 'NETHERLANDS', 'Netherlands', 'NLD', 528, 31),
                (151, 'AN', 'NETHERLANDS ANTILLES', 'Netherlands Antilles', 'ANT', 530, 599),
                (152, 'NC', 'NEW CALEDONIA', 'New Caledonia', 'NCL', 540, 687),
                (153, 'NZ', 'NEW ZEALAND', 'New Zealand', 'NZL', 554, 64),
                (154, 'NI', 'NICARAGUA', 'Nicaragua', 'NIC', 558, 505),
                (155, 'NE', 'NIGER', 'Niger', 'NER', 562, 227),
                (156, 'NG', 'NIGERIA', 'Nigeria', 'NGA', 566, 234),
                (157, 'NU', 'NIUE', 'Niue', 'NIU', 570, 683),
                (158, 'NF', 'NORFOLK ISLAND', 'Norfolk Island', 'NFK', 574, 672),
                (159, 'MP', 'NORTHERN MARIANA ISLANDS', 'Northern Mariana Islands', 'MNP', 580, 1670),
                (160, 'NO', 'NORWAY', 'Norway', 'NOR', 578, 47),
                (161, 'OM', 'OMAN', 'Oman', 'OMN', 512, 968),
                (162, 'PK', 'PAKISTAN', 'Pakistan', 'PAK', 586, 92),
                (163, 'PW', 'PALAU', 'Palau', 'PLW', 585, 680),
                (164, 'PS', 'PALESTINIAN TERRITORY, OCCUPIED', 'Palestinian Territory, Occupied', NULL, NULL, 970),
                (165, 'PA', 'PANAMA', 'Panama', 'PAN', 591, 507),
                (166, 'PG', 'PAPUA NEW GUINEA', 'Papua New Guinea', 'PNG', 598, 675),
                (167, 'PY', 'PARAGUAY', 'Paraguay', 'PRY', 600, 595),
                (168, 'PE', 'PERU', 'Peru', 'PER', 604, 51),
                (169, 'PH', 'PHILIPPINES', 'Philippines', 'PHL', 608, 63),
                (170, 'PN', 'PITCAIRN', 'Pitcairn', 'PCN', 612, 0),
                (171, 'PL', 'POLAND', 'Poland', 'POL', 616, 48),
                (172, 'PT', 'PORTUGAL', 'Portugal', 'PRT', 620, 351),
                (173, 'PR', 'PUERTO RICO', 'Puerto Rico', 'PRI', 630, 1787),
                (174, 'QA', 'QATAR', 'Qatar', 'QAT', 634, 974),
                (175, 'RE', 'REUNION', 'Reunion', 'REU', 638, 262),
                (176, 'RO', 'ROMANIA', 'Romania', 'ROM', 642, 40),
                (177, 'RU', 'RUSSIAN FEDERATION', 'Russian Federation', 'RUS', 643, 70),
                (178, 'RW', 'RWANDA', 'Rwanda', 'RWA', 646, 250),
                (179, 'SH', 'SAINT HELENA', 'Saint Helena', 'SHN', 654, 290),
                (180, 'KN', 'SAINT KITTS AND NEVIS', 'Saint Kitts and Nevis', 'KNA', 659, 1869),
                (181, 'LC', 'SAINT LUCIA', 'Saint Lucia', 'LCA', 662, 1758),
                (182, 'PM', 'SAINT PIERRE AND MIQUELON', 'Saint Pierre and Miquelon', 'SPM', 666, 508),
                (183, 'VC', 'SAINT VINCENT AND THE GRENADINES', 'Saint Vincent and the Grenadines', 'VCT', 670, 1784),
                (184, 'WS', 'SAMOA', 'Samoa', 'WSM', 882, 684),
                (185, 'SM', 'SAN MARINO', 'San Marino', 'SMR', 674, 378),
                (186, 'ST', 'SAO TOME AND PRINCIPE', 'Sao Tome and Principe', 'STP', 678, 239),
                (187, 'SA', 'SAUDI ARABIA', 'Saudi Arabia', 'SAU', 682, 966),
                (188, 'SN', 'SENEGAL', 'Senegal', 'SEN', 686, 221),
                (189, 'CS', 'SERBIA AND MONTENEGRO', 'Serbia and Montenegro', NULL, NULL, 381),
                (190, 'SC', 'SEYCHELLES', 'Seychelles', 'SYC', 690, 248),
                (191, 'SL', 'SIERRA LEONE', 'Sierra Leone', 'SLE', 694, 232),
                (192, 'SG', 'SINGAPORE', 'Singapore', 'SGP', 702, 65),
                (193, 'SK', 'SLOVAKIA', 'Slovakia', 'SVK', 703, 421),
                (194, 'SI', 'SLOVENIA', 'Slovenia', 'SVN', 705, 386),
                (195, 'SB', 'SOLOMON ISLANDS', 'Solomon Islands', 'SLB', 90, 677),
                (196, 'SO', 'SOMALIA', 'Somalia', 'SOM', 706, 252),
                (197, 'ZA', 'SOUTH AFRICA', 'South Africa', 'ZAF', 710, 27),
                (198, 'GS', 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'South Georgia and the South Sandwich Islands', NULL, NULL, 0),
                (199, 'ES', 'SPAIN', 'Spain', 'ESP', 724, 34),
                (200, 'LK', 'SRI LANKA', 'Sri Lanka', 'LKA', 144, 94),
                (201, 'SD', 'SUDAN', 'Sudan', 'SDN', 736, 249),
                (202, 'SR', 'SURINAME', 'Suriname', 'SUR', 740, 597),
                (203, 'SJ', 'SVALBARD AND JAN MAYEN', 'Svalbard and Jan Mayen', 'SJM', 744, 47),
                (204, 'SZ', 'SWAZILAND', 'Swaziland', 'SWZ', 748, 268),
                (205, 'SE', 'SWEDEN', 'Sweden', 'SWE', 752, 46),
                (206, 'CH', 'SWITZERLAND', 'Switzerland', 'CHE', 756, 41),
                (207, 'SY', 'SYRIAN ARAB REPUBLIC', 'Syrian Arab Republic', 'SYR', 760, 963),
                (208, 'TW', 'TAIWAN, PROVINCE OF CHINA', 'Taiwan, Province of China', 'TWN', 158, 886),
                (209, 'TJ', 'TAJIKISTAN', 'Tajikistan', 'TJK', 762, 992),
                (210, 'TZ', 'TANZANIA, UNITED REPUBLIC OF', 'Tanzania, United Republic of', 'TZA', 834, 255),
                (211, 'TH', 'THAILAND', 'Thailand', 'THA', 764, 66),
                (212, 'TL', 'TIMOR-LESTE', 'Timor-Leste', NULL, NULL, 670),
                (213, 'TG', 'TOGO', 'Togo', 'TGO', 768, 228),
                (214, 'TK', 'TOKELAU', 'Tokelau', 'TKL', 772, 690),
                (215, 'TO', 'TONGA', 'Tonga', 'TON', 776, 676),
                (216, 'TT', 'TRINIDAD AND TOBAGO', 'Trinidad and Tobago', 'TTO', 780, 1868),
                (217, 'TN', 'TUNISIA', 'Tunisia', 'TUN', 788, 216),
                (218, 'TR', 'TURKEY', 'Turkey', 'TUR', 792, 90),
                (219, 'TM', 'TURKMENISTAN', 'Turkmenistan', 'TKM', 795, 7370),
                (220, 'TC', 'TURKS AND CAICOS ISLANDS', 'Turks and Caicos Islands', 'TCA', 796, 1649),
                (221, 'TV', 'TUVALU', 'Tuvalu', 'TUV', 798, 688),
                (222, 'UG', 'UGANDA', 'Uganda', 'UGA', 800, 256),
                (223, 'UA', 'UKRAINE', 'Ukraine', 'UKR', 804, 380),
                (224, 'AE', 'UNITED ARAB EMIRATES', 'United Arab Emirates', 'ARE', 784, 971),
                (225, 'GB', 'UNITED KINGDOM', 'United Kingdom', 'GBR', 826, 44),
                (226, 'US', 'UNITED STATES', 'United States', 'USA', 840, 1),
                (227, 'UM', 'UNITED STATES MINOR OUTLYING ISLANDS', 'United States Minor Outlying Islands', NULL, NULL, 1),
                (228, 'UY', 'URUGUAY', 'Uruguay', 'URY', 858, 598),
                (229, 'UZ', 'UZBEKISTAN', 'Uzbekistan', 'UZB', 860, 998),
                (230, 'VU', 'VANUATU', 'Vanuatu', 'VUT', 548, 678),
                (231, 'VE', 'VENEZUELA', 'Venezuela', 'VEN', 862, 58),
                (232, 'VN', 'VIET NAM', 'Viet Nam', 'VNM', 704, 84),
                (233, 'VG', 'VIRGIN ISLANDS, BRITISH', 'Virgin Islands, British', 'VGB', 92, 1284),
                (234, 'VI', 'VIRGIN ISLANDS, U.S.', 'Virgin Islands, U.s.', 'VIR', 850, 1340),
                (235, 'WF', 'WALLIS AND FUTUNA', 'Wallis and Futuna', 'WLF', 876, 681),
                (236, 'EH', 'WESTERN SAHARA', 'Western Sahara', 'ESH', 732, 212),
                (237, 'YE', 'YEMEN', 'Yemen', 'YEM', 887, 967),
                (238, 'ZM', 'ZAMBIA', 'Zambia', 'ZMB', 894, 260),
                (239, 'ZW', 'ZIMBABWE', 'Zimbabwe', 'ZWE', 716, 263);

            INSERT INTO usuarios VALUES (NULL,'admin@admin.com','Admin','admin','admin1','','".$pwdEncriptada."',NULL,1,'a');
        ";

        if($db->multi_query($consulta)){
            $result = array(
                'status'=>'success',
                'code'=>200,
                'message'=>'Estructura creada correctamente'
            );
        }else{
            $result = array(
                'status'=>'error',
                'code'=>404,
                'message'=>'Error al crear la estructura'
            );
        }

        echo json_encode($result);

    });

    $app->run();
