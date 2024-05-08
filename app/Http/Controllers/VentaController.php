<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use App\Venta;
use App\Articulo;
use App\Inventario;
use App\DetalleVenta;
use App\User;
use App\CreditoVenta;
use App\CuotasCredito;
use App\Empresa;
use App\Caja;
use App\Factura;
use App\Http\Controllers\CifrasEnLetrasController;
use Illuminate\Support\Facades\Log;
use App\Notifications\NotifyAdmin;
use FPDF;
use chillerlan\QRCode\{QRCode, QROptions};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SebastianBergmann\Environment\Console;
use SimpleXMLElement;
use SoapClient;
use TheSeer\Tokenizer\Exception;
use App\Helpers\CustomHelpers;
use App\Rol;
use Illuminate\Support\Facades\File;
use Phar;
use PharData;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;

use function Ramsey\Uuid\v1;

class VentaController extends Controller
{
    private $fecha_formato;

    public function __construct()
    {
        session_start();
    }

    public function index(Request $request)
    {
        if (!$request->ajax())
            return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $usuario = \Auth::user();

        if ($buscar == '') {
            $ventas = Venta::join('users', 'ventas.idusuario', '=', 'users.id')
                ->select(
                    'ventas.cliente',
                    'ventas.id',
                    'ventas.tipo_comprobante',
                    'ventas.serie_comprobante',
                    'ventas.num_comprobante',
                    'ventas.fecha_hora',
                    'ventas.impuesto',
                    'ventas.total',
                    'ventas.estado',
                    'users.usuario'
                )
                ->orderBy('ventas.id', 'desc')->paginate(3);
        } else {
            $ventas = Venta::join('users', 'ventas.idusuario', '=', 'users.id')
                ->select(
                    'ventas.cliente',
                    'ventas.id',
                    'ventas.tipo_comprobante',
                    'ventas.serie_comprobante',
                    'ventas.num_comprobante',
                    'ventas.fecha_hora',
                    'ventas.impuesto',
                    'ventas.total',
                    'ventas.estado',
                    'users.usuario'
                )
                ->where('ventas.' . $criterio, 'like', '%' . $buscar . '%')
                ->orderBy('ventas.id', 'desc')->paginate(3);
        }

        return [
            'pagination' => [
                'total' => $ventas->total(),
                'current_page' => $ventas->currentPage(),
                'per_page' => $ventas->perPage(),
                'last_page' => $ventas->lastPage(),
                'from' => $ventas->firstItem(),
                'to' => $ventas->lastItem(),
            ],
            'ventas' => $ventas,
            'usuario' => $usuario
        ];
    }
    public function indexBuscar(Request $request)
    {
        if (!$request->ajax()) {
            return redirect('/');
        }

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $usuario = \Auth::user();
        $idRoles = $request->idRoles;
        $idAlmacen = $request->idAlmacen;
        $fechaInicio = $request->fechaInicio;
        $fechaFin = $request->fechaFin;
        $idRoles = ($idRoles == 0) ? null : $idRoles;
        $idAlmacen = ($idAlmacen == 0) ? null : $idAlmacen;

        if ($buscar == '') {
            $ventas = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
                ->join('users', 'ventas.idusuario', '=', 'users.id')
                ->join('detalle_ventas', 'ventas.id', '=', 'detalle_ventas.idventa')
                ->join('articulos', 'detalle_ventas.idarticulo', '=', 'articulos.id') 
                ->join('inventarios', 'articulos.id', '=', 'inventarios.idarticulo')
                ->select(
                    'ventas.id',
                    'ventas.tipo_comprobante',
                    'ventas.serie_comprobante',
                    'ventas.num_comprobante',
                    'ventas.fecha_hora',
                    'ventas.impuesto',
                    'ventas.total',
                    'ventas.estado',
                    'personas.nombre',
                    'users.usuario',
                    'users.idrol',
                    'detalle_ventas.idarticulo'
                )
                ->distinct()
                ->where(function($query) use ($idRoles) {
                    if ($idRoles !== null) {
                        $query->where('users.idrol', $idRoles);
                    }
                })
                ->where(function($query) use ($idAlmacen) {
                    if ($idAlmacen !== null) {
                        $query->where('inventarios.idalmacen', $idAlmacen);
                    }
                });

                // Filtrar por fechas solo si se proporcionan fechas distintas de la actual
                if ($fechaInicio !== now()->toDateString() || $fechaFin !== now()->addDay()->toDateString()) {
                    $ventas->whereBetween('ventas.fecha_hora', [$fechaInicio, $fechaFin]);
                }

            $ventas = $ventas->orderBy('ventas.id', 'desc')->paginate(3);
        } else {
            $ventas = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
                ->join('users', 'ventas.idusuario', '=', 'users.id')
                ->join('detalle_ventas', 'ventas.id', '=', 'detalle_ventas.idventa')
                ->join('articulos', 'detalle_ventas.idarticulo', '=', 'articulos.id') 
                ->join('inventarios', 'articulos.id', '=', 'inventarios.idarticulo')
                ->select(
                    'ventas.id',
                    'ventas.tipo_comprobante',
                    'ventas.serie_comprobante',
                    'ventas.num_comprobante',
                    'ventas.fecha_hora',
                    'ventas.impuesto',
                    'ventas.total',
                    'ventas.estado',
                    'personas.nombre',
                    'users.usuario',
                    'users.idrol',
                    'detalle_ventas.idarticulo'
                )
                ->distinct()
                ->where(function($query) use ($idRoles) {
                    if ($idRoles !== null) {
                        $query->where('users.idrol', $idRoles);
                    }
                })
                ->where(function($query) use ($idAlmacen) {
                    if ($idAlmacen !== null) {
                        $query->where('inventarios.idalmacen', $idAlmacen);
                    }
                })
                ->where('personas.' . $criterio, 'like', '%' . $buscar . '%');

            // Filtrar por fechas
            if ($fechaInicio !== now()->toDateString() || $fechaFin !== now()->addDay()->toDateString()) {
                $ventas->whereBetween('ventas.fecha_hora', [$fechaInicio, $fechaFin]);
            }

            $ventas = $ventas->orderBy('ventas.id', 'desc')->paginate(3);
        }
        return [
            'pagination' => [
                'total' => $ventas->total(),
                'current_page' => $ventas->currentPage(),
                'per_page' => $ventas->perPage(),
                'last_page' => $ventas->lastPage(),
                'from' => $ventas->firstItem(),
                'to' => $ventas->lastItem(),
            ],
            'ventas' => $ventas,
            'usuario' => $usuario
        ];
    }

    public function obtenerUltimoComprobante(Request $request)
    {
        $ultimoComprobante = DB::table('ventas')
            ->select('num_comprobante')
            ->orderBy('num_comprobante', 'desc')
            ->limit(1)
            ->first();

        if ($ultimoComprobante) {
            $lastComprobante = $ultimoComprobante->num_comprobante;
        } else {
            $lastComprobante = 1;
        }

        return response()->json(['last_comprobante' => $lastComprobante]);
    }

