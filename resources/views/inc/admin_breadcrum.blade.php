<ol class="breadcrumb noprint">
	@if (!empty($data['breadcrumbs']))
		@foreach ($data['breadcrumbs'] as $index => $val)
	<li class="breadcrumb-item"> <a href="{{ $val }}">{{ $index }}</a> </li>
		@endforeach
	@endif
    <!-- Breadcrumb Menu-->
  	<li class="breadcrumb-menu d-md-down-none">
    	<div class="btn-group">
        	<a class="btn" href="{{url('admin/profile')}}"><i class="fa fa-user"></i> &nbsp;{{ Auth::user()->name }}</a> 
            <a class="btn" href="{{url('admin/profile')}}"><i class="fa fa-pencil-square"></i> &nbsp;Profile</a>
		</div>
  	</li>
</ol>