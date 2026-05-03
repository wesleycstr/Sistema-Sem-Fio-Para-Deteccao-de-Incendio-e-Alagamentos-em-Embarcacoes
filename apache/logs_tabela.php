<?php

include 'conexao.php';

$sql = "

SELECT *

FROM logs

ORDER BY data_hora DESC

LIMIT 100

";

$result = $conn->query($sql);

if($result->num_rows == 0){

    echo "

    <tr>

        <td colspan='4'
        style='text-align:center;'>

        Nenhum log encontrado

        </td>

    </tr>

    ";

}

while($row = $result->fetch_assoc()){

    ?>

    <tr>

        <td><?= $row['id'] ?></td>

        <td><?= $row['id_sensor'] ?></td>

        <td><?= $row['data_hora'] ?></td>

        <td><?= $row['evento'] ?></td>

    </tr>

    <?php

}
?>
