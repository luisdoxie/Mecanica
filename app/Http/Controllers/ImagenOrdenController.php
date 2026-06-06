<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\ImagenOrden;
use App\Models\OrdenTrabajo;
use Illuminate\Http\Request;

class ImagenOrdenController extends Controller
{
    public function store(Request $request, OrdenTrabajo $orden)
    {
        $request->validate([
            'foto'       => 'required|image|max:5120',
            'tipo'       => 'required|in:RECEPCION,DAÑO,REPUESTO,ENTREGA',
            'descripcion'=> 'nullable|string|max:200',
        ], [
            'foto.required' => 'Seleccione una imagen.',
            'foto.image'    => 'El archivo debe ser una imagen.',
            'foto.max'      => 'La imagen no debe superar 5MB.',
            'tipo.required' => 'Seleccione el tipo de foto.',
        ]);

        try {
            $resultado = cloudinary()->uploadApi()->upload($request->file('foto')->getRealPath(), [
                'folder'        => 'taller-mecanico/ordenes/' . $orden->id,
                'resource_type' => 'image',
            ]);

            $empleado = Empleado::where('persona_id', auth()->user()->persona_id)->first();

            ImagenOrden::create([
                'orden_trabajo_id' => $orden->id,
                'tipo'             => $request->tipo,
                'cloudinary_url'   => $resultado['secure_url'],
                'cloudinary_id'    => $resultado['public_id'],
                'descripcion'      => $request->descripcion,
                'subida_por'       => $empleado?->id,
            ]);

            return back()->with('success', 'Foto subida correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al subir la imagen: ' . $e->getMessage());
        }
    }

    public function destroy(OrdenTrabajo $orden, ImagenOrden $imagen)
    {
        try {
            if ($imagen->cloudinary_id) {
                cloudinary()->uploadApi()->destroy($imagen->cloudinary_id);
            }
        } catch (\Exception $e) {
            // Continuar aunque Cloudinary falle
        }

        $imagen->delete();
        return back()->with('success', 'Foto eliminada.');
    }
}
