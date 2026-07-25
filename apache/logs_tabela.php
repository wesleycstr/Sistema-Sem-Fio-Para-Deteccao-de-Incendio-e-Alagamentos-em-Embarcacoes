<?php

include 'conexao.php';

$sql = "
SELECT
    logs.id,
    logs.id_sensor,
    sensores.device_token AS nome_sensor,
    logs.data_hora,
    logs.evento
FROM logs
LEFT JOIN sensores ON sensores.id = logs.id_sensor
ORDER BY logs.data_hora DESC
LIMIT 100
";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "
    <tr>
        <td colspan='4' style='text-align:center;'>
            Nenhum log encontrado
        </td>
    </tr>
    ";
}

while ($row = $result->fetch_assoc()) {
?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['nome_sensor'] ?></td>
        <td><?= $row['data_hora'] ?></td>
        <td><?= $row['evento'] ?></td>
    </tr>
<?php
}
?>