    public function obtenerCabecera(Request $request)
    {
        if (!$request->ajax())
            return redirect('/');

        $id = $request->id;
        $venta = Venta::join('users', 'ventas.idusuario', '=', 'users.id')
            ->select(
                'ventas.id',
                'ventas.tipo_comprobante',
                'ventas.serie_comprobante',
                'ventas.num_comprobante',
                'ventas.fecha_hora',
                'ventas.impuesto',
                'ventas.total',
                'ventas.estado',
                'ventas.cliente',
                'users.usuario',
                'ventas.mesa',
                'ventas.observacion'
            )
            ->where('ventas.id', '=', $id)
            ->orderBy('ventas.id', 'desc')->take(1)->get();

        return ['venta' => $venta];
    }
    public function obtenerDetalles(Request $request)
    {
        if (!$request->ajax())
            return redirect('/');

        $id = $request->id;
        $detalles = DetalleVenta::join('articulos', 'detalle_ventas.idarticulo', '=', 'articulos.id')
            ->select(
                'detalle_ventas.cantidad',
                'detalle_ventas.precio',
                'detalle_ventas.descuento',
                'articulos.nombre as articulo',
                'articulos.unidad_envase'
            )
            ->where('detalle_ventas.idventa', '=', $id)
            ->orderBy('detalle_ventas.id', 'desc')->get();

        return ['detalles' => $detalles];
    }
    public function pdf(Request $request, $id)
    {
        $venta = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
            ->join('users', 'ventas.idusuario', '=', 'users.id')
            ->select(
                'ventas.id',
                'ventas.tipo_comprobante',
                'ventas.serie_comprobante',
                'ventas.num_comprobante',
                'ventas.created_at',
                'ventas.impuesto',
                'ventas.total',
                'ventas.estado',
                'personas.nombre',
                'personas.tipo_documento',
                'personas.num_documento',
                'personas.direccion',
                'personas.email',
                'personas.telefono',
                'users.usuario'
            )
            ->where('ventas.id', '=', $id)
            ->orderBy('ventas.id', 'desc')->take(1)->get();

        $detalles = DetalleVenta::join('articulos', 'detalle_ventas.idarticulo', '=', 'articulos.id')
            ->select(
                'detalle_ventas.cantidad',
                'detalle_ventas.precio',
                'detalle_ventas.descuento',
                'articulos.nombre as articulo'
            )
            ->where('detalle_ventas.idventa', '=', $id)
            ->orderBy('detalle_ventas.id', 'desc')->get();

        $numventa = Venta::select('num_comprobante')->where('id', $id)->get();

        $pdf = \PDF::loadView('pdf.venta', ['venta' => $venta, 'detalles' => $detalles]);
        return $pdf->setPaper('a4', 'landscape')->download('venta-' . $numventa[0]->num_comprobante . '.pdf');

    }
    public function store(Request $request)
    {
        if (!$request->ajax())
            return redirect('/');

        try {
            DB::beginTransaction();

            $descu = '';
            $valorMaximoDescuentoEmpresa = Empresa::first();
            $valorMaximo = $valorMaximoDescuentoEmpresa->valorMaximoDescuento;
            $detalles = $request->data; //Array de detalles
            $idAlmacen = $request->idAlmacen;

            foreach ($detalles as $ep => $det) {
                $descu = $det['descuento'];
            }

            if ($descu > $valorMaximoDescuentoEmpresa->valorMaximoDescuento) {
                return [
                    'id' => -1,
                    'valorMaximo' => $valorMaximo
                ];
            } else {

                $ultimaCaja = Caja::latest()->first();

                if ($ultimaCaja) {
                    if ($ultimaCaja->estado == '1') {
                        $venta = new Venta();
                     
                        $venta->idusuario = \Auth::user()->id;
                        //$venta->idtipo_pago = $request->idtipo_pago;
                        
                        $venta->tipo_comprobante = $request->tipo_comprobante;
                        $venta->serie_comprobante = $request->serie_comprobante;
                        $venta->num_comprobante = $request->num_comprobante;
                        $venta->fecha_hora = now()->setTimezone('America/La_Paz');
                        $venta->impuesto = $request->impuesto;
                        if($request->has('mesa') && $request->mesa !='') {
                            $venta->mesa = $request->mesa;
                        }
                        if($request->has('cliente') && $request->cliente !='') {
                            $venta->cliente = $request->cliente;
                        }
                        $venta->total = $request->total;
                        
                        $venta->observacion = $request->observacion;
                        $venta->estado = 'Registrado';
                        $venta->idcaja = $ultimaCaja->id;
                        //---------registro credito_Ventas---
                        Log::info('DATOS REGISTRO ARTICULO VENTA:', [
                            'idcliente' => $request->idcliente,
                            'idusuario' => $request->id,
                            'idtipo_pago' => $request->idtipo_pago,
                            'tipo_comprobante' => $request->tipo_comprobante,
                            'serie_comprobante' => $request->serie_comprobante,
                            'num_comprobante' => $request->num_comprobante,
                            'fecha_hora' => $request->fecha_hora,
                            'impuesto' => $request->impuesto,
                            'total' => $request->total,
                            //'estado' => $request->precio_venta,
                            'idcaja' => $request->id,
                        ]);
                        $venta->save();
                        //-----hasta aqui----

                        if($request->idtipo_pago == 2){
                            //----REGIStRADO DE CREDITOS_VENTAAS--
                            $creditoventa = new CreditoVenta();
                            $creditoventa->idventa = $venta->id;
                            $creditoventa->idpersona = $request->idpersona;
                            $creditoventa->numero_cuotas = $request->numero_cuotas;
                            $creditoventa->tiempo_dias_cuota = $request->tiempo_dias_cuota;
                            $creditoventa->estado = $request->estadocrevent;//--OJO CON ESTO REPIDE EN VARIOS
                            Log::info('LLEGA_2 CREDITOS_VENTAS:', [
                                'DATOS' => $creditoventa,
                            ]);
                            $creditoventa->save();
                            //----HASTA AQUI REGIStRADO DE CREDITOS_VENTAS--

                            //------para Ver que daTos llega
                            $detallescuota = $request->cuotaspago;//Array de detalles
                            //Recorro todos los elementos
                            Log::info('LLEGA_3 CUOTAS_CREDITO:', [
                                'DATOS' => $detallescuota,
                            ]);
                             //----REGIStRADO DE CUOTAS_CREDITO--
                            foreach ($detallescuota as $detalle) {
                                $cuotascredito = new CuotasCredito();
                                $cuotascredito->idcredito = $creditoventa->id;
                                $cuotascredito->fecha_pago = $detalle['fechaPago'];
                                $cuotascredito->fecha_cancelado = $detalle['fechaCancelado'];
                                $cuotascredito->precio_cuota = $detalle['precioCuota'];
                                $cuotascredito->total_cancelado = $detalle['totalCancelado'];
                                $cuotascredito->saldo = $detalle['saldo'];
                                $cuotascredito->estado = $detalle['estadocuocre'];
                                $cuotascredito->save();
                            }
                            //---hastaa qui REGIStRADO DE CUOTAS_CREDITO--

                        }

                        $ultimaCaja->ventasContado = ($request->total) + ($ultimaCaja->ventasContado);
                        $ultimaCaja->save();

                        Log::info('venta', [
                            'data' => $ultimaCaja,
                            'idalmacen' => $idAlmacen,
                        ]);

                        foreach ($detalles as $ep => $det) {

                            /*$disminuirStock = Inventario::where('idalmacen', $idAlmacen)
                                                        ->where('idarticulo', $det['idarticulo'])
                                                        ->firstOrFail();
                            $disminuirStock->saldo_stock -= $det['cantidad'];
                            $disminuirStock->save();*/

                            $detalle = new DetalleVenta();
                            $detalle->idventa = $venta->id;
                            $detalle->idarticulo = $det['idarticulo'];
                            $detalle->cantidad = $det['cantidad'];
                            $detalle->precio = $det['precio'];
                            $detalle->descuento = $det['descuento'];
                            $detalle->save();
                        }
                        $fechaActual = date('Y-m-d');
                        $numVentas = DB::table('ventas')->whereDate('created_at', $fechaActual)->count();
                        $numIngresos = DB::table('ingresos')->whereDate('created_at', $fechaActual)->count();

                        $arreglosDatos = [
                            'ventas' => [
                                'numero' => $numVentas,
                                'msj' => 'Ventas'
                            ],
                            'ingresos' => [
                                'numero' => $numIngresos,
                                'msj' => 'Ingresos'
                            ]
                        ];
                        $allUsers = User::all();

                        foreach ($allUsers as $notificar) {
                            User::findOrFail($notificar->id)->notify(new NotifyAdmin($arreglosDatos));
                        }
                        DB::commit();
                        return [
                            'id' => $venta->id
                        ];
                    } else {
                        return [
                            'id' => -1,
                            'caja_validado' => 'Debe tener una caja abierta'
                        ];
                    }
                } else {
                    return [
                        'id' => -1,
                        'caja_validado' => 'Debe crear primero una apertura de caja'
                    ];
                }

            }
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax())
            return redirect('/');
        $venta = Venta::findOrFail($request->id);
        $venta->estado = 'Anulado';
        $venta->save();
    }

