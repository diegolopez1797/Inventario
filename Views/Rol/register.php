<div class="container">
  <h2>Crear Rol</h2>
  <form action="?controller=Rol&&action=save" method="POST">
  <?php echo Csrf::field(); ?>

    <div class="form-group">
      <label for="text"></label>
      <input type="text" name="descripcion" class="form-control" placeholder="Ingrese Descripcion del Rol" maxlength="20" required>
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <button type="button" href="?controller=Rol&&action=show" class="btn btn-primary">Cancelar</button>
  </form>
</div>
