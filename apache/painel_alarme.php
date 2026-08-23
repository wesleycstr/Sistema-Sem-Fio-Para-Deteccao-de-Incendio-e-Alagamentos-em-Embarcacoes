<!DOCTYPE html>
<html lang="pt-BR">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>

<title>Sistema Atalaia</title>

<style>

body{
    background:#121212;
    color:white;
    font-family:Arial;
    padding:20px;
}

.table-container{
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
    min-width:900px;
}

th, td{
    padding:15px;
    text-align:left;
    border-bottom:1px solid white;
}

th{
    background:#1f1f1f;
}

tr{
    transition:0.3s;
}

@keyframes piscar {

    0%   { background:#c62828; }

    50%  { background:#5a0000; }

    100% { background:#c62828; }

}

.alarme{
    animation: piscar 1s infinite;
    color:white;
    font-weight:bold;
}

.btnConfig{

    background:#1f1f1f;
    border:none;
    color:white;
    padding:10px;
    border-radius:8px;
    cursor:pointer;
    font-size:14px;
    float:right;
}

.btnConfig:hover{

    background:#333;

}

.campo{

    width:100%;
    padding:10px;
    margin-top:5px;
    margin-bottom:15px;
    background:#333;
    border:none;
    color:white;
    border-radius:5px;

}
.tabs{

    display:flex;
    gap:10px;
    margin-bottom:20px;
    position:relative;

}

.btnCadastrarSensor{

    margin-left:auto;

    background:#1f1f1f;

    border:none;

    color:white;

    padding:10px 14px;

    border-radius:8px;

    cursor:pointer;

    font-size:14px;

}

.tabButton{

    background:#1f1f1f;
    color:white;
    border:none;
    padding:12px 20px;
    cursor:pointer;
    border-radius:8px;
    font-size:16px;
    transition:0.3s;

}

.tabButton:hover{

    background:#333;

}

.tabButton.active{

    background:#2e7d32;

}

.aba{

    animation:fade 0.3s;

}

@keyframes fade{

    from{
        opacity:0;
    }

    to{
        opacity:1;
    }

}

.btnExcluir{

    background:#8b0000;
    border:none;
    color:white;
    padding:10px;
    border-radius:8px;
    cursor:pointer;
    font-size:14px;
    transition:0.3s;

}

.btnExcluir:hover{

    background:#c62828;

}
::-webkit-scrollbar{

    width:10px;

}

::-webkit-scrollbar-track{

    background:#1e1e1e;

}

::-webkit-scrollbar-thumb{

    background:#555;
    border-radius:10px;

}

::-webkit-scrollbar-thumb:hover{

    background:#777;

}

.painelGrid{

    display:grid;

    grid-template-columns:
    repeat(auto-fill,minmax(170px,1fr));

    gap:10px;

}


.cardSensor{

    border-radius:9px;

    padding:10px;

    color:white;

    cursor:pointer;

    transition:.25s;

    box-shadow:0 0 6px rgba(0,0,0,.4);

}


.cardSensor:hover{

    transform:scale(1.02);

}


.cardTitulo{

    font-size:15px;

    font-weight:bold;

    margin-bottom:8px;

    white-space:nowrap;

    overflow:hidden;

    text-overflow:ellipsis;

}


.cardValor{

    font-size:13px;

    margin:4px 0;

}


.cardStatus{

    margin-top:8px;

    text-align:center;

    font-size:15px;

    font-weight:bold;

}


.normal{

    background:#2e7d32;

}


.atencao{

    background:#f9a825;

    color:black;

}


.offline{

    background:#616161;

}


.cardSensor.alarme{

    background:#c62828;

    animation:piscar 1s infinite;

}

.dragHandle{

    cursor:grab;

    user-select:none;

}

.dragHandle:active{

    cursor:grabbing;

}

.linhaGhost{

    opacity:.4;

}

.opcaoSistema{

    background:#1e1e1e;

    padding:20px;

    margin-bottom:15px;

    border-radius:10px;

    box-shadow:0 0 8px rgba(0,0,0,.3);

}

.opcaoSistema h3{

    margin-top:0;

}

.opcaoSistema p{

    color:#bbb;

    margin-bottom:20px;

}

.botoesAlarme{

    display:flex;

    gap:10px;

    align-items:center;

}


.btnAtivarAlarme{

    background:#2e7d32;

    border:none;

    color:white;

    padding:10px 16px;

    border-radius:8px;

    cursor:pointer;

    font-size:14px;

}


.btnAtivarAlarme:hover{

    background:#388e3c;

}


.btnDesativarAlarme{

    background:#c62828;

    border:none;

    color:white;

    padding:10px 16px;

    border-radius:8px;

    cursor:pointer;

    font-size:14px;

}


.btnDesativarAlarme:hover{

    background:#d32f2f;

}

.tituloOpcao{

    margin-top:25px;

}


.btnAtivarScript{

    background:#2e7d32;

    border:none;

    color:white;

    padding:10px 16px;

    border-radius:8px;

    cursor:pointer;

    font-size:14px;

}


.btnAtivarScript:hover{

    background:#388e3c;

}


.btnDesativarScript{

    background:#c62828;

    border:none;

    color:white;

    padding:10px 16px;

    border-radius:8px;

    cursor:pointer;

    font-size:14px;

}


.btnDesativarScript:hover{

    background:#d32f2f;

}

.opcaoSistema h2{

    margin-top:0;

    margin-bottom:25px;

    font-size:22px;

    border-bottom:1px solid #444;

    padding-bottom:12px;

}

.btnExportarLogs{

    background:#1565c0;

    border:none;

    color:white;

    padding:10px 16px;

    border-radius:8px;

    cursor:pointer;

    font-size:14px;

}


.btnExportarLogs:hover{

    background:#1976d2;

}


.btnApagarLogs{

    background:#b71c1c;

    border:none;

    color:white;

    padding:10px 16px;

    border-radius:8px;

    cursor:pointer;

    font-size:14px;

}


.btnApagarLogs:hover{

    background:#d32f2f;

}

</style>

</head>

<body>

<div class="tabs">

    <button class="tabButton active"
    onclick="abrirAba('sensores')">

        🌡 Sensores Ambientais

    </button>


    <button class="tabButton"
    onclick="abrirAba('eventos')">

        🟢🔴 Sensores de Eventos

    </button>


    <button class="tabButton"
    onclick="abrirAba('painel')">

        ▦ Visualização em Grid

    </button>


    <button class="tabButton"
    onclick="abrirAba('logs')">

        📓 Logs

    </button>


    <button class="tabButton"
    onclick="abrirAba('opcoes')">

        ⚙️ Opções

    </button>


    <button
    class="btnCadastrarSensor"
    onclick="abrirCadastroSensor()">

        ➕ Cadastrar Sensor

    </button>

</div>

<!-- ABA SENSORES -->

<div id="abaSensores" class="aba">

<div class="table-container">

<table>

<thead>

<tr>
<th></th>
<th>ID</th>
<th>Localização</th>
<th>Temperatura</th>
<th>Umidade</th>
<th>CO</th>
<th>Status</th>
<th>Data/Hora</th>
<th></th>
<th></th>

</tr>

</thead>

<tbody id="tabela">

</tbody>

</table>

</div>

</div>

<div id="abaEventos"
class="aba"
style="display:none;">

<div class="table-container">

<table>

<thead>

<tr>

<th></th>

<th>Evento</th>

<th>Localização</th>

<th>Nível</th>

<th>Status</th>

<th>Data/Hora</th>

<th></th>

<th></th>

</tr>

</thead>

<tbody id="tabelaEventos">

</tbody>

</table>

</div>

</div>

<!-- ABA LOGS -->

<div id="abaLogs" class="aba" style="display:none;">

<div class="table-container">

<table>

<thead>

<tr>

<th>Evento</th>
<th>ID</th>
<th>Data/Hora</th>
<th>Registro</th>
<th>Ocorrência</th>

</tr>

</thead>

<tbody id="tabelaLogs">

</tbody>

</table>

</div>

</div>

<!-- ABA PAINEL -->

<div id="abaPainel" class="aba" style="display:none;">

    <div id="painelSensores" class="painelGrid">

    </div>

</div>

<div id="modal"
style="
display:none;
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.7);
justify-content:center;
align-items:center;
">

<div style="
background:#1e1e1e;
padding:30px;
border-radius:10px;
width:300px;
">

<h2>Configurar Alertas</h2>

<input type="hidden" id="sensorId">

<label>Temperatura Máx</label>
<input type="number" id="tempMax" class="campo">

<label>Umidade Min</label>
<input type="number" id="umiMin" class="campo">

<label>Umidade Máx</label>
<input type="number" id="umiMax" class="campo">

<label>CO Máx</label>
<input type="number" id="gasMax" class="campo">

<label>
<input type="checkbox" id="alarmeSonoro">
Ativar alarme sonoro
</label>

<br><br>

<label>
<input type="checkbox" id="executarScript">
Executar script em caso de alarme
</label>

<br><br>

<label>Caminho do Script</label>

<input
type="text"
id="scriptAlarme"
class="campo"
placeholder="/grafana-server/web/script.php">

<br><br>

<button onclick="salvarConfig()">
Salvar
</button>

<button onclick="fecharModal()">
Cancelar
</button>

</div>

</div>

<!-- ABA DE OPÇÕES -->

<div id="abaOpcoes"
     class="aba"
     style="display:none;">

    <div class="opcaoSistema">

        <h2>⚙️ Opções para sensores ambientais</h2>

        <!-- ==========================================
            ALARMES
        =========================================== -->

        <h3>🔊 Alarmes dos Sensores Ambientais</h3>

        <p>
            Controle o alarme sonoro de todos os sensores ambientais.
        </p>

        <div class="botoesAlarme">

            <button
                class="btnAtivarAlarme"
                onclick="ativarTodosAlarmes()">

                🔊 Ativar todos os alarmes

            </button>

            <button
                class="btnDesativarAlarme"
                onclick="desativarTodosAlarmes()">

                🔇 Desativar todos os alarmes

            </button>

        </div> <p> <p>

        <!-- ==========================================
            SCRIPTS
        =========================================== -->

        <h3 class="tituloOpcao">

            🖥 Execução de Scripts dos Sensores Ambientais

        </h3>

    <p>

        Controle a execução de scripts de alarme
        de todos os sensores ambientais.

    </p>

        <div class="botoesAlarme">

            <button
                class="btnAtivarScript"
                onclick="ativarTodosScripts()">

                ▶ Ativar todos os scripts

            </button>

            <button
                class="btnDesativarScript"
                onclick="desativarTodosScripts()">

                ⏹ Desativar todos os scripts

            </button>

        </div>
    </div>
    <div class="opcaoSistema">

        <h2>⚙️ Opções para sensores de eventos</h2>


        <!-- ==========================================
            ALARMES DOS SENSORES DE EVENTOS
        =========================================== -->

        <h3>🔊 Alarmes dos Sensores de Eventos</h3>

        <p>
            Controle o alarme sonoro de todos os sensores
            de eventos.
        </p>

        <div class="botoesAlarme">

            <button
                class="btnAtivarAlarme"
                onclick="ativarTodosAlarmesEventos()">

                🔊 Ativar todos os alarmes

            </button>

            <button
                class="btnDesativarAlarme"
                onclick="desativarTodosAlarmesEventos()">

                🔇 Desativar todos os alarmes

            </button>

        </div>
        <p> <p>

        <!-- ==========================================
            SCRIPTS DOS SENSORES DE EVENTOS
        =========================================== -->

        <h3 class="tituloOpcao">

            🖥 Execução de Scripts dos Sensores de Eventos

        </h3>

        <p>

            Controle a execução de scripts de alarme
            de todos os sensores de eventos.

        </p>

        <div class="botoesAlarme">

            <button
                class="btnAtivarScript"
                onclick="ativarTodosScriptsEventos()">

                ▶ Ativar todos os scripts

            </button>


            <button
                class="btnDesativarScript"
                onclick="desativarTodosScriptsEventos()">

                ⏹ Desativar todos os scripts

            </button>

        </div>

        <div class="opcaoSistema">

            <h2>📋 Gerenciamento dos Logs</h2>

            <h3>📤 Exportação dos Logs</h3>

            <p>
                Exporte todos os registros de log armazenados no sistema.
            </p>

            <div class="botoesAlarme">

                <button
                    class="btnExportarLogs"
                    onclick="exportarTodosLogs()">

                    📤 Exportar todos os logs

                </button>

            </div>
            <p><p>

            <h3 class="tituloOpcao">
                🗑️ Exclusão dos Logs
            </h3>

            <p>
                Apague permanentemente todos os registros de log
                armazenados no sistema.
            </p>

            <div class="botoesAlarme">

                <button
                    class="btnApagarLogs"
                    onclick="apagarTodosLogs()">

                    🗑️ Apagar todos os logs

                </button>

            </div>

        </div>

    </div>
</div>

<script>
function abrirModal(id){

    document.getElementById("modal").style.display = "flex";

    document.getElementById("sensorId").value = id;

    fetch("buscar_config.php?id=" + id)

    .then(response => response.json())

    .then(data => {

        document.getElementById("tempMax").value =
        data.temperatura_max;

        document.getElementById("umiMin").value =
        data.umidade_min;

        document.getElementById("umiMax").value =
        data.umidade_max;

        document.getElementById("gasMax").value =
        data.gas_max;

        document.getElementById("alarmeSonoro").checked =
        data.alarme_sonoro == 1;

        document.getElementById("executarScript").checked =
        data.executar_script == 1;

        document.getElementById("scriptAlarme").value =
        data.script_alarme ?? "";

    });

}

function fecharModal(){

    document.getElementById("modal").style.display = "none";

}

function salvarConfig(){

    const id = document.getElementById("sensorId").value;

    const tempMax =
    document.getElementById("tempMax").value;

    const umiMin =
    document.getElementById("umiMin").value;

    const umiMax =
    document.getElementById("umiMax").value;

    const gasMax =
    document.getElementById("gasMax").value;

    const alarmeSonoro =
    document.getElementById("alarmeSonoro").checked ? 1 : 0;

    const executarScript =
    document.getElementById("executarScript").checked ? 1 : 0;

    const scriptAlarme =
    encodeURIComponent(
    document.getElementById("scriptAlarme").value
    );

    fetch("salvar_config.php",{

        method:"POST",

        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },

        body:
        `id=${id}
        &tempMax=${tempMax}
        &umiMin=${umiMin}
        &umiMax=${umiMax}
        &gasMax=${gasMax}
        &alarmeSonoro=${alarmeSonoro}
        &executarScript=${executarScript}
        &scriptAlarme=${scriptAlarme}`

    })
    .then(response => response.text())
    .then(data => {

        alert("Configuração salva!");

        fecharModal();

    });

}</script>

<audio id="somAlarme" loop>

<source src="alarme.mp3" type="audio/mpeg">

</audio>

<script>
function abrirAba(nome){

    // ==========================================
    // ESCONDE TODAS AS ABAS
    // ==========================================

    document.getElementById("abaSensores").style.display = "none";

    document.getElementById("abaEventos").style.display = "none";

    document.getElementById("abaLogs").style.display = "none";

    document.getElementById("abaPainel").style.display = "none";

    document.getElementById("abaOpcoes").style.display = "none";


    // ==========================================
    // REMOVE O BOTÃO ATIVO
    // ==========================================

    document.querySelectorAll(".tabButton")
        .forEach(function(btn){

            btn.classList.remove("active");

        });


    // ==========================================
    // ABRE A ABA SELECIONADA
    // ==========================================

    switch(nome){

        case "sensores":

            document.getElementById(
                "abaSensores"
            ).style.display = "block";

            document.querySelectorAll(
                ".tabButton"
            )[0].classList.add("active");

            break;


        case "eventos":

            document.getElementById(
                "abaEventos"
            ).style.display = "block";

            document.querySelectorAll(
                ".tabButton"
            )[1].classList.add("active");

            break;


        case "painel":

            document.getElementById(
                "abaPainel"
            ).style.display = "block";

            document.querySelectorAll(
                ".tabButton"
            )[2].classList.add("active");

            break;


        case "logs":

            document.getElementById(
                "abaLogs"
            ).style.display = "block";

            document.querySelectorAll(
                ".tabButton"
            )[3].classList.add("active");

            break;


        case "opcoes":

            document.getElementById(
                "abaOpcoes"
            ).style.display = "block";

            document.querySelectorAll(
                ".tabButton"
            )[4].classList.add("active");

            break;

    }

}
</script>

<script>

let sortableCards = null;

function iniciarSortableCards(){

    if(sortableCards){

        sortableCards.destroy();

    }

    sortableCards = new Sortable(document.getElementById("painelSensores"),{

        animation:150,

        ghostClass:"cardGhost",

        onEnd(){

            salvarOrdemCards();

        }

    });

}

function salvarOrdemCards(){

    let ordem = [];

    document.querySelectorAll("#painelSensores .cardSensor")
        .forEach(function(card){

            ordem.push(card.dataset.id);

        });

    fetch("salvar_ordem_cards.php",{

        method:"POST",

        headers:{
            "Content-Type":"application/json"
        },

        body:JSON.stringify(ordem)

    })
    .then(r=>r.text())
    .then(console.log);

}

</script>


<script>

let arrastando = false;
let sortable = null;
let sortableCriado = false;
let alarmeEventoAnterior = false;

const audioAlarmeEvento = new Audio(
    "alarme.mp3"
);

audioAlarmeEvento.loop = true;
// Cria a conexão SSE
const source = new EventSource("stream.php");

source.onmessage = function(event){

    if(arrastando){
        return;
    }

    const dados = JSON.parse(event.data);

    console.log("Dados recebidos pelo SSE:", dados);
    console.log("alarmeEvento:", dados.alarmeEvento);

    // ==========================================
    // ATUALIZA TABELA DE SENSORES
    // ==========================================

    document.getElementById("tabela").innerHTML =
        dados.tabela;


    // ==========================================
    // ATUALIZA CARDS
    // ==========================================

    document.getElementById("painelSensores").innerHTML =
        dados.cards;


    // ==========================================
    // ATUALIZA TABELA DE EVENTOS
    // ==========================================

    document.getElementById("tabelaEventos").innerHTML =
        dados.eventos;


    // ==========================================
    // ALARME SONORO DOS EVENTOS
    // ==========================================

    const alarmeEventoAtual =
        dados.alarmeEvento === true;


    // ==========================================
    // EVENTO ACABOU DE SER ATIVADO
    // false → true
    // ==========================================

    if(
        alarmeEventoAtual &&
        !alarmeEventoAnterior
    ){

        console.log(
            "🔊 Alarme de evento ativado"
        );

        audioAlarmeEvento
            .play()
            .catch(function(error){

                console.log(
                    "Navegador bloqueou o áudio:",
                    error
                );

            });

    }


    // ==========================================
    // TODOS OS EVENTOS FORAM NORMALIZADOS
    // true → false
    // ==========================================

    if(
        !alarmeEventoAtual &&
        alarmeEventoAnterior
    ){

        console.log(
            "🔇 Alarme de evento desativado"
        );

        audioAlarmeEvento.pause();

        audioAlarmeEvento.currentTime = 0;

    }


    // ==========================================
    // GUARDA O ESTADO ATUAL
    // ==========================================

    alarmeEventoAnterior =
        alarmeEventoAtual;


    // ==========================================
    // SORTABLE
    // ==========================================

    if(!sortableCriado){

        iniciarSortable();

        iniciarSortableCards();

        sortableCriado = true;

    }

};

source.onerror = function(error){
    console.log("Erro SSE:", error);
};

function iniciarSortable(){

    sortable = new Sortable(document.getElementById("tabela"),{

        animation:150,

        handle:".dragHandle",

        onStart:function(){

            arrastando = true;

        },

        onEnd:function(){

            arrastando = false;

            salvarOrdem();

        }

    });

}

</script>

<script>

function salvarOrdem(){

    let ordem = [];

    const linhas = document.querySelectorAll("#tabela tr[data-id]");

    linhas.forEach(function(linha){

        ordem.push(linha.dataset.id);

    });

    fetch("salvar_ordem.php",{

        method:"POST",

        headers:{
            "Content-Type":"application/json"
        },

        body:JSON.stringify(ordem)

    })
    .then(response => response.text())
    .then(data => {

        console.log(data);

    });

}

/* LOGS */

function atualizarLogs(){

    fetch("logs_tabela.php")

    .then(response => response.text())

    .then(data => {

        document.getElementById("tabelaLogs")
        .innerHTML = data;

    });

}

setInterval(atualizarLogs, 2000);

atualizarLogs();

</script>

<!-- MODAL CADASTRO SENSOR -->

<div id="modalSensor"
style="
display:none;
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.7);
justify-content:center;
align-items:center;
z-index:999;
">

