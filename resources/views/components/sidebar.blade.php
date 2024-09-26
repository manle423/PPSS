@php
    $sidebar = json_decode(file_get_contents(resource_path('menu/sidebar.json')), true);
@endphp

<div class="sidebar pe-4 pb-3">
    <nav class="navbar bg-light navbar-light">
        <a class="navbar-brand mx-4 mb-3" href="{{ route('admin.dashboard') }}">
            <h3 class="text-primary">{{ config('app.name', 'Laravel') }}</h3>
        </a>
        <div class="d-flex align-items-center ms-4 mb-4">
            <div class="position-relative">
                <img class="rounded-circle" src="{{ asset('assets/vendor/img/user.jpg') }}" alt="" style="width: 40px; height: 40px;">
                <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
            </div>
            <div class="ms-3">
                <h6 class="mb-0">Pet products shop</h6>
                <span>{{ Auth::user()->full_name }}</span>
            </div>
        </div>
        <div class="navbar-nav w-100">
            @foreach ($sidebar['items'] as $item)
                @if (isset($item['dropdown']))
                    <div class="nav-item dropdown">
                        <a href="{{ url('admin/' . $item['url']) }}" class="nav-link dropdown-toggle {{ Request::is('admin/' . ltrim($item['url'], '/').'*') ? 'active' : '' }}" data-bs-toggle="dropdown">
                            <i class="{{ $item['icon'] }} me-2"></i>{{ $item['name'] }}
                        </a>
                        <div class="dropdown-menu bg-transparent border-0">
                            @foreach ($item['dropdown'] as $dropdownItem)
                                <a href="{{ url('admin/' . $dropdownItem['url']) }}" class="dropdown-item {{ Request::is('admin/' . ltrim($dropdownItem['url'], '/')) ? 'active' : '' }}">{{ $dropdownItem['name'] }}</a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ url('admin/' . $item['url']) }}" class="nav-item nav-link {{ Request::is('admin/' . ltrim($item['url'], '/')) ? 'active' : '' }}">
                        <i class="{{ $item['icon'] }} me-2"></i>{{ $item['name'] }}
                    </a>
                @endif
            @endforeach
        </div>
    </nav>
</div>