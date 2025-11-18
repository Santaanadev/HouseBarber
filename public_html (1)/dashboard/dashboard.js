// Carregar agendamentos reais do servidor
function carregarAgendamentos() {

  const lista = document.getElementById('lista-agendamentos');

  if (!lista) {
    console.warn("Elemento #lista-agendamentos não encontrado.");
    return;
  }

  fetch('../api/listar_agendamentos.php', { credentials: 'include' })
    .then(r => {
      if (!r.ok) throw new Error('Não autenticado');
      return r.json();
    })
    .then(data => {

      lista.innerHTML = '';

      if (data.length === 0) {
        lista.innerHTML = '<p>Nenhum agendamento encontrado.</p>';
        return;
      }

      data.forEach(item => {
        const div = document.createElement('div');
        div.className = 'agendamento';
        div.innerHTML = `
          <strong>${item.usuario_nome}</strong><br>
          Serviço: ${item.servico}<br>
          Data: ${item.data}<br>
          Horário: ${item.horario}<br>
          Status: ${item.status}
        `;
        lista.appendChild(div);
      });

      // Atualizar indicadores simples
      document.getElementById('qtdHoje').textContent = data.length;
      document.getElementById('qtdSemana').textContent = data.length;
      document.getElementById('proxCliente').textContent = data[0]?.usuario_nome || '—';
    })
    .catch(err => {
      console.error(err);
      lista.innerHTML = '<p>Erro ao carregar agendamentos. Faça login.</p>';
    });
}

// Inicializa ao carregar a página
carregarAgendamentos();
