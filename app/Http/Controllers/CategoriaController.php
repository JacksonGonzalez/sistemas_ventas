<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Categoria;

class CategoriaController extends Controller
{

    public function index()
    {
        $categorias = Categoria::all();
        return $categorias;
    }

    public function store(Request $request)
    {
        $categoria = new Categoria();
        $categoria->nombre = $request->name;
        $categoria->descripcion = $request->descripcion;
        $categoria->condicion = $request->condicion = '1';
        $categoria->save();
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($request->id);
        $categoria->nombre = $request->name;
        $categoria->descripcion = $request->descripcion;
        $categoria->condicion = $request->condicion = '1';
        $categoria->save();
    }

    public function desactivar(Request $request)
    {
        $categoria = Categoria::findOrFail($request->id);
        $categoria->condicion = $request->condicion = '0';
        $categoria->save();
    }


    public function activar(Request $request)
    {
        $categoria = Categoria::findOrFail($request->id);
        $categoria->condicion = $request->condicion = '1';
        $categoria->save();
    }

}
