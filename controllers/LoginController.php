<?php
namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController
{
    public static function login(Router $router)
    {
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                //Comprobar que existe el usuario
                $usuario = Usuario::where('email', $auth->email);

                if ($usuario) {
                    //Verificar la contraseña
                    if ($usuario->comprobarPassNVerificado($auth->password)) {
                        //Autenticar Usuario;
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //Redirect
                        if ($usuario->admin === '1') {
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');
                        } else {

                            header('Location: /cita');
                        }
                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario No encontrado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }

    public static function logout()
    {
        session_start();
        $_SESSION = [];
        header('Location: /');
    }

    public static function forgotpass(Router $router)
    {
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if (empty($alertas)) {
                $usuario = Usuario::where('email', $auth->email);

                if ($usuario && $usuario->confirmado === "1") {
                    //Generar token
                    $usuario->crearToken();
                    $usuario->guardar();

                    //Enviar Email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    //Mensaje de aviso de mail
                    Usuario::setAlerta('exito', 'Revisa tu casilla de correo');
                } else {
                    Usuario::setAlerta('error', 'El Usuario no Existes o aún no está confirmado');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/forget-pass', [
            'alertas' => $alertas
        ]);
    }

    public static function recoverpass(Router $router)
    {
        $alertas = [];
        $error = false;
        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            Usuario::setAlerta('error', 'Token No Válido!');
            $error = true;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //Leer la contraseña nueva y guardarla
            $password = new Usuario($_POST);
            $alertas = $password->validarPass();

            if (empty($alertas)) {
                $usuario->password = null;
                $usuario->password = $password->password;
                $usuario->hashPass();
                $usuario->token = null;

                $resultado = $usuario->guardar();
                if ($resultado) {
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/recoverpass', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    public static function register(Router $router)
    {
        $usuario = new Usuario;
        //Alertas vacias
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNVaCuenta();

            //Revisar que las alertas esten vacias
            if (empty($alertas)) {
                //Verificar que el usuario no este registrado
                $resultado = $usuario->userExists();

                if ($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    //Hass contraseña
                    $usuario->hashPass();

                    //Generar Token
                    $usuario->crearToken();

                    //Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                    $email->enviarConfirmacion();

                    //Crear el usuario
                    $resultado = $usuario->guardar();

                    if ($resultado) {
                        header('Location: /mensaje');
                    }

                    debuguear($usuario);
                }
            }
        }

        $router->render('auth/register', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function mensaje(Router $router)
    {
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router)
    {
        $alertas = [];
        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);

        if (empty($usuario)) {
            //Mostrar msj de error
            Usuario::setAlerta('error', 'Token no Válido');
        } else {
            //Modificar a user confirmado
            $usuario->confirmado = "1";
            $usuario->token = null;
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}
