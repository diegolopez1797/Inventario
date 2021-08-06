<div class="container">
  <h2>Crear Manzana</h2>
  <form action="?controller=Manzana&&action=save" method="POST">

    <div class="form-group">
      <label for="text"></label>
      <input type="text" name="descripcion" class="form-control" placeholder="Ingrese Descripcion" required>
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <button type="button" href="?controller=Manzana&&action=show" class="btn btn-primary">Cancelar</button>
  </form>
</div>