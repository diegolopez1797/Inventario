<div class="container">
	<h2>Editar Proyecto</h2>
	<form action="?controller=Proyecto&&action=update" method="POST">
	<?php echo Csrf::field(); ?>
		<input type="hidden" name="id" id="id" value="<?php echo $proyecto->getId(); ?>" >

		<div class="form-group">
			<label for="text">Descripcion</label>
			<input type="text" name="descripcion" id="descripcion" class="form-control" value="<?php echo h($proyecto->getDescripcion()); ?>" required>
		</div>

		<div class="form-group">
			<label for="responsable">Responsable (opcional)</label>
			<select name="responsable" id="responsable" class="form-control">
				<option value="">-- Sin responsable --</option>
				<?php foreach ($listaUsuario as $usuario) {?>
				<option value="<?php echo $usuario->getId(); ?>" <?php echo ($proyecto->getResponsableId() == $usuario->getId()) ? 'selected' : ''; ?>><?php echo h($usuario->getNombre() . ' ' . $usuario->getApellido()); ?></option>
				<?php } ?>
			</select>
		</div>

		<button type="submit" class="btn btn-primary">Actualizar</button>

	</form>
</div>