<div style="
background:#1e1e1e;
padding:30px;
border-radius:10px;
width:90%;
max-width:450px;
max-height:90vh;
overflow-y:auto;
box-sizing:border-box;
">

<h2>Cadastrar Sensor</h2>

<label>Identificador</label>

<input
type="text"
id="novoToken"
class="campo">

<label>Localização</label>

<input
type="text"
id="novaLocalizacao"
class="campo">

<label>Chave Secreta</label>

<input
type="text"
id="novaChave"
class="campo">

<label>Tipo do Sensor</label>

<select
id="tipoSensor"
class="campo"
onchange="alterarTipoSensor()">

<option value="ambiente">

🌡 Ambiente

</option>

<option value="evento">

🚨 Evento

</option>

</select>
<!-- CONFIGURAÇÕES DE EVENTO -->

<div id="configEvento" style="display:none;">

    <label>Canal da Placa</label>

    <select
        id="novoCanal"
        class="campo">

        <option value="1">Canal 1 (D1)</option>
        <option value="2">Canal 2 (D2)</option>
        <option value="3">Canal 3 (D3)</option>
        <option value="4">Canal 4 (D4)</option>
        <option value="5">Canal 5 (D5)</option>
        <option value="6">Canal 6 (D6)</option>
        <option value="7">Canal 7 (D7)</option>
        <option value="8">Canal 8 (D8)</option>

    </select>

    <label>Nome do Evento</label>

    <input
        type="text"
        id="nomeEvento"
        class="campo"
        placeholder="Ex.: Alagamento">

    <label>Nível do Evento</label>

    <select
        id="nivelEvento"
        class="campo">

        <option value="informacao">
            ℹ Informação
        </option>

        <option value="atencao">
            ⚠ Atenção
        </option>

        <option value="critico" selected>
            🚨 Crítico
        </option>

    </select>

