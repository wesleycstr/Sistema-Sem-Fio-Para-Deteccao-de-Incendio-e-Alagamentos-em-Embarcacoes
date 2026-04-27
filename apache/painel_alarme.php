<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tabela Dark Responsiva</title>

  <?php
    include 'conexao.php';
  ?>

  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #121212;
      color: #ffffff;
    }

    .container {
      padding: 20px;
    }

    h1 {
      text-align: center;
      margin-bottom: 20px;
    }

    .table-container {
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 600px;
    }

    th, td {
      padding: 12px;
      text-align: left;
    }

    th {
      background-color: #1f1f1f;
    }

    tr {
      transition: background 0.3s;
    }

    tr:nth-child(even) {
      background-color: #1a1a1a;
    }

    tr:nth-child(odd) {
      background-color: #161616;
    }

    td input[type="color"] {
      background: none;
      border: none;
      cursor: pointer;
      width: 40px;
      height: 30px;
    }

    /* Responsividade */
    @media (max-width: 600px) {
      table {
        font-size: 14px;
      }
    }
    @keyframes piscarVermelho {
  0%   { background-color: #c62828; }
  50%  { background-color: #5a0000; }
  100% { background-color: #c62828; }
}

.alarme {
  animation: piscarVermelho 1s infinite;
  color: #fff;
  font-weight: bold;
}
  </style>
</head>
<body>

<div class="container">
  <h1>Tabela Dark Responsiva</h1>

  <div class="table-container">
    <table id="tabela">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Status</th>
          <th>Cor</th>
        </tr>
      </thead>
<tbody>
<?php
include 'conexao.php';

$sql = "
SELECT 
  sensores.id,
  sensores.localizacao,
  alarme.status,
  alarme.cor
FROM sensores
LEFT JOIN alarme ON sensores.id = alarme.id_sensor
";

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {

    $classe = "";
    $cor = "#161616"; // padrão

    switch ($row['status']) {
        case 1:
            $cor = "#2e7d32"; // verde
            break;
        case 2:
            $cor = "#f9a825"; // amarelo
            break;
        case 3:
            $classe = "alarme"; // ativa o piscar
            break;
    }

    echo "<tr class='$classe' style='background-color: $cor'>
            <td>{$row['id']}</td>
            <td>{$row['localizacao']}</td>
            <td>{$row['status']}</td>
            <td>-</td>
          </tr>";
}?>
</tbody>
    </table>
  </div>
</div>

<script>
function mudarCor(input) {
  const linha = input.closest("tr");
  const id = linha.cells[0].innerText;
  const cor = input.value;

  linha.style.backgroundColor = cor;

  fetch("salvar_cor.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: `id=${id}&cor=${cor}`
  });
}
</script>

</body>
</html>
