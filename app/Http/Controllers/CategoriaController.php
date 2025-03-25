<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;

class CategoriaController extends Controller
{
    // GET /api/categorias
    public function index()
    {
        $categorias = Categoria::all();
        return response()->json($categorias);
    }

    // POST /api/categorias
    public function store(StoreCategoriaRequest $request)
    {
        $categoria = Categoria::create($request->validated());
        return response()->json($categoria, 201);
    }

    // GET /api/categorias/{categoria}
    public function show(Categoria $categoria)
    {
        return response()->json($categoria);
    }

    // PUT /api/categorias/{categoria}
    public function update(UpdateCategoriaRequest $request, Categoria $categoria)
    {
        $categoria->update($request->validated());
        return response()->json($categoria);
    }


    // DELETE /api/categorias/{categoria}
    public function destroy(Categoria $categoria)
    {
        $categoria->delete();
        return response()->json(null, 204); // 204 No Content
    }
}
