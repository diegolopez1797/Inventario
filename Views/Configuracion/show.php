<div class="container">
	<h2>Configuración General</h2>

	<div class="row">
		<div class="col-md-4">
			<p><strong>Logo actual</strong></p>
			<img src="<?php echo h($logoActual); ?>" alt="Logo actual" style="max-width:100%; border:1px solid #ddd; padding:8px; background:#fff;">
		</div>
		<div class="col-md-8">
			<form action="?controller=Configuracion&action=guardarLogo" method="POST" enctype="multipart/form-data">
				<?php echo Csrf::field(); ?>
				<div class="form-group">
					<label>Nueva imagen del logo</label>
					<input type="file" name="logo" class="form-control" accept=".png,.jpg,.jpeg,.gif,.svg" required>
					<p class="help-block">Formatos permitidos: PNG, JPG, GIF, SVG. Tamaño máximo 2 MB.</p>
				</div>
				<button type="submit" class="btn btn-primary">Guardar Logo</button>
			</form>
		</div>
	</div>
</div>
