<div class="container">
  <h2>Crear Material</h2>
  <form action="?controller=Material&&action=save" method="POST">
    <div class="form-group">
      <label for="text">Ingrese los datos del material:</label>
      <input type="number" class="form-control" placeholder="Ingrese Codigo" name="codigo" required>
    </div>

    <div class="form-group">
      <label for="text"></label>
      <input type="text" name="descripcion" class="form-control" placeholder="Ingrese Descripcion" required>
    </div>

    <div class="form-group">
      <label for="text"></label>
      <input type="text" name="unidad" class="form-control" placeholder="Ingrese Unidad" required>
    </div>
    <button type="submit" class="btn btn-primary">Guardar</button>
  </form>
</div>