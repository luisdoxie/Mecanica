<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="font-family: Arial, sans-serif; background:#f3f4f6; margin:0; padding:20px;">
    <div style="max-width:520px; margin:0 auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.1);">
        <div style="background:#1f2937; padding:28px; text-align:center;">
            <h1 style="color:#fff; font-size:20px; margin:8px 0 0;">Taller Mecanico Eusebio</h1>
        </div>
        <div style="padding:32px;">
            <h2 style="color:#1f2937; font-size:22px; margin:0 0 16px;">Su vehiculo esta listo</h2>
            <p style="color:#4b5563; line-height:1.6; margin:0 0 20px;">
                Estimado/a <strong>{{ $orden->vehiculo->cliente->persona->nombre ?? 'cliente' }}</strong>,
                le informamos que su vehiculo ha sido atendido y esta listo para ser retirado en nuestras instalaciones.
            </p>
            <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:16px; margin-bottom:24px;">
                <p style="margin:4px 0; color:#6b7280; font-size:14px;">Vehiculo</p>
                <p style="margin:4px 0; color:#1f2937; font-weight:bold;">
                    {{ $orden->vehiculo->marca }} {{ $orden->vehiculo->modelo }} &mdash;
                    <span style="font-family:monospace;">{{ $orden->vehiculo->placa }}</span>
                </p>
                <p style="margin:12px 0 4px; color:#6b7280; font-size:14px;">Orden N&deg;</p>
                <p style="margin:4px 0; color:#1f2937; font-weight:bold;">{{ $orden->id }}</p>
            </div>
            <p style="color:#4b5563; font-size:14px; line-height:1.6;">
                <strong>Direccion:</strong> Av. Principal, Local 4B<br>
                <strong>Horario:</strong> Lun &ndash; Vie 8am &ndash; 6pm | Sab 8am &ndash; 1pm<br>
                <strong>Telefono:</strong> +58 412-555-0100
            </p>
        </div>
        <div style="background:#f9fafb; border-top:1px solid #e5e7eb; padding:16px; text-align:center;">
            <p style="color:#9ca3af; font-size:12px; margin:0;">&copy; {{ date('Y') }} Taller Mecanico Eusebio. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
