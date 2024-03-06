<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-item"><a class="nav-link" href="{{ url('/') }}" target="_blank"><i class="nav-icon icon-home"></i>Main Site</a></li>
            <li class="nav-item"><a class="nav-link {{ in_array(current_method(), array('index')) ? 'active' : '' }}" href="{{ url('/admin-panel') }}"><i class="nav-icon icon-speedometer"></i>Dashboard</a></li>
            {!! menus_html() !!}
            <li class="nav-item"><a class="nav-link {{ in_array(current_method(), array('admin/platforms')) ? 'active' : '' }}" href="{{ url('admin/platforms') }}"><i class="nav-icon icon-speedometer"></i>Platforms Setting</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ url('/admin/logout') }}"><i class="nav-icon fa fa-lock"></i> Log Out</a></li>
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
