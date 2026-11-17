```php
<?php

include 'conexao.php';

// =====================================================
// CONFIGURAÇÃO DO FUSO HORÁRIO PARA EXIBIÇÃO
// =====================================================

$fusoUTC = new DateTimeZone('UTC');
$fusoLocal = new DateTimeZone('America/Recife');

// =====================================================
// CONSULTA DOS LOGS
// =====================================================

$sql = "
SELECT
    logs.id,
    logs.id_sensor,
    sensores.device_token AS nome_sensor,
    logs.data_hora,
    logs.evento,
    logs.ocorrencia
FROM logs
LEFT JOIN sensores ON sensores.id = logs.id_sensor
ORDER BY logs.data_hora DESC
LIMIT 100
";

$result = $conn->query($sql);

if (!$result) {
    die("Erro ao consultar logs: " . $conn->error);
}

// =====================================================
// VERIFICA SE EXISTEM REGISTROS
// =====================================================

if ($result->num_rows == 0) {
    echo "
    <tr>
        <td colspan='5' style='text-align:center;'>
            Nenhum log encontrado
        </td>
    </tr>
    ";
}

// =====================================================
// EXIBIÇÃO DOS REGISTROS
// =====================================================

while ($row = $result->fetch_assoc()) {

    // Interpreta o horário armazenado como UTC
    $dataUTC = new DateTime(
        $row['data_hora'],
        $fusoUTC
    );

    // Converte para o horário brasileiro
    $dataUTC->setTimezone($fusoLocal);

    // Formata para exibição
    $dataHoraLocal = $dataUTC->format('d/m/Y H:i:s');

    ?>

    <tr>
        <td><?= htmlspecialchars($row['id']) ?></td>

        <td>
            <?= htmlspecialchars($row['nome_sensor'] ?? '') ?>
        </td>

        <td>
            <?= htmlspecialchars($dataHoraLocal) ?>
        </td>

        <td>
            <?= htmlspecialchars($row['evento'] ?? '') ?>
        </td>

        <td>
            <?= htmlspecialchars($row['ocorrencia'] ?? '') ?>
        </td>
    </tr>

    <?php
}

?>
```