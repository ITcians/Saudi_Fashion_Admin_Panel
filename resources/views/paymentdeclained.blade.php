
<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none">


<!-- Mirrored from themesbrand.com/velzon/html/default/auth-404-alt.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 08 Sep 2022 15:04:55 GMT -->
<head>

    <meta charset="utf-8" />
    <title>SaudiFashion | Payment Declined</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Layout config Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

</head>

<body>

    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100" >
        <div class="bg-overlay"></div>
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-5">
                        <div class="card overflow-hidden" style="border-radius: 20px">
                            <div class="card-body p-4">
                                <div class="text-center">
                                    {{-- <lord-icon class="avatar-xl" src="https://cdn.lordicon.com/spxnqpau.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c"></lord-icon> --}}
                                    <div style="display: flex; justify-content: center; align-items: center;">
                                        <img src="/images/logo.png" class="card-logo card-logo-dark" alt="logo dark" height="50">
                                    </div>
                                    
                                    <h1 class="text-primary mb-4">Payment {{$response->response->message}} !</h1>
                                    <h4 class="text-uppercase">Your pament has been {{$response->response->message}} 😭</h4>
                                    <p class="text-muted mb-4">Please Try Again!</p>
                                    <a href="index.html" class="btn btn-success"><i class="mdi mdi-application-export me-1"></i>Back to App!</a>
                                </div>
                            </div>
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->

                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('assets/js/plugins.js') }}"></script>

</body>


<!-- Mirrored from themesbrand.com/velzon/html/default/auth-404-alt.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 08 Sep 2022 15:04:55 GMT -->
</html>