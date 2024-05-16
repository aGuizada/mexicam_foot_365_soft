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
          <li @click="menu=55" class="nav-item">
            <a class="nav-link" href="#">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="26" height="26" class="me-2">
                <path fill="none" d="M0 0h24v24H0z"/>
                <path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-3.5-8v2H11v2h2v-2h1a2.5 2.5 0 1 0 0-5h-4a.5.5 0 1 1 0-1h5.5V8H13V6h-2v2h-1a2.5 2.5 0 0 0 0 5h4a.5.5 0 1 1 0 1H8.5z" fill="rgba(255,255,255,1)"/>
              </svg>
              Ventas Realizadas
            </a>
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