</div>
<!-- CONFIGURAÇÕES DE AMBIENTE -->

<div id="configAmbiente">

<label>Temperatura Máx</label>

<input
type="number"
id="novoTempMax"
class="campo"
value="40">

<label>Umidade Min</label>

<input
type="number"
id="novoUmiMin"
class="campo"
value="20">

<label>Umidade Máx</label>

<input
type="number"
id="novoUmiMax"
class="campo"
value="80">

<label>CO Máx</label>

<input
type="number"
id="novoGasMax"
class="campo"
value="100">

</div>

<label>

<input
type="checkbox"
id="novoAlarmeSonoro">

Ativar alarme sonoro

</label>

<br><br>

<button
class="btnConfig"
onclick="salvarSensor()">

Salvar Sensor

</button>

<button
class="btnExcluir"
onclick="fecharCadastroSensor()">

Cancelar

</button>

</div>

</div>

<!-- MODAL EDITAR EVENTO -->

<div id="modalEditarEvento"
style="
display:none;
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.7);
justify-content:center;
align-items:center;
z-index:1000;
">

<div style="
background:#1e1e1e;
padding:30px;
border-radius:10px;
width:90%;
max-width:400px;
box-sizing:border-box;
">

<h2>Editar Evento</h2>

<input
type="hidden"
id="eventoEditandoId">

