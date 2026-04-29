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

</style>

</head>

<body>

<h1>Painel SISCAV</h1>

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

</body>
</html>
