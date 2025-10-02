<?php
include "conection.php";

$id_vehiculo = $_POST['id_vehiculo'];

$insert = "INSERT INTO hora_salida(vehiculo_id_vehiculo) VALUES($id_vehiculo)";
$query = mysqli_query($conn, $insert);

if ($insert) {
echo "<script>
        alert('El carro ya ha salido, ahora puedes imprimir la factura.');
        window.location.href='pdf.php?id_vehiculo=$id_vehiculo';</script>";
    }
         else {
    echo "<script> alert('Ha ocurrido un error, intentelo nuevamente.');</script>";
}
?>