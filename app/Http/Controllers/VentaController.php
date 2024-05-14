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
        $idRolUsuario = Auth::user()->idrol; // Obtener el rol del usuario autenticado

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
                ->orderBy('ventas.id', 'desc');

            if ($idRolUsuario != 1) { // Si no es administrador
                $ventas = $ventas->where('ventas.idusuario', Auth::user()->id); // Filtrar solo las ventas del usuario autenticado
            }

            $ventas = $ventas->paginate(10);
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
                ->orderBy('ventas.id', 'desc');

            if ($idRolUsuario != 1) { // Si no es administrador
                $ventas = $ventas->where('ventas.idusuario', Auth::user()->id); // Filtrar solo las ventas del usuario autenticado
            }

            $ventas = $ventas->paginate(10);
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
        $idRolUsuario = Auth::user()->idrol; // Obtener el rol del usuario autenticado
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
                ->where(function ($query) use ($idRoles) {
                    if ($idRoles !== null) {
                        $query->where('users.idrol', $idRoles);
                    }
                })
                ->where(function ($query) use ($idAlmacen) {
                    if ($idAlmacen !== null) {
                        $query->where('inventarios.idalmacen', $idAlmacen);
                    }
                });

            // Filtrar por fechas solo si se proporcionan fechas distintas de la actual
            if ($fechaInicio !== now()->toDateString() || $fechaFin !== now()->addDay()->toDateString()) {
                $ventas->whereBetween('ventas.fecha_hora', [$fechaInicio, $fechaFin]);
            }

            if ($idRolUsuario != 1) { // Si no es administrador
                $ventas = $ventas->where('ventas.idusuario', Auth::user()->id); // Filtrar solo las ventas del usuario autenticado
            }

            $ventas = $ventas->orderBy('ventas.id', 'desc')->paginate(3);
        } else {if ($criterio === 'usuario') { // Buscar por usuario
            $ventas = Venta::join('users', 'ventas.idusuario', '=', 'users.id')
                ->select(
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
                ->where('users.usuario', 'like', '%' . $buscar . '%')
                ->orderBy('ventas.id', 'desc');
    
            if ($idRolUsuario != 1) { // Si no es administrador
                $ventas = $ventas->where('ventas.idusuario', Auth::user()->id); // Filtrar solo las ventas del usuario autenticado
            }
    
            $ventas = $ventas->paginate(3);
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
                ->where(function ($query) use ($idRoles) {
                    if ($idRoles !== null) {
                        $query->where('users.idrol', $idRoles);
                    }
                })
                ->where(function ($query) use ($idAlmacen) {
                    if ($idAlmacen !== null) {
                        $query->where('inventarios.idalmacen', $idAlmacen);
                    }
                })
                ->where('personas.' . $criterio, 'like', '%' . $buscar . '%');
    
            // Filtrar por fechas
            if ($fechaInicio !== now()->toDateString() || $fechaFin !== now()->addDay()->toDateString()) {
                $ventas->whereBetween('ventas.fecha_hora', [$fechaInicio, $fechaFin]);
            }
    
            if ($idRolUsuario != 1) { // Si no es administrador
                $ventas = $ventas->where('ventas.idusuario', Auth::user()->id); // Filtrar solo las ventas del usuario autenticado
            }
    
            $ventas = $ventas->orderBy('ventas.id', 'desc')->paginate(3);
        }
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
        if (!$request->ajax()) {
            return redirect('/');
        }
    
        // Obtener el rol del usuario autenticado
        $rolUsuario = Auth::user()->idrol;
    
        // Verificar si el usuario es administrador
        if ($rolUsuario !== 1) {
            return response()->json([
                'error' => 'Sólo los administradores pueden anular ventas.'
            ], 403);
        }
    
        // Buscar la venta a anular
        $venta = Venta::findOrFail($request->id);
    
        // Anular la venta
        $venta->estado = 'Anulado';
        $venta->save();
    
        return response()->json([
            'mensaje' => 'Venta anulada correctamente.'
        ]);
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
                'ventas.cliente',
                'ventas.num_comprobante',
                DB::raw('GROUP_CONCAT(DISTINCT articulos.nombre SEPARATOR ", ") as articulo'),
                DB::raw('SUM(detalle_ventas.cantidad) as cantidad'),
                DB::raw('SUM(detalle_ventas.precio * detalle_ventas.cantidad) as total'),
                DB::raw('MAX(detalle_ventas.precio) as precio')
            )
            ->whereDate('ventas.created_at', $request->input('fecha'))
            ->groupBy('ventas.id', 'ventas.cliente', 'ventas.num_comprobante');
    
        if ($request->has('idCategoria') && $request->input('idCategoria') !== 'all') {
            $query->where('articulos.idcategoria', $request->input('idCategoria'));
        }
    
        $ventas = $query->get();
    
        if ($ventas->isEmpty()) {
            return response()->json(['mensaje' => 'Ninguna Venta Realizada en la Fecha Indicada']);
        }
    
        // Devolver las ventas como JSON
        return response()->json([
            'ventas' => $ventas
        ]);
    }
}