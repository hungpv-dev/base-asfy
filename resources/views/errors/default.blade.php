<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="en-US" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">


    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>Lỗi máy chủ</title>
    <link rel="stylesheet" href="<?= asset('css/theme.min.css') ?>">
</head>


<body>

<!-- ===============================================-->
<!--    Main Content-->
<!-- ===============================================-->
<main class="main" id="top">
    <div class="px-3">
        <div class="row min-vh-100 flex-center p-5">
            <div class="col-12 col-xl-10 col-xxl-8">
                <div class="row justify-content-center align-items-center g-5">
                    <div class="col-12 col-lg-6 text-center order-lg-1"><img class="img-fluid w-lg-100 d-dark-none" src="<?= asset('img/errors/500-illustration.png') ?>" alt="" width="400" /></div>
                    <div class="col-12 col-lg-6 text-center text-lg-start"><h1 class="text-primary text-center text-lg-start pb-4 fs-1">{{ $status }}</h1>
                        <h2 class="text-body-secondary fw-bolder mb-3">Lỗi xảy ra!</h2>
                        <p class="text-body mb-5">Đã xảy ra lỗi, vui lòng thử lại sau. </p><a class="btn btn-lg btn-primary" href="/">Về Trang Chủ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
</main>

</html>