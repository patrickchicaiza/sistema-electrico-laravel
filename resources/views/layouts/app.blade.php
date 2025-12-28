<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistema Eléctrico') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos personalizados -->
    <style>
        .navbar-brand {
            font-weight: bold;
            color: #fff !important;
        }

        .sidebar {
            min-height: calc(100vh - 56px);
            background: #343a40;
            padding-top: 20px;
        }

        .sidebar .nav-link {
            color: #adb5bd;
            padding: 10px 20px;
            margin: 5px 0;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background: #495057;
        }

        .sidebar .nav-link.active {
            color: #fff;
            background: #007bff;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .badge-estado {
            font-size: 0.8em;
            padding: 5px 10px;
        }
    </style>
</head>

<body>
    <!-- Navbar Principal -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-bolt"></i> Sistema Eléctrico
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Menú izquierda -->
                <ul class="navbar-nav me-auto">
                    @auth
                        <!-- Opciones según rol -->
                        @if(auth()->user()->es_cliente)
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                                    href="{{ route('dashboard') }}">
                                    <i class="fas fa-home"></i> Inicio
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}"
                                    href="{{ route('reportes.index') }}">
                                    <i class="fas fa-list"></i> Mis Reportes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reportes.create') ? 'active' : '' }}"
                                    href="{{ route('reportes.create') }}">
                                    <i class="fas fa-plus-circle"></i> Nuevo Reporte
                                </a>
                            </li>
                        @endif

                        @if(auth()->user()->es_tecnico)
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                                    href="{{ route('dashboard') }}">
                                    <i class="fas fa-home"></i> Inicio
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}"
                                    href="{{ route('reportes.index') }}">
                                    <i class="fas fa-tasks"></i> Reportes Asignados
                                </a>
                            </li>
                        @endif

                        @if(auth()->user()->es_administrador)
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                                    href="{{ route('dashboard') }}">
                                    <i class="fas fa-home"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}"
                                    href="{{ route('reportes.index') }}">
                                    <i class="fas fa-clipboard-list"></i> Todos los Reportes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                                    href="{{ route('users.index') }}">
                                    <i class="fas fa-users"></i> Usuarios
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}"
                                    href="{{ route('roles.index') }}">
                                    <i class="fas fa-user-shield"></i> Roles
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <!-- Menú derecha (usuario) -->
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Registro</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                                <span class="badge bg-info ms-1">
                                    {{ auth()->user()->getRoleNames()->first() }}
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-user"></i> Mi Perfil
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar solo para usuarios autenticados -->
            @auth
                <div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse" id="sidebarMenu">
                    <div class="position-sticky pt-3">
                        <ul class="nav flex-column">
                            <!-- Estadísticas rápidas según rol -->
                            @if(auth()->user()->es_cliente)
                                <li class="nav-item mb-2">
                                    <div class="text-white px-3">
                                        <small class="text-muted">Reportes activos</small>
                                        <h5 class="mb-0">{{ auth()->user()->reportes_activos_count }}/3</h5>
                                    </div>
                                </li>
                            @endif

                            @if(auth()->user()->es_tecnico)
                                <li class="nav-item mb-2">
                                    <div class="text-white px-3">
                                        <small class="text-muted">Asignados</small>
                                        <h5 class="mb-0">
                                            {{ auth()->user()->reportesComoTecnico()->whereIn('estado', ['asignado', 'en_proceso'])->count() }}
                                        </h5>
                                    </div>
                                </li>
                            @endif

                            @if(auth()->user()->es_administrador)
                                <li class="nav-item mb-2">
                                    <div class="text-white px-3">
                                        <small class="text-muted">Reportes pendientes</small>
                                        <h5 class="mb-0">{{ \App\Models\Reporte::pendientes()->count() }}</h5>
                                    </div>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            @endauth

            <!-- Contenido dinámico -->
            <main class="@auth col-md-9 col-lg-10 ms-sm-auto @else col-12 @endauth px-md-4 py-4">
                <!-- Mensajes de sesión -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Contenido específico de cada vista -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Scripts personalizados -->
    <script>
        // Activar tooltips
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Auto-ocultar alerts después de 5 segundos
        setTimeout(function () {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function (alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>

</html>