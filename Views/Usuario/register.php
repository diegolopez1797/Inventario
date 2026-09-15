<div class="container">
  <h2>Crear Usuario</h2>
  <form action="?controller=Usuario&&action=save" method="POST">
  <?php echo Csrf::field(); ?>

    <div class="form-group">
      <label for="text">Identificacion</label>
      <input type="number" class="form-control" placeholder="Ingrese Identificacion" name="identificacion" required>
    </div>

    <div class="form-group">
      <label for="text">Nombre</label>
      <input type="text" name="nombre" class="form-control" placeholder="Ingrese Nombre" required>
    </div>

    <div class="form-group">
      <label for="text">Apellido</label>
      <input type="text" name="apellido" class="form-control" placeholder="Ingrese Apellido" required>
    </div>

    <div class="form-group">
      <label for="text">Clave</label>
      <input type="password" name="clave" class="form-control" placeholder="Ingrese Clave" required>
    </div>

    <div class="form-group">
      <label for="text">Rol</label>
      <select name="rol" class="form-control">
        <?php foreach ($listaRol as $rol) {?>
        <option value="<?php echo $rol->getId(); ?>"><?php echo h($rol->getDescripcion()); ?></option>
        <?php } ?>
      </select>
      <br>
    </div>


    <button type="submit" class="btn btn-primary">Guardar</button>
  </form>
</div>