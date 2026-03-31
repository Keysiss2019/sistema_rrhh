<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Metadatos esenciales -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Título de la página -->
    <title>SISTEMA RRHH - IHCI</title>
    
    <!-- Bootstrap CSS desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- CSS de Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- CSS personalizado del proyecto -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="{{ Route::is('login') ? 'auth-body' : '' }}">
   
@auth
@if(!Route::is('login') && !Route::is('password.cambiar'))
    <!-- ===========================
         Header principal / Navbar
         =========================== -->
    <header class="navbar-ihci sticky-top shadow-sm">
        <div class="nav-flex-container">
            
            <!-- Logo del IHCI -->
            <div class="logo-nav">
                <a href="{{ url('dashboard') }}">
                    <img src="{{ asset('images/IHCI.png') }}" alt="IHCI">
                </a>
            </div>

            <!-- Menú de navegación principal -->
            <nav class="nav-modules">
               @if(auth()->user()->role_id == 1)
                  <!-- Módulo Seguridad -->
                  <div class="menu-item">
                     <i class="fa-solid fa-shield-halved"></i> Seguridad <i class="fa-solid fa-chevron-down small"></i>
                     <div class="submenu">
                         <a href="{{ route('roles.index') }}" class="submenu-item">Roles</a>
                         <a href="{{ route('departamentos.index') }}" class="submenu-item">Departamentos</a>
                         <a href="{{ route('permisos_sistema.index') }}" class="submenu-item">Permisos del sistema</a>
                         <a href="{{ route('usuarios.index') }}" class="submenu-item">Usuarios</a>
                         <a href="{{ route('firmas.index') }}" class="submenu-item">Gestión de Firmas</a>

                        </div>
                  </div>

                   <div class="menu-item">
                      <i class="fa-solid fa-gears"></i> Administración <i class="fa-solid fa-chevron-down small"></i>
                      <div class="submenu">
                          <a href="{{ route('empleado.index') }}" class="submenu-item">Empleados</a>
                          <a href="{{ route('politicas.index') }}" class="submenu-item">Políticas Vacaciones</a>
            
                        </div>
                   </div>
                @endif

                <!-- Módulo Permisos Laborales -->
                <div class="menu-item">
                    <i class="fa-solid fa-key"></i> Permisos Laborales <i class="fa-solid fa-chevron-down small"></i>
                    <div class="submenu">
                        <a href="{{ route('solicitudes.index') }}" class="submenu-item">Solicitudes laborales</a>
                        <a href="#" class="submenu-item">Vacaciones</a>
                        <a href="{{ route('tiempo_compensatorio.index') }}" class="submenu-item">Tiempos compensatorios</a>
                    </div>
                </div>

                <!-- Módulo Proyectos -->
                <div class="menu-item">
                    <i class="fa-solid fa-diagram-project"></i> Proyectos <i class="fa-solid fa-chevron-down small"></i>
                    <div class="submenu">
                        <a href="#" class="submenu-item">Registrar proyectos</a>
                        <a href="#" class="submenu-item">Evaluación de proyectos</a>
                    </div>
                </div>

                <!-- Módulo Informes y Estadísticas -->
                <div class="menu-item">
                    <i class="fa-solid fa-chart-line"></i> Informes y Estadísticas <i class="fa-solid fa-chevron-down small"></i>
                    <div class="submenu">
                        <a href="#" class="submenu-item">Informes</a>
                        <a href="#" class="submenu-item">Dashboards</a>
                    </div>
                </div>
            </nav>

            <!-- Icono de usuario / Dropdown -->
            <div class="dropdown">
               <button class="btn border-0 p-0 dropdown-toggle" type="button" data-bs-toggle="dropdown" style="box-shadow: none;">
                  <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 40px; height: 40px; border: 2px solid white;">
                      {{ strtoupper(substr(auth()->user()->usuario, 0, 1)) }}
                  </div>
               </button>
    
               <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0 rounded-4" style="width: 280px; overflow: hidden;">
        
                    <!-- Sección de Identidad -->
                   <div class="bg-light p-4 text-center">
                      <div class="rounded-circle bg-info d-flex align-items-center justify-content-center text-white fw-bold mx-auto mb-3 shadow-sm" style="width: 80px; height: 80px; font-size: 2.5rem;">
                           {{ strtoupper(substr(auth()->user()->usuario, 0, 1)) }}
                       </div>
                       <h6 class="fw-bold mb-0 text-dark">{{ auth()->user()->usuario }}</h6>
                       <p class="text-muted small mb-3">{{ auth()->user()->email }}</p>
            
                        <!-- ÚNICO BOTÓN DE GESTIÓN -->
                       <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalGestionPerfil">
                          Gestionar Perfil
                       </button>
                    </div>

                   <!-- Sección de Salida -->
                   <div class="p-3">
                      <form action="{{ route('logout') }}" method="POST">
                          @csrf
                          <button type="submit" class="btn btn-light btn-sm w-100 rounded-3 py-2 text-danger fw-bold">
                              <i class="fa-solid fa-right-from-bracket me-2"></i> Cerrar sesión
                          </button>
                      </form>
                   </div>

                </div>
            </div>
        </div>
    </header>
    @endif
