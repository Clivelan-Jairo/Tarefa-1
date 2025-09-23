(function(){
  const form = document.getElementById('note-form');
  const list = document.getElementById('notes-list');
  const empty = document.getElementById('empty');
  const title = document.getElementById('title');
  const titleCount = document.getElementById('title-count');
  const sortToggle = document.getElementById('sort-toggle');

  // contador de caracteres (CSR)
  title.addEventListener('input', () => {
    titleCount.textContent = `${title.value.length}/120`;
  });

  // Função para atualizar o contador de notas
  function updateNoteCount() {
    const noteCount = list.children.length;
    const noteCountElement = document.getElementById('note-count');
    if (noteCountElement) {
      noteCountElement.textContent = `Total de notas: ${noteCount}`;
    }
  }

  // Submit via fetch para criar ou atualizar nota (CSR)
  form.addEventListener('submit', async (e) => {
    e.preventDefault(); // Prevenir o comportamento padrão do formulário

    const fd = new FormData(form);
    const editingId = form.dataset.editing;

    if (editingId) {
      // Atualizar nota existente
      const data = {
        id: editingId,
        title: fd.get('title'),
        content: fd.get('content')
      };
      const res = await fetch('api.php', {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      });
      const json = await res.json();
      if (!json.ok) {
        alert(json.error || 'Erro ao atualizar nota');
        return;
      }

      // Atualizar a nota na lista
      const item = list.querySelector(`[data-id='${editingId}']`);
      item.querySelector('h3').textContent = json.data.title;
      item.querySelector('p').textContent = json.data.content;

      // Resetar o formulário
      form.dataset.editing = '';
      form.classList.remove('editing');
      form.reset();
      titleCount.textContent = '0/120';
    } else {
      // Criar nova nota
      const res = await fetch('api.php', {
        method: 'POST',
        body: fd
      });
      const json = await res.json();
      if (!json.ok) {
        alert(json.error || 'Erro ao criar nota');
        return;
      }

      // Se houver busca ativa, limpa o campo e recarrega sem filtro
      if (window.location.search.includes('q=')) {
        const searchInput = document.querySelector('.search-form input[name="q"]');
        if (searchInput) searchInput.value = '';
        window.location.href = window.location.pathname;
        return;
      }

      // Renderizar a nova nota no cliente
      const item = renderNote(json.data);
      list.prepend(item);
      empty.hidden = true;
      form.reset();
      titleCount.textContent = '0/120';

      // Atualizar contador
      updateNoteCount();
    }
  });

  // Delegação para excluir (CSR + API)
  list.addEventListener('click', async (e) => {
    const btn = e.target.closest('.delete-note');
    if (!btn) return;
    const id = btn.dataset.id;
    if (!confirm('Excluir esta nota?')) return;
    const res = await fetch('api.php?id=' + encodeURIComponent(id), {
      method: 'DELETE'
    });
    const json = await res.json();
    if (json.ok) {
      btn.closest('li').remove();
      if (!list.children.length) empty.hidden = false;

      // Atualizar contador
      updateNoteCount();
    } else {
      alert(json.error || 'Erro ao excluir');
    }
  });

  // Delegação para editar (CSR + API)
  list.addEventListener('click', async (e) => {
    const btn = e.target.closest('.edit-note');
    if (!btn) return;
    const id = btn.dataset.id;

    // Obter dados da nota para edição
    const res = await fetch('api.php?id=' + encodeURIComponent(id));
    const json = await res.json();
    if (!json.ok) {
      alert(json.error || 'Erro ao carregar nota para edição');
      return;
    }

    // Preencher o formulário com os dados da nota
    title.value = json.data.title;
    document.getElementById('content').value = json.data.content;
    titleCount.textContent = `${title.value.length}/120`;

    // Alterar comportamento do formulário para "atualizar"
    form.dataset.editing = id;
    form.classList.add('editing'); // Adicionar classe para sinalizar edição

    // Alterar o texto do botão para "Salvar alterações"
    const submitButton = form.querySelector('button[type="submit"]');
    submitButton.textContent = 'Salvar alterações';

    // Adicionar um evento para resetar o formulário ao sair do modo de edição
    form.addEventListener('reset', () => {
      form.classList.remove('editing');
      submitButton.textContent = 'Salvar';
    });
  });

  // Alterna ordenação no cliente (CSR)
  sortToggle.addEventListener('click', () => {
    const pressed = sortToggle.getAttribute('aria-pressed') === 'true';
    sortToggle.setAttribute('aria-pressed', String(!pressed));
    sortToggle.textContent = pressed ? 'Ordenar: mais recentes' : 'Ordenar: mais antigas';
    const items = Array.from(list.children);
    items.sort((a, b) => {
      const aTime = new Date(a.querySelector('time').getAttribute('datetime')).getTime();
      const bTime = new Date(b.querySelector('time').getAttribute('datetime')).getTime();
      return pressed ? aTime - bTime : bTime - aTime; // alterna
    });
    items.forEach(li => list.appendChild(li));
  });

  // Busca dinâmica de notas ao digitar e ao clicar em buscar
  const searchForm = document.querySelector('.search-form');
  const searchInput = document.querySelector('.search-form input[name="q"]');
  if (searchInput && searchForm) {
    let searchTimeout;
    function buscarNotas(q) {
      fetch('api.php?notes=1&q=' + encodeURIComponent(q))
        .then(res => res.json())
        .then(json => {
          if (json.ok && Array.isArray(json.data)) {
            list.innerHTML = '';
            let resultados = json.data;
            if (q) {
              // Filtra apenas pelo título
              resultados = resultados.filter(note =>
                note.title.toLowerCase().includes(q.toLowerCase())
              );
            }
            if (resultados.length) {
              resultados.forEach(note => {
                const item = renderNote(note);
                list.appendChild(item);
              });
              empty.hidden = true;
            } else {
              empty.hidden = false;
            }
            updateNoteCount();
          }
        });
    }
    searchInput.addEventListener('input', function () {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        buscarNotas(searchInput.value.trim());
      }, 300);
    });
    searchForm.addEventListener('submit', function (e) {
      e.preventDefault();
      buscarNotas(searchInput.value.trim());
    });
  }

  // Função para renderizar uma nova nota
  function renderNote(note) {
    const li = document.createElement('li');
    li.dataset.id = note.id;
    li.innerHTML = `
      <article>
        <h3>${note.title}</h3>
        <p>${note.content}</p>
        <time datetime="${note.created_at}">Criado em: ${note.created_at}</time>
        <div class="note-actions">
          <button class="edit-note" data-id="${note.id}"><i class="fas fa-edit"></i> Editar</button>
          <button class="delete-note" data-id="${note.id}"><i class="fas fa-trash"></i> Excluir</button>
        </div>
      </article>
    `;
    return li;
  }
})();
