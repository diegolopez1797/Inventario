<div class="container">
	<h2>Editar Usuario</h2>
	<form action="?controller=Usuario&&action=update" method="POST">
	<?php echo Csrf::field(); ?>

		<input type="hidden" name="id" id="id" value="<?php echo $usuario->getId(); ?>" >

		<div class="form-group">
			<label for="text">Identificacion</label>
			<input type="number" name="identificacion" id="identificacion" class="form-control" value="<?php echo $usuario->getIdentificacion(); ?>" required>
		</div>

		<div class="form-group">
			<label for="text">Nombre</label>
			<input type="text" name="nombre" id="nombre" class="form-control" value="<?php echo h($usuario->getNombre()); ?>" required>
		</div>

		<div class="form-group">
			<label for="text">Apellido</label>
			<input type="text" name="apellido" id="apellido" class="form-control" value="<?php echo h($usuario->getApellido()); ?>" required>
		</div>

		<div class="form-group">
			<label for="text">Clave</label>
			<input type="password" name="clave" id="clave" class="form-control" placeholder="Dejar en blanco para conservar la contraseña actual">
		</div>

		<div class="form-group">
			<label for="text">Rol</label>
			<select name="rol" class="form-control">
			<?php foreach ($listaRol as $rol) {?>
			<option value="<?php echo $rol->getId(); ?>" <?php echo ($usuario->getRolId() == $rol->getId()) ? 'selected' : ''; ?>><?php echo h($rol->getDescripcion()); ?></option>
			<?php } ?>
	      	</select>

		</div>

		<button type="submit" class="btn btn-primary">Actualizar</button>

	</form>
</div>