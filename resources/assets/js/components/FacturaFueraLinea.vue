<template>
  <main class="main text-white">
    <div class="container-fluid py-3"></div>
    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="container">
            <div class="row">
              <div class="col-md-15">
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                  <div class="modal-body">
                    <div class="form-group row">
                      <div class="col-md-6">
                        <div class="dropdown">
                          <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownComidas"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Categoria
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownComidas">
                            <button v-for="subcategoria in arrayBuscador" :key="subcategoria.id"
                              @click="listarArticulo('', subcategoria.id)"
                              :class="{ 'btn-primary': criterioA === subcategoria.id }" class="dropdown-item">
                              {{ subcategoria.nombre }}
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                    <h3 class="text-primary mb-4">Productos</h3>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-6">
                      <div class="col mb-3" v-for="articulo in arrayArticulo" :key="articulo.id">
                        <div class=" border-0 shadow-lg">
                          <button class="btn btn-block p-0 position-relative" @click="agregarDetalleModal(articulo)">
                            <div class="position-relative overflow-hidden">
                              <b-img v-if="articulo.fotografia"
                                :src="'img/articulo/' + articulo.fotografia + '?t=' + new Date().getTime()" fluid-grow
                                class="card-img-top rounded-lg object-fit-cover"
                                style="max-width: 150px; max-height: 150px;">
                                <div
                                  class="img-overlay position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center text-white bg-dark bg-opacity-50"
                                  @mouseover="zoomImage = true" @mouseleave="zoomImage = false"
                                  :class="{ 'd-none': !zoomImage }">
                                  <span class="fa fa-search-plus fa-2x"></span>
                                </div>
                              </b-img>
                            </div>
                            <div class="card-body text-center pt-3">
                              <h5 class="card-title mb-2 fw-bold titulo-pequeno">{{ articulo.nombre }}</h5>
                              <p class="card-text mb-0 text-primary fw-bold">Bs. {{ articulo.precio_venta }}</p>

                            </div>
                            <div class="card-hover-effect position-absolute top-0 start-0 w-100 h-100 rounded-lg"
                              style="background-color: rgba(255, 0, 0, 0.2); opacity: 0; transition: opacity 0.3s ease;">
                            </div>

                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="container-fluid fixed-bottom pb-3">
            <div class="row justify-content-end pe-3">
              <div class="col-auto position-relative">
                <span
                  class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger cart-badge">
                  <span class="cart-badge-count">{{ arrayDetalle.length }}</span>
                </span>
                <Button class="p-button-md floating-button" data-toggle="modal" data-target="#exampleModal"
                  data-whatever="@getbootstrap" style="background-color: #212529; border-color: #800080;">
                  <i class="pi pi-shopping-cart" style="font-size: 3rem; color: white;"></i>
                </Button>
              </div>
            </div>
          </div>

          <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
              <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                  <h5 class="modal-title" id="exampleModalLabel">REGISTRAR VENTAS</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <!-- Contenido del modal -->
                  <div class="modal-body">
                    <div class="form-group row">
                      <div class="form-group row">
                        <div class="col-md-5">
                          <!-- campos cliente -->
                          <div v-show="paraLlevar" class="form-group">
                            <label for="cliente" class="form-label fw-bold text-uppercase small">Cliente(*)</label>
                            <input type="text" id="cliente" class="form-control form-control-sm rounded"
                              placeholder="Nombre del Cliente" v-model="cliente" ref="cliente">
                          </div>
                          <div v-show="!paraLlevar" class="form-group">
                            <label for="mesero" class="form-label fw-bold text-uppercase small">Mesero(*)</label>
                            <input type="text" id="mesero" class="form-control form-control-sm rounded"
                              placeholder="Nombre del Mesero" v-model="usuario_autenticado" ref="mesero" readonly>
                          </div>

                          <div class="form-check">
                            <input type="checkbox" id="paraLlevar" aria-label="Checkbox for following text input"
                              v-model="paraLlevar" class="form-check-input">
                            <label for="paraLlevar" class="form-label fw-bold text-uppercase small">
                              Para llevar</label>
                          </div>
                        </div>
                        <div class="col-md-3" v-show="!paraLlevar">
                          <div class="form-group">
                            <label for="mesa" class="form-label fw-bold text-uppercase small">Número Mesa</label>
                            <input type="number" id="mesa" class="form-control form-control-sm rounded" v-model="mesa">
                          </div>

                        </div>
                        <div class="col-md-3" v-show="!paraLlevar">
                          <div class="form-group">
                            <label for="num_comprobante" class="form-label fw-bold text-uppercase small">Número
                              Ticket</label>
                            <input type="text" id="num_comprobante" class="form-control form-control-sm rounded"
                              v-model="num_comprob" ref="numeroComprobanteRef" readonly>
                          </div>
                        </div>
                      </div>
                      <!-- Otros campos ocultos -->
                      <input type="hidden" id="nombreCliente" class="form-control form-control-sm" readonly>
                      <input type="hidden" id="idcliente" class="form-control form-control-sm" readonly>
                      <input type="hidden" id="tipo_documento" class="form-control form-control-sm" readonly>
                      <input type="hidden" id="complemento_id" class="form-control form-control-sm"
                        v-model="complemento_id" ref="complementoIdRef" readonly>
                      <input type="hidden" id="usuarioAutenticado" class="form-control form-control-sm"
                        v-model="usuarioAutenticado" readonly>
                      <input type="hidden" id="documento" class="form-control form-control-sm" readonly value="0000">
                      <input type="hidden" id="email" class="form-control form-control-sm" readonly
                        value="sinnombre@gmail.com">
                      <input type="hidden" id="idAlmacen" class="form-control form-control-sm" readonly value="1">

                      <!-- Observaciones -->
                      <div class="col-md-12">
                        <div class="form-group">
                          <label for="observacion" class="form-label fw-bold text-uppercase small">Observaciones</label>
                          <input type="text" id="observacion" class="form-control form-control-sm rounded"
                            v-model="observacion">
                        </div>
                      </div>

                      <!-- Tipo de Pago -->
                      <div class="col-md-12">
                        <div class="form-group">
                          <label>Tipo pago</label>
                          <select class="form-select form-select-sm rounded" id="idtipo_pago" v-model="tipoPago"
                            @change="manejarTipoPago" required>
                            <option value="" disabled>Seleccione</option>
                            <option value="1">Efectivo</option>
                            <option value="2">QR</option>
                          </select>
                          <div v-if="!tipoPago" class="text-danger small">Por favor, seleccione un tipo de pago.</div>
                        </div>
                      </div>

                      <!-- Vista para pago en efectivo -->
                      <template v-if="tipoPago === '1'">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label for="montoEfectivo" class="form-label fw-bold text-uppercase small">Monto en
                              Efectivo:</label>
                            <input type="number" id="montoEfectivo" class="form-control form-control-sm rounded"
                              v-model="montoEfectivo" @input="calcularCambio" required>
                            <div v-if="montoEfectivoError" class="text-danger small">{{ montoEfectivoError }}</div>
                          </div>
                          <div class="col-md-12">
                            <label class="form-label fw-bold text-uppercase small" id="montoTotal">Total a Pagar: {{
                              total = (calcularTotal).toFixed(2) }}</label>
                          </div>
                          <div class="col-md-12" v-if="cambio">
                            <label class="form-label fw-bold text-uppercase small">Cambio: {{ cambio.toFixed(2)
                              }}</label>
                          </div>
                        </div>
                      </template>

                      <!-- Vista para pago con QR -->
                      <template v-if="tipoPago === '2'">
                        <div class="container">
                          <div class="row justify-content-center">
                            <div class="col-md-8">
                              <div class="form-group">
                                <label for="alias">Alias:</label>
                                <input type="text" class="form-control" v-model="alias" />
                              </div>
                              <div class="form-group">
                                <label for="montoEfectivo">Monto:</label>
                                <span class="font-weight-bold">{{ montoEfectivo = (calcularTotal).toFixed(2) }}</span>
                              </div>
                              <button class="btn btn-primary mb-2" @click="generarQr">Generar QR</button>
                              <div v-if="qrImage" class="mb-2">
                                <img :src="qrImage" alt="Código QR" class="img-fluid" />
                              </div>
                              <button class="btn btn-secondary mb-2" @click="verificarEstado" v-if="qrImage">Verificar
                                Estado de Pago</button>
                              <div v-if="estadoTransaccion" class="card p-2">
                                <div class="font-weight-bold">Estado Actual:</div>
                                <div>
                                  <span :class="'badge badge-' + badgeSeverity">{{ estadoTransaccion.objeto.estadoActual
                                    }}</span>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </template>
                      <!-- Detalles de Venta -->
                      <div class="col-md-12">
                        <div class="table-responsive">
                          <table class="table table-bordered table-striped table-sm">
                            <thead class="thead-dark">
                              <tr>
                                <th class="small">Opciones</th>
                                <th class="small">Artículo</th>
                                <th class="small">Cantidad</th>
                                <th class="small">Subtotal</th>
                              </tr>
                            </thead>
                            <tbody v-if="arrayDetalle.length">
                              <tr v-for="(detalle, index) in arrayDetalle" :key="detalle.id">
                                <td>
                                  <button @click="eliminarDetalle(index)" type="button" class="btn btn-danger btn-sm">
                                    <i class="icon-close"></i>
                                  </button>
                                </td>
                                <td class="small">{{ detalle.articulo }}</td>
                                <td>
                                  <span style="color:red;" v-show="detalle.cantidad > detalle.stock"
                                    class="small">Stock:
                                    {{ detalle.stock }}</span>
                                  <input v-model="detalle.cantidad" type="number"
                                    class="form-control form-control-sm rounded">
                                </td>
                                <td class="small">{{ (detalle.precio * detalle.cantidad - detalle.descuento).toFixed(2)
                                  }}
                                </td>
                              </tr>
                              <tr class="table-active">
                                <td colspan="3" align="right" class="fw-bold small">Total: Bs.</td>
                                <td id="montoTotal" class="fw-bold small">{{ total=(calcularTotal).toFixed(2) }}</td>


                              </tr>
                              <tr class="table-active">

                                <td colspan="3" align="right" class="fw-bold small">Cambio: Bs.</td>
                                <td colspan="3" align="right" class="fw-bold small"> {{ cambio.toFixed(2)
                                  }}</td>
                              </tr>
                            </tbody>
                            <tbody v-else>
                              <tr>
                                <td colspan="6" class="text-center small">No hay artículos agregados</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                  <button type="button" class="btn btn-primary" @click="registrar()">Registrar</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