<label>Canal da Placa</label>

<select
id="editarCanal"
class="campo">

<option value="1">Canal 1 (D1)</option>
<option value="2">Canal 2 (D2)</option>
<option value="3">Canal 3 (D3)</option>
<option value="4">Canal 4 (D4)</option>
<option value="5">Canal 5 (D5)</option>
<option value="6">Canal 6 (D6)</option>
<option value="7">Canal 7 (D7)</option>
<option value="8">Canal 8 (D8)</option>

</select>

<label>Nome do Evento</label>

<input
type="text"
id="editarNomeEvento"
class="campo">

<label>Nível do Evento</label>

<select
id="editarNivelEvento"
class="campo">

<option value="informacao">
ℹ Informação
</option>

<option value="atencao">
⚠ Atenção
</option>

<option value="critico">
🚨 Crítico
</option>

</select>

<label>

<input
type="checkbox"
id="editarAlarmeSonoro">

Ativar alarme sonoro

</label>

<br><br>

<label>

    <input
        type="checkbox"
        id="editarExecutarScript"
    >

    Executar script quando o evento for ativado

</label>

<br><br>

<label>Script de Alarme</label>

    <input
        type="text"
        id="editarScriptAlarme"
        class="campo"
        placeholder="/var/www/html/alarme_evento.php"
    >

