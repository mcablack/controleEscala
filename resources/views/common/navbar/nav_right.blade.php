@section('content_top_nav_right')
    <li class="nav-item dropdown">
        {{-- Exibe a quantidade total de notificações --}}
        <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
            <i class="far fa-bell"></i>
            <span class="badge badge-warning navbar-badge">15</span>
        </a>
        {{-- Detalhamento das notificações --}}
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px;">
            <span class="dropdown-item dropdown-header">15 Notifications</span>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
                <i class="fas fa-envelope mr-2"></i> 4 new messages
                <span class="float-right text-muted text-sm">3 mins</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
                <i class="fas fa-users mr-2"></i> 8 friend requests
                <span class="float-right text-muted text-sm">12 hours</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
                <i class="fas fa-file mr-2"></i> 3 new reports
                <span class="float-right text-muted text-sm">2 days</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
    </li>
    {{-- Botão sair do sistema --}}
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-sign-out-alt"></i> Sair do sistema
        </a>
    </li>
@endsection