<?php
    require_once 'vendor/autoload.php';

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

        $consulta = "INSERT INTO usuarios VALUES (DEFAULT,".
                    "'{$data['correo']}',".
                    "'{$data['nombre']}',".
                    "'{$data['apellidos']}',".
                    "'{$data['nick']}',".
                    "'{$data['fecha']}',".
                    "'{$data['pwd']}',".
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