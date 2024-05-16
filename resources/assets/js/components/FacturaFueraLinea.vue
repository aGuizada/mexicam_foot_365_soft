<template>
  <main class="main  text-white">
    <div class="container-fluid py-3"></div>
    <!-- Ejemplo de tabla Listado -->
    <div class="card">

      <!-- Listado-->

      <!--Fin Listado-->
      <!-- Detalle-->

      <div class="card-body">
        <div class="row">
          <!-- Columna de Comidas -->
          <div class="container">
            <div class="row">
              <div class="col-md-15">
                <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                  <div class="modal-body">
                    <!-- Dropdown de Comidas -->
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
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-6"> <!-- Cambio aquí -->
                      <!-- Tarjetas de Artículos -->
                      <div class="col mb-3" v-for="articulo in arrayArticulo" :key="articulo.id">
                        <div class="card h-100 border-0 shadow-lg">
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
                              <h5 class="card-title mb-2 fw-bold">{{ articulo.nombre }}</h5>
                              <p class="card-text mb-0 text-primary">Bs. {{ articulo.precio_venta }}</p>
                            </div>
                            <div class="card-hover-effect position-absolute top-0 start-0 w-100 h-100 rounded-lg"
                              style="background-color: rgba(255, 255, 255, 0.2); opacity: 0; transition: opacity 0.3s ease;">
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







          <button type="button" class="btn btn-primary"
            style="position: fixed; bottom: 20px; right: 20px; left: 20px; z-index: 1000;" data-toggle="modal"
            data-target="#exampleModal" data-whatever="@getbootstrap">
            REALIZAR VENTA
          </button>



          <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">

                <div class="col-md-15">
                  <div class="card bg-dark text-white rounded-lg shadow-lg">
                    <div class="card-body">
                      <div class="form-group row">
                        <div class="col-md-5">
                          <div v-show="paraLlevar" class="form-group">
                            <label for="cliente" class="form-label fw-bold text-uppercase small">Cliente(*)</label>
                            <input type="text" id="cliente" class="form-control form-control-sm rounded-pill"
                              placeholder="Nombre del Cliente" v-model="cliente" ref="cliente">
                          </div>
                          <div v-show="!paraLlevar" class="form-group">
                            <label for="mesero" class="form-label fw-bold text-uppercase small">Mesero(*)</label>
                            <input type="text" id="mesero" class="form-control form-control-sm rounded-pill"
                              placeholder="Nombre del Mesero" v-model="usuario_autenticado" ref="mesero" readonly>
                          </div>
                          <div class="form-group">
                            <label for="paraLlevar" class="form-label fw-bold text-uppercase small">Para llevar:
                              <span class="text-danger">*</span>
                              <input type="checkbox" id="paraLlevar" aria-label="Checkbox for following text input"
                                v-model="paraLlevar" class="form-check-input">
                            </label>
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

                        <div v-show="!paraLlevar" class="col-md-5">
                          <div class="form-group">
                            <label for="mesa" class="form-label fw-bold text-uppercase small">Num Mesa(*)</label>
                            <input type="number" id="mesa" class="form-control form-control-sm rounded-pill"
                              v-model="mesa">
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <label for="num_comprobante" class="form-label fw-bold text-uppercase small">Número
                              Ticket</label>
                            <input type="text" id="num_comprobante" class="form-control form-control-sm rounded-pill"
                              v-model="num_comprob" ref="numeroComprobanteRef" readonly>
                          </div>
                        </div>
                        <!-- Otros campos según la lógica de tu aplicación -->

                        <!-- Observaciones -->
                        <div class="col-md-12">
                          <div class="form-group">
                            <label for="observacion"
                              class="form-label fw-bold text-uppercase small">Observaciones</label>
                            <input type="text" id="observacion" class="form-control form-control-sm rounded-pill"
                              v-model="observacion">
                          </div>
                        </div>

                        <!-- Tipo de Pago -->
                        <div class="col-md-12">
                          <div class="form-group">
                            <label>Tipo pago</label>
                            <select class="form-select form-select-sm rounded-pill" id="idtipo_pago" v-model="tipoPago"
                              @change="manejarTipoPago" required>
                              <option value="" disabled selected>Seleccione</option>
                              <option value="1">Efectivo</option>
                              <option value="2">QR</option>
                            </select>
                            <div v-if="!tipoPago" class="text-danger small">Por favor, seleccione un tipo de pago.
                            </div>
                          </div>
                        </div>

                        <!-- Vista para pago en efectivo -->
                        <template v-if="tipoPago === '1'">
                          <div class="col-md-12">
                            <div class="form-group">
                              <label for="montoEfectivo" class="form-label fw-bold text-uppercase small">Monto en
                                Efectivo:</label>
                              <input type="number" id="montoEfectivo" class="form-control form-control-sm rounded-pill"
                                v-model="montoEfectivo" @input="calcularCambio">
                            </div>
                          </div>
                          <div class="col-md-12">
                            <label class="form-label fw-bold text-uppercase small">Total a Pagar: {{
                              totalAPagar.toFixed(2)
                            }}</label>
                          </div>
                          <div class="col-md-12" v-if="cambio">
                            <label class="form-label fw-bold text-uppercase small">Cambio: {{ cambio.toFixed(2)
                              }}</label>
                          </div>
                        </template>

                        <!-- Vista para pago con QR -->
                        <template v-if="tipoPago === '2'">
                          <div class="d-flex justify-content-center align-items-center">
                            <div>
                              <label for="alias">Alias:</label>
                              <input type="text" class="form-control" v-model="alias" />
                              <br>
                              <label for="montoQR">Monto:</label>
                              <span class="font-weight-bold">{{ total=(calcularTotal).toFixed(2) }}</span>
                              <br>
                              <button class="btn btn-primary" @click="generarQr">Generar QR</button>

                              <!-- Espacio para mostrar la imagen del código QR -->
                              <div v-if="qrImage">
                                <img :src="qrImage" alt="Código QR" />
                              </div>

                              <!-- Botón para verificar estado -->
                              <button class="btn btn-secondary" @click="verificarEstado" v-if="qrImage">Verificar
                                Estado de
                                Pago</button>

                              <!-- Mostrar estado de transacción -->
                              <div v-if="estadoTransaccion" class="card p-2">
                                <div class="font-weight-bold">Estado Actual:</div>
                                <div>
                                  <span :class="'badge badge-' + badgeSeverity">{{
                              estadoTransaccion.objeto.estadoActual
                            }}</span>
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
                                      {{ detalle.stock
                                      }}</span>
                                    <input v-model="detalle.cantidad" type="number"
                                      class="form-control form-control-sm">
                                  </td>
                                  <td class="small">{{ (detalle.precio * detalle.cantidad -
                              detalle.descuento).toFixed(2) }}
                                  </td>
                                </tr>
                                <tr class="table-active">
                                  <td colspan="3" align="right" class="fw-bold small">Total: Bs.</td>
                                  <td id="montoTotal" class="fw-bold small">{{ total=(calcularTotal).toFixed(2) }}
                                  </td>
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

                        <!-- Botones -->
                        <div class="col-md-12">
                          <div class="form-group row">
                            <button class="col-md-6 btn btn-danger btn-sm rounded-pill" type="button"
                              data-dismiss="modal">Cerrar</button>
                            <div class="col-md-6">
                              <button type="button" class="btn btn-primary btn-sm rounded-pill w-100"
                                @click="registrar()">Registrar
                                Venta</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Columna de Detalles de Venta -->
        </div>
      </div>
      <!-- Fin Detalle-->
      <!--Ver ingreso-->

      <!--Fin ver ingreso-->
    </div>
    <!-- Fin ejemplo de tabla Listado -->
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
      montoEfectivo: 0,
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
      mesa: 0,
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

    volverAPaginaDeVenta() {
      this.tipoPago = '';
      this.precio = ''; // Asegúrate de ajustar esto según cómo estés manejando el precio
      this.$router.push('./FacturaFueraLinea.vue'); // Ejemplo de redireccionamiento a la página de venta
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
    atajoButton: function (event) {
      //console.log(event.keyCode);
      //console.log(event.ctrlKey);
      if (event.shiftKey && event.keyCode === 81) {
        event.preventDefault();
        this.$refs.impuestoRef.focus();
      }
      if (event.shiftKey && event.keyCode === 87) {
        event.preventDefault();
        this.$refs.serieComprobanteRef.focus();
      }
      if (event.shiftKey && event.keyCode === 69) {
        event.preventDefault();
        this.$refs.numeroComprobanteRef.focus();
      }
      if (event.shiftKey && event.keyCode === 82) {
        event.preventDefault();
        this.$refs.articuloRef.focus();
      }
      if (event.shiftKey && event.keyCode === 84) {
        event.preventDefault();
        this.$refs.precioRef.focus();
      }
      if (event.shiftKey && event.keyCode === 89) {
        event.preventDefault();
        this.$refs.cantidadRef.focus();
      }
      if (event.shiftKey && event.keyCode === 85) {
        event.preventDefault();
        this.$refs.descuentoRef.focus();
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
    agregarDetalleModal(data = []) {
      let me = this;
      if (me.encuentra(data['id'])) {
        swal({
          type: 'error',
          title: 'Error...',
          text: 'Este Artículo ya se encuentra agregado!',
        })
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
    registrarVenta() {
      if (this.validarVenta()) {
        return;
      }

      let me = this;
      console.log("cliente ", this.cliente);
      console.log("mesa ", this.mesa);
      console.log("Carrito ", this.arrayDetalle);

      axios.post('/venta/registrar', {
        'idcliente': this.idcliente,
        'cliente': this.cliente,
        'mesa': this.mesa,
        'idtipo_pago': this.tipoPago,
        'observacion': this.observacion,
        'tipo_comprobante': this.tipo_comprobante,
        'serie_comprobante': this.serie_comprobante,
        'num_comprobante': this.num_comprob,
        'impuesto': this.impuesto,
        'total': this.total,
        'idAlmacen': this.idAlmacen,
        'data': this.arrayDetalle
      }).then(function (response) {
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
          me.observacion = '',
            me.arrayDetalle = [];
          //window.open('/factura/imprimir/' + response.data.id);
          swal('VENTA REGISTRADA', 'Correctamente', 'success');
          me.arrayProductos = [];
          me.listarVenta(1, '', 'id');
        } else {
          if (response.data.valorMaximo) {
            //alert('El valor de descuento no puede exceder el '+ response.data.valorMaximo);
            swal('Aviso', 'El valor de descuento no puede exceder el ' + response.data.valorMaximo, 'warning');
            return;
          } else {
            //alert(response.data.caja_validado);
            swal('Aviso', response.data.caja_validado, 'warning');
            return;
          }
          //console.log(response.data.valorMaximo)
        }
      }).catch(function (error) {
        console.log(error);
      });
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

    paqueteFactura() {
      let me = this;

      let numeroFactura = document.getElementById("num_comprobante").value;
      let cuf = "464646464";
      let cufdValor = document.getElementById("cufdValor");
      let cufd = cufdValor.textContent;
      let direccionValor = document.getElementById("direccion");
      let direccion = direccionValor.textContent;
      var tzoffset = (new Date()).getTimezoneOffset() * 60000;
      let fechaEmision = (new Date(Date.now() - tzoffset)).toISOString().slice(0, -1);
      let id_cliente = document.getElementById("idcliente").value;
      let nombreRazonSocial = document.getElementById("nombreCliente").value;
      let tipoDocumentoIdentidad = 1;
      let numeroDocumento = document.getElementById("documento").value;
      let complemento = document.getElementById("complemento_id").value;
      let montoTotalValor = document.getElementById("montoTotal");
      let montoTotal = montoTotalValor.textContent;
      let descuentoAdicional = document.getElementById("descuentoAdicional").value;
      let leyenda = "Ley N° 453: El proveedor de servicios debe habilitar medios e instrumentos para efectuar consultas y reclamaciones.";
      let usuario = document.getElementById("usuarioAutenticado").value;
      let codigoPuntoVenta = this.puntoVentaAutenticado;

      let cafc = this.scodigorecepcion === 5 || this.scodigorecepcion === 6 || this.scodigorecepcion === 7
        ? document.getElementById("cafc").value
        : null;



      var factura = [];
      factura.push({
        cabecera: {
          nitEmisor: "5153610012",
          razonSocialEmisor: "365 SOFT",
          municipio: "Cochabamba",
          telefono: "77490451",
          numeroFactura: numeroFactura,
          cuf: cuf,
          cufd: cufd,
          codigoSucursal: 0,
          direccion: direccion,
          codigoPuntoVenta: codigoPuntoVenta,
          fechaEmision: fechaEmision,
          nombreRazonSocial: nombreRazonSocial,
          codigoTipoDocumentoIdentidad: tipoDocumentoIdentidad,
          numeroDocumento: numeroDocumento,
          complemento: complemento,
          codigoCliente: numeroDocumento,
          codigoMetodoPago: 1,
          numeroTarjeta: null,
          montoTotal: montoTotal,
          montoTotalSujetoIva: montoTotal,
          codigoMoneda: 1,
          tipoCambio: 1,
          montoTotalMoneda: montoTotal,
          montoGiftCard: null,
          descuentoAdicional: descuentoAdicional,
          codigoExcepcion: 0,
          cafc: cafc,
          leyenda: leyenda,
          usuario: usuario,
          codigoDocumentoSector: 1
        }
      })
      me.arrayProductos.forEach(function (prod) {
        factura.push({ detalle: prod })
      })

      var datos = { factura };

      axios.post('/venta/paqueteFactura', {
        factura: datos,
        id_cliente: id_cliente,
        fechaEmision: fechaEmision,
        cufd: cufd,
        cafc: cafc,
      })
        .then(function (response) {
          var data = response.data;
          me.listarVenta(1, '', 'id');
          if (data.message === "Factura registrada correctamente") {
            swal(
              'FACTURA REGISTRADA',
              'Correctamente',
              'success'
            )
            me.arrayProductos = [];
          }
        })
        .catch(function (error) {
          swal({
            title: 'ERROR AL REGISTRAR LA FACTURA',
            type: 'warning',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            confirmButtonClass: 'btn btn-success',
            cancelButtonClass: 'btn btn-danger',
            buttonsStyling: false,
            reverseButtons: true
          })
        });
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

  }
}
</script>
<style>
/* Fuentes */
@import url('https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap');

body {
  font-family: 'Roboto', sans-serif;
  background-color: #6d6d6d;
  color: #000000;
}

/* Colores */
.bg-dark {
  background-color: #181818 !important;
}

.text-primary {
  color: #7e57c2 !important;
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
  background-color: #7e57c2;
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
</style>