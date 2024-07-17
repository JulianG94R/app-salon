<?php 

require_once __DIR__ . '/../includes/app.php';

use Controllers\AdminController;
use Controllers\ApiController;
use Controllers\CitaController;
use Controllers\LoginController;
use Controllers\ServicioController;
use MVC\Router;

$router = new Router();

//Crear Cuenta
$router->get('/register', [LoginController::class, 'register']);
$router->post('/register', [LoginController::class, 'register']);

//Confirmar Cuenta
$router->get('/confirmar-cuenta', [LoginController::class, 'confirmar']);
$router->get('/mensaje', [LoginController::class, 'mensaje']);

//Iniciar sesion
$router->get('/', [LoginController::class, 'login']);
$router->post('/', [LoginController::class, 'login']);

//Cerrar Sesión
$router->get('/logout', [LoginController::class, 'logout']);


//Recuperar contraseña
$router->get('/forgotpass', [LoginController::class, 'forgotpass']);
$router->post('/forgotpass', [LoginController::class, 'forgotpass']);

$router->get('/recoverpass', [LoginController::class, 'recoverpass']);
$router->post('/recoverpass', [LoginController::class, 'recoverpass']);


//Area Privada
$router->get('/cita', [CitaController::class, 'index']);
$router->get('/admin', [AdminController::class, 'index']);

//API Citas
$router->get('/api/servicios', [ApiController::class, 'index']);
$router->post('/api/citas', [ApiController::class, 'guardar']);
$router->post('/api/eliminar', [ApiController::class, 'eliminar']);


//CRUD Servicios
$router->get('/servicios', [ServicioController::class, 'index']);
$router->get('/servicios/crear', [ServicioController::class, 'crear']);
$router->post('/servicios/crear', [ServicioController::class, 'crear']);
$router->get('/servicios/actualizar', [ServicioController::class, 'actualizar']);
$router->post('/servicios/actualizar', [ServicioController::class, 'actualizar']);
$router->post('/servicios/eliminar', [ServicioController::class, 'eliminar']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();


//Recordar en consola iniciar gulp y en una nueva ir a cd public y despues iniciar PHP