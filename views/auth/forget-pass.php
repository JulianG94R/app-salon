<h1 class="nombre-pagina">Olvidé Contraseña</h1>
<p class="descripcion-pagina">Reestablece la contraseña escribiendo el Correo registrado</p>

<?php include_once __DIR__ . '/../templates/alertas.php' ?>

<form action="/forgotpass" class="formulario" method="POST">
    <div class="campo">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" placeholder="Ingrese su E-mail">
    </div>
    <input type="submit" class="boton" value="Enviar">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Iniciar Sesión</a>
    <a href="/register">¿No tienes ninguna cuenta? Registrate</a>
</div>