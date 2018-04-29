<?php
    require_once 'vendor/autoload.php';
    require_once('piramide-uploader/PiramideUploader.php');

    $app = new \Slim\Slim();
    $db = new mysqli('localhost','root','','groupreads');

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

    $app->run();