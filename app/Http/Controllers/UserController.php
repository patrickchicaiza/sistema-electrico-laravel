<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    // Middleware para proteger TODAS las rutas
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:ver-usuarios')->only('index', 'show');
        $this->middleware('permission:crear-usuarios')->only('create', 'store');
        $this->middleware('permission:editar-usuarios')->only('edit', 'update');
        $this->middleware('permission:eliminar-usuarios')->only('destroy');
    }

    public function index(Request $request): View
    {
        $userActual = auth()->user();

        // Lógica de visibilidad mejorada
        if ($userActual->hasRole('super_admin') || $userActual->hasRole('administrador')) {
            // Admin ve todos excepto super_admins (a menos que sea super_admin)
            $query = User::query();

            if (!$userActual->hasRole('super_admin')) {
                // Administrador normal NO ve super_admins
                $query->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'super_admin');
                });
            }

            $users = $query->latest()->paginate(10);
        } elseif ($userActual->hasRole('tecnico')) {
            // Técnico se ve a sí mismo y a administradores (para contactar)
            $users = User::where('id', $userActual->id)
                ->orWhereHas('roles', function ($q) {
                    $q->whereIn('name', ['administrador', 'super_admin']);
                })
                ->get();
        } else {
            // Cliente solo se ve a sí mismo
            $users = User::where('id', $userActual->id)->get();
        }

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $userActual = auth()->user();
        $rolesDisponibles = Role::pluck('name', 'name');

        // Filtrar roles según permisos
        if (!$userActual->hasRole('super_admin')) {
            // Quitar super_admin si no es super_admin
            $rolesDisponibles = $rolesDisponibles->except('super_admin');
        }

        // Si es administrador normal, solo puede crear: cliente, tecnico
        if ($userActual->hasRole('administrador') && !$userActual->hasRole('super_admin')) {
            $rolesDisponibles = $rolesDisponibles->only(['cliente', 'tecnico']);
        }

        return view('users.create', compact('rolesDisponibles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $userActual = auth()->user();

        // Validación básica
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:8',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'roles' => 'required|array',
            'roles.*' => 'in:cliente,tecnico,administrador,super_admin'
        ]);

        // Validación de seguridad: ¿puede asignar estos roles?
        $rolesSolicitados = $request->roles;

        if (!$userActual->hasRole('super_admin')) {
            // No super_admin no puede asignar super_admin
            if (in_array('super_admin', $rolesSolicitados)) {
                return back()->withErrors(['roles' => 'No tienes permiso para asignar rol super_admin']);
            }

            // Administrador normal solo puede asignar cliente/tecnico
            if ($userActual->hasRole('administrador')) {
                $rolesPermitidos = ['cliente', 'tecnico'];
                if (array_diff($rolesSolicitados, $rolesPermitidos)) {
                    return back()->withErrors(['roles' => 'Solo puedes asignar roles: cliente o técnico']);
                }
            }
        }

        // Crear usuario
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'telefono' => $validated['telefono'] ?? null,
            'direccion' => $validated['direccion'] ?? null,
        ]);

        // Asignar roles
        $user->assignRole($rolesSolicitados);

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado correctamente');
    }

    // ... (show, edit, update, destroy similares con estas validaciones)


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id): View
    {
        $userActual = auth()->user();
        $user = User::findOrFail($id);

        // Validar que el usuario actual PUEDE ver este usuario
        if (!$this->puedeVerUsuario($userActual, $user)) {
            abort(403, 'No autorizado para ver este usuario');
        }

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id): View
    {
        $userActual = auth()->user();
        $user = User::findOrFail($id);

        // Validar que el usuario actual PUEDE editar este usuario
        if (!$this->puedeEditarUsuario($userActual, $user)) {
            abort(403, 'No autorizado para editar este usuario');
        }

        // Obtener roles disponibles según permisos
        $rolesDisponibles = Role::pluck('name', 'name');

        // Filtrar roles según permisos del usuario actual
        if (!$userActual->hasRole('super_admin')) {
            $rolesDisponibles = $rolesDisponibles->except('super_admin');
        }

        // Si es administrador normal, solo puede asignar: cliente, tecnico
        if ($userActual->hasRole('administrador') && !$userActual->hasRole('super_admin')) {
            $rolesDisponibles = $rolesDisponibles->only(['cliente', 'tecnico']);
        }

        $userRole = $user->roles->pluck('name', 'name')->all();

        return view('users.edit', compact('user', 'rolesDisponibles', 'userRole'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $userActual = auth()->user();
        $user = User::findOrFail($id);

        // Validar que el usuario actual PUEDE editar este usuario
        if (!$this->puedeEditarUsuario($userActual, $user)) {
            abort(403, 'No autorizado para editar este usuario');
        }

        // Validación básica
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|confirmed|min:8',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'roles' => 'required|array',
            'roles.*' => 'in:cliente,tecnico,administrador,super_admin'
        ]);

        // Validación de seguridad: ¿puede asignar estos roles?
        $rolesSolicitados = $request->roles;

        if (!$userActual->hasRole('super_admin')) {
            // No super_admin no puede asignar super_admin
            if (in_array('super_admin', $rolesSolicitados)) {
                return back()->withErrors(['roles' => 'No tienes permiso para asignar rol super_admin']);
            }

            // Administrador normal solo puede asignar cliente/tecnico
            if ($userActual->hasRole('administrador')) {
                $rolesPermitidos = ['cliente', 'tecnico'];
                if (array_diff($rolesSolicitados, $rolesPermitidos)) {
                    return back()->withErrors(['roles' => 'Solo puedes asignar roles: cliente o técnico']);
                }
            }

            // Usuario normal no puede cambiarse a sí mismo a administrador
            if ($userActual->id == $id && in_array('administrador', $rolesSolicitados)) {
                return back()->withErrors(['roles' => 'No puedes cambiarte a administrador']);
            }
        }

        // Preparar datos para actualizar
        $datosActualizar = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'telefono' => $validated['telefono'] ?? null,
            'direccion' => $validated['direccion'] ?? null,
        ];

        // Actualizar contraseña solo si se proporcionó
        if (!empty($validated['password'])) {
            $datosActualizar['password'] = Hash::make($validated['password']);
        }

        // Actualizar usuario
        $user->update($datosActualizar);

        // Sincronizar roles (elimina los antiguos y asigna los nuevos)
        $user->syncRoles($rolesSolicitados);

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado correctamente');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $userActual = auth()->user();
        $user = User::findOrFail($id);

        // Validar que el usuario actual PUEDE eliminar este usuario
        if (!$this->puedeEliminarUsuario($userActual, $user)) {
            abort(403, 'No autorizado para eliminar este usuario');
        }

        // No permitir eliminarse a sí mismo
        if ($userActual->id == $id) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes eliminarte a ti mismo');
        }

        // No permitir eliminar super_admin a menos que sea super_admin
        if ($user->hasRole('super_admin') && !$userActual->hasRole('super_admin')) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes eliminar a un super administrador');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado correctamente');
    }

    /**
     * Métodos auxiliares para validar permisos
     */
    private function puedeVerUsuario($userActual, $userAVer): bool
    {
        // Super admin puede ver a todos
        if ($userActual->hasRole('super_admin')) {
            return true;
        }

        // Administrador puede ver a todos excepto super_admins
        if ($userActual->hasRole('administrador')) {
            return !$userAVer->hasRole('super_admin');
        }

        // Técnico puede verse a sí mismo y a administradores
        if ($userActual->hasRole('tecnico')) {
            return $userAVer->id == $userActual->id ||
                $userAVer->hasRole('administrador') ||
                $userAVer->hasRole('super_admin');
        }

        // Cliente solo puede verse a sí mismo
        return $userAVer->id == $userActual->id;
    }

    private function puedeEditarUsuario($userActual, $userAEditar): bool
    {
        // No se puede editar a super_admin a menos que seas super_admin
        if ($userAEditar->hasRole('super_admin') && !$userActual->hasRole('super_admin')) {
            return false;
        }

        // Super admin puede editar a todos
        if ($userActual->hasRole('super_admin')) {
            return true;
        }

        // Administrador puede editar clientes y técnicos
        if ($userActual->hasRole('administrador')) {
            return $userAEditar->hasRole('cliente') ||
                $userAEditar->hasRole('tecnico');
        }

        // Usuarios solo pueden editar su propia cuenta
        return $userAEditar->id == $userActual->id;
    }

    private function puedeEliminarUsuario($userActual, $userAEliminar): bool
    {
        // Reglas similares a puedeEditarUsuario pero más restrictivas
        if ($userAEliminar->hasRole('super_admin')) {
            return $userActual->hasRole('super_admin') && $userActual->id != $userAEliminar->id;
        }

        if ($userActual->hasRole('super_admin')) {
            return $userActual->id != $userAEliminar->id;
        }

        if ($userActual->hasRole('administrador')) {
            return !$userAEliminar->hasRole('administrador') &&
                !$userAEliminar->hasRole('super_admin') &&
                $userActual->id != $userAEliminar->id;
        }

        return false; // Clientes y técnicos no pueden eliminar usuarios
    }
}