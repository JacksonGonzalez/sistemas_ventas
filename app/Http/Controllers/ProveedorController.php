<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Proveedor;
use App\Persona;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        //validar seguridad por HTTP
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if($buscar == ''){
            $personas = Proveedor::join('personas', 'proveedores.id', '=', 'personas.id')
            ->select('personas.id', 'personas.nombre', 'personas.tipo_documento', 'personas.num_documento', 
            'personas.direccion', 'personas.telefono', 'personas.email', 'proveedores.contacto', 'proveedores.telefono_contacto')
            ->orderBy('personas.id', 'DESC')->paginate(3);
        }else{
            $personas = Proveedor::join('personas', 'proveedores.id', '=', 'personas.id')
            ->select('personas.id', 'personas.nombre', 'personas.tipo_documento', 'personas.num_documento', 
            'personas.direccion', 'personas.telefono', 'personas.email', 'proveedores.contacto', 'proveedores.telefono_contacto')            
            ->where('personas.'.$criterio, 'like', '%'.$buscar.'%')->orderBy('personas.id', 'desc')->paginate(3);
        }

        return [
            'pagination' => [
                'total'         => $personas->total(),
                'current_page'  => $personas->currentPage(),
                'per_page'      => $personas->perPage(),
                'last_page'     => $personas->lastPage(),
                'from'          => $personas->firstItem(),
                'to'            => $personas->lastItem(),
            ],
            'personas' => $personas
        ];
    } 

    public function store(Request $request)
    {
        //validar seguridad por HTTP
        if(!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();
            $persona = new Persona();
            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;

            $persona->save(); 
            
            $proveedor = new Proveedor();
            $proveedor->contacto = $request->contacto;
            $proveedor->telefono_contacto = $request->telefono_contacto;
            $proveedor->id = $persona->id;

            $proveedor->save();

            DB::commit();

        }catch(Exception $e){
            DB::rollback();
        }

    }

    public function update(Request $request)
    {
        //validar seguridad por HTTP
        if(!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            //Busca primero el proveedor a modificar
            $proveedor  = Proveedor::findOrFail($request->id);

            $persona  = Persona::findOrFail($proveedor->id);

            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;

            $persona->save(); 

            $proveedor->contacto = $request->contacto;
            $proveedor->telefono_contacto = $request->telefono_contacto;

            $proveedor->save();

            DB::commit();

        }catch(Exception $e){
            DB::rollback();
        }
    }
}