<br><br>

<button onclick="salvarEdicaoEvento()">
Salvar
</button>

<button onclick="fecharModalEditarEvento()">
Cancelar
</button>

</div>

</div>

<script>

function abrirCadastroSensor(){

    document.getElementById("modalSensor")
    .style.display = "flex";

}

function fecharCadastroSensor(){

    document.getElementById("modalSensor")
    .style.display = "none";

}

function salvarSensor(){

    /*const nome =
    document.getElementById("novoNome").value;*/

    const localizacao =
    document.getElementById("novaLocalizacao").value;

    const token =
    document.getElementById("novoToken").value;

    const chave =
    document.getElementById("novaChave").value;

    const tempMax =
    document.getElementById("novoTempMax").value;

    const umiMin =
    document.getElementById("novoUmiMin").value;

    const umiMax =
    document.getElementById("novoUmiMax").value;

    const gasMax =
    document.getElementById("novoGasMax").value;

    const tipoSensor =
    document.getElementById("tipoSensor").value;

    const nomeEvento =
    document.getElementById("nomeEvento").value;

    const nivelEvento =
    document.getElementById("nivelEvento").value;

    const canal =
    document.getElementById("novoCanal").value;

    const alarmeSonoro =
    document.getElementById(
    "novoAlarmeSonoro"
    ).checked ? 1 : 0;

    const executarScript =
    document.getElementById("novoExecutarScript").checked ? 1 : 0;

    const scriptAlarme =
    document.getElementById("novoScriptAlarme").value;

    fetch(

        window.location.origin +
        "/cadastrar_sensor.php",

    {

        method:"POST",

        headers:{
            "Content-Type":
            "application/x-www-form-urlencoded"
        },

        body:

        /*"nome=" + encodeURIComponent(nome) +*/

        "&localizacao=" +
        encodeURIComponent(localizacao) +

        "&token=" +
        encodeURIComponent(token) +

        "&chave=" +
        encodeURIComponent(chave) +

        "&tempMax=" + tempMax +

        "&umiMin=" + umiMin +

        "&umiMax=" + umiMax +

        "&gasMax=" + gasMax +

        "&alarmeSonoro=" +
        alarmeSonoro +

        "&tipoSensor=" +
        encodeURIComponent(tipoSensor) +

        "&nomeEvento=" +
        encodeURIComponent(nomeEvento) +

        "&nivelEvento=" +
        encodeURIComponent(nivelEvento) +

        "&canal=" +
        encodeURIComponent(canal) +

        "&executarScript=" +
        executarScript +

        "&scriptAlarme=" +
        encodeURIComponent(scriptAlarme)

    })

    .then(response => response.text())

    .then(data => {

        alert(data);

        fecharCadastroSensor();

    })

    .catch(error => {

        console.log(error);

    });

}

