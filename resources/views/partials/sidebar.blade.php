<!-- Toggle Button for Mobile -->
<button id="sidebar-toggle" class="lg:hidden fixed top-20 left-4 z-50 p-2 bg-white text-gray-600 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
    <i class="fas fa-bars text-sm"></i>
</button>

<!-- Sidebar Overlay for Mobile -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-30 z-20 lg:hidden hidden transition-all duration-300"></div>

<!-- Sidebar -->
<div id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-white shadow-lg transform -translate-x-full transition-all duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col border-r border-gray-200">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
        <div class="flex items-center space-x-3">
            <div class="h-10 flex items-center justify-center">
                <img src="{{ asset('images/logo_azzuhdi.png') }}" alt="Logo Azzuhdi" class="h-9 drop-shadow-sm">
            </div>
            <div>
                <span class="text-lg font-bold text-blue-800 sidebar-text">
                    {{ auth()->user()->role === 'admin' ? 'Admin Panel' : 'Absensi' }}
                </span>
                <p class="text-xs text-blue-600 sidebar-text">Sistem Absensi Digital</p>
            </div>
        </div>
        <div class="flex items-center space-x-1">
            <!-- Desktop Toggle Button - Langsung klik tanpa hover -->
            <button id="desktop-toggle" class="hidden lg:flex p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-md transition-all duration-200 items-center justify-center">
                <i class="fas fa-chevron-left text-sm transition-transform duration-200" id="toggle-icon"></i>
            </button>
            <!-- Mobile Close Button -->
            <button id="sidebar-close" class="lg:hidden p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-md transition-all duration-200">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 px-4 py-4 overflow-y-auto">
        <ul class="space-y-2">
            <!-- Dashboard - Menu untuk semua role -->
            <li>
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard') }}" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                    <i class="fas fa-home w-5 h-5 mr-3 text-gray-500 group-hover:text-blue-500"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>
            
            <!-- Menu khusus Admin -->
            @if(auth()->user()->role === 'admin')
                <!-- Dropdown Menu Management -->
                <li class="relative">
                    <button onclick="toggleDropdown()" class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                        <div class="flex items-center">
                            <i class="fas fa-users-cog w-5 h-5 mr-3 text-gray-500 group-hover:text-blue-500"></i>
                            <span class="sidebar-text">Management</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs text-gray-400 group-hover:text-blue-500 transition-all duration-200 sidebar-arrow" id="dropdown-arrow"></i>
                    </button>
                    
                    <!-- Dropdown Content -->
                    <div id="dropdown-content" class="hidden mt-1 ml-8 space-y-1 dropdown-content">
                        <a href="{{ route('admin.users.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-100 hover:text-blue-600 transition-all duration-200">
                            <i class="fas fa-users w-4 h-4 mr-3"></i>
                            <span class="sidebar-text">Users</span>
                        </a>
                        <a href="{{ route('admin.attendance.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-100 hover:text-blue-600 transition-all duration-200">
                            <i class="fas fa-clipboard-list w-4 h-4 mr-3"></i>
                            <span class="sidebar-text">Rekap Absensi</span>
                        </a>
                        <a href="{{ route('admin.leave-requests.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-100 hover:text-blue-600 transition-all duration-200">
                            <i class="fas fa-calendar-times w-4 h-4 mr-3"></i>
                            <span class="sidebar-text">Manajemen Ijin</span>
                        </a>
                        <a href="{{ route('admin.audit.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-100 hover:text-blue-600 transition-all duration-200">
                            <i class="fas fa-clipboard-check w-4 h-4 mr-3"></i>
                            <span class="sidebar-text">Audit Log</span>
                        </a>
                        <a href="{{ route('admin.subjects.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-100 hover:text-blue-600 transition-all duration-200">
                            <i class="fas fa-book w-4 h-4 mr-3"></i>
                            <span class="sidebar-text">Mata Pelajaran</span>
                        </a>
                        <a href="{{ route('admin.schedules.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-100 hover:text-blue-600 transition-all duration-200">
                            <i class="fas fa-calendar-alt w-4 h-4 mr-3"></i>
                            <span class="sidebar-text">Jadwal Mengajar</span>
                        </a>
                    </div>
                </li>
                
            @endif
            
            <!-- Menu khusus User -->
            @if(auth()->user()->role === 'user')
                <li>
                    <a href="{{ route('user.attendance.history') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                        <i class="fas fa-history w-5 h-5 mr-3 text-gray-500 group-hover:text-blue-500"></i>
                        <span class="sidebar-text">Riwayat Absensi</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.leave-requests.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                        <i class="fas fa-calendar-times w-5 h-5 mr-3 text-gray-500 group-hover:text-blue-500"></i>
                        <span class="sidebar-text">Ajukan Ijin</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.schedules.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                        <i class="fas fa-calendar-alt w-5 h-5 mr-3 text-gray-500 group-hover:text-blue-500"></i>
                        <span class="sidebar-text">Jadwal Mengajar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.schedules.calendar') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                        <i class="fas fa-calendar-week w-5 h-5 mr-3 text-gray-500 group-hover:text-blue-500"></i>
                        <span class="sidebar-text">Kalender Jadwal</span>
                    </a>
                </li>
            @endif
            
            <!-- Menu untuk semua role -->
            {{-- <li>
                <a href="#" class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                    <i class="fas fa-question-circle w-5 h-5 mr-3 text-gray-500 group-hover:text-blue-500"></i>
                    <span class="sidebar-text">Help</span>
                </a>
            </li> --}}
        </ul>
    </nav>
    
    <!-- User Profile Section -->
    <div class="border-t border-gray-200 p-4">
        <div class="flex items-center space-x-3 cursor-pointer hover:bg-gray-50 rounded-lg p-2 transition-all duration-200" onclick="toggleUserDropdown()">
            <div class="relative">
                @php
                    $initial = auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : '?';
                    $colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-pink-500'];
                    $color = $colors[ord($initial) % count($colors)];
                @endphp
                <div class="w-10 h-10 {{ $color }} rounded-lg flex items-center justify-center text-white font-semibold shadow-sm">
                    {{ $initial }}
                </div> 
                <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 border-2 border-white rounded-full"></div>
            </div>
            <div class="flex-1 min-w-0 sidebar-text">
                <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
            </div>
            <i class="fas fa-chevron-up text-xs text-gray-400 transition-all duration-200 sidebar-arrow" id="user-dropdown-arrow"></i>
        </div>
        
        <!-- User Dropdown Menu -->
        <div id="user-dropdown-content" class="hidden mt-2 py-1 bg-white border border-gray-200 rounded-lg shadow-md">
            <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-all duration-200">
                <i class="fas fa-user-edit w-4 h-4 mr-3 text-gray-500"></i>
                <span class="sidebar-text">Edit Profile</span>
            </a>
            <!-- Tombol/logout link -->
            <a href="#"
            id="btn-logout"
            class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 transition-all duration-200">
                <i class="fas fa-sign-out-alt w-4 h-4 mr-3 text-gray-500"></i>
                <span class="sidebar-text">Keluar</span>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </div>
