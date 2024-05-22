<template>
    <main class="main  text-white">
        <div class="container-fluid py-3"></div>
        <!-- Ejemplo de tabla Listado -->
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Ventas

            </div>
            <!-- Listado-->
            <template v-if="listado == 1">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <select class="form-control col-md-3" v-model="criterio">
                                    <option value="" disabled selected>Seleccione</option>
                                    <option value="tipo_comprobante">Tipo Comprobante</option>
                                    <option value="num_comprobante">Número Comprobante</option>
                                    <option value="fecha_hora">Fecha-Hora</option>
                                    <option value="usuario">Usuario</option>
                                </select>
                                <input type="text" v-model="buscar" @keyup="listarVenta(1, buscar, criterio)"
                                    class="form-control" placeholder="Texto a buscar">
                                <!--button type="submit" @click="listarVenta(1, buscar, criterio)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button-->
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table v-if="esAdministrador || arrayVenta.length > 0"
                            class="table table-bordered table-striped table-sm custom-table">
                            <thead>
                                <tr>
                                    <th>Opciones</th>
                                    <th>Usuario</th>
                                    <th>Cliente</th>
                                    <th>Tipo Comprobante</th>
                                    <th>Número Comprobante</th>
                                    <th>Fecha Hora</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tr v-if="!esAdministrador && arrayVenta.length === 0">
                                <td colspan="8" class="text-center">No hay ventas realizadas por usted.</td>
                            </tr>
                            <tbody>
                                <tr v-for="(venta) in arrayVenta" :key="venta.id">
                                    <td>
                                        <button type="button" @click="verVenta(venta.id)"
                                            class="btn btn-success btn-sm">
                                            <i class="icon-eye"></i>
                                        </button> &nbsp;

                                        <template v-if="venta.estado == 'Registrado' && idrol !== 2">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                @click="desactivarVenta(venta.id)">
                                                <i class="icon-trash"></i>
                                            </button>
                                        </template>

                                        <button type="button" @click="imprimirTicket(venta.id)"
                                            class="btn btn-info btn-sm">
                                            Imprimir Ticket
                                        </button>
                                    </td>

                                    <td v-text="venta.usuario"></td>
                                    <td v-text="venta.cliente"></td>
                                    <td v-text="venta.tipo_comprobante"></td>
                                    <td v-text="venta.num_comprobante"></td>
                                    <td v-text="venta.fecha_hora"></td>
                                    <td v-text="venta.total"></td>
                                    <td v-text="venta.estado"></td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-else>No hay ventas disponibles.</div>
                    </div>
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a class="page-link" href="#"
                                    @click.prevent="cambiarPagina(pagination.current_page - 1, buscar, criterio)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page"
                                :class="[page == isActived ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page, buscar, criterio)"
                                    v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#"
                                    @click.prevent="cambiarPagina(pagination.current_page + 1, buscar, criterio)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </template>
            <!--Fin Listado-->
            <!-- Detalle-->


            <!-- Fin Detalle-->
            <!--Ver ingreso-->
            <template v-else-if="listado == 2">
                <div class="card-body">
                    <div class="row border rounded mx-auto my-4 p-3"
                        style="max-width: 800px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
                        <div class="row border rounded mx-auto my-4 p-3 bg-white "
                            style="box-shadow: 0 15px 25px rgba(0, 0, 0, 0.5);">
                            <div class=" col-md-4">
                                <div class="form-group mb-3">
                                    <label
                                        class="text-uppercase text-black-90 font-weight-bold mb-1"><strong>Categoría</strong></label>
                                    <p class="text-danger font-weight-bold mb-0" v-text="categoria"></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label
                                        class="text-uppercase text-black-90 font-weight-bold mb-1"><strong>Cliente</strong></label>
                                    <p class="text-dark mb-0" v-text="cliente"></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="text-uppercase text-black-90 font-weight-bold mb-1"><strong>Tipo
                                            Comprobante</strong></label>
                                    <p class="text-dark mb-0" v-text="tipo_comprobante"></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="text-uppercase text-black-90 font-weight-bold mb-1"><strong>Número
                                            Comprobante</strong></label>
                                    <p class="text-dark mb-0" v-text="num_comprobante"></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="text-uppercase text-black-90 font-weight-bold mb-1"><strong>Número
                                            Mesa</strong></label>
                                    <p class="text-dark mb-0" v-text="mesa"></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label
                                        class="text-uppercase text-black-90 font-weight-bold mb-1"><strong>Observaciones</strong></label>
                                    <p class="text-dark mb-0" v-text="observacion"></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="text-uppercase text-black-90 font-weight-bold mb-1"><strong>Tipo de
                                            Pago</strong></label>
                                    <p class="text-dark mb-0" v-if="tipoPago === 'qr'">Pago por QR</p>
                                    <p class="text-dark mb-0" v-else-if="tipoPago === 'efectivo'">Pago en efectivo</p>
                                    <p class="text-dark mb-0" v-else>Tipo de pago no especificado</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row border rounded mx-auto my-4 p-3" style="max-width: 900px;">
                        <div class="table-responsive col-md-12">
                            <table class="table table-borderless table-hover">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>Artículo</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody v-if="arrayDetalle.length">
                                    <tr v-for="detalle in arrayDetalle" :key="detalle.id">
                                        <td v-text="detalle.articulo"></td>
                                        <td v-text="detalle.precio"></td>
                                        <td v-text="detalle.cantidad"></td>
                                        <td>{{ detalle.precio * detalle.cantidad }}</td>
                                    </tr>

                                    <tr class="bg-success text-white font-weight-bold">
                                        <td colspan="3" class="text-right">Total</td>
                                        <td>$ {{ total }}</td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="4" class="text-center">No hay artículos agregados</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <button type="button" @click="ocultarDetalle()" class="btn btn-secondary">Cerrar</button>
                        </div>
                    </div>
                </div>
            </template>
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

    },
    methods: {



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