</script>

<script>

function excluirSensor(id){

    const confirmar = confirm(

        "Deseja realmente excluir este sensor?"

    );

    if(!confirmar){

        return;

    }

    fetch(

        window.location.origin +
        "/excluir_sensor.php",

    {

        method:"POST",

        headers:{
            "Content-Type":
            "application/x-www-form-urlencoded"
        },

        body:"id=" + id

    })

    .then(response => response.text())

    .then(data => {

        alert(data);

    })

    .catch(error => {

        console.log(error);

    });

}

</script>

<script>
function alterarTipoSensor(){

    const tipo =
    document.getElementById("tipoSensor").value;

    const ambiente =
    document.getElementById("configAmbiente");

    const evento =
    document.getElementById("configEvento");

    switch(tipo){

        case "ambiente":

            ambiente.style.display = "block";
            evento.style.display = "none";

            break;

        case "evento":

            ambiente.style.display = "none";
            evento.style.display = "block";

            break;
    }

}
</script>

<script>
function abrirModalEvento(id){

    fetch(
        window.location.origin +
        "/buscar_evento.php?id=" + id
    )

    .then(response => response.json())

    .then(evento => {

        if(evento.erro){

            alert(evento.erro);
            return;

        }

        // ==========================================
        // ID
        // ==========================================

        document.getElementById(
            "eventoEditandoId"
        ).value = evento.id;


        // ==========================================
        // CANAL
        // ==========================================

        document.getElementById(
            "editarCanal"
        ).value = evento.canal;


        // ==========================================
        // NOME
        // ==========================================

        document.getElementById(
            "editarNomeEvento"
        ).value = evento.nome_evento;


        // ==========================================
        // NÍVEL
        // ==========================================

        document.getElementById(
            "editarNivelEvento"
        ).value = evento.nivel_evento;


        // ==========================================
        // ALARME SONORO
        // ==========================================

        const checkboxAlarme =
            document.getElementById(
                "editarAlarmeSonoro"
            );

        checkboxAlarme.checked =
            Number(evento.alarme_sonoro) === 1;


        // Guarda o valor original
        checkboxAlarme.dataset.original =
            checkboxAlarme.checked ? "1" : "0";


        // ==========================================
        // EXECUÇÃO DE SCRIPT
        // ==========================================

        const checkboxScript =
            document.getElementById(
                "editarExecutarScript"
            );

        checkboxScript.checked =
            Number(evento.executar_script) === 1;


        // Guarda o valor original
        checkboxScript.dataset.original =
            checkboxScript.checked ? "1" : "0";


        // ==========================================
        // CAMINHO DO SCRIPT
        // ==========================================

        document.getElementById(
            "editarScriptAlarme"
        ).value =
            evento.script_alarme || "";


        // ==========================================
        // ABRE MODAL
        // ==========================================

        document.getElementById(
            "modalEditarEvento"
        ).style.display = "flex";

    })

    .catch(error => {

        console.error(error);

        alert("Erro ao carregar evento.");

    });

}


