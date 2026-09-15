<div class="container">
  <h2>Crear Tipo de Ubicación</h2>
  <form action="?controller=TipoUbicacion&&action=save" method="POST">
  <?php echo Csrf::field(); ?>

    <div class="form-group">
      <label for="descripcion">Descripcion</label>
      <input type="text" name="descripcion" id="descripcion" class="form-control" placeholder="Torre, Piso, Apartamento..." required>
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <button type="button" href="?controller=TipoUbicacion&&action=show" class="btn btn-primary">Cancelar</button>
  </form>
</div>
