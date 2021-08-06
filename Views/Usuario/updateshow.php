<div class="container">
	<h2>Editar Usuario</h2>
	<form action="?controller=Usuario&&action=update" method="POST">

		<input type="hidden" name="id" id="id" value="<?php echo $usuario->getId(); ?>" >

		<div class="form-group">
			<label for="text">Identificacion</label>
			<input type="number" name="identificacion" id="identificacion" class="form-control" value="<?php echo $usuario->getIdentificacion(); ?>" required>
		</div>

		<div class="form-group">
			<label for="text">Nombre</label>
			<input type="text" name="nombre" id="nombre" class="form-control" value="<?php echo $usuario->getNombre(); ?>" required>
		</div>

		<div class="form-group">
			<label for="text">Apellido</label>
			<input type="text" name="apellido" id="apellido" class="form-control" value="<?php echo $usuario->getApellido(); ?>" required>
		</div>

		<div class="form-group">
			<label for="text">Clave</label>
			<input type="text" name="clave" id="clave" class="form-control" value="<?php echo $usuario->getClave(); ?>" required>
		</div>

		<div class="form-group">
			<label for="text">Rol</label>
			<select name="rol" class="form-control">
			<?php
			if ($usuario->getRolId()==1) {
				echo '<option selected="" value="1">Administrador</option>';
			}else{
				echo '<option selected="" value="2">Almacenista</option>';
			}

			?>
	         	<option value="1">Administrador</option> 
	         	<option value="2">Almacenista</option>  
	      	</select>
			
		</div>

		<button type="submit" class="btn btn-primary">Actualizar</button>

	</form>
</div>