function salvarEdicaoEvento(){

    const id =
        document.getElementById(
            "eventoEditandoId"
        ).value;

    const canal =
        document.getElementById(
            "editarCanal"
        ).value;

    const nomeEvento =
        document.getElementById(
            "editarNomeEvento"
        ).value;

    const nivelEvento =
        document.getElementById(
            "editarNivelEvento"
        ).value;

    const alarmeSonoro =
        document.getElementById(
            "editarAlarmeSonoro"
        ).checked ? 1 : 0;

    const executarScript =
        document.getElementById(
            "editarExecutarScript"
        ).checked ? 1 : 0;

    const scriptAlarme =
        document.getElementById(
            "editarScriptAlarme"
        ).value;


    // ==========================================
    // VALIDAÇÃO
    // ==========================================

    if(nomeEvento.trim() === ""){

        alert("Informe o nome do evento.");

        return;

    }


    // ==========================================
    // DEBUG
    // ==========================================

    console.log("ID:", id);
    console.log("Canal:", canal);
    console.log("Alarme sonoro:", alarmeSonoro);
    console.log("Executar script:", executarScript);
    console.log("Script:", scriptAlarme);


    // ==========================================
    // ENVIO
    // ==========================================

    const dados =

        "id=" +
        encodeURIComponent(id) +

        "&canal=" +
        encodeURIComponent(canal) +

        "&nomeEvento=" +
        encodeURIComponent(nomeEvento) +

        "&nivelEvento=" +
        encodeURIComponent(nivelEvento) +

        "&alarmeSonoro=" +
        encodeURIComponent(alarmeSonoro) +

        "&executarScript=" +
        encodeURIComponent(executarScript) +

        "&scriptAlarme=" +
        encodeURIComponent(scriptAlarme);


    fetch(
        window.location.origin +
        "/editar_evento.php",
        {

            method:"POST",

            headers:{
                "Content-Type":
                "application/x-www-form-urlencoded"
            },

            body:dados

        }
    )

    .then(response => response.text())

    .then(data => {

        console.log(
            "Resposta:",
            data
        );

        alert(data);

        fecharModalEditarEvento();

    })

    .catch(error => {

        console.error(error);

        alert(
            "Erro ao editar evento."
        );

    });

}


function fecharModalEditarEvento(){

    document.getElementById(
        "modalEditarEvento"
    ).style.display = "none";

}

