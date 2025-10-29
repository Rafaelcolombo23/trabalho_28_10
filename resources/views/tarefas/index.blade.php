<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <title>Tarefas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {{-- Bootstrap 5 CSS --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<main class="container py-4">

  {{-- Mensagens --}}
  @if(session('ok'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('ok') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
  @if(session('info'))
    <div class="alert alert-primary alert-dismissible fade show" role="alert">
      {{ session('info') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">
      <strong>Erros:</strong>
      <ul class="mb-0">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <div class="row g-4">
    {{-- Card de criação --}}
    <div class="col-12 col-lg-4">
      <div class="card shadow-sm">
        <div class="card-header fw-semibold">Nova tarefa</div>
        <div class="card-body">
          <form method="POST" action="{{ route('tarefas.store') }}" class="row gy-3">
            @csrf

            <div class="col-12">
              <label for="categoria_id" class="form-label">Categoria</label>
              <select id="categoria_id" name="categoria_id" class="form-select" required>
                <option value="">Selecione...</option>
                @foreach($categorias as $cat)
                  <option value="{{ $cat->id }}" @selected(old('categoria_id')==$cat->id)>{{ $cat->nome }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12">
              <label for="nome" class="form-label">Nome</label>
              <input type="text" id="nome" name="nome" class="form-control" value="{{ old('nome') }}" placeholder="Ex.: Enviar relatório" required>
            </div>

            <div class="col-12">
              <label for="descricao" class="form-label">Descrição (opcional)</label>
              <textarea id="descricao" name="descricao" class="form-control" rows="3" placeholder="Detalhes...">{{ old('descricao') }}</textarea>
            </div>

            <div class="col-12">
              <label for="data_fim" class="form-label">Prazo (opcional)</label>
              <input type="datetime-local" id="data_fim" name="data_fim" class="form-control" value="{{ old('data_fim') }}">
            </div>

            <div class="col-12 d-grid">
              <button class="btn btn-dark" type="submit">Criar tarefa</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- Card de listagem --}}
    <div class="col-12 col-lg-8">
      <div class="card shadow-sm">
        <div class="card-header fw-semibold d-flex align-items-center justify-content-between">
          <span>Tarefas</span>
        </div>
        <div class="card-body p-0">
          @if($tarefas->count() === 0)
            <div class="p-4 text-muted">Nenhuma tarefa por aqui ainda.</div>
          @else
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Tarefa</th>
                    <th class="text-nowrap">Categoria</th>
                    <th class="text-nowrap">Prazo</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                  </tr>
                </thead>
                <tbody>
                @foreach($tarefas as $t)
                  @php
                    $atrasada = !$t->concluido && $t->data_fim && now()->gt($t->data_fim);
                  @endphp
                  <tr @class(['table-warning'=>$atrasada])>
                    <td>
                      <div class="fw-semibold">{{ $t->nome }}</div>
                      @if($t->descricao)
                        <div class="text-muted small">{{ $t->descricao }}</div>
                      @endif
                      <div class="text-muted small">Criada em {{ $t->created_at->format('d/m/Y H:i') }}</div>
                    </td>
                    <td class="text-nowrap">{{ optional($t->categoria)->nome ?? '—' }}</td>
                    <td class="text-nowrap">
                      @if($t->data_fim)
                        {{ $t->data_fim->format('d/m/Y H:i') }}
                        @if($atrasada)
                          <span class="badge text-bg-warning ms-1">Atrasada</span>
                        @endif
                      @else
                        <span class="text-muted">Sem prazo</span>
                      @endif
                    </td>
                    <td>
                      @if($t->concluido)
                        <span class="badge text-bg-success">Concluída</span>
                      @else
                        <span class="badge text-bg-secondary">Aberta</span>
                      @endif
                    </td>
                    <td>
                      <div class="d-flex gap-2 justify-content-end">
                        {{-- Toggle concluir/reabrir --}}
                        <form method="POST" action="{{ route('tarefas.toggle-complete', $t) }}">
                          @csrf @method('PATCH')
                          <button class="btn btn-outline-success btn-sm" type="submit">
                            {{ $t->concluido ? 'Reabrir' : 'Concluir' }}
                          </button>
                        </form>

                        {{-- Editar (abre modal) --}}
                        <button
                          class="btn btn-outline-primary btn-sm"
                          data-bs-toggle="modal"
                          data-bs-target="#modal-editar"
                          data-id="{{ $t->id }}"
                          data-categoria_id="{{ $t->categoria_id }}"
                          data-nome="{{ $t->nome }}"
                          data-descricao="{{ $t->descricao }}"
                          data-data_fim="{{ optional($t->data_fim)->format('Y-m-d\TH:i') }}"
                          data-concluido="{{ $t->concluido ? 1 : 0 }}"
                        >
                          Editar
                        </button>

                        {{-- Excluir --}}
                        <form method="POST" action="{{ route('tarefas.destroy', $t) }}" onsubmit="return confirm('Excluir esta tarefa?')">
                          @csrf @method('DELETE')
                          <button class="btn btn-outline-danger btn-sm" type="submit">Excluir</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @endforeach
                </tbody>
              </table>
            </div>

            <div class="p-3">
              {{-- Paginação (Bootstrap 5) --}}
              {{ $tarefas->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- Modal único de edição --}}
  <div class="modal fade" id="modal-editar" tabindex="-1" aria-labelledby="modal-editar-label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form method="POST" id="form-edit" action="#">
          @csrf
          @method('PUT')

          <div class="modal-header">
            <h5 class="modal-title" id="modal-editar-label">Editar tarefa</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
          </div>

          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Categoria</label>
              <select name="categoria_id" id="edit-categoria" class="form-select" required>
                @foreach($categorias as $cat)
                  <option value="{{ $cat->id }}">{{ $cat->nome }}</option>
                @endforeach
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Nome</label>
              <input type="text" name="nome" id="edit-nome" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Descrição</label>
              <textarea name="descricao" id="edit-descricao" class="form-control" rows="3"></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">Prazo</label>
              <input type="datetime-local" name="data_fim" id="edit-data-fim" class="form-control">
            </div>

            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" id="edit-concluido" name="concluido">
              <label class="form-check-label" for="edit-concluido">
                Marcar como concluída
              </label>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Salvar</button>
          </div>

        </form>
      </div>
    </div>
  </div>

</main>

{{-- Bootstrap 5 JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Monta a URL da rota update substituindo o :id
  const updateUrlTemplate = "{{ route('tarefas.update', ':id') }}";

  const modalEditar = document.getElementById('modal-editar');
  if (modalEditar) {
    modalEditar.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      if (!button) return;

      const id          = button.getAttribute('data-id');
      const categoriaId = button.getAttribute('data-categoria_id');
      const nome        = button.getAttribute('data-nome') || '';
      const descricao   = button.getAttribute('data-descricao') || '';
      const dataFim     = button.getAttribute('data-data_fim') || '';
      const concluido   = button.getAttribute('data-concluido') === '1';

      // Preenche o form
      const form = document.getElementById('form-edit');
      form.action = updateUrlTemplate.replace(':id', id);

      document.getElementById('edit-categoria').value = categoriaId || '';
      document.getElementById('edit-nome').value = nome;
      document.getElementById('edit-descricao').value = descricao;
      document.getElementById('edit-data-fim').value = dataFim;
      document.getElementById('edit-concluido').checked = concluido;
    });
  }
</script>
</body>
</html>
