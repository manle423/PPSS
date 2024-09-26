{{-- trang chủ website --}}
@extends('layouts.shop')
@section('content')
    <!-- Featurs Section Start -->
    <div class="container-fluid py-5 mb-5 hero-header">
    </div>
    <div class="container-fluid fruite py-5">
  
               
       
   
    <!-- Featurs Section End -->


    <!-- Fruits Shop Start-->
    <div class="container-fluid fruite py-5">
      
    <div class="container py-5">
            <div class="tab-class text-center">
               
            <div class="row g-4">
                    <div class="col-lg-4 text-start">
                        <h1>Our products</h1>
                    </div>
                    <div class="col-lg-8 text-end">
                        <ul class="nav nav-pills d-inline-flex text-center mb-5">
                            <li class="nav-item">
                                <a class="d-flex m-2 py-2 bg-light rounded-pill active" data-bs-toggle="pill"
                                    href="#tab-1">
                                    <span class="text-dark" style="width: 130px;">All Products</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="d-flex py-2 m-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-2">
                                    <span class="text-dark" style="width: 130px;">Clothes</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="d-flex m-2 py-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-3">
                                    <span class="text-dark" style="width: 130px;">Toys</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="d-flex m-2 py-2 bg-light rounded-pill" data-bs-toggle="pill" href="#tab-4">
                                    <span class="text-dark" style="width: 130px;">Foods</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
                <div class="tab-content"> 
                <div id="tab-1" class="tab-pane fade show p-0 active">
                <div class="row g-4">
                <div class="col-lg-12">
                <div class="row g-4">
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="{{ asset('assets/vendor/img/toys.jpg') }}"
                                                    class="img-fluid w-100 rounded-top" alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">Toys</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>Toys</h4>
                                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te
                                                    incididunt</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                                    <a href="#"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                            class="fa fa-shopping-bag me-2 text-primary"></i> Add to
                                                        cart</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="{{ asset('assets/vendor/img/toys.jpg') }}"
                                                    class="img-fluid w-100 rounded-top" alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">Toys</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>Toys</h4>
                                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te
                                                    incididunt</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                                    <a href="#"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                            class="fa fa-shopping-bag me-2 text-primary"></i> Add to
                                                        cart</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="{{ asset('assets/vendor/img/toys.jpg') }}"
                                                    class="img-fluid w-100 rounded-top" alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">Toys</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>Toys</h4>
                                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te
                                                    incididunt</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                                    <a href="#"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                            class="fa fa-shopping-bag me-2 text-primary"></i> Add to
                                                        cart</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="{{ asset('assets/vendor/img/toys.jpg') }}"
                                                    class="img-fluid w-100 rounded-top" alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">Toys</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>Toys</h4>
                                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te
                                                    incididunt</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                                    <a href="#"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                            class="fa fa-shopping-bag me-2 text-primary"></i> Add to
                                                        cart</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="{{ asset('assets/vendor/img/toys.jpg') }}"
                                                    class="img-fluid w-100 rounded-top" alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">Toys</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>Toys</h4>
                                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te
                                                    incididunt</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                                    <a href="#"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                            class="fa fa-shopping-bag me-2 text-primary"></i> Add to
                                                        cart</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="{{ asset('assets/vendor/img/toys.jpg') }}"
                                                    class="img-fluid w-100 rounded-top" alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">Toys</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>Toys</h4>
                                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te
                                                    incididunt</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                                    <a href="#"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                            class="fa fa-shopping-bag me-2 text-primary"></i> Add to
                                                        cart</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="{{ asset('assets/vendor/img/toys.jpg') }}"
                                                    class="img-fluid w-100 rounded-top" alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">Toys</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>Toys</h4>
                                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te
                                                    incididunt</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                                    <a href="#"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                            class="fa fa-shopping-bag me-2 text-primary"></i> Add to
                                                        cart</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="{{ asset('assets/vendor/img/toys.jpg') }}"
                                                    class="img-fluid w-100 rounded-top" alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">Toys</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>Toys</h4>
                                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod te
                                                    incididunt</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                                    <a href="#"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                            class="fa fa-shopping-bag me-2 text-primary"></i> Add to
                                                        cart</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                </div>
                </div>
                </div>
                </div>        
            </div>
           </div>
   </div>
   </div>
   </div>
                    <!-- Fact Start -->
                    <div class="container-fluid py-5">
                        <div class="container">
                            <div class="bg-light p-5 rounded">
                                <div class="row g-4 justify-content-center">
                                    <div class="col-md-6 col-lg-6 col-xl-3">
                                        <div class="counter bg-white rounded p-5">
                                            <i class="fa fa-users text-secondary"></i>
                                            <h4>satisfied customers</h4>
                                            <h1>1963</h1>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-6 col-xl-3">
                                        <div class="counter bg-white rounded p-5">
                                            <i class="fa fa-users text-secondary"></i>
                                            <h4>quality of service</h4>
                                            <h1>99%</h1>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-6 col-xl-3">
                                        <div class="counter bg-white rounded p-5">
                                            <i class="fa fa-users text-secondary"></i>
                                            <h4>quality certificates</h4>
                                            <h1>33</h1>
                                        </div>
                                    </div>
                                 
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Fact Start -->


                    <!-- Tastimonial End -->
                @endsection
