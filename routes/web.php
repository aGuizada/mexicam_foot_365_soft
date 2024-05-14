<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['guest']], function () {
    Route::get('/', 'Auth\LoginController@showLoginForm');
    Route::post('/', 'Auth\LoginController@login')->name('login');
});

Route::group(['middleware' => ['auth']], function () {

    Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
    Route::get('/dashboard', 'DashboardController');
    //Notificaciones
    Route::post('/notification/get', 'NotificationController@get');

    Route::get('/main', function () {
        return view('contenido/contenido');
    })->name('main');


    Route::group(['middleware' => ['Vendedor']], function () {
        Route::get('/cliente', 'ClienteController@index');
        Route::post('/cliente/registrar', 'ClienteController@store');
        Route::put('/cliente/actualizar', 'ClienteController@update');
        Route::get('/cliente/selectCliente', 'ClienteController@selectCliente');

        Route::get('/articulo/buscarArticuloVenta', 'ArticuloController@buscarArticuloVenta');
        Route::get('/articulo/listarArticuloVenta', 'ArticuloController@listarArticuloVenta');

        Route::get('/venta', 'VentaController@index');
        Route::get('/ventaBuscar', 'VentaController@indexBuscar');
        Route::post('/venta/registrar', 'VentaController@store');
        Route::put('/venta/desactivar', 'VentaController@desactivar');
        Route::get('/venta/obtenerCabecera', 'VentaController@obtenerCabecera');
        Route::get('/venta/obtenerDetalles', 'VentaController@obtenerDetalles');
        Route::get('/venta/pdf/{id}', 'VentaController@pdf')->name('venta_pdf');
        Route::get('/venta/imprimir/{id}', 'VentaController@imprimirTicket');
    });

    Route::group(['middleware' => ['Administrador']], function () {

        Route::get('/empresa', 'EmpresaController@index');
        //Route::post('/empresa/registrar', 'EmpresaController@store');
        Route::put('/empresa/actualizar', 'EmpresaController@update');
        // Route::put('/empresa/desactivar', 'EmpresaController@desactivar');
        // Route::put('/empresa/activar', 'EmpresaController@activar');
        Route::get('/empresa/selectEmpresa', 'EmpresaController@selectEmpresa');

        //Rutas de configuracion de trabajo
        Route::get('/configuracion/saldos-negativos', 'ConfiguracionTrabajoController@obtenerSaldosNegativos');
        Route::get('/configuracion/iva', 'ConfiguracionTrabajoController@obtenerIva');
        Route::get('/configuracion/editar', 'ConfiguracionTrabajoController@edit');
        Route::put('/configuracion/actualizar', 'ConfiguracionTrabajoController@update');
        Route::get('/backup', 'BackupDbController@createBackup');
        Route::get('/categoria', 'CategoriaController@index');
        Route::post('/categoria/registrar', 'CategoriaController@store');
        Route::put('/categoria/actualizar', 'CategoriaController@update');
        Route::put('/categoria/desactivar', 'CategoriaController@desactivar');
        Route::put('/categoria/activar', 'CategoriaController@activar');
        Route::get('/categoria/selectCategoria', 'CategoriaController@selectCategoria');


        Route::get('/articulo', 'ArticuloController@index');
        Route::post('/articulo/registrar', 'ArticuloController@store');
        Route::post('/articulo/actualizar', 'ArticuloController@update');
        Route::put('/articulo/desactivar', 'ArticuloController@desactivar');
        Route::put('/articulo/activar', 'ArticuloController@activar');
        Route::get('/articulo/buscarArticulo', 'ArticuloController@buscarArticulo');
        Route::get('/articulo/listarArticulo', 'ArticuloController@listarArticulo');
        Route::get('/articulo/listarArticuloVenta', 'ArticuloController@listarArticuloVenta');
        Route::get('/articulo/buscarArticuloVenta', 'ArticuloController@buscarArticuloVenta');
        Route::get('/articulo/listarPdf', 'ArticuloController@listarPdf')->name('articulos_pdf');
        Route::get('/articulo/listarArticuloPedido', 'ArticuloController@listPedProve'); //aumente esto 21 sept


       
        Route::get('/cliente', 'ClienteController@index');
        Route::post('/cliente/registrar', 'ClienteController@store');
        Route::put('/cliente/actualizar', 'ClienteController@update');
        Route::get('/cliente/selectCliente', 'ClienteController@selectCliente');
        Route::get('/cliente/listarReporteClienteExcel', 'ClienteController@listarReporteClienteExcel');

        Route::get('/venta', 'VentaController@index');
        Route::post('/venta/registrar', 'VentaController@store');
        Route::put('/venta/desactivar', 'VentaController@desactivar');
        Route::get('/venta/obtenerCabecera', 'VentaController@obtenerCabecera');
        Route::get('/venta/obtenerDetalles', 'VentaController@obtenerDetalles');
        Route::get('/venta/pdf/{id}', 'VentaController@pdf')->name('venta_pdf');

        //new
        Route::post('/venta/verificarComunicacion', 'VentaController@verificarComunicacion');
        Route::post('/venta/cuis', 'VentaController@cuis');
        Route::post('/venta/cufd', 'VentaController@cufd');
        Route::post('/venta/emitirFactura', 'VentaController@emitirFactura');
        Route::post('/venta/insertarFactura', 'VentaController@insertarFactura');
        Route::post('/venta/paqueteFactura', 'VentaController@paqueteFactura');
        Route::post('/venta/enviarPaquete', 'VentaController@enviarPaquete');
        Route::post('/venta/validarPaquete', 'VentaController@validacionRecepcionPaqueteFactura');
      

        Route::get('/rol', 'RolController@index');
        Route::get('/rol/selectRol', 'RolController@selectRol');

        Route::get('/user', 'UserController@index');
        Route::post('/user/registrar', 'UserController@store');
        Route::post('/user/actualizar', 'UserController@update');
        Route::put('/user/desactivar', 'UserController@desactivar');
        Route::put('/user/activar', 'UserController@activar');
        Route::get('/user/listarReporteUsuariosExcel', 'UserController@listarReporteUsuariosExcel');

        //Rura para que el usuario pueda editar su perfil
        Route::get('/user/editarpersona', 'UserController@editarPersona');
        //Route::put('/editarperfil', 'UserController@editarPerfil');

        Route::get('/ingreso', 'IngresoController@index');
        Route::post('/ingreso/registrar', 'IngresoController@store');
        Route::put('/ingreso/desactivar', 'IngresoController@desactivar');
        Route::get('/ingreso/obtenerCabecera', 'IngresoController@obtenerCabecera');
        Route::get('/ingreso/obtenerDetalles', 'IngresoController@obtenerDetalles');

        //------sucursales
        //Lisar sucursal
        Route::get('/sucursal', 'SucursalController@index');
        //---desactivar registro
        Route::post('/sucursal/registrar', 'SucursalController@store');

        Route::put('/sucursal/activar', 'SucursalController@activar');
        Route::put('/sucursal/desactivar', 'SucursalController@desactivar');
        // actualizar 
        Route::put('/sucursal/actualizar', 'SucursalController@update');
        Route::get('/sucursal/selectSucursal', 'SucursalController@selectSucursal');

        //Puntos de Venta
        Route::get('/puntoVenta', 'PuntoVentaController@index');
        Route::get('/puntoVenta/obtenerDatosTipoPuntoVenta', 'PuntoVentaController@obtenerDatosTipoPuntoVenta');
        Route::get('/puntoVenta/obtenerDatosSucursal', 'PuntoVentaController@obtenerDatosSucursal');
        Route::post('/puntoVenta/registrar', 'PuntoVentaController@store');
        Route::post('/puntoVenta/habilitar', 'VentaController@registroPuntoVenta');
        Route::post('/puntoVenta/cerrar', 'VentaController@cierrePuntoVenta');
        Route::put('/puntoVenta/cambioEstado', 'PuntoVentaController@cambioEstado');

       
       
        //APERTURA/CIERRE CAJA
        //Listar
        Route::get('/caja', 'CajaController@index');
        Route::post('/caja/registrar', 'CajaController@store');
        Route::put('/caja/depositar', 'CajaController@depositar');
        Route::put('/caja/retirar', 'CajaController@retirar');
        Route::get('/caja/transacciones', 'CajaController@transacciones');
        Route::post('/caja/arqueoCaja', 'CajaController@arqueoCaja');
        Route::put('/caja/cerrar', 'CajaController@cerrar');

        //TRANSACCIONES CAJA
        Route::get('/transacciones/{id}', 'TransaccionesCajaController@index');

        //ARQUEO CAJA
        Route::post('/arqueoCaja/registrar', 'ArqueoCajaController@store');

        //FACTURAS
        Route::get('/factura', 'SiatController@index');
        Route::get('/factura/getFactura/{id}', 'SiatController@getFactura');
        Route::get('/factura/imprimir/{id}', 'VentaController@imprimirFactura');
        Route::get('/factura/anular/{cuf}/{motivoSeleccionado}', 'VentaController@anulacionFactura');
        Route::get('/factura/sincronizarActividades', 'VentaController@sincronizarActividades');
        Route::get('/factura/sincronizarParametricaTiposFactura', 'VentaController@sincronizarParametricaTiposFactura');
        Route::get('/factura/sincronizarListaLeyendasFactura', 'VentaController@sincronizarListaLeyendasFactura');
        Route::get('/factura/sincronizarListaProductosServicios', 'VentaController@sincronizarListaProductosServicios');
        Route::get('/factura/sincronizarParametricaMotivoAnulacion', 'VentaController@sincronizarParametricaMotivoAnulacion');
        Route::get('/factura/sincronizarParametricaEventosSignificativos', 'VentaController@sincronizarParametricaEventosSignificativos');
        Route::get('/factura/sincronizarParametricaUnidadMedida', 'VentaController@sincronizarParametricaUnidadMedida');
        Route::get('/factura/obtenerDatosMotivoAnulacion', 'FacturaController@obtenerDatosMotivoAnulacion');
    
        //Obtener último numero de comprobante
        Route::get('/ruta-a-tu-endpoint-laravel-para-obtener-ultimo-comprobante', 'VentaController@obtenerUltimoComprobante');
        
        //Obtener último numero de codigoSucursal
        Route::get('/ruta-api-para-obtener-ultimo-codigo-sucursal', 'SucursalController@obtenerUltimoCodigoSucursal');

        //Obtener la sesion guardada de Codigo Evento
        Route::get('/obtener-datos-sesion', 'EventosSignificativosController@obtenerDatosSesion');

        Route::get('/backup', 'BackupDbController@createBackup');


        //inventarios
        //Route::get('/inventarios', 'InventarioController@index');
        Route::post('/inventarios/registrar', 'InventarioController@store');

        Route::get('/inventarios/productosporvencer', 'InventarioController@productosPorVencer');
        Route::get('/inventarios/productosvencidos', 'InventarioController@productosVencidos');
        Route::get('/inventarios/productosbajostock', 'InventarioController@productosBajoStock');
        Route::get('/inventarios/listarReportePorVencerExcel', 'InventarioController@listarReportePorVencerExcel');
        Route::get('/inventarios/listarReporteVencidosExcel', 'InventarioController@listarReporteVencidosExcel');
        Route::get('/inventarios/listarReporteBajoStockExcel', 'InventarioController@listarReporteBajoStockExcel');
       //listar en traspaso para seleccionar el arTiculo de invenTario
        Route::get('/inventarios/itemLote/{tipo}', 'InventarioController@indexItemLote'); //listato por filtro
        //saldostosk
        Route::get('/inventarios/saldostock', 'InventarioController@indexsaldostock'); //listar el saldo_stock

        //traspaso
        Route::get('/list/traspasos', 'TraspasoController@index');
        Route::post('/traspaso/registrar', 'TraspasoController@store');
        Route::get('/traspaso/obtenerTraspaso', 'TraspasoController@indexPorID');

        //Eventos Significativos
        Route::get('/eventos', 'EventosSignificativosController@index');
        Route::get('/eventos/obtenerDatosMotivoEvento', 'EventosSignificativosController@obtenerDatosMotivoEvento');
        Route::post('/eventos/registrar', 'EventosSignificativosController@store');
        Route::put('/eventos/finalizarEvento', 'EventosSignificativosController@finalizarEvento');
        Route::put('/eventos/errorEvento', 'EventosSignificativosController@errorEvento');
        Route::put('/eventos/cambioEstadoEvento', 'EventosSignificativosController@cambioEstadoEvento');
        Route::post('/factura/eventosSignificativos', 'VentaController@registroEventoSignificativo');

   
        //-----seleccionar usuario , roles REGISTROS DE VENTAS
        Route::get('/roles/selectRoles', 'VentaController@selectRoles');

        //REPORTES
        Route::get('/ventas-diarias', 'VentaController@reporteVentasDiarias');

        Route::post('/qr/generarqr', 'QrController@generarQr');
        Route::post('/qr/verificarestado', 'QrController@verificarEstado');


    });

    //RUTA PARA RECUPERAR LA SESSION CON EL ID DE LA PERSONA LOGUEADA
    Route::get('/api/session', function () {
        return response()->json([
            'id' => session('id')
        ]);
    });
});

//Route::get('/home', 'HomeController@index')->name('home');
