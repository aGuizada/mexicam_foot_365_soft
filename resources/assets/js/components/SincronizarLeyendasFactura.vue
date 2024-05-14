<template>
  <main class="main">
    <!-- Breadcrumb -->
    <div class="container-fluid">
      <!-- Ejemplo de tabla Listado -->
      <div class="card">

        <div class="card-body">
          <div>
            <pre>{{ result }}</pre>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
export default {
  data() {
    return {
      result: '', // Variable para almacenar el contenido del XML
    }
  },
  methods: {
    async obtenerActividades() {
      try {
        const response = await axios.get('/factura/sincronizarListaLeyendasFactura'); // Cambia la ruta
        this.result = response.data; // Almacenar el contenido del XML
      } catch (error) {
        console.error('Error al obtener actividades', error);
      }
    },
  },
  mounted() {
    this.obtenerActividades();
  }
}
</script>
<style></style>