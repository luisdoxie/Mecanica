<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $config = DB::table('config_taller')->pluck('valor', 'clave');
        return view('admin.configuracion.index', compact('config'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|string|max:200',
            'direccion' => 'required|string|max:500',
            'telefono'  => 'required|string|max:50',
            'horario'   => 'required|string|max:200',
            'email'     => 'nullable|email|max:150',
            'slogan'    => 'nullable|string|max:300',
            'qr_imagen' => 'nullable|image|max:4096',
        ]);

        $campos = ['nombre', 'direccion', 'telefono', 'horario', 'email', 'slogan'];

        foreach ($campos as $clave) {
            DB::table('config_taller')->updateOrInsert(
                ['clave' => $clave],
                ['valor' => $request->get($clave, ''), 'updated_at' => now(), 'created_at' => now()]
            );
        }

        if ($request->hasFile('qr_imagen')) {
            $result = Cloudinary::upload($request->file('qr_imagen')->getRealPath(), [
                'folder' => 'taller/qr',
            ]);
            DB::table('config_taller')->updateOrInsert(
                ['clave' => 'qr_imagen_url'],
                ['valor' => $result->getSecurePath(), 'updated_at' => now(), 'created_at' => now()]
            );
        }

        ActivityLogger::log('Configuración del taller actualizada', 'Configuracion');

        return back()->with('success', 'Configuración guardada correctamente.');
    }
}
