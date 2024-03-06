<header class="app-header navbar">
    <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show">
        <span class="navbar-toggler-icon"></span>
    </button>

    <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon"></span>
    </button>

    <a class="navbar-brand" style="justify-content: start;" href="javascript:;">
        <img class="navbar-brand-full" src="{{ uploads_url() . 'img/addmee-logo.png' }}" height="45" alt="">
        <img class="navbar-brand-minimized" src="{{ uploads_url() . 'img/addmee-logo.png' }}" height="45"
            alt="">
    </a>

    <ul class="nav navbar-nav ml-auto">
        <li class="nav-item dropdown mr-3">
            <a class="nav-link" data-toggle="dropdown" href="javascript:;" role="button" aria-haspopup="true"
                aria-expanded="false">
                <i class="fa fa-chevron-down"></i>
                <img class="img-avatar no-margin" src="{{ assets_url('admin/img/avatar.png') }}" alt="admin">
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-header text-center">
                    <strong>User</strong>
                </div>
                <a class="dropdown-item" href="{{ url('admin/profile') }}"> <i class="fa fa-user"></i> Profile</a>
                <a class="dropdown-item" href="{{ url('admin/logout') }}"><i class="fa fa-lock"></i> Logout</a>
            </div>
        </li>
    </ul>
    <!--<button class="navbar-toggler aside-menu-toggler d-md-down-none" type="button" data-toggle="aside-menu-lg-show">
      <span class="navbar-toggler-icon"></span>
    </button>-->
    <!--<button class="navbar-toggler aside-menu-toggler d-lg-none" type="button" data-toggle="aside-menu-show">
      <span class="navbar-toggler-icon"></span>
    </button>-->
</header>