@endauth

   @auth
    <!-- ===========================
         Sección Hero (solo en la página home/dashboard)
         =========================== -->
    @if(Request::is('home') || Request::is('dashboard'))
    <section class="ihci-hero-container">
        <div class="hero-content">
            <h1>Una Exitosa Gestión Cultural</h1>
            <p class="fs-5">Impulsando la plástica contemporánea en Honduras desde 1963.</p>
            <!-- Botón para abrir modal de historia -->
            <button class="btn btn-history shadow-lg" data-bs-toggle="modal" data-bs-target="#modalHistoria">
                <i class="fa-solid fa-eye me-2"></i>Leer Historia
            </button>
        </div>
    </section>
    @endif
    @endauth

    <!-- ===========================
         Contenedor principal del contenido de la página
         =========================== -->
    <main class="container py-5">
        @yield('content') <!-- Aquí se inyecta el contenido específico de cada vista -->
    </main>

    <!-- ===========================
         Modal de historia cultural
         =========================== -->
    <!-- Modal Historia -->
<div class="modal fade" id="modalHistoria" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0">

            <!-- Header del modal -->
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Nuestra Gestión Cultural</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del modal con scroll -->
            <div class="modal-body p-4">
                <p>
                    La vasta gestión cultural del IHCI inició en el año de 1963 con la celebración del I Salón Anual de Pintura, 
                    certamen en el que resultó ganador Arturo López Rodezno, personaje que destacó en el mundo diplomático y de la plástica 
                    con obras de trascendental importancia a nivel de país y allende de nuestras fronteras.  
                </p>
                <p>
                    Es así, bajo el patrocinio institucional de este centro de enseñanza del idioma inglés, que artistas como Juan Ramón Laínez, 
                    Ezequiel Padilla Ayestas, Mario Castillo, Gregorio Sabillón, Gelasio Giménez, Virgilio Guardiola, Luis H. Padilla, Alejo Lara, 
                    Aníbal Cruz, Carlos Garay, entre otros, inician una etapa excepcional en la historia de la plástica contemporánea.  
                </p>
                <p>
                    Es el éxito de este modelo de gestión cultural un catalizador para abrir un espacio expositivo en la Calle Real de Comayagüela, 
                    lugar donde convergen -hasta 1998- propuestas experimentales e ideológicas con una simbología atrayente a un público ávido de conocimiento más allá de lo tradicional.
                </p>
                <p>
                    La mancuerna conformada por la Galería de Arte Marianita Zepeda y la Biblioteca James G. Blaine, fue un factor atractivo para la comunidad intelectual que identificó estos espacios en la ciudad gemela de Tegucigalpa para el necesario fortalecimiento de un tejido social a la otra orilla del río.   
                </p>
                <p>
                    En el año de 1987, el IHCI planteó a la comunidad artística la necesidad de llevar a cabo un concurso con el objetivo de cimentar las bases, en ese entonces, de una incipiente tradición escultórica.  
                    Es el artista Obed Valladares el ganador del Primer Premio de ese naciente certamen que, hasta nuestros días, continúa ininterrumpidamente dando un espacio a la manifestación de la técnica tridimensional.  
                    Los concursos de cerámica y escultura permiten descollar, además de Valladares, a Julio Navarro, Pastor Sabillón, Jesús Zelaya, Blas Aguilar, Marcia Ney, entre otros.
                </p>
            </div>

            <!-- Footer opcional -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>


    <!-- ===========================
         Scripts de JavaScript
         =========================== -->
         <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS con Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS de Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
