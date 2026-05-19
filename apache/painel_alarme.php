<!DOCTYPE html>
<html lang="pt-BR">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>SISCAV</title>

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

</style>

</head>

<body>

<div class="tabs">

<div style="margin-bottom:20px;">

<button class="btnConfig"
onclick="abrirCadastroSensor()">

➕ Cadastrar Sensor

</button>

</div>

<button class="tabButton active"
onclick="abrirAba('sensores')">

Sensores

</button>

<button class="tabButton"
onclick="abrirAba('logs')">

Logs

</button>

</div>

<!-- ABA SENSORES -->

<div id="abaSensores" class="aba">

<div class="table-container">

<table>

<thead>

<tr>

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

<!-- ABA LOGS -->

<div id="abaLogs" class="aba" style="display:none;">

<div class="table-container">

<table>

<thead>

<tr>

<th>ID</th>
<th>Sensor</th>
<th>Data/Hora</th>
<th>Evento</th>

</tr>

</thead>

<tbody id="tabelaLogs">

</tbody>

</table>

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

<button onclick="salvarConfig()">
Salvar
</button>

<button onclick="fecharModal()">
Cancelar
</button>

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
        &alarmeSonoro=${alarmeSonoro}`

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
    const som = document.getElementById("somAlarme");

let tocando = false;

function verificarAlarmes(){

    fetch("verificar_alarme.php")

    .then(response => response.json())

    .then(data => {

        if(data.alarme == true){

            if(!tocando){

                som.play();

                tocando = true;

            }

        }else{

            som.pause();

            som.currentTime = 0;

            tocando = false;

        }

    });

}

setInterval(verificarAlarmes, 1000);
</script>

<script>
    document.body.addEventListener("click", () => {
    som.play().then(() => {
        som.pause();
        som.currentTime = 0;
    });
}, { once: true });
</script>
<script>
    function abrirAba(aba){

    /* esconder abas */

    document.getElementById("abaSensores")
    .style.display = "none";

    document.getElementById("abaLogs")
    .style.display = "none";

    /* remover ativo */

    document.querySelectorAll(".tabButton")
    .forEach(btn => {

        btn.classList.remove("active");

    });

    /* mostrar aba correta */

    if(aba === "sensores"){

        document.getElementById("abaSensores")
        .style.display = "block";

        document.querySelectorAll(".tabButton")[0]
        .classList.add("active");

    }

    if(aba === "logs"){

        document.getElementById("abaLogs")
        .style.display = "block";

        document.querySelectorAll(".tabButton")[1]
        .classList.add("active");

    }

}
</script>

<script>

/* ABAS */

function abrirAba(nome){

    document.getElementById("abaSensores")
    .style.display = "none";

    document.getElementById("abaLogs")
    .style.display = "none";

    document.querySelectorAll(".tabButton")
    .forEach(btn => btn.classList.remove("active"));

    if(nome == "sensores"){

        document.getElementById("abaSensores")
        .style.display = "block";

        document.querySelectorAll(".tabButton")[0]
        .classList.add("active");

    }

    if(nome == "logs"){

        document.getElementById("abaLogs")
        .style.display = "block";

        document.querySelectorAll(".tabButton")[1]
        .classList.add("active");

    }

}

/* STREAM SENSORES */

const source = new EventSource("stream.php");

source.onmessage = function(event){

    document.getElementById("tabela").innerHTML =
    event.data;

};

source.onerror = function(error){

    console.log("Erro SSE:", error);

};

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
max-width:400px;
max-height:90vh;
overflow-y:auto;
box-sizing:border-box;
">

<h2>Cadastrar Sensor</h2>

<label>Nome do Sensor</label>
<input type="text"
id="novoNome"
class="campo">

<label>Localização</label>
<input type="text"
id="novaLocalizacao"
class="campo">

<label>Device Token</label>
<input
type="text"
id="novoToken"
class="campo">

<label>Chave Secreta</label>
<input type="text"
id="novaChave"
class="campo">

<label>Temperatura Máx</label>
<input type="number"
id="novoTempMax"
class="campo"
value="40">

<label>Umidade Min</label>
<input type="number"
id="novoUmiMin"
class="campo"
value="20">

<label>Umidade Máx</label>
<input type="number"
id="novoUmiMax"
class="campo"
value="80">

<label>CO Máx</label>
<input type="number"
id="novoGasMax"
class="campo"
value="100">

<label>

<input type="checkbox"
id="novoAlarmeSonoro">

Ativar alarme sonoro

</label>

<br><br>

<button onclick="salvarSensor()">

Salvar Sensor

</button>

<button onclick="fecharCadastroSensor()">

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

    const nome =
    document.getElementById("novoNome").value;

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

    const alarmeSonoro =

    document.getElementById(
    "novoAlarmeSonoro"
    ).checked ? 1 : 0;

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

        "nome=" + encodeURIComponent(nome) +

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
        alarmeSonoro

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

</body>
</html>