import vSelect from 'vue-select';

export default {

  data() {
    return {

      //qr
      alias: '',
      montoQR: 0,
      qrImage: '',
      aliasverificacion: '',
      estadoTransaccion: null,
      currency: 'BOB', // Define tu moneda

      tipoPago: '',
      montoEfectivo: "",
      montoEfectivoError: '',
      cambio: 0,
      totalAPagar: 0,
      arrayBuscador: [],
      venta_id: 0,
      idcliente: 0,
      usuarioAutenticado: null,
      puntoVentaAutenticado: null,
      cliente: '',
      mesero: '',
      email: '',
      cliente: '',
      nombreCliente: '',
      documento: '',
      tipo_documento: '',
      complemento_id: '',
      descuentoAdicional: 0.00,
      tipo_comprobante: 'TICKET',
      serie_comprobante: '',
      last_comprobante: 0,
      num_comprob: "",
      impuesto: 0.18,
      total: 0.0,
      totalImpuesto: 0.0,
      totalParcial: 0.0,
      arrayVenta: [],
      arrayCliente: [],
      arrayDetalle: [],
      arrayProductos: [],
      mostrarTipoComprobante: false,
      listado: 1,
      modal: 0,
      tituloModal: '',
      tipoAccion: 0,
      errorVenta: 0,
      errorMostrarMsjVenta: [],
      pagination: {
        'total': 0,
        'current_page': 0,
        'per_page': 0,
        'last_page': 0,
        'from': 0,
        'to': 0,
      },
      offset: 3,
      criterio: '',
      buscar: '',
      criterioA: '0',
      buscarA: '',
      arrayArticulo: [],
      idarticulo: 0,
      codigo: '',
      articulo: '',
      medida: '',
      codigoProductoSin: '',
      precio: 0,
      cantidad: 1,
      descuento: 0,
      sTotal: 0,
      stock: 0,
      valorMaximoDescuento: '',
      mostrarDireccion: true,
      mostrarCUFD: true,
      mostrarEnviarPaquete: true,
      mostrarValidarPaquete: false,
      cafc: '',
      scodigomotivo: null,
      mesa: '',
      observacion: '',
      usuario_autenticado: '',
      paraLlevar: false,
      categoria: '',
      //almacenes
      arrayAlmacenes: [],
      idAlmacen: 1,
    }
  },
  components: {
    vSelect
  },
  computed: {
    esAdministrador() {
      return this.idrol === 1; // Asume que el idrol 1 corresponde al administrador
    },
    isActived: function () {
      return this.pagination.current_page;
    },

    //Calcula los elementos de la paginación
    pagesNumber: function () {
      if (!this.pagination.to) {
        return [];
      }

      var from = this.pagination.current_page - this.offset;
      if (from < 1) {
        from = 1;
      }

      var to = from + (this.offset * 2);
      if (to >= this.pagination.last_page) {
        to = this.pagination.last_page;
      }

      var pagesArray = [];
      while (from <= to) {
        pagesArray.push(from);
        from++;
      }
      return pagesArray;
    },
    calcularTotal: function () {
      var resultado = 0.0;
      for (var i = 0; i < this.arrayDetalle.length; i++) {
        resultado += (this.arrayDetalle[i].precio * this.arrayDetalle[i].cantidad - this.arrayDetalle[i].descuento)
      }
      resultado -= this.descuentoAdicional;
      return resultado;
    },

    calcularSubTotal: function () {
      var resultado = 0.0;
      for (var i = 0; i < this.arrayDetalle.length; i++) {
        resultado = resultado + (this.arrayDetalle[i].precio * this.arrayDetalle[i].cantidad - this.arrayDetalle[i].descuento)
      }
      return resultado;
    },
    badgeSeverity() {
      if (this.estadoTransaccion && this.estadoTransaccion.objeto.estadoActual === 'PENDIENTE') {
        return 'danger'; // Rojo para estado PENDIENTE
      } else if (this.estadoTransaccion && this.estadoTransaccion.objeto.estadoActual === 'PAGADO') {
        return 'success'; // Verde para estado PAGADO
      } else {
        return 'info'; // Otros estados
      }
    }

  },
  methods: {
    actualizarFechaHora() {
      const now = new Date();
      this.alias = now.toLocaleString();
    },

    recargarPagina() {
      window.location.reload(); // Recargar la página actual
    },
    mostrarDetalle() {
      let me = this;
      me.montoQR = '';
      me.montoEfectivo = '';
      me.tipoPago = '';
      me.selectAlmacen();
      me.listado = 0;
      me.listarArticulo('', '3');
      me.nombreCliente = '';
      me.idcliente = 0;
      me.tipo_documento = '';
      me.complemento_id = '';
      me.documento = '';
      me.email = '';
      me.cafc = '';
      me.idproveedor = 0;
      me.tipo_comprobante = 'TICKET';
      me.serie_comprobante = '';
      me.nextNumber();
      //me.num_comprobante = '';
      me.impuesto = 0.18;
      me.total = 0.0;
      me.idarticulo = 0;
      me.articulo = '';
      me.cantidad = 0;
      me.precio = 0;
      me.arrayDetalle = [];
    },
    agregarDetalleModal(data = []) {
      let me = this;
      if (me.encuentra(data['id'])) {
        swal({
          type: 'error',
          title: 'Error...',
          text: 'Este Artículo ya se encuentra agregado!',
        });
      } else {
        me.arrayDetalle.push({
          idarticulo: data['id'],
          articulo: data['nombre'],
          cantidad: 1,
          precio: data['precio_venta'],
          descuento: 0,
          stock: data['stock'],
          medida: data['medida'],
        });
        swal({
          position: 'center',
          icon: 'success',
          title: 'Artículo agregado al carrito',
          showConfirmButton: false,
          timer: 500,
          width: '400px',

        });
      }
    },
    validarVenta() {
      let me = this;
      me.errorVenta = 0;
      me.errorMostrarMsjVenta = [];
      var art;

      if (me.paraLlevar) {
        if (!me.cliente) me.errorMostrarMsjVenta.push("Ingrese el Nombre de un Cliente");
      }

      // if (me.tipo_comprobante == 0) me.errorMostrarMsjVenta.push("Seleccione el Comprobante");
      // if (me.arrayDetalle.length <= 0) me.errorMostrarMsjVenta.push("Ingrese detalles");
      if (!me.tipoPago) {
        me.errorMostrarMsjVenta.push("Seleccione un tipo de pago (Efectivo o QR)");
        me.errorVenta = 1;
        return 'noTipoPago';
      } else if (me.tipoPago === '1' && me.montoEfectivo === 0) {
        me.errorMostrarMsjVenta.push("El monto en efectivo no puede ser cero");
        me.errorVenta = 1;
      }

      if (me.errorMostrarMsjVenta.length) {
        me.errorVenta = 1;
        return true;
      }

      return me.errorVenta;
    },

    registrarVenta() {
      const vm = this;
      const validacion = this.validarVenta();

      if (validacion === 'noTipoPago') {
        swal('Aviso', 'Por favor, seleccione un tipo de pago.', 'warning');
        return;
      }

      if (validacion) {
        swal('Aviso', 'Por favor, complete todos los campos requeridos.', 'warning');
        return;
      }

      let me = this;
      console.log("cliente ", this.cliente);
      console.log("mesa ", this.mesa);
      console.log("Carrito ", this.arrayDetalle);

      let postData = {
        'idcliente': this.idcliente,
        'cliente': this.cliente,
        'idtipo_pago': this.tipoPago,
        'observacion': this.observacion,
        'tipo_comprobante': this.tipo_comprobante,
        'serie_comprobante': this.serie_comprobante,
        'num_comprobante': this.num_comprob,
        'impuesto': this.impuesto,
        'total': this.montoEfectivo,
        'idAlmacen': this.idAlmacen,
        'data': this.arrayDetalle
      };

      if (this.mesa) {
        postData.mesa = this.mesa;
      }

      axios.post('/venta/registrar', postData)
        .then(function (response) {
          console.log(response.data.id);

          if (response.data.id > 0) {
            me.listado = 1;
            me.listarVenta(1, '', 'num_comprob');
            me.idproveedor = 0;
            me.cliente = '';
            me.tipo_comprobante = 'TICKET';
            me.serie_comprobante = '';
            me.num_comprob = '';
            me.impuesto = 0.18;
            me.total = 0.0;
            me.idarticulo = 0;
            me.articulo = '';
            me.cantidad = 0;
            me.precio = 0;
            me.stock = 0;
            me.codigo = '';
            me.descuento = 0;
            me.mesa = 0;
            me.observacion = '';
            me.arrayDetalle = [];
            swal('VENTA REGISTRADA', 'Correctamente', 'success').then(() => {
              vm.recargarPagina();
            });
            me.arrayProductos = [];
            me.listarVenta(1, '', 'id');
          } else {
            if (response.data.valorMaximo) {
              swal('Aviso', 'El valor de descuento no puede exceder el ' + response.data.valorMaximo, 'warning');
              return;
            } else {
              swal('Aviso', response.data.caja_validado, 'warning');
              return;
            }
          }
        }).catch(function (error) {
          console.log(error);
        });
    },



    verificarEstado() {
      axios.post('/qr/verificarestado', {
        alias: this.aliasverificacion,
      })
        .then(response => {
          this.estadoTransaccion = response.data;
        })
        .catch(error => {
          console.error(error);
        });
    },

    generarQr() {
      this.aliasverificacion = this.alias;
      axios.post('/qr/generarqr', {
        alias: this.alias,
        monto: this.calcularTotal
      })
        .then(response => {
          const imagenBase64 = response.data.objeto.imagenQr;
          this.qrImage = `data:image/png;base64,${imagenBase64}`;
        })
        .catch(error => {
          console.error(error);
        });

      this.alias = '';
      this.montoQR = 0;
    },



    calcularCambio() {
      // Calcula el total a pagar sumando el subtotal de todos los artículos seleccionados
      this.cambio = this.montoEfectivo - this.totalAPagar;

      // Calcula el cambio restando el total a pagar del monto en efectivo
      this.cambio = this.montoEfectivo - this.totalAPagar;
    },
    calcularTotalArticulos() {
      // Sumar el subtotal de todos los artículos seleccionados
      return this.arrayDetalle.reduce((total, detalle) => {
        return total + (detalle.precio * detalle.cantidad - detalle.descuento);
      }, 0);
    },
    manejarTipoPago() {
      if (this.tipoPago === '1') {
        // Mostrar input para monto en efectivo
        this.mostrarMontoEfectivo = true;
        // Calcular el totalAPagar
        this.totalAPagar = this.calcularTotal;
        console.log('Pago en efectivo');
      } else if (this.tipoPago === '2') {
        // Lógica para manejar el pago con QR
        this.mostrarMontoEfectivo = false;
        console.log('Pago con QR');
      }
    },


    nextNumber() {
      if (!this.num_comprob || this.num_comprob === "") {
        this.last_comprobante++;
        // Completa con ceros a la izquierda hasta alcanzar 5 dígitos
        this.num_comprob = this.last_comprobante.toString().padStart(5, "0");
      }
    },


    listarVenta(page, buscar, criterio, per_page = 10) {
      let me = this;
      var url = '/venta?page=' + page + '&buscar=' + buscar + '&criterio=' + criterio + '&per_page=' + per_page;
      axios.get(url)
        .then(function (response) {
          var respuesta = response.data;
          me.arrayVenta = respuesta.ventas.data;
          me.pagination = respuesta.pagination;
          me.arrayVenta = me.arrayVenta.map(item => {
            if (item.cliente === null) {
              return { ...item, cliente: 'Sin Nombre' };
            } else {
              return item;
            }
          });
        })
        .catch(function (error) {
          console.log(error);
        });
    },
    selectCliente(search, loading) {
      let me = this;
      loading(true)
      var url = '/cliente/selectCliente?filtro=' + search;
      axios.get(url).then(function (response) {
        //console.log(response.clientes);
        let respuesta = response.data;
        q: search
        me.arrayCliente = respuesta.clientes;
        loading(false)
      })
        .catch(function (error) {
          console.log(error);
        });
    },
    getDatosCliente(val1) {
      let me = this;
      me.loading = true;
      me.idcliente = val1.id;
      //console.log(val1);
      this.email = val1.email;
      this.nombreCliente = val1.nombre;
      this.documento = val1.num_documento;
      this.tipo_documento = val1.tipo_documento;
      this.complemento_id = val1.complemento_id;

    },

    buscarArticulo() {
      let me = this;
      var url = '/articulo/buscarArticuloVenta?filtro=' + me.codigo;

      axios.get(url).then(function (response) {
        var respuesta = response.data;
        console.log(respuesta);
        me.arrayArticulo = respuesta.articulos;

        if (me.arrayArticulo.length > 0) {
          me.articulo = me.arrayArticulo[0]['nombre'];
          me.idarticulo = me.arrayArticulo[0]['id'];
          me.codigo = me.arrayArticulo[0]['codigo'];
          me.precio = me.arrayArticulo[0]['precio_venta'];
          me.stock = me.arrayArticulo[0]['stock'];
          me.medida = me.arrayArticulo[0]['medida'];
          me.codigoProductoSin = me.arrayArticulo[0]['codigoProductoSin'];
        }
        else {
          me.articulo = 'No existe este articulo';
          me.idarticulo = 0;
        }
      })
        .catch(function (error) {
          console.log(error);
        });
    },
    listarLinea(page, buscar, criterio) {
      let me = this;
      var url = '/categoria?page=' + page + '&buscar=' + buscar + '&criterio=' + criterio;
      axios.get(url).then(function (response) {
        var respuesta = response.data;
        me.arrayBuscador = respuesta.categorias.data;

      })
        .catch(function (error) {
          console.log(error);
        });
    },

    imprimirTicket(id) {
      axios.get('/venta/imprimir/' + id, { responseType: 'blob' })
        .then(function (response) {
          window.location.href = "docs/ticket.pdf";
          const fileURL = 'docs/ticket.pdf';
          window.open(fileURL, '_blank');
          console.log("Se generó el Ticket correctamente");
        })
        .catch(function (error) {
          console.log(error);
        });
    },

    cambiarPagina(page, buscar, criterio) {
      let me = this;
      //Actualiza la página actual
      me.pagination.current_page = page;
      //Envia la petición para visualizar la data de esa página
      me.listarVenta(page, buscar, criterio);
    },

    encuentra(id) {
      var sw = 0;
      for (var i = 0; i < this.arrayDetalle.length; i++) {
        if (this.arrayDetalle[i].idarticulo == id) {
          sw = true;
        }
      }
      return sw;
    },
    eliminarDetalle(index) {
      let me = this;
      me.arrayDetalle.splice(index, 1);
    },
    agregarDetalle() {
      let me = this;

      let actividadEconomica = 474110;
      let codigoProductoSin = document.getElementById("codigoProductoSin").value;
      let codigoProducto = document.getElementById("codigo").value;
      let descripcion = document.getElementById("nombre_producto").value;
      let cantidad = document.getElementById("cantidad").value;
      let unidadMedida = 57;
      let precioUnitario = document.getElementById("precio").value;
      let montoDescuento = document.getElementById("descuento").value;
      let subTotalValor = document.getElementById("sTotal");
      let subTotal = subTotalValor.textContent;
      let numeroSerie = null;
      let numeroImei = null;


      if (me.idarticulo == 0 || me.cantidad == 0 || me.precio == 0) {

      } else {
        if (me.encuentra(me.idarticulo)) {
          swal({
            type: 'error',
            title: 'Error...',
            text: 'Este Artículo ya se encuentra agregado!',
          })
        } else {
          if (me.cantidad > me.stock) {
            swal({
              type: 'error',
              title: 'Error...',
              text: 'No hay stock disponible!',
            })
          } else {
            me.arrayDetalle.push({
              idarticulo: me.idarticulo,
              articulo: me.articulo,
              medida: me.medida,
              cantidad: me.cantidad,
              precio: me.precio,
              descuento: me.descuento,
              stock: me.stock,
            });

            me.arrayProductos.push({
              actividadEconomica: actividadEconomica,
              codigoProductoSin: codigoProductoSin,
              codigoProducto: codigoProducto,
              descripcion: descripcion,
              cantidad: cantidad,
              unidadMedida: unidadMedida,
              precioUnitario: precioUnitario,
              montoDescuento: montoDescuento,
              subTotal: subTotal,
              numeroSerie: numeroSerie,
              numeroImei: numeroImei
            });

            me.codigo = '';
            me.idarticulo = 0;
            me.articulo = '';
            me.medida = '';
            me.cantidad = 0;
            me.precio = 0;
            me.descuento = 0;
            me.stock = 0;
            me.sTotal = 0;
          }
        }

      }

    },

    listarArticulo(page, criterioA) {
      let me = this;
      var url = '/articulo/listarArticuloVenta?page=' + page + '&criterio=' + criterioA + '&idAlmacen=' + this.idAlmacen;
      axios.get(url).then(function (response) {
        var respuesta = response.data;
        console.log('Respuesta ', respuesta);
        me.arrayArticulo = respuesta.articulos.data;

      })
        .catch(function (error) {
          console.log(error);
        });
    },
    obtenerDatosUsuario() {
      axios.get('/venta')
        .then(response => {
          this.usuarioAutenticado = response.data.usuario.usuario;
          this.usuario_autenticado = this.usuarioAutenticado;
          this.puntoVentaAutenticado = response.data.usuario.idpuntoventa;
        })
        .catch(error => {
          console.error(error);
        });
    },
    registrar() {
      this.registrarVenta();
      //this.paqueteFactura();
      //this.num_comprob;
    },


    selectAlmacen() {
      let me = this;
      var url = '/almacen/selectAlmacen';
      axios.get(url).then(function (response) {
        var respuesta = response.data;
        me.arrayAlmacenes = respuesta.almacenes;
        console.log(me.arrayAlmacenes);

      })
        .catch(function (error) {
          console.log(error);
        });
    },
    getAlmacenProductos(almacen) {
      let me = this;
      me.idAlmacen = 1;
      console.log(me.idAlmacen);
    },
    validarVenta() {
      let me = this;
      me.errorVenta = 0;
      me.errorMostrarMsjVenta = [];
      var art;

      me.arrayDetalle.map(function (x) {
        if (x.cantidad > x.stock) {
          art = x.articulo + " Stock insuficiente";
          me.errorMostrarMsjVenta.push(art);
        }
      });
      if (me.paraLlevar) {
        if (!me.cliente) me.errorMostrarMsjVenta.push("Ingrese el Nombre de un Cliente");
      }
      if (me.paraLlevar == false) {
        if (me.mesa === 0 && !me.mesa) me.errorMostrarMsjVenta.push("Ingresar Numero de Mesa")
      }
      if (me.tipo_comprobante == 0) me.errorMostrarMsjVenta.push("Seleccione el Comprobante");
      if (me.arrayDetalle.length <= 0) me.errorMostrarMsjVenta.push("Ingrese detalles");

      if (me.errorMostrarMsjVenta.length) me.errorVenta = 1;

      return me.errorVenta;
    },




    ocultarDetalle() {
      this.listado = 1;
    },

    verVenta(id) {
      let me = this;
      me.listado = 2;

      //Obtener datos del ingreso
      var arrayVentaT = [];
      var url = '/venta/obtenerCabecera?id=' + id;

      axios.get(url).then(function (response) {
        var respuesta = response.data;
        arrayVentaT = respuesta.venta;
        me.categoria = (arrayVentaT[0]['cliente'] == null) ? 'Mesa' : 'Llevar';
        me.cliente = (arrayVentaT[0]['cliente'] != null) ? arrayVentaT[0]['cliente'] : '';
        me.tipo_comprobante = arrayVentaT[0]['tipo_comprobante'];
        me.serie_comprobante = arrayVentaT[0]['serie_comprobante'];
        me.num_comprobante = arrayVentaT[0]['num_comprobante'];
        me.impuesto = arrayVentaT[0]['impuesto'];
        me.total = arrayVentaT[0]['total'];
        me.mesa = (arrayVentaT[0]['mesa'] != null) ? arrayVentaT[0]['mesa'] : 'SIN MESA';
        me.observacion = (arrayVentaT[0]['observacion'] == null) ? 'SIN OBSERVACION' : arrayVentaT[0]['observacion'];
      })
        .catch(function (error) {
          console.log(error);
        });

      //obtener datos de los detalles
      var url = '/venta/obtenerDetalles?id=' + id;

      axios.get(url).then(function (response) {
        //console.log(response);
        var respuesta = response.data;
        me.arrayDetalle = respuesta.detalles;

      })
        .catch(function (error) {
          console.log(error);
        });
    },
    cerrarModal() {
      this.modal = 0;
      this.tituloModal = '';
      me.idproveedor = 0;
      me.cliente = '';
      me.tipo_comprobante = 'TICKET';
      me.serie_comprobante = '';
      me.num_comprob = '';
      me.impuesto = 0.18;
      me.total = 0.0;
      me.idarticulo = 0;
      me.articulo = '';
      me.cantidad = 0;
      me.precio = 0;
      me.stock = 0;
      me.codigo = '';
      me.descuento = 0;
      me.arrayDetalle = [];
    },
    abrirModal() {
      if (this.idAlmacen == 0) {
        return;
      }
      //this.listarArticulo();
      this.modal = 1;
      this.tituloModal = 'Seleccione los articulos que desee';

    },

    desactivarVenta(id) {
      // Mostrar el diálogo de confirmación
      swal({
        title: '¿Está seguro de anular esta venta?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar!',
        cancelButtonText: 'Cancelar',
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger',
        buttonsStyling: false,
        reverseButtons: true
      }).then((result) => {
        if (result.value) {
          let me = this;

          axios.put('/venta/desactivar', {
            'id': id
          }).then(function (response) {
            me.listarVenta(1, '', 'num_comprobante');
            swal(
              'Anulado!',
              'La venta ha sido anulada con éxito.',
              'success'
            );
          }).catch(function (error) {
            console.log(error);
          });
        } else if (result.dismiss === swal.DismissReason.cancel) {
          // El usuario ha cancelado la acción
        }
      });
    },


  },

  created() {
    // Realiza una solicitud AJAX para obtener los datos de sesión
    axios.get('/obtener-datos-sesion')
      .then(response => {
        console.log('Estado de la respuesta:', response.status);
        // Asigna los datos de sesión a las variables del componente Vue
        this.scodigorecepcion = response.data.scodigorecepcion;
        console.log('Valor de scodigorecepcion:', this.scodigorecepcion);

        // Una vez obtenidos los datos de sesión, realiza la solicitud para obtener el último comprobante
        axios.get('/ruta-a-tu-endpoint-laravel-para-obtener-ultimo-comprobante')
          .then(response => {
            const lastComprobante = response.data.last_comprobante;
            // Asigna el último comprobante a la variable del componente Vue
            this.last_comprobante = lastComprobante;

            // Llama a una función para procesar el siguiente número
            this.nextNumber();
          })
          .catch(error => {
            console.error('Error al obtener el último comprobante:', error);
          });
      })
      .catch(error => {
        console.error('Error al obtener datos de sesión:', error);
      });
  },


  mounted() {
    this.listarVenta(1, this.buscar, this.criterio, 10);
    window.addEventListener('keydown', this.atajoButton);
    this.obtenerDatosUsuario();
    this.listarArticulo(1, this.buscar, this.criterio);
    this.listarArticulo('', '1');
    this.listarLinea('1', '', 'nombreLinea');
    this.actualizarFechaHora();


  }
}
</script>
<style>
/* Fuentes */
@import url('https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap');

