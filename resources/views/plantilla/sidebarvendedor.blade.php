<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li @click="menu=42" class="nav-item">
                <a class="nav-link active" href="#"><i class="icon-speedometer"></i> Escritorio</a>
            </li>
                
            <li @click="menu=39" class="nav-item">
              <a class="nav-link" href="#">
                <i class="icon-basket me-2"></i>
                Ventas
              </a>
            </li>    
          <li @click="menu=60" class="nav-item">
  <a class="nav-link" href="#">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" class="me-2">
      <path fill="none" d="M0 0h24v24H0z"/>
      <path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-3.5-8v2H11v2h2v-2h1a2.5 2.5 0 1 0 0-5h-4a.5.5 0 1 1 0-1h5.5V8H13V6h-2v2h-1a2.5 2.5 0 0 0 0 5h4a.5.5 0 1 1 0 1H8.5z" fill="rgba(255,255,255,1)"/>
    </svg>
    Mis Ventas
  </a>
</li>
        </ul>
    </nav>
</div>