<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RoleController extends Controller
{
    // Roles protegidos del sistema (no se pueden modificar/eliminar)
    private $rolesProtegidos = ['super_admin', 'administrador', 'tecnico', 'cliente'];

    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver-roles')->only(['index', 'show']);
        $this->middleware('permission:crear-roles')->only(['create', 'store']);
        $this->middleware('permission:editar-roles')->only(['edit', 'update']);
        $this->middleware('permission:eliminar-roles')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $query = Role::query()->with('permissions');

        // Super admin ve todos los roles
        if (!$user->hasRole('super_admin')) {
            // Otros no ven el rol super_admin
            $query->where('name', '!=', 'super_admin');
        }

        $roles = $query->orderBy('id', 'DESC')->paginate(10);

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = auth()->user();
        $permissions = Permission::orderBy('name')->get()->groupBy(function ($permission) {
            // Agrupar permisos por categoría (basado en el nombre)
            $parts = explode('-', $permission->name);
            return $parts[0] ?? 'general';
        });

        // Filtrar permisos según usuario
        if (!$user->hasRole('super_admin')) {
            // No permitir crear roles con permisos peligrosos
            $permissions = $permissions->map(function ($group) {
                return $group->filter(function ($permission) {
                    return !in_array($permission->name, [
                        'eliminar-usuarios',
                        'eliminar-roles',
                        'eliminar-reportes'
                    ]);
                });
            })->filter(function ($group) {
                return $group->count() > 0;
            });
        }

        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $this->validate($request, [
            'name' => 'required|unique:roles,name|regex:/^[a-z0-9_-]+$/|min:3|max:50',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id'
        ], [
            'name.regex' => 'El nombre solo puede contener letras minúsculas, números, guiones y guiones bajos',
            'permissions.required' => 'Debe seleccionar al menos un permiso'
        ]);

        // Validar que no intente crear roles protegidos
        $nombreRol = strtolower($request->name);
        if (in_array($nombreRol, $this->rolesProtegidos)) {
            return back()->withErrors([
                'name' => 'No puedes crear un rol con nombre reservado. Nombres reservados: ' .
                    implode(', ', $this->rolesProtegidos)
            ])->withInput();
        }

        // Validar permisos según usuario
        if (!$user->hasRole('super_admin')) {
            $permisosProhibidos = Permission::whereIn('name', [
                'eliminar-usuarios',
                'eliminar-roles',
                'eliminar-reportes'
            ])->pluck('id')->toArray();

            if (array_intersect($request->permissions, $permisosProhibidos)) {
                return back()->withErrors([
                    'permissions' => 'No tienes permiso para asignar permisos de eliminación'
                ])->withInput();
            }
        }

        // Crear rol
        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')
            ->with('success', 'Rol "' . $role->name . '" creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $role = Role::with('permissions')->findOrFail($id);
        $user = auth()->user();

        // Validar acceso
        if ($role->name == 'super_admin' && !$user->hasRole('super_admin')) {
            abort(403, 'No autorizado para ver este rol');
        }

        // Agrupar permisos para mostrar mejor
        $permisosAgrupados = $role->permissions->groupBy(function ($permission) {
            $parts = explode('-', $permission->name);
            return $parts[0] ?? 'general';
        });

        return view('roles.show', compact('role', 'permisosAgrupados'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $role = Role::with('permissions')->findOrFail($id);
        $user = auth()->user();

        // Validar que no sea rol protegido
        if (in_array($role->name, $this->rolesProtegidos)) {
            abort(403, 'No puedes editar roles del sistema: ' . $role->name);
        }

        // Validar que no intente editar super_admin
        if ($role->name == 'super_admin' && !$user->hasRole('super_admin')) {
            abort(403, 'No autorizado para editar este rol');
        }

        $permissions = Permission::orderBy('name')->get()->groupBy(function ($permission) {
            $parts = explode('-', $permission->name);
            return $parts[0] ?? 'general';
        });

        // Filtrar permisos según usuario
        if (!$user->hasRole('super_admin')) {
            $permissions = $permissions->map(function ($group) {
                return $group->filter(function ($permission) {
                    return !in_array($permission->name, [
                        'eliminar-usuarios',
                        'eliminar-roles',
                        'eliminar-reportes'
                    ]);
                });
            })->filter(function ($group) {
                return $group->count() > 0;
            });
        }

        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $user = auth()->user();
        $role = Role::findOrFail($id);

        // Validar que no sea rol protegido
        if (in_array($role->name, $this->rolesProtegidos)) {
            return redirect()->route('roles.index')
                ->with('error', 'No puedes editar roles del sistema: ' . $role->name);
        }

        $this->validate($request, [
            'name' => 'required|regex:/^[a-z0-9_-]+$/|min:3|max:50|unique:roles,name,' . $id,
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id'
        ], [
            'name.regex' => 'El nombre solo puede contener letras minúsculas, números, guiones y guiones bajos'
        ]);

        // Validar permisos según usuario
        if (!$user->hasRole('super_admin')) {
            $permisosProhibidos = Permission::whereIn('name', [
                'eliminar-usuarios',
                'eliminar-roles',
                'eliminar-reportes'
            ])->pluck('id')->toArray();

            if (array_intersect($request->permissions, $permisosProhibidos)) {
                return back()->withErrors([
                    'permissions' => 'No tienes permiso para asignar permisos de eliminación'
                ])->withInput();
            }
        }

        // Actualizar
        $role->name = $request->name;
        $role->save();

        $role->syncPermissions($request->permissions);

        return redirect()->route('roles.index')
            ->with('success', 'Rol "' . $role->name . '" actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $role = Role::findOrFail($id);
        $user = auth()->user();

        // Validar que no sea rol protegido
        if (in_array($role->name, $this->rolesProtegidos)) {
            return redirect()->route('roles.index')
                ->with('error', 'No puedes eliminar roles del sistema: ' . $role->name);
        }

        // Validar que no tenga usuarios asignados
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'No puedes eliminar un rol con usuarios asignados. ' .
                    'Reasigna los usuarios primero.');
        }

        // Solo super_admin puede eliminar roles
        if (!$user->hasRole('super_admin')) {
            abort(403, 'No autorizado para eliminar roles');
        }

        $nombreRol = $role->name;
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Rol "' . $nombreRol . '" eliminado exitosamente');
    }

    /**
     * API: Obtener permisos de un rol (para AJAX)
     */
    public function getPermisos($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        return response()->json([
            'permissions' => $role->permissions->pluck('id')
        ]);
    }
}