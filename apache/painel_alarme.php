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
        <tr style="background-color: red">
          <td>1</td>
          <td>Sensor A</td>
          <td>Ativo</td>
        </tr>
        <tr style="background-color: green">
          <td>2</td>
          <td>Sensor B</td>
          <td>Inativo</td>
          <td><input type="color" onchange="mudarCor(this)"></td>
        </tr>
        <tr>
          <td>3</td>
          <td>Sensor C</td>
          <td>Ativo</td>
          <td><input type="color" onchange="mudarCor(this)"></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<script>
  function mudarCor(input) {
    const linha = input.closest("tr");
    linha.style.backgroundColor = input.value;
  }
</script>

</body>
</html>
