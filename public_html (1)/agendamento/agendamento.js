const calendar = document.getElementById("calendar");
const monthName = document.getElementById("monthName");
const horariosDiv = document.getElementById("horarios");
const confirmarBtn = document.getElementById("confirmar");
const msgConfirmacao = document.getElementById("mensagemConfirmacao");
//DOM

let hoje = new Date();
let anoAtual = hoje.getFullYear();
let mesAtual = hoje.getMonth();

let diaSelecionado = null;
let horarioSelecionado = null;

const meses = [
  "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
  "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
];

// -----------------------------
// GERAR CALENDÁRIO FORMULÁRIOX
// -----------------------------
function gerarCalendario() {
  calendar.innerHTML = "";
  monthName.textContent = `${meses[mesAtual]} ${anoAtual}`;

  const primeiroDiaSemana = new Date(anoAtual, mesAtual, 1).getDay();
  const totalDias = new Date(anoAtual, mesAtual + 1, 0).getDate();

  // Espaços vazios antes do início do mês
  for (let i = 0; i < primeiroDiaSemana; i++) {
    const vazio = document.createElement("div");
    vazio.classList.add("disabled");
    calendar.appendChild(vazio);
  }

  // Dias do mês
  for (let dia = 1; dia <= totalDias; dia++) {
    const div = document.createElement("div");
    div.textContent = dia;

    const dataLoop = new Date(anoAtual, mesAtual, dia);
    const diaSemana = dataLoop.getDay();

    const hojeComparacao = new Date(
      hoje.getFullYear(),
      hoje.getMonth(),
      hoje.getDate()
    );

    const diaAtual = new Date(anoAtual, mesAtual, dia);

    const diaPassado = diaAtual < hojeComparacao;
    const domingo = diaSemana === 0;
    const segunda = diaSemana === 1;

    if (diaPassado || domingo || segunda) {
      div.classList.add("disabled");
    } else {
      div.addEventListener("click", () => selecionarDia(div, dia));
    }

    calendar.appendChild(div);
  }
}

// -----------------------------
// SELECIONAR DIA
// -----------------------------
function selecionarDia(div, dia) {
  document.querySelectorAll(".calendar div").forEach((d) =>
    d.classList.remove("selected")
  );

  div.classList.add("selected");

  // CORREÇÃO:
  diaSelecionado = `${dia}/${mesAtual + 1}/${anoAtual}`;

  gerarHorarios();
}


// -----------------------------
// HORÁRIOS (07h às 17h)
// -----------------------------
function gerarHorarios() {
  horariosDiv.innerHTML = "";
  horarioSelecionado = null;
  confirmarBtn.style.display = "none";

  document.getElementById("titulo-horarios").style.display = "block";

  for (let h = 7; h <= 17; h++) {
    const hora = `${h}:00`;

    const btn = document.createElement("div");
    btn.classList.add("horario");
    btn.textContent = hora;

    btn.addEventListener("click", () => selecionarHorario(btn, hora));

    horariosDiv.appendChild(btn);
  }
}

function selecionarHorario(btn, hora) {
  document.querySelectorAll(".horario").forEach((h) =>
    h.classList.remove("selected")
  );

  btn.classList.add("selected");
  horarioSelecionado = hora;

  confirmarBtn.style.display = "block";
}

// -----------------------------
// CONFIRMAR AGENDAMENTO - EVENT LIST 
// -----------------------------
confirmarBtn.addEventListener("click", () => {

  confirmarBtn.style.display = "none";

  msgConfirmacao.textContent =
    `Agendamento confirmado para o dia ${diaSelecionado} de ${meses[mesAtual]} às ${horarioSelecionado}!`;

  msgConfirmacao.style.display = "block";

  // desaparecer mensagem depois de 4s (opcional)
  setTimeout(() => {
    msgConfirmacao.style.display = "none";
  }, 4000);
});

// -----------------------------
// Trocar mês
// -----------------------------
document.getElementById("nextMonth").addEventListener("click", () => {
  mesAtual++;
  if (mesAtual > 11) {
    mesAtual = 0;
    anoAtual++;
  }
  gerarCalendario();
});

document.getElementById("prevMonth").addEventListener("click", () => {
  mesAtual--;
  if (mesAtual < 0) {
    mesAtual = 11;
    anoAtual--;
  }
  gerarCalendario();
});

// Inicializar
gerarCalendario();


// Enviar agendamento para o servidor
function enviarAgendamentoServidor() {
  // precisa de um select de servicos no HTML ou definir servico padrão
  const servicoInput = document.getElementById('servico_select');
  const servico_id = servicoInput ? servicoInput.value : 1;
  const formData = new FormData();
  formData.append('servico_id', servico_id);
  // data no formato YYYY-MM-DD
  const dataParts = diaSelecionado.split('/');
  const dia = dataParts[0].padStart(2,'0');
  const mes = (mesAtual+1).toString().padStart(2,'0');
  const ano = anoAtual;
  const data = `${ano}-${mes}-${dia}`;
  formData.append('data', data);
  formData.append('horario', horarioSelecionado);

  fetch('../api/salvar_agendamento.php', {
    method: 'POST',
    body: formData,
    credentials: 'include'
  })
  .then(r => r.json())
  .then(res => {
    if (res.success) {
      msgConfirmacao.textContent = 'Agendamento salvo com sucesso!';
      // opcional: atualizar UI
      setTimeout(()=>{ msgConfirmacao.style.display='none'; }, 3000);
    } else {
      msgConfirmacao.textContent = res.error || 'Erro ao salvar agendamento';
    }
  })
  .catch(err => {
    msgConfirmacao.textContent = 'Erro de rede ao salvar agendamento';
    console.error(err);
  });
}

// Chamar o envio ao confirmar
confirmarBtn.addEventListener('click', () => {
  enviarAgendamentoServidor();
});