body {
  font-family: 'Roboto', sans-serif;
  background-color: #e1dcdc;
  color: #000000;
}

/* Colores */
.bg-dark {
  background-color: #181818 !important;
}

.text-primary {
  color: #000000 !important;
}

.btn-primary {
  background-color: #885dd2;
  border-color: #7e57c2;
}

.btn-primary:hover {
  background-color: #6d4cac;
  border-color: #6d4cac;
}

/* Efectos */
.card {
  border: none;
  border-radius: 10px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease-in-out;
}



/* Tablas */
.table {
  background-color: #242424;
  color: #e0e0e0;
}

.table th,
.table td {
  border-color: #333333;
}

.table thead th {
  border-bottom: 2px solid #7e57c2;
}

/* Inputs */
.form-control {
  background-color: #ffffff;
  /* Cambio: Fondo blanco para inputs */
  border: 1px solid #e0e0e0;
  border-radius: 20px;
  color: #000000;
  /* Cambio: Texto negro para inputs */
}

.form-control:focus {
  background-color: #ffffff;
  box-shadow: 0 0 0 0.2rem rgba(126, 87, 194, 0.25);
}

/* Dropdown */
.dropdown-menu {
  background-color: hsl(0, 0%, 100%);
  border: none;
  border-radius: 10px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
}

