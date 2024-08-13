<!doctype html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link rel="stylesheet" href="{{ asset('css/simplebar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/choices.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/leaflet.css">') }}">
    <link rel="stylesheet" href="{{ asset('css/MarkerCluster.css">') }}">
    <link rel="stylesheet" href="{{ asset('css/MarkerCluster.Default.css') }}">
    <link rel="stylesheet" href="{{ css('custom-style.css') }}">
    <title>@yield('title')</title>
</head>

<body>
    <main class="main" id="top"></main>
    <nav class="navbar navbar-top fixed-top navbar-expand-lg" id="navbarTop">
        <div class="navbar-logo">
            <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarTopCollapse" aria-controls="navbarTopCollapse"
                aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span
                        class="toggle-line"></span></span></button>
            <a class="navbar-brand me-1 me-sm-3" href="/">
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center"><img src="{{ asset('img/mbt/MBT-Logo.png') }}" alt="MBT"
                            width="90" />
                        <p class="logo-text ms-2 d-none d-sm-block"></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="collapse navbar-collapse navbar-top-collapse order-1 order-lg-0 justify-content-center"
            id="navbarTopCollapse">
            <ul class="navbar-nav navbar-nav-top" data-dropdown-on-hover="data-dropdown-on-hover">
                <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button"
                        data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true"
                        aria-expanded="false"><span class="uil fs-8 me-2 uil-chart-pie"></span>Trang Chủ</a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <li><a class="dropdown-item" href="/bao-cao-tong-quan">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="clipboard"></span>Báo cáo tổng quan
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button"
                        data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true"
                        aria-expanded="false"><span class="uil fs-8 me-2 uil-cube"></span>Vật tư</a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <li><a class="dropdown-item" href="/vat-tu/danh-muc-vat-tu">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="minus"></span>Danh mục vật tư
                                </div>
                            </a>
                        </li>
                        <li><a class="dropdown-item" href="/vat-tu/danh-sach-vat-tu">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="minus"></span>Danh sách vật tư
                                </div>
                            </a>
                        </li>
                        <li><a class="dropdown-item" href="/vat-tu/vat-tu-lau-chua-dung">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="minus"></span>Vật
                                    tư lâu chưa dùng
                                </div>
                            </a>
                        </li>
                        <li><a class="dropdown-item" href="/vat-tu/don-hang-vat-tu">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="minus"></span>Đơn
                                    hàng vật tư
                                </div>
                            </a>
                        </li>
                        <li><a class="dropdown-item" href="/vat-tu/don-hang-tra-nha-cung-cap">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="minus"></span>Đơn hàng vật tư trả lại nhà cung cấp
                                </div>
                            </a>
                        </li>
                        <li><a class="dropdown-item" href="/vat-tu/don-vi">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="minus"></span>Đơn vị tính
                                </div>
                            </a>
                        </li>
                        <li><a class="dropdown-item" href="/vat-tu/hang-hoa-ton-kho">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="minus"></span>Hàng hóa tồn kho
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!"
                        data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true"
                        aria-expanded="false"><span class="uil fs-8 me-2 uil-puzzle-piece"></span>Xuất - Nhập</a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <li>
                            <a class="dropdown-item" href="/nhap-xuat/nhap-vat-tu-sll">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="minus"></span>Nhập vật tư SLL</div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/nhap-xuat/nhap-xuat-sll-chi-nhanh">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="minus"></span>Xuất vật tư SLL Chi nhánh (Xuất nhiều máy)</div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/nhap-xuat/xuat-nhan-ban-mot-may">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="minus"></span>Xuất vật tư SLL (Xuất nhân bản một máy)</div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/nhap-xuat/xuat-ban-vat-tu">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="minus"></span>Xuất bán vật tư</div>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!" role="button"
                        data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true"
                        aria-expanded="false"><span class="uil fs-8 me-2 uil-document-layout-right"></span>Sản
                        phẩm</a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <li>
                            <a class="dropdown-item" href="/san-pham/btp-crt/danh-sach">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="minus"></span>BTP Trụ - Bối Hạ</div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/san-pham/san-pham-sua-chua/danh-sach">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="minus"></span>Sản phẩm sửa chữa</div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/san-pham/vo-may-xa-kep">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="minus"></span>Vỏ
                                    máy - Xà kẹp</div>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="/nha-cung-cap/danh-sach"
                        role="button"><span class="uil fs-8 me-2 uil-document-layout-right"></span>Nhà cung cấp</a>
                </li>
                <li class="nav-item dropdown"><a class="nav-link dropdown-toggle lh-1" href="#!"
                        data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true"
                        aria-expanded="false"><span class="uil fs-8 me-2 uil-puzzle-piece"></span>Báo cáo</a>
                    <ul class="dropdown-menu navbar-dropdown-caret">
                        <li>
                            <a class="dropdown-item" href="/bao-cao/bao-cao-nhap">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="minus"></span>Báo cáo nhập</div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/bao-cao/bao-cao-xuat">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="minus"></span>Báo cáo xuất</div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/bao-cao/bao-cao-can-tra">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="minus"></span>Báo cáo xuất trả</div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/bao-cao/block-so-phieu-loi">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="minus"></span>Block số phiếu lỗi</div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/bao-cao/ton-kho-x5">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="minus"></span>Tồn kho X5</div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/bao-cao/tong-quan-kho-x5">
                                <div class="dropdown-item-wrapper"><span class="me-2 uil"
                                        data-feather="minus"></span>Tổng quan kho X5</div>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <ul class="navbar-nav navbar-nav-icons flex-row">
            <li class="nav-item">
                <div class="theme-control-toggle fa-icon-wait px-2">
                    <input class="form-check-input ms-0 theme-control-toggle-input" type="checkbox"
                        data-theme-control="phoenixTheme" value="dark" id="themeControlToggle" />
                    <label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle"
                        data-bs-toggle="tooltip" data-bs-placement="left" title="Đổi giao diện"><span class="icon"
                            data-feather="moon"></span></label>
                    <label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle"
                        data-bs-toggle="tooltip" data-bs-placement="left" title="Đổi giao diện"><span class="icon"
                            data-feather="sun"></span></label>
                </div>
            </li>
            <li class="nav-item dropdown"><button class="nav-link lh-1 pe-0" id="navbarDropdownUser"
                    data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true"
                    aria-expanded="false">
                    <div class="avatar avatar-l ">
                        <img class="rounded-circle " src="{{ asset('img/teams/avatar-rounded.webp') }}"
                            alt="avatar" />
                    </div>
                </button>
                <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border"
                    aria-labelledby="navbarDropdownUser">
                    <div class="card position-relative border-0">
                        <div class="card-body p-0">
                            <div class="text-center pt-4 pb-3">
                                <div class="avatar avatar-xl ">
                                    <img class="rounded-circle " src="{{ asset('img/teams/avatar.webp') }}"
                                        alt="user" />

                                </div>
                                <h6 class="mt-2 text-body-emphasis">Phạm Văn Hùng</h6>
                            </div>
                        </div>
                        <div class="overflow-auto scrollbar" style="height: 10rem;">
                            <ul class="nav d-flex flex-column mb-2 pb-1">
                                <li class="nav-item"><a class="nav-link px-3" href="#!"> <span
                                            class="me-2 text-body" data-feather="user"></span><span>Trang cá
                                            nhân</span></a></li>
                                <li class="nav-item"><a class="nav-link px-3" href="#!"> <span
                                            class="me-2 text-body" data-feather="pie-chart"></span>Trang chủ</a></li>
                                <li class="nav-item"><a class="nav-link px-3" href="#!"> <span
                                            class="me-2 text-body" data-feather="lock"></span>Hoạt động</a></li>
                                <li class="nav-item"><a class="nav-link px-3" href="#!"> <span
                                            class="me-2 text-body" data-feather="settings"></span>Cài đặt </a></li>
                                <li class="nav-item"><a class="nav-link px-3" href="#!"> <span
                                            class="me-2 text-body" data-feather="help-circle"></span>Trợ giúp</a></li>
                                <li class="nav-item"><a class="nav-link px-3" href="#!"> <span
                                            class="me-2 text-body" data-feather="globe"></span>Ngôn ngữ</a></li>
                            </ul>
                        </div>
                        <div class="card-footer p-2 border-top border-translucent">
                            <div class="px-4"> <a class="btn btn-phoenix-secondary d-flex flex-center w-100"
                                    href="{{ route('logout') }}"> <span class="me-2" data-feather="log-out">
                                    </span>Đăng xuất</a></div>
                            <div class="my-2 text-center fw-bold fs-10 text-body-quaternary"><a
                                    class="text-body-quaternary me-1" href="#!">Chính sách bảo mật</a>&bull;<a
                                    class="text-body-quaternary mx-1" href="#!">Điều khoản</a>&bull;<a
                                    class="text-body-quaternary ms-1" href="#!">Cookies</a></div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </nav>
    @yield('content')
    <footer class="footer position-absolute">
        <div class="row g-0 justify-content-between align-items-center h-100">
            <div class="col-12 col-sm-auto text-center">
                <p class="mb-0 mt-2 mt-sm-0 text-body">Sản phẩm được phát triển bởi<a class="mx-1"
                        href="https://asfy.vn">ASFY TECH</a>
                    <span class="d-none d-sm-inline-block"></span><span
                        class="d-none d-sm-inline-block mx-1">|</span><br class="d-sm-none" /> &copy;
                    <?php echo date('Y'); ?>
                </p>
            </div>
        </div>
    </footer>

    <div class="modal fade" id="modalSuccessNotification" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border border-translucent shadow-lg">
                <div>
                    <div class="modal-header px-card border-0">
                        <div class="w-100 d-flex justify-content-center align-items-start">
                            <div>
                                <h5 class="mb-0 lh-sm text-success success-message">thông báo</h5>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-center align-items-center border-0">
                        <a class="open-link" href="javascript:" hidden title="Xem"><button
                                class="btn btn-sm btn-phoenix-primary text-center px-3" type="button"><span
                                    class="fs-8">Xem</span></button></a>
                        <button class="btn btn-phoenix-secondary pe-4" type="button" data-bs-dismiss="modal"
                            aria-label="Close">Đóng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalErrorNotification" tabindex="-1"
        aria-labelledby="modalSuccessNotificationLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border border-translucent shadow-lg">
                <div>
                    <div class="modal-header px-card border-0">
                        <div class="w-100 d-flex justify-content-center align-items-start">
                            <div>
                                <p class="mb-0 fs-7 lh-sm text-danger error-message text-center"></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-center align-items-center border-0">
                        <button class="btn btn-secondary text-center px-3" type="button" data-bs-dismiss="modal"
                            aria-label="Close"><span class="fs-8">Đóng</span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalConfirmDelete" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border border-translucent">
                <div>
                    <div class="modal-header px-card border-0">
                        <div class="w-100 d-flex justify-content-center align-items-start">
                            <div>
                                <h5 class="mb-0 lh-sm text-body-highlight confirm-message">lỗi</h5>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer d-flex justify-content-center align-items-center border-0">
                        <button class="btn btn-danger px-4 btn-confirm" title="Xoá">Xoá</button>
                        <button class="btn btn-secondary pe-4" type="button" data-bs-dismiss="modal"
                            aria-label="Close">Huỷ</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>


<script src="{{ asset('js/simplebar.min.js') }}"></script>
<script src="{{ asset('js/config.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/anchor.min.js') }}"></script>
<script src="{{ asset('js/is.min.js') }}"></script>
<script src="{{ asset('js/all.min.js') }}"></script>
<script src="{{ asset('js/lodash.min.js') }}"></script>
<script src="{{ asset('js/list.min.js') }}"></script>
<script src="{{ asset('js/feather.min.js') }}"></script>
<script src="{{ asset('js/dayjs.min.js') }}"></script>
<script src="{{ asset('js/phoenix.js') }}"></script>
<script src="{{ asset('js/echarts.min.js') }}"></script>
<script src="{{ asset('js/flatpickr.min.js') }}"></script>
<script src="{{ asset('js/vn.js') }}"></script>
<script src="{{ asset('js/choices.min.js') }}"></script>
<script src="{{ asset('js/chart.js') }}"></script>
<script src="{{ asset('js/axios.js') }}"></script>
<script src="{{ js('custom.js') }}"></script>
<script src="{{ js('customClass.js') }}"></script>

@yield('script')

</html>
