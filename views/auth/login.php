<h1 class="nombre-pagina">LOGIN</h1>
<p class="descripcion-pagina">Inicia Sesión con tus datos</p>

<?php include_once __DIR__ . '/../templates/alertas.php' ?>

<form action="/" class="formulario" method="POST">
    <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" placeholder="Ingrese su email" name="email">
    </div>

    <div class="campo">
        <label for="password">Contraseña</label>
        <input type="password" id="password" placeholder="Ingrese su contraseña" name="password">
    </div>

    <input type="submit" value="Iniciar Sesión" class="boton">
</form>

<div class="acciones">
    <a href="/register">¿No tienes ninguna cuenta? Registrate</a>
    <a href="/forgotpass">Has olvidado tu contraseña?</a>
</div>