.dropdown-item {
  color: #0e0d0d;
  transition: all 0.3s ease-in-out;
}

.dropdown-item:hover {
  background-color: #020202;
  color: #ffffff;
}

/*hasta aqui */
.modal-content {
  width: 100% !important;
  position: absolute !important;
}

.mostrar {
  display: list-item !important;
  opacity: 1 !important;
  position: absolute !important;
  background-color: #3c29297a !important;
}

.div-error {
  display: flex;
  justify-content: center;
}

.text-error {
  color: red !important;
  font-weight: bold;
}

@media (max-width: 767px) {
  .row-cols-1>* {
    flex: 0 0 50%;
    /* Dos elementos por fila en dispositivos móviles */
    max-width: 50%;
    /* Dos elementos por fila en dispositivos móviles */
  }
}

.card-img {
  width: 120px;
  height: auto;
  border-top-right-radius: 8px;
  border-bottom-right-radius: 8px;
}

.texto-largo {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  /* Número de líneas a mostrar */
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  /* Agrega puntos suspensivos al final del texto truncado */
  white-space: initial;
  /* Evita que el texto se desborde horizontalmente */
  padding-left: 0rem;
  padding-right: 0rem;


}

.custom-table {
  width: 100%;
  /* Ajusta el ancho al 100% del contenedor */
  margin-bottom: 400px;
}

.object-fit-cover {
  object-fit: cover;

}

.table {
  border-radius: 0.5rem;
  overflow: hidden;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.table thead th {
  background-color: #343a40;
  color: #fff;
  border-color: #454d55;
  text-align: center;
}

.floating-button {
  padding: 10px 15px 1px 15px;
  border: none;
  border-radius: 8px;
  font-size: 28px;
  cursor: pointer;
}

.floating-buttons {
  position: fixed;
  bottom: 90px;
  right: 20px;
  z-index: 900;
}

.cart-badge {
  width: 30px;
  height: 30px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.cart-badge-count {
  font-size: 20px;
  font-weight: bold;
  color: white;
}

.titulo-pequeno {
  font-size: 14px;
  /* Ajusta el tamaño de fuente según tus necesidades */
}
</style>