<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=h, initial-scale=1.0">
    <title>Notitificación Rol de Pago</title>
</head>

<body>
    <h4>
        {{ $nombre }}
        <small>{{ $identificacion }}</small>
    </h4>
    <p><strong>Documento:</strong> FACTURA <strong>Num.:</strong> {{ $factura_numero }}</p>
    <p><strong>Num. Autorizacion:</strong> {{ $factura_autorizacion }}</p>
    <p><strong>Fecha Emision:</strong> {{ $date }}</p>
    <p><strong>Total:</strong> {{ $factura_total }}</p>

</body>

</html>