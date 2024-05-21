<template>
    <main class="main">
        <div class="container-fluid py-3"></div>
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> REPORTE DEL DIA
            </div>
            <div class="card-body">
                <template v-if="listado == 1">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <b-form-datepicker v-model="fecha" locale="es"
                                        class="form-control"></b-form-datepicker>
                                </div>

                                <select v-model="filtro" class="form-select me-3" @change="onFiltroChange">
                                    <option value="0" disabled>Seleccione</option>
                                    <option value="all">Todas las categorías</option>
                                    <option v-for="categoria in arrayCategoria" :key="categoria.id"
                                        :value="categoria.id">{{ categoria.nombre }}</option>
                                    <option value="usuarios">Usuarios</option>
                                </select>

                                <input v-if="mostrarBusquedaUsuario" v-model="buscarUsuario" @input="buscarUsuarios"
                                    placeholder="Buscar Usuario" class="form-control me-3" />

                                <select v-if="mostrarUsuarios" v-model="idusuario" class="form-select me-3">
                                    <option v-for="usuario in usuariosFiltrados" :key="usuario.id" :value="usuario.id">
                                        {{ usuario.nombre }}</option>
                                </select>
                            </div>
                        </div>

                        <button type="button" @click="generarReporte" class="btn btn-light">
                            <i class="fa fa-search"></i> Generar Reporte
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Opciones</th>
                                    <th>Cliente</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Total</th>
                                    <th>Número Factura</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="venta in arrayVentas" :key="venta.id">
                                    <td>
                                        <button type="button" @click="verVenta(venta.id)"
                                            class="btn btn-success btn-sm"><i class="icon-eye"></i></button>
                                    </td>
                                    <td>{{ venta.cliente ? venta.cliente : 'Sin Nombre' }}</td>
                                    <td>{{ venta.articulo }}</td>
                                    <td>{{ venta.cantidad }}</td>
                                    <td>{{ venta.precio }}</td>
                                    <td>{{ venta.total }}</td>
                                    <td>{{ venta.num_comprobante }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-right">
                            <strong>Total Ganado: </strong>Bs. {{ totalGanado }}
                        </div>
                        <button @click="exportarPDF" class="btn btn-danger">Exportar a PDF</button>
                    </div>
                    <nav>
                        <ul class="pagination justify-content-center mt-3">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a class="page-link" href="#"
                                    @click.prevent="cambiarPagina(pagination.current_page - 1, buscar, criterio)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page"
                                :class="[page == isActived ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page, buscar, criterio)">{{
                                    page }}</a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#"
                                    @click.prevent="cambiarPagina(pagination.current_page + 1, buscar, criterio)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                </template>

                <template v-if="listado == 2">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label>Cliente</label>
                                <p>{{ cliente }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>Impuesto</label>
                            <p>{{ impuesto }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tipo Comprobante</label>
                                <p>{{ tipo_comprobante }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Serie Comprobante</label>
                                <p>{{ serie_comprobante }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Número Comprobante</label>
                                <p>{{ num_comprobante }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Artículo</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Descuento</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody v-if="arrayDetalle.length">
                                <tr v-for="detalle in arrayDetalle" :key="detalle.id">
                                    <td>{{ detalle.articulo }}</td>
                                    <td>{{ detalle.precio }}</td>
                                    <td>{{ detalle.cantidad }}</td>
                                    <td>{{ detalle.descuento }}</td>
                                    <td>{{ (detalle.precio * detalle.cantidad - detalle.descuento).toFixed(2) }}</td>
                                </tr>
                                <tr class="table-active">
                                    <td colspan="4" class="text-end fw-bold">Total:</td>
                                    <td>$ {{ total.toFixed(2) }}</td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="5" class="text-center">No hay artículos agregados</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group row mt-3">
                        <div class="col-md-12">
                            <button type="button" @click="ocultarDetalle()" class="btn btn-secondary">Cerrar</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </main>
</template>

<script>
import axios from 'axios';
import jsPDF from 'jspdf';
import 'jspdf-autotable';

export default {
    data() {
        return {
            filtro: '0',
            idusuario: 'all',
            mostrarBusquedaUsuario: false,
            mostrarUsuarios: false,
            buscarUsuario: '',
            usuariosFiltrados: [],
            arrayVentas: [],
            arrayCategoria: [],
            arrayUsuario: [],
            value: '',
            context: null,
            fecha: new Date().toISOString().split('T')[0],
            cliente: "",
            num_comprobante: "",
            impuesto: "",
            tipo_comprobante: "",
            serie_comprobante: "",
            total: null,
            listado: 1,
            arrayVenta: [],
            arrayCliente: [],
            arrayDetalle: [],
            arrayProductos: [],
            rol_id: 0,
            nombre: '',
            descripcion: '',
            idcategoria: 'all',
            modal: 0,
            tituloModal: '',
            tipoAccion: 0,
            pagination: {
                'total': 0,
                'current_page': 0,
                'per_page': 0,
                'last_page': 0,
                'from': 0,
                'to': 0,
            },
            offset: 3,
            criterio: 'nombre',
            buscar: ''
        }
    },
    computed: {
        isActived() {
            return this.pagination.current_page;
        },

        totalGanado() {
            this.arrayVentas.reverse();
            return this.arrayVentas.reduce(
                (total, venta) => total + venta.precio * venta.cantidad,
                0
            ).toFixed(2);
        },

        pagesNumber() {
            if (!this.pagination.to) {
                return [];
            }

            let from = this.pagination.current_page - this.offset;
            if (from < 1) {
                from = 1;
            }

            let to = from + (this.offset * 2);
            if (to >= this.pagination.last_page) {
                to = this.pagination.last_page;
            }

            const pagesArray = [];
            while (from <= to) {
                pagesArray.push(from);
                from++;
            }
            return pagesArray;
        }
    },
    methods: {
        onContext(ctx) {
            this.context = ctx;
        },
        cambiarPagina(page) {
            this.pagination.current_page = page;
            this.listarRol(page, this.buscar, this.criterio);
        },
        ocultarDetalle() {
            this.listado = 1;
            this.codigo = null;
            this.arrayArticulo.length = 0;
            this.precioseleccionado = null;
            this.medida = null;
            this.nombreCliente = null;
            this.documento = null;
            this.email = null;
        },
        verVenta(id) {
            let me = this;
            me.listado = 2;

            var url = '/venta/obtenerCabecera?id=' + id;
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                var arrayVentaT = respuesta.venta;

                me.cliente = arrayVentaT[0]['nombre'];
                me.tipo_comprobante = arrayVentaT[0]['tipo_comprobante'];
                me.serie_comprobante = arrayVentaT[0]['serie_comprobante'];
                me.num_comprobante = arrayVentaT[0]['num_comprobante'];
                me.impuesto = arrayVentaT[0]['impuesto'];
                me.total = arrayVentaT[0]['total'];
            })
                .catch(function (error) {
                    console.log(error);
                });

            var url = '/venta/obtenerDetalles?id=' + id;
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                me.arrayDetalle = respuesta.detalles;
            })
                .catch(function (error) {
                    console.log(error);
                });
        },
        selectUsuario() {
            let me = this;
            var url = '/usuario/selectUsuario';
            axios.get(url)
                .then(function (response) {
                    var respuesta = response.data;
                    me.arrayUsuario = respuesta.usuarios;
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        buscarUsuarios() {
            this.usuariosFiltrados = this.arrayUsuario.filter(usuario =>
                usuario.nombre.toLowerCase().includes(this.buscarUsuario.toLowerCase())
            );
        },
        generarReporte() {
            let me = this;
            var url = '/ventas-diarias?fecha=' + me.fecha + '&idCategoria=' + me.idcategoria + '&idUsuario=' + me.idusuario;
            axios.get(url)
                .then(function (response) {
                    if ('mensaje' in response.data && response.data.mensaje === 'Ninguna Venta Realizada en la Fecha Indicada') {
                        swal("Ninguna Venta", "No se realizaron ventas en la fecha seleccionada", "info");
                    } else {
                        var respuesta = response.data;
                        me.arrayVentas = respuesta.ventas;
                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        selectCategoria() {
            let me = this;
            var url = '/categoria/selectCategoria';
            axios.get(url).then(function (response) {
                var respuesta = response.data;
                me.arrayCategoria = respuesta.categorias;
            })
                .catch(function (error) {
                    console.log(error);
                });
        },
        exportarPDF() {
            const pdf = new jsPDF();

            pdf.setFont('bold');
            const titulo = 'Reporte de Ventas Diarias';
            const fecha = `Fecha: ${this.fecha}`;
            pdf.setFont('normal');

            const pageSize = pdf.internal.pageSize;
            const pageWidth = pageSize.width;
            const textWidth = pdf.getStringUnitWidth(titulo) * pdf.internal.getFontSize() / pdf.internal.scaleFactor;

            const xPosition = (pageWidth - textWidth) / 2;

            pdf.text(titulo, xPosition, 10);
            pdf.text(fecha, 15, 20);

            const tableYPosition = 30;

            const columns = ['Cliente', 'Producto', 'Cantidad', 'Precio', 'Total', 'Número Factura'];
            const rows = this.arrayVentas.map(venta => [venta.cliente, venta.articulo, venta.cantidad, venta.precio, (venta.precio * venta.cantidad), venta.num_comprobante]);
            pdf.autoTable({ head: [columns], body: rows, startY: tableYPosition });

            pdf.setFont('bold');
            const totalGanado = `Total Ganado: Bs. ${this.totalGanado}`;
            pdf.text(totalGanado, 15, pdf.autoTable.previous.finalY + 10);
            pdf.setFont('normal');

            pdf.save('reporte_ventas_diarias.pdf');
        },
        cambiarPagina(page, buscar, criterio) {
            let me = this;
            me.pagination.current_page = page;
            me.listarRol(page, buscar, criterio);
        },
        onFiltroChange() {
            if (this.filtro === 'usuarios') {
                this.mostrarBusquedaUsuario = true;
                this.mostrarUsuarios = true;
            } else {
                this.mostrarBusquedaUsuario = false;
                this.mostrarUsuarios = false;
            }
        }
    },
    mounted() {
        this.selectCategoria();
        this.selectUsuario();
        this.generarReporte();
    }
}
</script>

<style>
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
</style>
