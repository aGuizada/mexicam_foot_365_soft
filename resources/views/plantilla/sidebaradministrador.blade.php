<div class="sidebar">
  <nav class="sidebar-nav">
    <ul class="nav">
      <li @click="menu=42" class="nav-item">
        <a class="nav-link active" href="#"><i class="fa fa-dashboard"></i> Escritorio</a>
      </li>
      <li class="nav-title"> Operaciones </li>
      <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-cutlery"></i> Restaurante</a>
        <ul class="nav-dropdown-items">
        <li @click="menu=13" class="nav-item">
        <a class="nav-link" href="#"><i class="fa fa-cutlery"></i> Restaurante</a>
      </li>

          <li @click="menu=14" class="nav-item">
            <a class="nav-link" href="#"><i class="fa fa-building"></i> Sucursales</a>
          </li>
        </ul>
      </li>
      <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-shopping-cart"></i> Ventas</a>
        <ul class="nav-dropdown-items">
          <li @click="menu=16" class="nav-item">
            <a class="nav-link" href="#"><i class="fa fa-money"></i> Apertura/Cierre Caja</a>
          </li>
          <li @click="menu=39" class="nav-item">
            <a class="nav-link" href="#"><i class="fa fa-shopping-cart"></i> Ventas</a>
          </li>
            <li @click="menu=55" class="nav-item">
            <a class="nav-link" href="#"><i class="fa fa-shopping-cart"></i> Reporte Ventas</a>
          </li>
      </li>

        </ul>
      </li>
      <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-barcode"></i> Productos</a>
        <ul class="nav-dropdown-items">
          <li @click="menu=2" class="nav-item">
            <a class="nav-link" href="#"><i class="fa fa-cubes"></i> Productos</a>
          </li>
        </ul>
      </li>
      <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-file-text"></i> Reportes</a>
        <ul class="nav-dropdown-items">
          <li @click="menu=45" class="nav-item">
            <a class="nav-link" href="#"><i class="fa fa-file-text"></i> Reporte Ventas Diarias</a>
          </li>
        </ul>
      </li>
      <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-key"></i> Accesos</a>
        <ul class="nav-dropdown-items">
          <li @click="menu=7" class="nav-item">
            <a class="nav-link" href="#"><i class="fa fa-users"></i> Usuarios</a>
          </li>
          <li @click="menu=8" class="nav-item">
            <a class="nav-link" href="#"><i class="fa fa-user-circle"></i> Roles y Permisos</a>
          </li>
        </ul>
      </li>
    </ul>
  </nav>
</div>