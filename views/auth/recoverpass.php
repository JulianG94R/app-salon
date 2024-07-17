<h1 class="nombre-pagina">Recuperar Cuenta</h1>
<p class="descripcion-pagina">Escribe tu nueva contraseña</p>

<?php include_once __DIR__ . '/../templates/alertas.php' ?>

<?php if($error) return; ?>

<form class="formulario" method="POST">
    <div class="campo">
        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password" placeholder="Contraseña Nueva">
    </div>

    <input type="submit" class="boton" value="Guardar Cambios">
</form>

<div class="acciones">
    <a href="/register">¿No tienes ninguna cuenta? Registrate</a>
    <a href="/">¿Ya tiene cuenta? Iniciar Sesión</a>
</div>