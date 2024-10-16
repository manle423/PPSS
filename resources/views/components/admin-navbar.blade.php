<nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
    <a href="#" class="navbar-brand d-flex d-lg-none me-4">
        <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
    </a>
    <a href="#" class="sidebar-toggler flex-shrink-0">
        <i class="fa fa-bars"></i>
    </a>
    <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
        <form class="d-none d-md-flex ms-4" id="searchForm">
            <input class="form-control border-0" type="search" id="searchInput" name="query" placeholder="Enter keyword..." required>
            <button type="submit" class="btn btn-primary btn-sm ms-2">Search</button>
        </form>
    </nav>

    <div class="navbar-nav align-items-center ms-auto">
       
        <div class="nav-item dropdown">
          
           
        </div>
        <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <img class="rounded-circle me-lg-2" src="{{ asset('assets/vendor/img/user.jpg') }}"
                    alt="" style="width: 40px; height: 40px;">
                <span class="d-none d-lg-inline-flex"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
               
                <a href="{{ route('admin.password.request') }}" class="dropdown-item">Change password</a>
                @guest
                    @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                    @endif
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                @endguest
            </div>
        </div>
    </div>
</nav>

<!-- Modal Popup -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background-color: #fff;">
            <div class="modal-header">
                <h5 class="modal-title" id="searchModalLabel">Navigation Options</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>You searched for: <span class="text-primary" id="searchQuery"></span></p>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.category.list') }}" class="btn btn-outline-primary" id="categoryListBtn" style="display:none;">Go to Categories List</a>
                    <a href="{{ route('admin.category.create') }}" class="btn btn-outline-secondary" id="categoryCreateBtn" style="display:none;">Go to Create Category</a>
                    <a href="{{ route('admin.products.list') }}" class="btn btn-outline-secondary" id="productListBtn" style="display:none;">Go to Products List</a>
                    <a href="{{ route('admin.products.create') }}" class="btn btn-outline-secondary" id="productCreateBtn" style="display:none;">Go to Create Product</a>
                    <a href="{{ route('admin.customers.list') }}" class="btn btn-outline-success" id="customerListBtn" style="display:none;">Go to Customers List</a>
                    <a href="{{ route('admin.orders.list') }}" class="btn btn-outline-warning" id="orderListBtn" style="display:none;">Go to Orders List</a>
                    <a href="{{ route('admin.coupon.list') }}" class="btn btn-outline-info" id="couponListBtn" style="display:none;">Go to Coupons List</a>
                    <a href="{{ route('admin.coupon.create') }}" class="btn btn-outline-info" id="couponCreateBtn" style="display:none;">Go to Create Coupon</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchForm = document.getElementById('searchForm');
        const searchInput = document.getElementById('searchInput');
        const searchModal = new bootstrap.Modal(document.getElementById('searchModal'));

        searchForm.addEventListener('submit', function (e) {
            e.preventDefault(); // Ngăn tải lại trang

            const query = searchInput.value.trim().toLowerCase();
            document.getElementById('searchQuery').innerText = query;

            // Hiển thị hoặc ẩn các nút điều hướng dựa trên từ khóa
            toggleButtons(query);

            // Hiển thị modal
            searchModal.show();
        });

        function toggleButtons(query) {
    const categoryKeywords = ['category', 'cat', 'cate', 'categories'];
    const productKeywords = ['product', 'pro', 'products', 'produc', 'prod'];
    const customerKeywords = ['customer', 'cus', 'customers', 'cust', 'cu'];
    const orderKeywords = ['order', 'ord', 'orders'];
    const couponKeywords = ['coupon', 'cou', 'coupons', 'coupo', 'coup'];
    const addKeywords = ['add','insert','new','create']
    const listKeywords = ['list','lis','lit']
    
    const hasCategory = categoryKeywords.some(keyword => query.includes(keyword));
    if (hasCategory) {
        document.getElementById('categoryListBtn').style.display = 'block';  
        document.getElementById('categoryCreateBtn').style.display = 'block'; 
    } else {
        document.getElementById('categoryListBtn').style.display = 'none';   
        document.getElementById('categoryCreateBtn').style.display = 'none';  
    }

    const hasProduct = productKeywords.some(keyword => query.includes(keyword));
    if (hasProduct) {
        document.getElementById('productListBtn').style.display = 'block';   
        document.getElementById('productCreateBtn').style.display = 'block';  
    } else {
        document.getElementById('productListBtn').style.display = 'none';    
        document.getElementById('productCreateBtn').style.display = 'none';   
    }

    // Kiểm tra từ khóa cho Customer
    const hasCustomer = customerKeywords.some(keyword => query.includes(keyword));
    if (hasCustomer) {
        document.getElementById('customerListBtn').style.display = 'block';  
    } else {
        document.getElementById('customerListBtn').style.display = 'none';  
    }

    // Kiểm tra từ khóa cho Order
    const hasOrder = orderKeywords.some(keyword => query.includes(keyword));
    if (hasOrder) {
        document.getElementById('orderListBtn').style.display = 'block';  
    } else {
        document.getElementById('orderListBtn').style.display = 'none';  
    }

    // Kiểm tra từ khóa cho Coupon
    const hasCoupon = couponKeywords.some(keyword => query.includes(keyword));
    if (hasCoupon) {
        document.getElementById('couponListBtn').style.display = 'block';  
        document.getElementById('couponCreateBtn').style.display = 'block';  
    } else {
        document.getElementById('couponListBtn').style.display = 'none';  
        document.getElementById('couponCreateBtn').style.display = 'none';  
    }

    const hasAdd = addKeywords.some(keyword => query.includes(keyword));
if (hasAdd) {
    document.getElementById('categoryCreateBtn').style.display = 'block'; 
    document.getElementById('productCreateBtn').style.display = 'block'; 
    document.getElementById('couponCreateBtn').style.display = 'block'; 
} else {
    document.getElementById('categoryCreateBtn').style.display = 'none'; 
    document.getElementById('productCreateBtn').style.display = 'none'; 
    document.getElementById('couponCreateBtn').style.display = 'none'; 
}

const hasList = listKeywords.some(keyword => query.includes(keyword));
if (hasList) {
    document.getElementById('categoryListBtn').style.display = 'block'; 
    document.getElementById('productListBtn').style.display = 'block'; 
    document.getElementById('couponListBtn').style.display = 'block'; 
    document.getElementById('customerListBtn').style.display = 'block'; 
    document.getElementById('orderListBtn').style.display = 'block'; 
} else {
    document.getElementById('categoryListBtn').style.display = 'none'; 
    document.getElementById('productListBtn').style.display = 'none'; 
    document.getElementById('couponListBtn').style.display = 'none'; 
    document.getElementById('customerListBtn').style.display = 'none'; 
    document.getElementById('orderListBtn').style.display = 'none'; 
}
}
    });
</script>
