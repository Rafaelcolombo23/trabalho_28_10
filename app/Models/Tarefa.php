<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarefa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tarefas';
    protected $fillable = [
        'categoria_id',
        'nome',
        'descricao',
        'data_inicio',
        'data_fim',
        'concluido',
        'concluido_em',
    ];

    protected $casts = [
        'concluido' => 'boolean',
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
        'concluido_em' => 'datetime',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function scopeAbertas($query)
    {
        return $query->where('concluido', false);
    }
    public function scopeDaCategoria($query, $categoriaId)
    {
        return $query->where('categoria_id', $categoriaId);
    }

    public function marcarComoConcluida(): void
    {
        $this->forceFill([
            'concluido' => true,
            'concluido_em' => now(),
        ])->save();
    }

    public function desmarcarConclusao(): void
    {
        $this->forceFill([
            'concluido' => false,
            'concluido_em' => null,
        ])->save();
    }

    public function getAtrasadaAttribute(): bool
    {
        return !$this->concluido && $this->data_fim && now()->greaterThan($this->data_fim);
    }
}