    public function verificarComunicacion(){
        require "SiatController.php";
            $siat = new SiatController();
            $res = $siat->verificarComunicacion();
            if($res->RespuestaComunicacion->transaccion==true){
                echo json_encode($res, JSON_UNESCAPED_UNICODE);
            }else{
                $msg="Falló la comunicación";
                echo json_encode($msg, JSON_UNESCAPED_UNICODE);
            }
    }

    public function cuis(){
        $user = Auth::user();
        $puntoVenta = $user->idpuntoventa;
        $sucursal = $user->sucursal;
        $codSucursal = $sucursal->codigoSucursal;


        require "SiatController.php";
        $siat = new SiatController();
        $res = $siat->cuis($puntoVenta, $codSucursal);
        $res->RespuestaCuis->codigo;
        $_SESSION['scuis'] = $res->RespuestaCuis->codigo;
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    public function cufd(){
        $user = Auth::user();
        $puntoVenta = $user->idpuntoventa;
        $sucursal = $user->sucursal;
        $codSucursal = $sucursal->codigoSucursal;

        if(!isset($_SESSION['scufd'])){
            require "SiatController.php";
            $siat = new SiatController();
            $res = $siat->cufd($puntoVenta, $codSucursal);
            if($res->RespuestaCufd->transaccion==true){
                $cufd = $res->RespuestaCufd->codigo;
                $codigoControl = $res->RespuestaCufd->codigoControl;
                $direccion = $res->RespuestaCufd->direccion;
                $fechaVigencia = $res->RespuestaCufd->fechaVigencia;
                $_SESSION['scufd'] = $cufd;
                $_SESSION['scodigoControl'] = $codigoControl;
                $_SESSION['sdireccion'] = $direccion;
                $_SESSION['sfechaVigenciaCufd'] = $fechaVigencia;
            }else{
                $res=false;
            }
        }else{
            $fechaVigencia = substr($_SESSION['sfechaVigenciaCufd'],0,16);
            $fechaVigencia = str_replace("T", " ", $fechaVigencia);
            if($fechaVigencia<date('Y-m-d H:i')){
                require "SiatController.php";
                $siat = new SiatController();
                $res = $siat->cufd($puntoVenta, $codSucursal);
                if($res->RespuestaCufd->transaccion==true){
                    $cufd = $res->RespuestaCufd->codigo;
                    $codigoControl = $res->RespuestaCufd->codigoControl;
                    $direccion = $res->RespuestaCufd->direccion;
                    $fechaVigencia = $res->RespuestaCufd->fechaVigencia;
                    $_SESSION['scufd'] = $cufd;
                    $_SESSION['scodigoControl'] = $codigoControl;
                    $_SESSION['sdireccion'] = $direccion;
                    $_SESSION['sfechaVigenciaCufd'] = $fechaVigencia;
                }else{
                    $res=false;
                }
                }else{
                    $res['transaccion'] = true;
                    $res['codigo'] = $_SESSION['scufd'];
                    $res['fechaVigencia'] = $_SESSION['sfechaVigenciaCufd'];
                    $res['direccion'] = $_SESSION['sdireccion'];
                }
                }
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarActividades(){
        $user = Auth::user();
        $puntoVenta = $user->idpuntoventa;
        $sucursal = $user->sucursal;
        $codSucursal = $sucursal->codigoSucursal;

        require "SiatController.php";
        $siat = new SiatController();
        $res = $siat->sincronizarActividades($puntoVenta, $codSucursal);
        //$mensaje = $res->RespuestaListaActividades;
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaTiposFactura(){
        $user = Auth::user();
        $puntoVenta = $user->idpuntoventa;
        $sucursal = $user->sucursal;
        $codSucursal = $sucursal->codigoSucursal;

        require "SiatController.php";
        $siat = new SiatController();
        $res = $siat->sincronizarParametricaTiposFactura($puntoVenta, $codSucursal);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);   
    }

    public function sincronizarListaProductosServicios(){
        $user = Auth::user();
        $puntoVenta = $user->idpuntoventa;
        $sucursal = $user->sucursal;
        $codSucursal = $sucursal->codigoSucursal;

        require "SiatController.php";
        $siat = new SiatController();
        $res = $siat->sincronizarListaProductosServicios($puntoVenta, $codSucursal);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaMotivoAnulacion(){
        $user = Auth::user();
        $puntoVenta = $user->idpuntoventa;
        $sucursal = $user->sucursal;
        $codSucursal = $sucursal->codigoSucursal;

        require "SiatController.php";
        $siat = new SiatController();
        $res = $siat->sincronizarParametricaMotivoAnulacion($puntoVenta, $codSucursal);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaEventosSignificativos(){
        $user = Auth::user();
        $puntoVenta = $user->idpuntoventa;
        $sucursal = $user->sucursal;
        $codSucursal = $sucursal->codigoSucursal;

        require "SiatController.php";
        $siat = new SiatController();
        $res = $siat->sincronizarParametricaEventosSignificativos($puntoVenta, $codSucursal);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarListaLeyendasFactura(){
        $user = Auth::user();
        $puntoVenta = $user->idpuntoventa;
        $sucursal = $user->sucursal;
        $codSucursal = $sucursal->codigoSucursal;

        require "SiatController.php";
        $siat = new SiatController();
        $res = $siat->sincronizarListaLeyendasFactura($puntoVenta, $codSucursal);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaUnidadMedida(){
        $user = Auth::user();
        $puntoVenta = $user->idpuntoventa;
        $sucursal = $user->sucursal;
        $codSucursal = $sucursal->codigoSucursal;

        require "SiatController.php";
        $siat = new SiatController();
        $res = $siat->sincronizarParametricaUnidadMedida($puntoVenta, $codSucursal);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }

    public function emitirFactura(Request $request){    

        $user = Auth::user();
        $puntoVenta = $user->idpuntoventa;
        $sucursal = $user->sucursal;
        $codSucursal = $sucursal->codigoSucursal;

        $datos = $request->input('factura');
        $id_cliente = $request->input('id_cliente');
            
        $valores = $datos['factura'][0]['cabecera'];
        $nitEmisor = str_pad($valores['nitEmisor'], 13, "0", STR_PAD_LEFT);
            
        $fechaEmision = $valores['fechaEmision'];
        $fecha_formato = str_replace("T", "", $fechaEmision);
        $fecha_formato = str_replace("-", "", $fecha_formato);
        $fecha_formato = str_replace(":", "", $fecha_formato);
        $fecha_formato = str_replace(".", "", $fecha_formato);
        $sucursal = str_pad($codSucursal, 4, "0", STR_PAD_LEFT);
        $modalidad = 2;
        $tipoEmision = 1;
        $tipoFactura = 1;
        $tipoDocSector = str_pad(1, 2, "0", STR_PAD_LEFT);
        $numeroFactura = str_pad($valores['numeroFactura'], 10, "0", STR_PAD_LEFT);
        $puntoVentaCuf = str_pad($puntoVenta, 4, "0", STR_PAD_LEFT);
        $codigoControl = $_SESSION['scodigoControl'];
        $cadena = $nitEmisor . $fecha_formato . $sucursal . $modalidad . $tipoEmision . $tipoFactura . $tipoDocSector . $numeroFactura . $puntoVentaCuf;
        $numDig = 1;
        $limMult = 9;
        $x10 = false;
        $mod11 = CustomHelpers::calculaDigitoMod11($cadena, $numDig, $limMult, $x10);
        $cadena2 = $cadena . $mod11;
        
        $pString = $cadena2;
        $bas16 = CustomHelpers::base16($pString);
        
        $cuf = strtoupper($bas16) . $codigoControl;
            
        $datos['factura'][0]['cabecera']['cuf'] = $cuf;
            
        $temporal = $datos['factura'];
        $xml_temporal = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?><facturaComputarizadaCompraVenta xsi:noNamespaceSchemaLocation=\"facturaComputarizadaCompraVenta.xsd\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"></facturaComputarizadaCompraVenta>");

        $this->formato_xml($temporal, $xml_temporal);
        $xml_temporal->asXML(public_path("docs/facturaxml.xml"));
        $gzdata = gzencode(file_get_contents(public_path("docs/facturaxml.xml")), 9);
        $fp = fopen(public_path("docs/facturaxml.xml.zip"), "w");
        fwrite($fp, $gzdata);
        fclose($fp);
        $archivo = $gzdata;
        $hashArchivo = hash("sha256", file_get_contents(public_path("docs/facturaxml.xml")));
            
        $numeroFactura = $valores['numeroFactura'];
        $codigoMetodoPago = $valores['codigoMetodoPago'];
        $montoTotal = $valores['montoTotal'];
        $montoTotalSujetoIva = $valores['montoTotalSujetoIva'];
        $descuentoAdicional = $valores['descuentoAdicional'];
        $productos = file_get_contents(public_path("docs/facturaxml.xml"));
            
        $data = $this->insertarFactura($request, $id_cliente, $numeroFactura, $cuf, $fechaEmision, $codigoMetodoPago, $montoTotal, $montoTotalSujetoIva, $descuentoAdicional, $productos);

        if ($data) {
            // Registro exitoso
            require "SiatController.php";
            $siat = new SiatController();
            $resFactura = $siat->recepcionFactura($archivo, $fechaEmision, $hashArchivo, $puntoVenta, $codSucursal);
            //var_dump($resFactura);
            if ($resFactura->RespuestaServicioFacturacion->codigoDescripcion === "VALIDADA"){
                $mensaje = $resFactura->RespuestaServicioFacturacion->codigoDescripcion;
            }else if($resFactura->RespuestaServicioFacturacion->codigoDescripcion === "RECHAZADA"){
                $mensaje = $resFactura->RespuestaServicioFacturacion->mensajesList->descripcion;
            }
            echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
            
        } 
    }

    public function paqueteFactura(Request $request)
    {
            $user = Auth::user();
            $puntoVenta = $user->idpuntoventa;
            $sucursal = $user->sucursal;
            $codSucursal = $sucursal->codigoSucursal;

            $datos = $request->input('factura');
            $id_cliente = $request->input('id_cliente');
            $cafc = $request->input('cafc');
            $_SESSION['scafc'] = $cafc;
                
            $valores = $datos['factura'][0]['cabecera'];
            $nitEmisor = str_pad($valores['nitEmisor'], 13, "0", STR_PAD_LEFT);
                
            $fechaEmision = $valores['fechaEmision'];
            $fecha_formato = str_replace("T", "", $fechaEmision);
            $fecha_formato = str_replace("-", "", $fecha_formato);
            $fecha_formato = str_replace(":", "", $fecha_formato);
            $fecha_formato = str_replace(".", "", $fecha_formato);
            $sucursal = str_pad($codSucursal, 4, "0", STR_PAD_LEFT);
            $modalidad = 2;
            $tipoEmision = 2;
            $tipoFactura = 1;
            $tipoDocSector = str_pad(1, 2, "0", STR_PAD_LEFT);
            $numeroFactura = str_pad($valores['numeroFactura'], 10, "0", STR_PAD_LEFT);
            $puntoVentaCuf = str_pad($puntoVenta, 4, "0", STR_PAD_LEFT);
            $codigoControl = $_SESSION['scodigoControl'];
            $cadena = $nitEmisor . $fecha_formato . $sucursal . $modalidad . $tipoEmision . $tipoFactura . $tipoDocSector . $numeroFactura . $puntoVentaCuf;
            $numDig = 1;
            $limMult = 9;
            $x10 = false;
            $mod11 = CustomHelpers::calculaDigitoMod11($cadena, $numDig, $limMult, $x10);
            $cadena2 = $cadena . $mod11;
            
            $pString = $cadena2;
            $bas16 = CustomHelpers::base16($pString);
            
            $cuf = strtoupper($bas16) . $codigoControl;
                
            $datos['factura'][0]['cabecera']['cuf'] = $cuf;
                

            // Crear una carpeta temporal
            $carpetaTemporal = public_path("docs/temporal/");
            if (!file_exists($carpetaTemporal)) {
                mkdir($carpetaTemporal, 0777, true);
                chmod($carpetaTemporal, 0777);
            }

            // Guardar el archivo XML en la carpeta temporal
            $temporal = $datos['factura'];
            $xml_temporal = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?><facturaComputarizadaCompraVenta xsi:noNamespaceSchemaLocation=\"facturaComputarizadaCompraVenta.xsd\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"></facturaComputarizadaCompraVenta>");
            $this->formato_xml($temporal, $xml_temporal);
            $nombreArchivo = "facturaxml" . $fecha_formato . ".xml";
            $xml_temporal->asXML(public_path("docs/temporal/" . $nombreArchivo));

            $numeroFactura = $valores['numeroFactura'];
            $codigoMetodoPago = $valores['codigoMetodoPago'];
            $montoTotal = $valores['montoTotal'];
            $montoTotalSujetoIva = $valores['montoTotalSujetoIva'];
            $descuentoAdicional = $valores['descuentoAdicional'];
            $productos = file_get_contents(public_path("docs/temporal/" . $nombreArchivo));

            $data = $this->insertarFactura($request, $id_cliente, $numeroFactura, $cuf, $fechaEmision, $codigoMetodoPago, $montoTotal, $montoTotalSujetoIva, $descuentoAdicional, $productos);
            if ($data === true) {
                // Si la inserción fue exitosa, devolver una respuesta JSON
                return response()->json(['message' => 'Factura registrada correctamente']);
            } else {
                // Si la inserción no fue exitosa, devolver una respuesta JSON con un mensaje de error
                return response()->json(['message' => 'Error al registrar la factura'], 500); // 500 indica un error interno del servidor
            }
    }


    public function enviarPaquete(Request $request)
    {
        $user = Auth::user();
        $puntoVenta = $user->idpuntoventa;
        $sucursal = $user->sucursal;
        $codSucursal = $sucursal->codigoSucursal;
        // Ruta al directorio que deseas comprimir en el archivo TAR
        $carpetaFuente = public_path("docs/temporal");
        // Nombre del archivo TAR resultante
        $nombreArchivoTAR = 'docs/temporal.tar';

        try {
            // Obtener la lista de archivos en el directorio
            $archivosEnDirectorio = scandir($carpetaFuente);
            
            $archivos = array_diff($archivosEnDirectorio, array('.', '..'));

            // Obtener el número de archivos en la carpeta
            $numeroFacturas = count($archivos);

            // Verificar si la cantidad de archivos excede 500
            if ($numeroFacturas > 500) {
                // Si supera el límite, muestra un mensaje de error
                echo 'La cantidad de archivos excede el límite de 500.';
                return;
            }

            // Crear un objeto PharData para el archivo TAR
            $tar = new PharData($nombreArchivoTAR);

            // Agregar el contenido del directorio al archivo TAR
            $tar->buildFromDirectory($carpetaFuente);
            
            // Comprimir el archivo TAR utilizando Gzip
            $gzdata = gzencode(file_get_contents(public_path($nombreArchivoTAR)), 9);
            $fp = fopen(public_path("docs/temporal.tar.zip"), "w");
            fwrite($fp, $gzdata);
            fclose($fp);
            $archivo = $gzdata;
            $hashArchivo = hash("sha256", file_get_contents(public_path("docs/temporal.tar.zip")));
            $nombreArchivoZIP = public_path("docs/temporal.tar.zip");

            require "SiatController.php";
            $siat = new SiatController();
            $res = $siat->recepcionPaqueteFactura($archivo, $request->fechaEmision, $hashArchivo, $numeroFacturas, $puntoVenta, $codSucursal);
             // Verificar el valor de transacción y asignar el mensaje correspondiente
            if ($res->RespuestaServicioFacturacion->codigoDescripcion === "PENDIENTE") {
                $mensaje = $res->RespuestaServicioFacturacion->codigoDescripcion;
                $_SESSION['scodigorecepcion'] = $res->RespuestaServicioFacturacion->codigoRecepcion;

                // Eliminar el archivo TAR si existe
                if (file_exists($nombreArchivoTAR)) {
                unlink($nombreArchivoTAR);
                }
                // Eliminar el archivo ZIP si existe
                if (file_exists($nombreArchivoZIP)) {
                    unlink($nombreArchivoZIP);
                }
                // Eliminar la carpeta temporal si existe y está vacía
                if (is_dir($carpetaFuente)) {
                    $this->eliminarDirectorio($carpetaFuente);
                } 

            } else if($res->RespuestaServicioFacturacion->codigoDescripcion === "RECHAZADA"){
                $mensajes = $res->RespuestaServicioFacturacion->mensajesList;

                if (is_array($mensajes)) {
                    $descripciones = array_map(function($mensaje) {
                        return $mensaje->descripcion;
                    }, $mensajes);
                    $mensaje = $descripciones;
                }
            }
            echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
            //var_dump($res);
 
        } catch (Exception $e) {
            echo "Error al crear el archivo TAR comprimido o al enviarlo al servicio: " . $e->getMessage();
        }
    }

    public function validacionRecepcionPaqueteFactura(){
        $user = Auth::user();
        $puntoVenta = $user->idpuntoventa;
        $sucursal = $user->sucursal;
        $codSucursal = $sucursal->codigoSucursal;

        require "SiatController.php";
        $siat = new SiatController();
        $res = $siat->validacionRecepcionPaqueteFactura($puntoVenta, $codSucursal);
         if ($res->RespuestaServicioFacturacion->codigoDescripcion === "VALIDADA") {
            $mensaje = $res->RespuestaServicioFacturacion->codigoDescripcion;
        } else if($res->RespuestaServicioFacturacion->codigoDescripcion === "OBSERVADA"){
            $mensajes = $res->RespuestaServicioFacturacion->mensajesList;

            if (is_array($mensajes)) {
                $descripciones = array_map(function($mensaje) {
                    return $mensaje->descripcion;
                }, $mensajes);
                $mensaje = $descripciones;
            }
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        //var_dump($res);
    }


    public function eliminarDirectorio($directorio) {
        if (!is_dir($directorio)) {
            return;
        }
    
        $archivos = glob($directorio . '/*');
        foreach ($archivos as $archivo) {
            is_dir($archivo) ? $this->eliminarDirectorio($archivo) : unlink($archivo);
        }
    
        rmdir($directorio);
    }

    public function insertarFactura(Request $request, $id_cliente, $numeroFactura, $cuf, $fechaEmision, $codigoMetodoPago, $montoTotal, $montoTotalSujetoIva, $descuentoAdicional, $productos){
        if (!$request->ajax()) {
            return response()->json(['error' => 'Acceso no autorizado'], 401);
        }

        $factura = new Factura();
        $factura->idcliente = $id_cliente;
        $factura->numeroFactura = $numeroFactura;
        $factura->cuf = $cuf;
        $factura->fechaEmision = $fechaEmision;
        $factura->codigoMetodoPago = $codigoMetodoPago;
        $factura->montoTotal = $montoTotal;
        $factura->montoTotalSujetoIva = $montoTotalSujetoIva;
        $factura->descuentoAdicional = $descuentoAdicional;
        $factura->productos = $productos;
        $factura->estado = 1;
        
        $success = $factura->save();
    
        return $success;
    }

    public function formato_xml($temporal, $xml_temporal){
        $ns_xsi="http://www.w3.org/2001/XMLSchema-instance";
            foreach($temporal as $key => $value){
                if(is_array($value)){
                    if(!is_numeric($key)){
                        $subnodo = $xml_temporal->addChild("$key");
                        $this->formato_xml($value, $subnodo);
                    }else{
                        $this->formato_xml($value, $xml_temporal);
                    }
                }else{
                    if($value == null && $value <> '0'){
                        $hijo = $xml_temporal->addChild("$key", "$value");
                        $hijo->addAttribute('xsi:nil', 'true', $ns_xsi);
                    }else{
                        $xml_temporal->addChild("$key", "$value");
                    }
                }
            }
    }

    public function anulacionFactura($cuf, $motivoSeleccionado){
        $user = Auth::user();
        $puntoVenta = $user->idpuntoventa;
        $sucursal = $user->sucursal;
        $codSucursal = $sucursal->codigoSucursal;

        require "SiatController.php";
        $siat = new SiatController();
        $res = $siat->anulacionFactura($cuf, $motivoSeleccionado, $puntoVenta, $codSucursal);
        if($res->RespuestaServicioFacturacion->transaccion === true){
            $mensaje = $res->RespuestaServicioFacturacion->codigoDescripcion;
        }else{
            $mensaje = $res->RespuestaServicioFacturacion->mensajesList->descripcion;
        }
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        //var_dump($res);
    }

    public function registroEventoSignificativo(Request $request){
        $user = Auth::user();
        $puntoVenta = $user->idpuntoventa;
        $sucursal = $user->sucursal;
        $codSucursal = $sucursal->codigoSucursal;

        $descripcion = $request->descripcion;
        $cufdEvento = $request->cufdEvento;
        $codigoMotivoEvento = $request->codigoMotivoEvento;
        $inicioEvento = $request->inicioEvento;
        $finEvento = $request->finEvento;

        require "SiatController.php";
        $siat = new SiatController();
        $res = $siat->registroEventoSignificativo($descripcion, $cufdEvento, $codigoMotivoEvento, $inicioEvento, $finEvento, $puntoVenta, $codSucursal);
         // Verificar el valor de transacción y asignar el mensaje correspondiente
        if ($res->RespuestaListaEventos->transaccion === true) {
            $mensaje = $res->RespuestaListaEventos->codigoRecepcionEventoSignificativo;
            $_SESSION['scodigoevento'] = $res->RespuestaListaEventos->codigoRecepcionEventoSignificativo;
        } else {
            $mensaje = $res->RespuestaListaEventos->mensajesList->descripcion;
        }

        // Imprimir o retornar el mensaje, o realizar otras acciones según tu necesidad
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        //var_dump($res);
    }

    public function registroPuntoVenta(Request $request){
        $user = Auth::user();
        $puntoVenta = $user->idpuntoventa;
        $sucursal = $user->sucursal;
        $codSucursal = $sucursal->codigoSucursal;

        $nombre = $request->nombre;
        $descripcion = $request->descripcion;
        $nit = $request->nit;
        $idtipopuntoventa = $request->idtipopuntoventa;
        $idsucursal = $request->idsucursal;

        require "SiatController.php";
        $siat = new SiatController();
        $res = $siat->registroPuntoVenta($nombre, $descripcion, $nit, $idtipopuntoventa, $idsucursal, $puntoVenta, $codSucursal);
         // Verificar el valor de transacción y asignar el mensaje correspondiente
        if ($res->RespuestaRegistroPuntoVenta->transaccion === true) {
            $mensaje = $res->RespuestaRegistroPuntoVenta->codigoPuntoVenta;
        } else {
            $mensaje = $res->RespuestaRegistroPuntoVenta->mensajesList->descripcion;
        }

        // Imprimir o retornar el mensaje, o realizar otras acciones según tu necesidad
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        //var_dump($res);
    }

    public function cierrePuntoVenta(Request $request){
        $user = Auth::user();
        $sucursal = $user->sucursal;
        $codSucursal = $sucursal->codigoSucursal;
        $codigoPuntoVenta = $request->codigoPuntoVenta;
        $codigoSucursal = $request->codigoSucursal;
        $nit = $request->nit;

        require "SiatController.php";
        $siat = new SiatController();
        $res = $siat->cierrePuntoVenta($codigoPuntoVenta, $nit, $codSucursal);
         // Verificar el valor de transacción y asignar el mensaje correspondiente
        if ($res->RespuestaCierrePuntoVenta->transaccion === true) {
            $mensaje = $res->RespuestaCierrePuntoVenta->codigoPuntoVenta;
        } else {
            $mensaje = $res->RespuestaCierrePuntoVenta->mensajesList->descripcion;
        }

        // Imprimir o retornar el mensaje, o realizar otras acciones según tu necesidad
        echo json_encode($mensaje, JSON_UNESCAPED_UNICODE);
        //var_dump($res);
    }

    public function imprimirTicket($id)
    {
        $venta = Venta::join('users', 'ventas.idusuario', '=', 'users.id')
            ->select(
                'ventas.id',
                'ventas.tipo_comprobante',
                'ventas.serie_comprobante',
                'ventas.num_comprobante',
                'ventas.created_at',
                'ventas.impuesto',
                'ventas.total',
                'ventas.estado',
                'users.usuario',
                'ventas.mesa',
                'ventas.observacion'
            )
            ->where('ventas.id', '=', $id)
            ->orderBy('ventas.id', 'desc')
            ->take(1)
            ->first();
       
        $comprobante = $venta->num_comprobante;

        $detalles = DetalleVenta::join('articulos', 'detalle_ventas.idarticulo', '=', 'articulos.id')
            ->select(
                'detalle_ventas.cantidad',
                'detalle_ventas.precio',
                'detalle_ventas.descuento',
                'articulos.nombre as articulo',
                'articulos.unidad_envase'
            )
            ->where('detalle_ventas.idventa', '=', $id)
            ->orderBy('detalle_ventas.id', 'desc')
            ->get();
        
        $pdf = new FPDF();
        $usuario = User::join('personas','personas.id','=','users.id')
        ->select('personas.nombre')
        ->where('users.id', '=', $venta->usuario)
        ->take(1)
        ->first();
        $comprobante = $venta->num_comprobante;
        $montoTotal = $venta->total;
        $cliente = $venta->cliente;
        $mesa = $venta->mesa;
        $pdf->AddPage('P', array(70, 150));
        $pdf->SetMargins(0, 0); 
        $pdf->SetAutoPageBreak(false);
        
        $pdf->SetFont('Arial', '', 9);
        if ($cliente == ''){
            $pdf->SetFont('helvetica', 'B', 10); // Establece la fuente en negrita
            $pdf->Cell(7, 3, "Para Mesa", 0, 1, 'C');
            $pdf->SetFont('helvetica', '', 10);
        }
        else{
            $pdf->SetFont('helvetica', 'B', 10); // Establece la fuente en negrita
            $pdf->Cell(7, 3, "Para Llevar", 0, 1, 'C');
            $pdf->SetFont('helvetica', '', 10);
        }
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(70, 9, "TICKET DE COMPRA", 0, 1, 'C');
        if ($cliente == ''){
            $pdf->Cell(0, 10, "Num Mesa: $mesa", 0, 1, 'C');
        }
        else {
            $pdf->Cell(0, 10, "Cliente: $cliente", 0, 1, 'C');
        }
        
        $pdf->Cell(0, 10, "Usuario: $venta->usuario", 0, 1, 'C');

        $anchoCelda = $pdf->GetPageWidth() / 3;
        $tamañoTexto = 10;
        foreach ($detalles as $detalle) {
            $cantidad = $detalle->cantidad;
            $precio = $detalle->precio;
            $descuento = $detalle->descuento;
            $nombreArticulo = $detalle->articulo;
            $unidadEnvase = $detalle->unidad_envase;
            $total = $precio * $cantidad;

            $pdf->SetFontSize($tamañoTexto - 2);
            // Utilizar los datos en la creación del PDF
            $pdf->Cell($anchoCelda, 10, "$cantidad", 0, 0, 'C');
            $pdf->Cell($anchoCelda, 10, "$nombreArticulo", 0, 0, 'C');
            $pdf->Cell($anchoCelda, 10, "Bs. $total", 0, 1, 'C');
        }

        $pdf->Cell(63, 10, "Total: Bs. $montoTotal", 0, 1, 'R');
        $pdf->Cell(55, 10, "***Gracias por su Compra***", 0, 1, 'R');


        

        $pdfPath = public_path('docs/ticket.pdf');
        $printerName = "POS-80";
        $pdf->Output($pdfPath, 'F');
    
        $cutCommand = "\x1B" . "m";
        file_put_contents($pdfPath, $cutCommand, FILE_APPEND);

        // Imprimir el PDF con Adobe Reader
        $escapedPdfPath = escapeshellarg($pdfPath);
        $escapedPrinterName = escapeshellarg($printerName);
        $acrobatPath = '"C:\\Program Files\\Adobe\\Acrobat Reader DC\\Reader\\AcroRd32.exe"';
        $command = "$acrobatPath /t $escapedPdfPath $escapedPrinterName";
        $output = shell_exec($command);

        // Verificar la salida o manejar errores si es necesario
        if ($output === null) {
            return response()->json(['error' => 'Error al imprimir el archivo PDF.']);
        } else {
            return response()->json(['message' => 'Archivo PDF enviado a la impresora.']);
        }

        return response()->download($pdfPath);
    }


    public function imprimirFactura($id){

        $facturas = Factura::join('personas', 'facturas.idcliente', '=', 'personas.id')
        ->select('facturas.*','personas.nombre as razonSocial', 'personas.email as email', 'personas.num_documento as documentoid', 'personas.complemento_id as complementoid')
        ->where('facturas.id', '=', $id)
        ->orderBy('facturas.id', 'desc')->paginate(3);
        
        Log::info('Resultado', [
            //'facturas' => $facturas,
            'idFactura' => $id,
        ]);

        $xml = $facturas[0]->productos;
        $archivoXML = new SimpleXMLElement($xml);
        $nitEmisor = $archivoXML->cabecera[0]->nitEmisor;
        $numeroFactura = str_pad($archivoXML->cabecera[0]->numeroFactura, 5, "0", STR_PAD_LEFT);
        $cuf = $archivoXML->cabecera[0]->cuf;
        $direccion = $archivoXML->cabecera[0]->direccion;
        $telefono = $archivoXML->cabecera[0]->telefono;
        $municipio = $archivoXML->cabecera[0]->municipio;
        $fechaEmision =  $archivoXML->cabecera[0]->fechaEmision;
        $documentoid =  $archivoXML->cabecera[0]->numeroDocumento;
        $razonSocial =  $archivoXML->cabecera[0]->nombreRazonSocial;
        $codigoCliente =  $archivoXML->cabecera[0]->codigoCliente;
        $montoTotal =  $archivoXML->cabecera[0]->montoTotal;
        $descuentoAdicional =  $archivoXML->cabecera[0]->descuentoAdicional;
        $leyenda =  $archivoXML->cabecera[0]->leyenda;
        $complementoid = $archivoXML->cabecera[0]->complemento;

        
        $totalpagar = number_format(floatval($montoTotal),2);
        $totalpagar = str_replace(',','', $totalpagar);
        $totalpagar = str_replace('.',',', $totalpagar);
        $cifrasEnLetras = new CifrasEnLetrasController();
        $letra=($cifrasEnLetras->convertirBolivianosEnLetras($totalpagar));


        $url = 'https://pilotosiat.impuestos.gob.bo/consulta/QR?nit='.$nitEmisor.'&cuf='.$cuf.'&numero='.$numeroFactura.'&t=2';
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'imageBase64' => false,
            'scale' => 10,
        ]);
        $qrCode = new QRCode($options);
        $qrCode->render($url, public_path('qr/qrcode.png'));

        
        $pdf = new FPDF('P','mm','Letter');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(60,4, utf8_decode('CONTAB SRL'),0,0,'C');
        $pdf->Cell(40,4, '',0,0,'C');
        $pdf->Cell(27,4, '',0,0,'C');
        $pdf->Cell(38,4, 'NIT',0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(32,4, $nitEmisor,0,1,'L');

        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(60,4, utf8_decode('CASA MATRIZ'),0,0,'C');
        $pdf->Cell(40,4, '',0,0,'C');
        $pdf->Cell(27,4, '',0,0,'C');
        $pdf->Cell(38,4, utf8_decode('FACTURA N°'),0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(32,4, $numeroFactura,0,1,'L');
        
        $pdf->Cell(60,4, utf8_decode('N° Punto de Venta 0'),0,0,'C');
        $pdf->Cell(40,4, '',0,0,'C');
        $pdf->Cell(27,4, '',0,0,'C');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(38,4, utf8_decode('CÓD. AUTORIZACIÓN'),0,0,'L');
        $pdf->SetFont('Arial','',8);
        $y=$pdf->GetY();
        $pdf->MultiCell(32,4, $cuf,0,'L');
        
        $pdf->SetY($y+4);
        $pdf->MultiCell(60,3, utf8_decode($direccion),0,'C');

        $pdf->Cell(60,4, utf8_decode('Teléfono: '.$telefono),0,1,'C');
        $pdf->Cell(60,4, utf8_decode($municipio),0,1,'C');

        $pdf->Ln(5);
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(0,6, utf8_decode('FACTURA'),0,1,'C');
        
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(0,4, utf8_decode('(Con Derecho a Crédito Fiscal)'),0,1,'C');

        $pdf->Ln(5);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(40,5, utf8_decode('Fecha:'),0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(60,5, $fechaEmision,0,0,'L');
        
        $pdf->Cell(27,5, '',0,0,'C');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(38,5, 'NIT/CI/CEX:    ',0,0,'R');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(32,5, $documentoid."-".$complementoid,0,1,'L');

        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(40,5, utf8_decode('Nombre/Razón Social:'),0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(60,5, utf8_decode($razonSocial),0,0,'L');
        $pdf->Cell(27,5, '',0,0,'C');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(38,5, 'Cod. Cliente:    ',0,0,'R');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(32,5, $documentoid,0,1,'L');

        $pdf->Ln(5);
        $pdf->SetFont('Arial','B',8);
        $y=$pdf->GetY();
        $pdf->MultiCell(25,3.5, utf8_decode('CÓDIGO PRODUCTO / SERVICIO'),1,'C');
        $pdf->SetY($y);
        $pdf->SetX(35);
        $pdf->MultiCell(25,3.5, utf8_decode("\nCANTIDAD\n "),1,'C');
        $pdf->SetY($y);
        $pdf->SetX(60);
        $pdf->MultiCell(20,3.5, utf8_decode("\nUNIDAD DE MEDIDA"),1,'C');
        $pdf->SetY($y);
        $pdf->SetX(80);
        $pdf->MultiCell(50,3.5, utf8_decode("\nDESCRIPCIÓN\n "),1,'C');
        $pdf->SetY($y);
        $pdf->SetX(130);
        $pdf->MultiCell(25,3.5, utf8_decode("\nPRECIO UNITARIO"),1,'C');
        $pdf->SetY($y);
        $pdf->SetX(155);
        $pdf->MultiCell(25,3.5, utf8_decode("\nDESCUENTO\n "),1,'C');
        $pdf->SetY($y);
        $pdf->SetX(180);
        $pdf->MultiCell(27,3.5, utf8_decode("\nSUBTOTAL\n "),1,'C');
        

        $pdf->SetFont('Arial','',8);
        $detalle = $archivoXML->detalle;
        $sumaSubTotales = 0.0;
        foreach ($detalle as $p) {
            $pdf->Cell(25,5, $p->codigoProducto,1,0,'L');
            $pdf->Cell(25,5, $p->cantidad,1,0,'R');
            $pdf->Cell(20,5, $p->unidadMedida,1,0,'L');
            $pdf->Cell(50,5, $p->descripcion,1,0,'L');
            $pdf->Cell(25,5, number_format(floatval($p->precioUnitario),2),1,0,'R');
            $pdf->Cell(25,5, number_format(floatval($p->montoDescuento),2),1,0,'R');
            $pdf->Cell(27,5, number_format(floatval($p->subTotal),2),1,1,'R');

            //Sumar el subTotal actual
            $sumaSubTotales += floatval($p->subTotal);
        }

        $pdf->Cell(120,5, '',0,0,'L');
        $pdf->Cell(50,5, 'SUBTOTAL Bs.',1,0,'R');
        $pdf->Cell(27,5, number_format(floatval($sumaSubTotales),2),1,1,'R');

        $pdf->Cell(120,5, '',0,0,'L');
        $pdf->Cell(50,5, 'DESCUENTO Bs.',1,0,'R');
        $pdf->Cell(27,5, number_format(floatval($descuentoAdicional),2),1,1,'R');

        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(120,5,'Son: '.ucfirst($letra),0,0,'L');
        $pdf->SetFont('Arial','',8);
        $pdf->Cell(50,5, 'TOTAL Bs.',1,0,'R');
        $pdf->Cell(27,5, number_format(floatval(($montoTotal)),2),1,1,'R');

        $pdf->Cell(120,5, '',0,0,'L');
        $pdf->Cell(50,5, 'MONTO GIFT CARD Bs.',1,0,'R');
        $pdf->Cell(27,5, '0.00',1,1,'R');

        $pdf->Cell(120,5, '',0,0,'L');
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(50,5, 'MONTO A PAGAR Bs.',1,0,'R');
        $pdf->Cell(27,5,  number_format(floatval(($montoTotal)),2),1,1,'R');

        $pdf->Cell(120,5, '',0,0,'L');
        $pdf->Cell(50,5, utf8_decode('IMPORTE BASE CRÉDITO FISCAL'),1,0,'R');
        $pdf->Cell(27,5,  number_format(floatval(($montoTotal)),2),1,1,'R');
-
        $pdf->Ln(10);
        $y = $pdf->GetY();
        $pdf->SetFont('Arial','',7);
        $pdf->Cell(170,5, utf8_decode('ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAÍS, EL USO ILÍCITO SERÁ SANCIONADO PENALMENTE DE ACUERDO A LEY'),0,1,'C');
        $pdf->Image(public_path('qr/qrcode.png'), 182, $y-3, 25, 'PNG');
        
        $pdf->Ln(4);
        $pdf->Cell(170,5, utf8_decode($leyenda),0,1,'C');

        $pdf->Ln(2);
        $pdf->Cell(170,5, utf8_decode('"Este documento es la Representación Gráfica de un Documento Fiscal Digital emitido en una modalidad de facturación en línea"'),0,1,'C');

        $pdf->Output(public_path('docs/factura.pdf'), 'F');
        return response()->download(public_path('docs/factura.pdf'));
    }

    public function selectRoles(Request $request)
    {
        if (!$request->ajax())
            return redirect('/');
        $roles = Rol::where('condicion', '=', '1')
            ->select('id', 'nombre')->orderBy('nombre', 'asc')->get();
        return ['roles' => $roles];
    }

    public function reporteVentasDiarias(Request $request)
    {
        // Validar la presencia de la fecha en la solicitud
        $request->validate([
            'fecha' => 'required|date',
        ]);

        // Obtener las ventas para la fecha dada
        $query = DetalleVenta::join('ventas', 'detalle_ventas.idventa', '=', 'ventas.id')
            ->join('articulos', 'detalle_ventas.idarticulo', '=', 'articulos.id')
            ->select(
                'articulos.nombre as articulo',
                'ventas.cliente as cliente',
                'detalle_ventas.cantidad',
                'detalle_ventas.precio',
                'detalle_ventas.descuento',
                'ventas.num_comprobante'
            )
            ->whereDate('ventas.created_at', $request->input('fecha'));

        if ($request->has('idCategoria') && $request->input('idCategoria') !== 'all') {
            $query->where('articulos.idcategoria', $request->input('idCategoria'));
        }

        $ventas = $query->get();

        if ($ventas->isEmpty()) {
            return response()->json(['mensaje' => 'Ninguna Venta Realizada en la Fecha Indicada']);
        }

        //$totalGanado = $ventas->sum('total');

        // Devolver las ventas como JSON
        return response()->json([
            'ventas' => $ventas
            //'totalGanado' => $totalGanado
        ]);
    }

}
