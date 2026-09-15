<div class="container">
  <h2>Crear Proyecto</h2>
  <form action="?controller=Proyecto&&action=save" method="POST">
  <?php echo Csrf::field(); ?>

    <div class="form-group">
      <label for="text"></label>
      <input type="text" name="descripcion" class="form-control" placeholder="Ingrese Descripcion" required>
    </div>

    <div class="form-group">
      <label for="responsable">Responsable (opcional)</label>
      <select name="responsable" id="responsable" class="form-control">
        <option value="">-- Sin responsable --</option>
        <?php foreach ($listaUsuario as $usuario) {?>
        <option value="<?php echo $usuario->getId(); ?>"><?php echo h($usuario->getNombre() . ' ' . $usuario->getApellido()); ?></option>
        <?php } ?>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <button type="button" href="?controller=Material&&action=show" class="btn btn-primary">Cancelar</button>
  </form>
</div>