function excluirEvento(id){

    if(!confirm(
        "Tem certeza que deseja excluir este evento?"
    )){

        return;

    }

    fetch(
        window.location.origin +
        "/excluir_evento.php",

        {

            method:"POST",

            headers:{
                "Content-Type":
                "application/x-www-form-urlencoded"
            },

            body:
                "id=" +
                encodeURIComponent(id)

        }
    )

    .then(response => response.text())

    .then(data => {

        alert(data);

    })

    .catch(error => {

        console.error(error);

        alert("Erro ao excluir evento.");

    });

}

</script>

<script>

function ativarTodosAlarmes(){

    if(!confirm(
        "Deseja ativar o alarme sonoro de TODOS os sensores ambientais?"
    )){

        return;

    }

    fetch("ativar_todos_alarmes.php")

    .then(response => response.text())

    .then(data => {

        alert(data);

    })

    .catch(error => {

        console.error(error);

        alert(
            "Erro ao ativar os alarmes."
        );

    });

}

function desativarTodosAlarmes(){

    if(!confirm(
        "Deseja desativar o alarme sonoro de TODOS os sensores ambientais?"
    )){

        return;

    }

    fetch("desativar_todos_alarmes.php")

    .then(response => response.text())

    .then(data => {

        alert(data);

    })

    .catch(error => {

        console.error(error);

        alert(
            "Erro ao desativar os alarmes."
        );

    });

}

function ativarTodosScripts(){

    if(!confirm(
        "Deseja ativar a execução de scripts de TODOS os sensores ambientais?"
    )){

        return;

    }

    fetch("ativar_todos_scripts.php")

    .then(response => response.text())

    .then(data => {

        alert(data);

    })

    .catch(error => {

        console.error(error);

        alert(
            "Erro ao ativar os scripts."
        );

    });

}

function desativarTodosScripts(){

    if(!confirm(
        "Deseja desativar a execução de scripts de TODOS os sensores ambientais?"
    )){

        return;

    }

    fetch("desativar_todos_scripts.php")

    .then(response => response.text())

    .then(data => {

        alert(data);

    })

    .catch(error => {

        console.error(error);

        alert(
            "Erro ao desativar os scripts."
        );

    });

}

function ativarTodosAlarmesEventos(){

    if(!confirm(
        "Deseja ativar o alarme sonoro de TODOS os sensores de eventos?"
    )){

        return;

    }

    fetch("ativar_todos_alarmes_eventos.php")

    .then(response => response.text())

    .then(data => {

        alert(data);

    })

    .catch(error => {

        console.error(error);

        alert(
            "Erro ao ativar os alarmes dos sensores de eventos."
        );

    });

}

function desativarTodosAlarmesEventos(){

    if(!confirm(
        "Deseja desativar o alarme sonoro de TODOS os sensores de eventos?"
    )){

        return;

    }

    fetch("desativar_todos_alarmes_eventos.php")

    .then(response => response.text())

    .then(data => {

        alert(data);

    })

    .catch(error => {

        console.error(error);

        alert(
            "Erro ao desativar os alarmes dos sensores de eventos."
        );

    });

}

function ativarTodosScriptsEventos(){

    if(!confirm(
        "Deseja ativar a execução de scripts de TODOS os sensores de eventos?"
    )){

        return;

    }

    fetch("ativar_todos_scripts_eventos.php")

    .then(response => response.text())

    .then(data => {

        alert(data);

    })

    .catch(error => {

        console.error(error);

        alert(
            "Erro ao ativar os scripts dos sensores de eventos."
        );

    });

}

function desativarTodosScriptsEventos(){

    if(!confirm(
        "Deseja desativar a execução de scripts de TODOS os sensores de eventos?"
    )){

        return;

    }

    fetch("desativar_todos_scripts_eventos.php")

    .then(response => response.text())

    .then(data => {

        alert(data);

    })

    .catch(error => {

        console.error(error);

        alert(
            "Erro ao desativar os scripts dos sensores de eventos."
        );

    });

}
</script>

<script>
    function exportarTodosLogs(){

        window.location.href =
            "exportar_logs.php";

    }

    function apagarTodosLogs(){

    const confirmar = confirm(

        "ATENÇÃO!\n\n" +

        "Todos os registros de logs serão apagados " +
        "permanentemente.\n\n" +

        "Esta operação não pode ser desfeita.\n\n" +

        "Deseja realmente continuar?"

    );


    if(!confirmar){

        return;

    }


    fetch("apagar_todos_logs.php")

    .then(response => response.json())

    .then(data => {

        alert(data.mensagem);


        if(data.sucesso){

            /*
             * Atualiza a aba de logs
             */

            abrirAba("logs");

        }

    })

    .catch(error => {

        console.error(error);

        alert(
            "Erro ao apagar os logs."
        );

    });

}
</script>
</body>
</html>