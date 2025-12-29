<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use App\Models\Evidencia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ReporteController extends Controller
{
    /**
     * Constructor - Proteger rutas con permisos
     */
    public function __construct()
    {
        $this->middleware('auth');

        // Permisos específicos
        $this->middleware('permission:ver-reportes')->only(['index', 'show']);
        $this->middleware('permission:crear-reportes')->only(['create', 'store']);
        $this->middleware('permission:editar-reportes')->only(['edit', 'update']);
        $this->middleware('permission:eliminar-reportes')->only(['destroy']);
        $this->middleware('permission:asignar-reportes')->only(['asignar', 'updateAsignacion']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = Reporte::query()->with(['cliente', 'tecnico', 'evidencias']);

        // FILTROS COMUNES
        if ($request->has('estado') && $request->estado != 'todos') {
            $query->where('estado', $request->estado);
        }

        if ($request->has('prioridad') && $request->prioridad != 'todos') {
            $query->where('prioridad', $request->prioridad);
        }

        // FILTRAR SEGÚN ROL
        if ($user->hasRole('cliente')) {
            // Cliente solo ve SUS reportes
            $query->where('user_id', $user->id);

        } elseif ($user->hasRole('tecnico')) {
            // Técnico ve reportes ASIGNADOS a él
            $query->where('tecnico_asignado_id', $user->id);

        } elseif ($user->hasRole('administrador') || $user->hasRole('super_admin')) {
            // Administradores ven todos (ya está por defecto)
        } else {
            // Por seguridad, si no tiene rol conocido, solo sus reportes
            $query->where('user_id', $user->id);
        }

        // ORDENAR
        $query->latest();

        $reportes = $query->paginate(15);

        return view('reportes.index', compact('reportes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = auth()->user();

        // Validar que sea cliente y pueda crear reportes
        if (!$user->hasRole('cliente')) {
            abort(403, 'Solo los clientes pueden crear reportes');
        }

        // Usar el atributo del modelo User (que SÍ existe)
        if (!$user->puede_crear_reporte) {
            return redirect()->route('reportes.index')
                ->with('error', 'Has alcanzado el límite de 3 reportes activos. Espera a que se resuelvan algunos.');
        }

        return view('reportes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        // Validar que sea cliente
        if (!$user->hasRole('cliente')) {
            abort(403, 'Solo los clientes pueden crear reportes');
        }

        // Validar límite de 3 reportes activos
        if (!$user->puede_crear_reporte) {
            return redirect()->route('reportes.index')
                ->with('error', 'Has alcanzado el límite de 3 reportes activos. Espera a que se resuelvan algunos.');
        }

        // Validación
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|min:10|max:1000',
            'direccion' => 'required|string|max:500',
            'prioridad' => 'required|in:alta,media,baja',
            'evidencias.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120' // 5MB máximo
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Crear reporte
        $reporte = Reporte::create([
            'user_id' => $user->id,
            'descripcion' => $request->descripcion,
            'direccion' => $request->direccion,
            'prioridad' => $request->prioridad,
            'estado' => 'pendiente'
        ]);

        // Subir evidencias (fotos)
        if ($request->hasFile('evidencias')) {
            foreach ($request->file('evidencias') as $index => $file) {
                if ($file->isValid()) {
                    $path = $file->store('reportes/' . $reporte->codigo, 'public');

                    Evidencia::create([
                        'reporte_id' => $reporte->id,
                        'imagen_path' => $path,
                        'tipo' => 'antes',
                        'descripcion' => $request->input("descripcion_evidencia.$index", 'Evidencia del reporte')
                    ]);
                }
            }
        }

        return redirect()->route('reportes.show', $reporte->id)
            ->with('success', 'Reporte creado exitosamente. Código: ' . $reporte->codigo);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $user = auth()->user();
        $reporte = Reporte::with(['cliente', 'tecnico', 'evidencias'])->findOrFail($id);

        // Validar permisos para ver este reporte
        if (!$this->puedeVerReporte($user, $reporte)) {
            abort(403, 'No tienes permiso para ver este reporte');
        }

        // Obtener técnicos disponibles para asignar (solo admin)
        $tecnicosDisponibles = [];
        if ($user->can('asignar-reportes') && $reporte->estado == 'pendiente') {
            $tecnicosDisponibles = User::tecnicosDisponibles()->get();
        }

        return view('reportes.show', compact('reporte', 'tecnicosDisponibles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $user = auth()->user();
        $reporte = Reporte::findOrFail($id);

        // Validar permisos para editar
        if (!$this->puedeEditarReporte($user, $reporte)) {
            abort(403, 'No tienes permiso para editar este reporte');
        }

        return view('reportes.edit', compact('reporte'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $user = auth()->user();
        $reporte = Reporte::findOrFail($id);

        // DEBUG: Ver qué datos llegan
        \Log::info('Reporte Update - Datos recibidos:', $request->all());

        // Validar permisos para editar
        if (!$this->puedeEditarReporte($user, $reporte)) {
            abort(403, 'No tienes permiso para editar este reporte');
        }

        // Reglas de validación BASE (para todos)
        $rules = [
            'descripcion' => 'required|string|min:10|max:1000',
            'direccion' => 'required|string|max:500',
        ];

        // Cliente solo puede editar descripción, dirección y prioridad si está pendiente
        if ($user->hasRole('cliente') && $reporte->user_id == $user->id) {
            if ($reporte->estado != 'pendiente') {
                return back()->with('error', 'No puedes editar un reporte que ya está en proceso');
            }
            // ¡AGREGAR PRIORIDAD para clientes!
            $rules['prioridad'] = 'required|in:alta,media,baja';
        }

        // Técnico puede actualizar estado y solución
        if ($user->hasRole('tecnico') && $reporte->tecnico_asignado_id == $user->id) {
            $rules['estado'] = 'required|in:en_proceso,resuelto,cancelado';
            $rules['solucion'] = 'required_if:estado,resuelto,cancelado|string|max:1000';
        }

        // Administradores pueden editar todo
        if ($user->hasRole('administrador') || $user->hasRole('super_admin')) {
            $rules['prioridad'] = 'required|in:alta,media,baja';
            $rules['estado'] = 'required|in:pendiente,asignado,en_proceso,resuelto,cancelado';
            $rules['solucion'] = 'nullable|string|max:1000';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            \Log::error('Validación falló:', $validator->errors()->toArray());
            return back()->withErrors($validator)->withInput();
        }

        // Preparar datos para actualizar
        $data = [];

        // Cliente actualiza descripción, dirección y prioridad
        if ($user->hasRole('cliente')) {
            $data = [
                'descripcion' => $request->descripcion,
                'direccion' => $request->direccion,
                'prioridad' => $request->prioridad // ¡IMPORTANTE!
            ];
        }

        // Técnico actualiza estado y solución
        if ($user->hasRole('tecnico')) {
            $data = [
                'estado' => $request->estado,
                'solucion' => $request->solucion
            ];

            // Si se marca como resuelto, poner fecha de cierre
            if ($request->estado == 'resuelto' && !$reporte->fecha_cierre) {
                $data['fecha_cierre'] = now();
            }

            // Subir evidencias del técnico
            if ($request->hasFile('evidencias_tecnico')) {
                foreach ($request->file('evidencias_tecnico') as $file) {
                    if ($file->isValid()) {
                        $path = $file->store('reportes/' . $reporte->codigo . '/tecnico', 'public');

                        Evidencia::create([
                            'reporte_id' => $reporte->id,
                            'imagen_path' => $path,
                            'tipo' => 'despues',
                            'descripcion' => 'Evidencia del trabajo realizado'
                        ]);
                    }
                }
            }
        }

        // Administradores actualizan todo
        if ($user->hasRole('administrador') || $user->hasRole('super_admin')) {
            $data = $request->only(['descripcion', 'direccion', 'prioridad', 'estado', 'solucion']);

            if ($request->estado == 'resuelto' && !$reporte->fecha_cierre) {
                $data['fecha_cierre'] = now();
            }
        }

        // DEBUG: Ver qué se va a actualizar
        \Log::info('Datos a actualizar:', $data);

        // Actualizar reporte
        $reporte->update($data);

        // DEBUG: Ver resultado
        \Log::info('Reporte actualizado. Nuevos valores:', $reporte->toArray());

        return redirect()->route('reportes.show', $reporte->id)
            ->with('success', 'Reporte actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $user = auth()->user();
        $reporte = Reporte::findOrFail($id);

        // Solo administradores pueden eliminar reportes
        if (!$user->hasRole('administrador') && !$user->hasRole('super_admin')) {
            abort(403, 'Solo administradores pueden eliminar reportes');
        }

        // No eliminar reportes resueltos o en proceso
        if (!in_array($reporte->estado, ['pendiente', 'cancelado'])) {
            return redirect()->route('reportes.show', $reporte->id)
                ->with('error', 'No se pueden eliminar reportes en proceso o resueltos');
        }

        // Eliminar evidencias (Storage)
        foreach ($reporte->evidencias as $evidencia) {
            Storage::disk('public')->delete($evidencia->imagen_path);
        }

        $reporte->delete();

        return redirect()->route('reportes.index')
            ->with('success', 'Reporte eliminado exitosamente');
    }

    /**
     * Asignar técnico a un reporte (para administradores)
     */
    public function asignar(Request $request, $id): RedirectResponse
    {
        $user = auth()->user();

        if (!$user->can('asignar-reportes')) {
            abort(403, 'No tienes permiso para asignar reportes');
        }

        $reporte = Reporte::findOrFail($id);

        // Validar que el reporte esté pendiente
        if ($reporte->estado != 'pendiente') {
            return back()->with('error', 'Solo se pueden asignar reportes pendientes');
        }

        $validator = Validator::make($request->all(), [
            'tecnico_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Verificar que el usuario sea técnico
        $tecnico = User::find($request->tecnico_id);
        if (!$tecnico->hasRole('tecnico')) {
            return back()->with('error', 'El usuario seleccionado no es un técnico');
        }

        // Asignar
        $reporte->update([
            'tecnico_asignado_id' => $tecnico->id,
            'estado' => 'asignado'
        ]);

        return redirect()->route('reportes.show', $reporte->id)
            ->with('success', 'Técnico asignado exitosamente');
    }

    /**
     * Cambiar estado del reporte (para técnicos)
     */
    public function cambiarEstado(Request $request, $id): RedirectResponse
    {
        $user = auth()->user();
        $reporte = Reporte::findOrFail($id);

        // Validar que el técnico esté asignado a este reporte
        if (!$user->hasRole('tecnico') || $reporte->tecnico_asignado_id != $user->id) {
            abort(403, 'No tienes permiso para cambiar el estado de este reporte');
        }

        $validator = Validator::make($request->all(), [
            'estado' => 'required|in:en_proceso,resuelto'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $data = ['estado' => $request->estado];

        // Si se marca como resuelto, poner fecha de cierre
        if ($request->estado == 'resuelto') {
            $data['fecha_cierre'] = now();
            $data['solucion'] = $request->solucion ?: 'Reporte resuelto por el técnico';
        }

        $reporte->update($data);

        return redirect()->route('reportes.show', $reporte->id)
            ->with('success', 'Estado actualizado exitosamente');
    }

    /**
     * Métodos auxiliares de permisos
     */
    private function puedeVerReporte($user, $reporte): bool
    {
        // Super admin y administradores ven todo
        if ($user->hasRole('administrador') || $user->hasRole('super_admin')) {
            return true;
        }

        // Cliente ve sus reportes
        if ($user->hasRole('cliente') && $reporte->user_id == $user->id) {
            return true;
        }

        // Técnico ve reportes asignados a él
        if ($user->hasRole('tecnico') && $reporte->tecnico_asignado_id == $user->id) {
            return true;
        }

        return false;
    }

    private function puedeEditarReporte($user, $reporte): bool
    {
        // Administradores pueden editar cualquier reporte
        if ($user->hasRole('administrador') || $user->hasRole('super_admin')) {
            return true;
        }

        // Cliente puede editar SUS reportes si están pendientes
        if ($user->hasRole('cliente') && $reporte->user_id == $user->id) {
            return $reporte->estado == 'pendiente';
        }

        // Técnico puede editar reportes asignados a él
        if ($user->hasRole('tecnico') && $reporte->tecnico_asignado_id == $user->id) {
            return in_array($reporte->estado, ['asignado', 'en_proceso']);
        }

        return false;
    }
}