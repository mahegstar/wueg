<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
        </ul>
    </form>
    <ul class="navbar-nav navbar-right">
        <li class="dropdown">
            <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <img alt="image" src="{{ asset('img/avatar/avatar-1.png') }}" class="rounded-circle mr-1">
                <div class="d-sm-none d-lg-inline-block">{{ Auth::user()->name ?? 'User' }}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ route('profile') }}" class="dropdown-item has-icon">
                    <i class="far fa-user"></i> {{ __('Profile') }}
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('logout') }}" class="dropdown-item has-icon text-danger" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</nav>
<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('dashboard') }}">Elite Quiz</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('dashboard') }}">EQ</a>
        </div>
        <ul class="sidebar-menu">
            <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-fire"></i> <span>{{ __('Dashboard') }}</span>
                </a>
            </li>
            
            <li class="menu-header">{{ __('Quiz Management') }}</li>
            
            <li class="{{ request()->is('main-category') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('category.index') }}">
                    <i class="fas fa-th-large"></i> <span>{{ __('Main Categories') }}</span>
                </a>
            </li>
            
            <li class="{{ request()->is('sub-category') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('subcategory.index') }}">
                    <i class="fas fa-th"></i> <span>{{ __('Sub Categories') }}</span>
                </a>
            </li>
            
            <li class="dropdown {{ request()->is('create-questions*') || request()->is('manage-questions') || request()->is('import-questions') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-question-circle"></i> <span>{{ __('Questions') }}</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ request()->is('create-questions') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('questions.index') }}">{{ __('Create Questions') }}</a>
                    </li>
                    <li class="{{ request()->is('manage-questions') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('questions.manage') }}">{{ __('Manage Questions') }}</a>
                    </li>
                    <li class="{{ request()->is('import-questions') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('questions.import') }}">{{ __('Import Questions') }}</a>
                    </li>
                </ul>
            </li>
            
            <li class="dropdown {{ request()->is('contest*') ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown"><i class="fas fa-trophy"></i> <span>{{ __('Contests') }}</span></a>
                <ul class="dropdown-menu">
                    <li class="{{ request()->is('contest') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('contest.index') }}">{{ __('Manage Contests') }}</a>
                    </li>
                </ul>
            </li>
            
            <li class="menu-header">{{ __('User Management') }}</li>
            
            <li class="{{ request()->is('users') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('users') }}">
                    <i class="fas fa-users"></i> <span>{{ __('Users') }}</span>
                </a>
            </li>
        </ul>
    </aside>
</div>