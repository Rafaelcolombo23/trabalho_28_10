<?php

namespace App\Http\Controllers;

use App\Models\Tarefa;
use App\Models\Categoria;
use Illuminate\Http\Request;

class TarefaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tarefas = \App\Models\Tarefa::with('categoria')
            ->orderByRaw('concluido asc')
            ->orderByRaw('CASE WHEN data_fim IS NULL THEN 1 ELSE 0 END')
            ->orderBy('data_fim')
            ->paginate(15)
            ->withQueryString();

        $categorias = \App\Models\Categoria::orderBy('nome')->get();

        return view('tarefas.index', compact('tarefas', 'categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = Categoria::orderBy('nome')->get();
        return response()->json($categorias);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'categoria_id' => ['required', 'exists:categorias,id'],
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
        ]);

        $tarefa = Tarefa::create($data);


        return redirect()->route('tarefas.index')->with('ok', 'Tarefa criada!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tarefa $tarefa)
    {
        $tarefa->load('categoria');
        return response()->json($tarefa);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tarefa $tarefa)
    {
        $categorias = Categoria::orderBy('nome')->get();
        return response()->json(['tarefa' => $tarefa, 'categorias' => $categorias]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tarefa $tarefa)
    {
        $data = $request->validate([
            'categoria_id' => ['required', 'exists:categorias,id'],
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'data_inicio' => ['nullable', 'date'],
            'data_fim' => ['nullable', 'date', 'after_or_equal:data_inicio'],
            'concluido' => ['nullable', 'boolean'],
        ]);

        if (array_key_exists('concluido', $data)) {
            $data['concluido_em'] = $data['concluido'] ? now() : null;
        }

        $tarefa->update($data);


        return redirect()->route('tarefas.index')->with('ok', 'Tarefa atualizada!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tarefa $tarefa)
    {
        $tarefa->delete();
        return redirect()->route('tarefas.index')->with('ok', 'Tarefa excluída!');
    }

    public function toggleComplete(Tarefa $tarefa)
    {
        if ($tarefa->concluido) {
            $tarefa->update(['concluido' => false, 'concluido_em' => null]);
        } else {
            $tarefa->update(['concluido' => true, 'concluido_em' => now()]);
        }

        return redirect()->back()->with('ok', 'Status atualizado!');
    }
}
