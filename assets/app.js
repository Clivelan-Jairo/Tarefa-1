(function(){
  const form = document.getElementById('note-form');
  const list = document.getElementById('notes-list');
  const empty = document.getElementById('empty');
  const title = document.getElementById('title');
  const titleCount = document.getElementById('title-count');
  const sortToggle = document.getElementById('sort-toggle');
  const cancelEditBtn = document.getElementById('cancel-edit');
  const menuToggle = document.querySelector('.menu-toggle');

  // contador de caracteres (CSR)
  if (title && titleCount) {
    title.addEventListener('input', () => {
      titleCount.textContent = `${title.value.length}/120`;
    });
  }

  // Lógica para o menu hambúrguer
  if (menuToggle) {
    menuToggle.addEventListener('click', () => {
      const headerContent = document.querySelector('.header-content');
      const isMenuOpen = headerContent.classList.toggle('menu-open');
      menuToggle.setAttribute('aria-expanded', isMenuOpen);

      // Alterna o ícone
      menuToggle.querySelector('i').classList.toggle('fa-bars', !isMenuOpen);
      menuToggle.querySelector('i').classList.toggle('fa-times', isMenuOpen);
    });
  }

  // Função para atualizar o contador de notas
  function updateNoteCount() {
    const noteCount = list ? list.children.length : 0;
    const noteCountElement = document.getElementById('note-count');
    if (noteCountElement) {
      noteCountElement.textContent = `Total de notas: ${noteCount}`;
    }
  }

  // Função para resetar o formulário de edição
  function resetForm() {
    form.dataset.editing = '';
    form.classList.remove('editing');
    form.reset();
    titleCount.textContent = '0/120';
    form.querySelector('button[type="submit"]').textContent = 'Salvar';
    if (cancelEditBtn) cancelEditBtn.style.display = 'none';
    document.querySelector('.new-note h2').textContent = 'Criar nova nota';
  }

  // Evento para o botão de cancelar edição
  if (cancelEditBtn) {
    cancelEditBtn.addEventListener('click', resetForm);
  }

  // Submit via fetch para criar ou atualizar nota (CSR)
  if (form) form.addEventListener('submit', async (e) => {
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
      resetForm();
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

      // Após criar a nota, redireciona para a página de notas
      alert('Nota criada com sucesso!');
      window.location.href = 'notas.php';
    }
  });

  // Delegação para excluir (CSR + API)
  if (list) list.addEventListener('click', async (e) => {
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
  if (list) list.addEventListener('click', (e) => {
    const btn = e.target.closest('.edit-note');
    if (!btn) return;
    const id = btn.dataset.id;
    // Redireciona para a página de edição (index.php) com o ID da nota
    window.location.href = `index.php?edit=${id}`;
  });

  // Alterna ordenação no cliente (CSR)
  const sortToggleBtn = document.getElementById('sort-toggle');
  if (sortToggleBtn) {
    sortToggleBtn.addEventListener('click', () => {
      const currentOrder = sortToggleBtn.dataset.order;
      const newOrder = currentOrder === 'desc' ? 'asc' : 'desc';
      sortToggleBtn.dataset.order = newOrder;

      const items = Array.from(list.children);
      items.sort((a, b) => {
        const aTime = new Date(a.querySelector('time').getAttribute('datetime')).getTime();
        const bTime = new Date(b.querySelector('time').getAttribute('datetime')).getTime();
        return newOrder === 'asc' ? aTime - bTime : bTime - aTime;
      });
      items.forEach(li => list.appendChild(li));
    });
  }

  // Busca dinâmica de notas ao digitar e ao clicar em buscar
  const searchForm = document.querySelector('.search-form');
  const searchInput = document.querySelector('.search-form input[name="q"]');
  if (searchInput && searchForm) {
    let searchTimeout;

    async function buscarNotas(query) {
      const res = await fetch(`api.php?q=${encodeURIComponent(query)}`);
      const json = await res.json();

      if (json.ok && Array.isArray(json.data)) {
        list.innerHTML = ''; // Limpa a lista atual
        if (json.data.length > 0) {
          json.data.forEach(note => {
            const noteElement = renderNote(note);
            list.appendChild(noteElement);
          });
          empty.hidden = true;
        } else {
          empty.hidden = false;
        }
        updateNoteCount();
      } else {
        console.error("Erro ao buscar notas:", json.error);
      }
    }

    searchInput.addEventListener('input', () => {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        buscarNotas(searchInput.value.trim());
      }, 300);
    });

    // Previne o envio do formulário que recarrega a página
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
        <h3></h3>
        <p></p>
        <time datetime=""></time>
        <div class="note-actions">
          <button class="btn btn-edit edit-note" data-id="${note.id}"><i class="fas fa-edit"></i> Editar</button>
          <button class="delete-note" data-id="${note.id}"><i class="fas fa-trash"></i> Excluir</button>
        </div>
      </article>
    `;
    return li;
  }
})();
