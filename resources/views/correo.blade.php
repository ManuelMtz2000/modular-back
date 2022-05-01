<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0">
    <title>Publicación extravios CUCEI</title>
</head>
<body>
    <p>Hola! El usuario {{ $user->nombre }} ha reconocido tu publicación.</p>
    <p style="font-weight: bold;">Datos de la publicación:</p>
    <ul>
        <li>Publicación: {{ $publicacion->desc_objetoC }}</li>
        <li>Lugar: {{ $publicacion->lugar }}</li>
    </ul>
    <p>Si eres quien tiene el objeto, no olvides preguntar algo que lo identifique como suyo (stickers, fondo de pantalla, rayones o quebraduras, entre otras).</p>
    <p>En caso contrario, no olvides dar estas características para poder recuperar tus pertenencias.</p>
    <p style="font-weight: bold;">Comunicate con esta persona a este correo:</p>
    <ul>
        <li>Correo: {{ $user->correo }}</li>
    </ul>
    @if ($mensaje)
        <p>El usuario escribe lo siguiente: </p>
        <p>{{ $mensaje }}</p>
    @endif
    <p>El usuario te entregara un folio con el que podras cerrar la publicación.</p>
    <img src="https://scontent.fgdl5-4.fna.fbcdn.net/v/t39.30808-6/278333227_113225811353608_4409159446750627371_n.png?_nc_cat=111&ccb=1-5&_nc_sid=09cbfe&_nc_eui2=AeEGzfUOLyrvGTFvKAQM_dHDUvQ6FWObWDZS9DoVY5tYNnDgnpd6oEXBjsBZqTdwTIxaTB0lqaJDU4mmDfHKcJtZ&_nc_ohc=6qDhYQkQNm0AX8TdBVG&_nc_ht=scontent.fgdl5-4.fna&oh=00_AT8eOSOJ4gbw65pCtdCJ4cx71vmoJDQKXa5o9h-CCQFtpw&oe=62727C09" alt="">
</body>
</html>