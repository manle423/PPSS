<div class="container-fluid fixed-top">
    <div class="container px-0">
        <nav class="navbar bg-white navbar-expand-xl">
            <a href="{{ route('home') }}" class="navbar-brand">
                <h1 class="text-primary display-6">Pet Product Shop</h1>
            </a>
            <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarCollapse">
                <span class="fa fa-bars text-primary"></span>
            </button>
            <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                <div class="navbar-nav mx-auto">
                    <a href="{{ route('home') }}" class="nav-item nav-link active">Home</a>
                    <a href="{{ route('shop') }}" class="nav-item nav-link">Shop</a>
                    <a  href="{{ route('contact') }}" class="nav-item nav-link">Contact</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                        <div class="dropdown-menu m-0 bg-secondary rounded-0">
                            {{-- <a href="{{ route('cart') }}" class="dropdown-item">Cart</a> --}}
                            <a href="{{ route('checkout') }}" class="dropdown-item">Checkout</a>
                            <a href="{{ route('404') }}" class="dropdown-item">404 Page</a>
                        </div>
                    </div>
                  
                </div>
                <div class="d-flex m-2 me-0">
                    <button class="btn-search btn border border-secondary btn-md-square rounded-circle bg-white me-4"
                        data-bs-toggle="modal" data-bs-target="#searchModal"><i
                            class="fas fa-search text-primary"></i></button>
                    {{-- <a href="{{ route('cart') }}" class="position-relative ms-3 me-3 my-2"> --}}
                        <i class="fa fa-shopping-bag fa-2x"></i>
                        <span
                            class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1"
                            style="top: -5px; left: 15px; height: 20px; min-width: 20px;">3</span>
                    </a>
                    {{-- Check if user login --}}
                    @guest
                        <a href="#" class="my-2 ms-3 d-flex align-items-center" data-bs-toggle="modal"
                            data-bs-target="#loginModal">
                            <i class="fas fa-sign-in-alt fa-2x"></i> 
                            <span class="nav-item nav-link">Login</span>
                        </a>
                        <a href="#" class="my-2 ms-3 d-flex align-items-center" data-bs-toggle="modal"
                            data-bs-target="#registerModal">
                            <i class="fas fa-user-plus fa-2x"></i>
                            <span class="nav-item nav-link">Register</span>
                        </a>
                    @else
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle d-flex align-items-center"
                                data-bs-toggle="dropdown">
                                <i class="fas fa-user fa-2x"></i>
                            </a>
                            <div class="dropdown-menu m-0 bg-secondary rounded-0">
                                <a href="#" class="dropdown-item">Profile</a>
                                <a href="#" class="dropdown-item">Settings</a>
                                <a href="{{ route('logout') }}" class="dropdown-item"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </nav>
    </div>
</div>
