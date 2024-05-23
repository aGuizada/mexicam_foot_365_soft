<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;
use App\Exports\ProductExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Inventario;
use App\Articulo;
use App\Precio;

class ArticuloController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax())
            return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '') {
            $articulos = Articulo::join('categorias', 'articulos.idcategoria', '=', 'categorias.id')


                ->select(
                    'articulos.id',
                    'articulos.idcategoria',

                   
                    'articulos.nombre',
                    'articulos.nombre_generico',
                    'articulos.costo_compra',
                    //aumente12julio

                    'articulos.unidad_envase',
                    'articulos.precio_list_unid',
                    'articulos.precio_costo_unid',
                    'articulos.precio_costo_paq',

                    'categorias.nombre as nombre_categoria',
                    //aumente 5 julio

                    'articulos.precio_uno',
                    'articulos.precio_dos',
                    'articulos.precio_tres',
                    'articulos.precio_cuatro',

                    'articulos.precio_venta',
                    'articulos.stock',
                    'articulos.descripcion',
                    'articulos.condicion',
                    'articulos.fotografia'
                )
                ->orderBy('articulos.id', 'desc')->paginate(5);
        } else {
            $articulos = Articulo::join('categorias','articulos.idcategoria', '=', 'categorias.id')
                ->select(
                    'articulos.id',
                    'articulos.idcategoria',
                    'articulos.codigo',
                    'articulos.nombre',

                    'articulos.unidad_envase',
                    'articulos.precio_list_unid',
                    'articulos.precio_costo_unid',
                    'articulos.precio_costo_paq',
                    //aumente 5 julio

                    'articulos.precio_uno',
                    'articulos.precio_dos',
                    'articulos.precio_tres',
                    'articulos.precio_cuatro',

                    'articulos.precio_venta',
                    'articulos.stock',
                    'articulos.descripcion',
                    'articulos.condicion',
                    'articulos.fotografia'
                )
                ->where('articulos.' . $criterio, 'like', '%' . $buscar . '%')
                ->orderBy('articulos.id', 'desc')->paginate(5);
        }


        return [
            'pagination' => [
                'total' => $articulos->total(),
                'current_page' => $articulos->currentPage(),
                'per_page' => $articulos->perPage(),
                'last_page' => $articulos->lastPage(),
                'from' => $articulos->firstItem(),
                'to' => $articulos->lastItem(),
            ],
            'articulos' => $articulos
        ];
    }
    public function listarArticulo(Request $request)
    {
        if (!$request->ajax())
            return redirect('/');

        Log::info('Data', [
            'idProveedorController' => $request->idProveedor,
        ]);


        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $idProveedor = $request->idProveedor;

        if ($buscar == '') {
            $articulos = Articulo::join('categorias', 'articulos.idcategoria', '=', 'categorias.id')
                ->join('proveedores', 'articulos.idproveedor', '=', 'proveedores.id')
                ->join('personas', 'proveedores.id', '=', 'personas.id')
                ->select('articulos.id', 'articulos.idcategoria', 'articulos.codigo', 'articulos.nombre', 'categorias.nombre as nombre_categoria', 'articulos.precio_costo_unid', 'articulos.stock', 'personas.nombre as nombre_proveedor', 'articulos.descripcion', 'articulos.condicion', 'articulos.unidad_envase', 'articulos.fotografia')
                ->where('proveedores.id', '=', $idProveedor)
                ->orderBy('articulos.id', 'desc')->paginate(10);
        } else {
            $articulos = Articulo::join('categorias', 'articulos.idcategoria', '=', 'categorias.id')
                ->join('proveedores', 'articulos.idproveedor', '=', 'proveedores.id')
                ->join('personas', 'proveedores.id', '=', 'personas.id')
                ->select('articulos.id', 'articulos.idcategoria', 'articulos.codigo', 'articulos.nombre', 'categorias.nombre as nombre_categoria', 'articulos.precio_costo_unid', 'articulos.stock', 'personas.nombre as nombre_proveedor', 'articulos.descripcion', 'articulos.condicion', 'articulos.unidad_envase', 'articulos.fotografia')
                ->where('articulos.' . $criterio, 'like', '%' . $buscar . '%')
                ->orderBy('articulos.id', 'desc')->paginate(10);
        }


        return ['articulos' => $articulos];
    }
    public function listarArticuloVenta(Request $request)
    {
        if (!$request->ajax())
            return redirect('/');
    
        $criterio = $request->input('criterio');
    
        $articulos = Articulo::join('medidas', 'articulos.idmedida', '=', 'medidas.id')
            ->join('categorias', 'articulos.idcategoria', '=', 'categorias.id')
            ->select('articulos.id', 'articulos.idcategoria', 'articulos.codigo', 'articulos.nombre','articulos.fotografia', 'categorias.nombre as nombre_categoria', 'articulos.precio_venta', 'articulos.stock', 'articulos.descripcion', 'articulos.condicion', 'medidas.descripcion_medida as medida')
            ->where('articulos.stock', '>', '0')
            ->orderBy('articulos.id', 'asc');
    
        if ($criterio != '0') {
            $articulos->where('articulos.idcategoria', '=', $criterio);
        }
    
        $articulos = $articulos->paginate(100);
    
        return [
            'pagination' => [
                'total' => $articulos->total(),
                'current_page' => $articulos->currentPage(),
                'per_page' => $articulos->perPage(),
                'last_page' => $articulos->lastPage(),
                'from' => $articulos->firstItem(),
                'to' => $articulos->lastItem(),
            ],
            'articulos' => $articulos
        ];
    }
    public function listarPdf()
    {
        return Excel::download(new ProductExport, 'articulos.xlsx');
    }
    public function buscarArticulo(Request $request)
    {
        if (!$request->ajax())
            return redirect('/');

        $filtro = $request->filtro;
        $articulos = Articulo::where('codigo', '=', $filtro)
            ->select('id', 'codigo', 'nombre', 'precio_costo_unid', 'fotografia', 'unidad_envase', 'descripcion')->orderBy('nombre', 'asc')->take(1)->get();

        return ['articulos' => $articulos];
    }
   
    public function buscarArticuloVenta(Request $request)
{
    if (!$request->ajax()) {
        return redirect('/');
    }

    $filtro = $request->filtro;
    $idUsuario = $request->idusuario; // Agregamos el parámetro para el ID del usuario

    $ventas = Venta::join('usuarios', 'ventas.id_usuario', '=', 'usuarios.id')
        ->join('articulos', 'ventas.id_articulo', '=', 'articulos.id')
        ->select(
            'ventas.id',
            'ventas.fecha_venta',
            'usuarios.nombre as nombre_usuario',
            'articulos.nombre as nombre_articulo',
            'articulos.precio_venta',
            'ventas.cantidad',
            'ventas.total'
        )
        ->where('usuarios.id', '=', $idusuario)
        ->where('articulos.codigo', '=', $filtro)
        ->orderBy('ventas.fecha_venta', 'desc')
        ->get();

    return ['ventas' => $ventas];
}

    public function store(Request $request)
    {
        if (!$request->ajax())
            return redirect('/');
        $articulo = new Articulo();
        $articulo->idcategoria = $request->idcategoria;

        $articulo->codigo = '1';

        $articulo->nombre = $request->nombre;

        $articulo->nombre_generico = $articulo->nombre; //aumete 12julio

        $articulo->unidad_envase = '1';
        $articulo->precio_venta = $request->precio_venta;

        $articulo->precio_uno = '0.00';
        $articulo->precio_dos = '0.00';
        $articulo->precio_tres = '0.00';
        $articulo->precio_cuatro = '0.00';

        $articulo->costo_compra = '0.00'; //new

        $articulo->stock = '1000';
       
        $articulo->precio_costo_unid = '0.00';
        $articulo->precio_costo_paq = '0.00';
        $articulo->descripcion = $request->descripcion;
        //$articulo->fecha_vencimiento = $request->fecha_vencimiento;
        $articulo->condicion = '1';
        if ($request->hasFile('fotografia')) {
            if ($request->hasFile('fotografia')) {
                $imagen = $request->file("fotografia");
                $nombreimagen = Str::slug($request->nombre) . "." . $imagen->guessExtension();
                $ruta = public_path("img/articulo/");

                // Crear el directorio si no existe
                if (!File::isDirectory($ruta)) {
                    File::makeDirectory($ruta, 0755, true);
                }

                // Copiar la imagen al directorio
                copy($imagen->getRealPath(), $ruta . $nombreimagen);

                $articulo->fotografia = $nombreimagen;
            }
        }
        Log::info('DATOS REGISTRO ARTICULO:', [
            'idcategoria' => $request->idcategoria,
            'codigo' => $request->codigo,
            'nombre' => $request->nombre,
            'nombre_generico' => $request->nombre_generico,
            'precio_venta' => $request->precio_venta,
            'stock' => $request->stock,
            'descripcion' => $request->descripcion,
            'precio_uno' => $request->precio_uno,
            'precio_dos' => $request->precio_dos,
            'precio_tres' => $request->precio_tres,
            'precio_cuatro' => $request->precio_cuatro,
            'fotografia' => $nombreimagen,

        ]);
        $articulo->save();

    }
    public function update(Request $request)
    {
        if (!$request->ajax())
            return redirect('/');

        try {
            DB::beginTransaction();

            $articulo = Articulo::findOrFail($request->id);
            $articulo->idcategoria = $request->idcategoria;

            $articulo->codigo = $request->codigo;
            $articulo->nombre = $request->nombre;

            $articulo->nombre_generico = $request->nombre_generico; //aumente esto 5 julio

            $articulo->precio_venta = $request->precio_venta;

            $articulo->precio_uno = $request->precio_uno;
            $articulo->precio_dos = $request->precio_dos;
            $articulo->precio_tres = $request->precio_tres;
            $articulo->precio_cuatro = $request->precio_cuatro;

            $articulo->stock = $request->stock;
            $articulo->descripcion = $request->descripcion;
            //$articulo->fecha_vencimiento = $request->fecha_vencimiento;
            //$articulo->condicion = '1';

            $nombreimagen = " ";
            if ($request->hasFile('fotografia')) {
                // Eliminar imagen anterior si existe
                if ($articulo->fotografia != '' && Storage::exists('public/img/articulo/' . $articulo->fotografia)) {
                    Storage::delete('public/img/articulo/' . $articulo->fotografia);
                }

                $imagen = $request->file("fotografia");
                $nombreimagen = Str::slug($request->nombre) . "." . $imagen->guessExtension();
                $imagen->storeAs('public/img/articulo', $nombreimagen);

                $ruta = public_path("img/articulo/");

                // Copiar la imagen al directorio
                copy($imagen->getRealPath(), $ruta . $nombreimagen);


                $articulo->fotografia = $nombreimagen;
            }
            Log::info('DATOS ACTUALIZADOS DE ARTICULO:', [
                'idcategoria' => $request->idcategoria,
                'codigo' => $request->codigo,
                'nombre' => $request->nombre,
                'nombre_generico' => $request->nombre_generico,
                //'unidad_envase'=>$request->unidad_envase,
                'precio_venta' => $request->precio_venta,
                'stock' => $request->stock,
                'descripcion' => $request->descripcion,
                'fotografia' => $nombreimagen,
                'idmedida' => $request->idmedida,
                'precio_uno' => $request->precio_uno,
                'precio_dos' => $request->precio_dos,
                'precio_tres' => $request->precio_tres,
                'precio_cuatro' => $request->precio_cuatro,

            ]);

            $articulo->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }

    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax())
            return redirect('/');
        $articulo = Articulo::findOrFail($request->id);
        $articulo->condicion = '0';
        $articulo->save();
    }

    public function activar(Request $request)
    {
        if (!$request->ajax())
            return redirect('/');
        $articulo = Articulo::findOrFail($request->id);
        $articulo->condicion = '1';
        $articulo->save();
    }
    //--------LISTADO PARA PEDIDO PROVEEDOR---------------
    public function listPedProve(Request $request)
    {
        if (!$request->ajax())
            return redirect('/');

        Log::info('Data', [
            'idProveedor' => $request->idProveedor,
            'buscar' => $request->buscar,
            'criterio' => $request->criterio,
        ]);

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $idProveedor = $request->idProveedor;

        $articulos = Articulo::join('categorias', 'articulos.idcategoria', '=', 'categorias.id')
            ->join('proveedores', 'articulos.idproveedor', '=', 'proveedores.id')
            ->join('personas', 'proveedores.id', '=', 'personas.id')
            ->select(
                'articulos.id',
                //'articulos.idcategoria', 
                'articulos.codigo',
                'articulos.nombre as articulo',
                //'categorias.nombre as nombre_categoria', 
                'articulos.precio_costo_unid as precio',
                'articulos.precio_costo_paq',
                //'articulos.stock', 
                'personas.nombre as nombre_proveedor',
                'articulos.fotografia',
                'articulos.descripcion',
                //'articulos.fecha_vencimiento', 
                //'articulos.condicion', 
                'articulos.unidad_envase as unidad_x_paquete'
            )
            ->where('proveedores.id', '=', $idProveedor);
        //->orderBy('articulos.id', 'desc')->paginate(10);
        if (!empty($buscar)) {
            $articulos = $articulos->where(function ($query) use ($criterio, $buscar) {
                $query->where('articulos.' . $criterio, 'like', '%' . $buscar . '%');
            });
        }
        $articulos = $articulos->orderBy('articulos.id', 'desc')->paginate(4);
        return [
            'pagination' => [
                'total' => $articulos->total(),
                'current_page' => $articulos->currentPage(),
                'per_page' => $articulos->perPage(),
                'last_page' => $articulos->lastPage(),
                'from' => $articulos->firstItem(),
                'to' => $articulos->lastItem(),
            ],
            'articulos' => $articulos
        ];
    }
}