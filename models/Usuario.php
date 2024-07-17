<?php

namespace Model;

class Usuario extends ActiveRecord
{

    //Base de datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'telefono', 'admin', 'confirmado', 'token'];


    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? 0;
        $this->confirmado = $args['confirmado'] ?? 0;
        $this->token = $args['token'] ?? '';
    }

    //Mensajes de Validacion
    public function validarNVaCuenta()
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre es Obligatorio';
        }

        if (!$this->apellido) {
            self::$alertas['error'][] = 'El Apellido es Obligatorio';
        }

        if (!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }

        if (!$this->password) {
            self::$alertas['error'][] = 'La contraseña es Obligatoria';
        }

        if (strlen($this->password) < 8 || strlen($this->password) > 16) {
            self::$alertas['error'][] = 'La contraseña debe tener entre 8 y 16 caracteres';
        }

        return self::$alertas;
    }

    public function validarLogin(){
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }

        if(!$this->password){
            self::$alertas['error'][] = 'La Contraseña es Obligatoria';
        }

        return self::$alertas;
    }

    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }

        return self::$alertas;
    }

    public function validarPass(){
        if(!$this->password){
            self::$alertas['error'][] = 'La Contraseña es Obligatoria';
        }
         
        if(strlen($this->password) < 8 || strlen($this->password) > 16){
            self::$alertas['error'][] = 'La Contraseña debe tener entre 8 y 16 caracteres';
        }

        return self::$alertas;
    }

    public function userExists(){
        $query = " SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";

        $resultado = self::$db->query($query);

        if($resultado->num_rows){
            self::$alertas['error'][] = 'El usuario ya existe';
        }
        return $resultado;
    }

    public function hashPass(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken(){
        $this->token = uniqid();
    }

    public function comprobarPassNVerificado($password){
       $resultado = password_verify($password, $this->password);

       if(!$resultado || !$this->confirmado){
        self::$alertas['error'][] = 'Contraseña incorrecta o cuenta no confirmada';
       } else {
        return true;
       }
    }
}