</div>

<style>
.sidebar-collapsed {
    width: 4rem !important;
}
.sidebar-collapsed .sidebar-text {
    display: none;
}
.sidebar-collapsed .sidebar-arrow {
    display: none;
}
.sidebar-collapsed .dropdown-content {
    display: none !important;
}
.sidebar-collapsed #user-dropdown-content {
    display: none !important;
}
</style>

<script>
// Toggle dropdown function
function toggleDropdown() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar.classList.contains('sidebar-collapsed')) {
        return; // Don't open dropdown when sidebar is collapsed
    }
    
    const dropdown = document.getElementById('dropdown-content');
    const arrow = document.getElementById('dropdown-arrow');
    
    dropdown.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

// Toggle user dropdown function
function toggleUserDropdown() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar.classList.contains('sidebar-collapsed')) {
        return; // Don't open dropdown when sidebar is collapsed
    }
    
    const dropdown = document.getElementById('user-dropdown-content');
    const arrow = document.getElementById('user-dropdown-arrow');
    
    dropdown.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

// Sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarClose = document.getElementById('sidebar-close');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const desktopToggle = document.getElementById('desktop-toggle');
    const toggleIcon = document.getElementById('toggle-icon');
    
    // Mobile toggle sidebar
    sidebarToggle?.addEventListener('click', function(e) {
        e.stopPropagation();
        sidebar.classList.remove('-translate-x-full');
        sidebarOverlay.classList.remove('hidden');
    });
    
    // Desktop toggle sidebar (collapse/expand) - LANGSUNG KLIK
    desktopToggle?.addEventListener('click', function(e) {
        e.stopPropagation();
        
        if (sidebar.classList.contains('sidebar-collapsed')) {
            // Expand sidebar
            sidebar.classList.remove('sidebar-collapsed');
            toggleIcon.classList.remove('rotate-180');
        } else {
            // Collapse sidebar
            sidebar.classList.add('sidebar-collapsed');
            toggleIcon.classList.add('rotate-180');
            
            // Close any open dropdowns when collapsing
            document.getElementById('dropdown-content')?.classList.add('hidden');
            document.getElementById('user-dropdown-content')?.classList.add('hidden');
            document.getElementById('dropdown-arrow')?.classList.remove('rotate-180');
            document.getElementById('user-dropdown-arrow')?.classList.remove('rotate-180');
        }
    });
    
    // Close sidebar function for mobile
    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
    }
    
    sidebarClose?.addEventListener('click', function(e) {
        e.stopPropagation();
        closeSidebar();
    });
    
    sidebarOverlay?.addEventListener('click', closeSidebar);
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        const isLargeScreen = window.innerWidth >= 1024;
        
        if (!isLargeScreen && !sidebar.contains(event.target) && 
            event.target !== sidebarToggle && !sidebarToggle?.contains(event.target)) {
            closeSidebar();
        }
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('dropdown-content');
        const userDropdown = document.getElementById('user-dropdown-content');
        
        if (!event.target.closest('.relative') && dropdown && !dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
            document.getElementById('dropdown-arrow')?.classList.remove('rotate-180');
        }
        
        if (!event.target.closest('.border-t') && userDropdown && !userDropdown.classList.contains('hidden')) {
            userDropdown.classList.add('hidden');
            document.getElementById('user-dropdown-arrow')?.classList.remove('rotate-180');
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            // Desktop mode
            sidebarOverlay.classList.add('hidden');
        } else {
            // Mobile mode - reset collapsed state
            if (sidebar.classList.contains('sidebar-collapsed')) {
                sidebar.classList.remove('sidebar-collapsed');
                toggleIcon.classList.remove('rotate-180');
            }
        }
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('btn-logout').addEventListener('click', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Konfirmasi Logout',
            text: 'Apakah Anda yakin ingin keluar dari sistem?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Keluar',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    });
</script>