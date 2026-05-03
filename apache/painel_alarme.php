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

</style>

</head>

<body>

<!--<h1>Painel SISCAV</h1>-->

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

</tr>

</thead>

<tbody id="tabela">

</tbody>

</table>

</div>

<script>

const source = new EventSource("stream.php");

source.onmessage = function(event){

    document.getElementById("tabela").innerHTML = event.data;

};

source.onerror = function(error){

    console.log("Erro SSE:", error);

};

</script>

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

</body>
</html>
