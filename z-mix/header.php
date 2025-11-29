<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">


    <?php
    // Định nghĩa tiêu đề và mô tả mặc định


    $page_title = "Máy tính bộ, văn phòng, gaming, AI, server giá tốt";
    $page_description = "PC để bàn, máy tính đồng bộ, vi tính văn phòng giá rẻ, đa dạng cấu hình. Thương hiệu máy tính Việt Nam, trả góp, giao hàng nhanh, bảo hành chính hãng.";

    // Kiểm tra nếu biến global đã được thiết lập bởi trang con
    if (isset($GLOBALS['page_specific_title'])) {
        $page_title = $GLOBALS['page_specific_title'];
    }
    if (isset($GLOBALS['page_specific_description'])) {
        $page_description = $GLOBALS['page_specific_description'];
    }
    ?>

    <meta name="description" content="<?= htmlspecialchars($page_description); ?>">
    <title><?= htmlspecialchars($page_title); ?></title>

    <?php
    // Lấy URL hiện tại
    $canonical = "https://rosacomputer.vn" . strtok($_SERVER["REQUEST_URI"], '?');
    ?>
    <link rel="canonical" href="<?= htmlspecialchars($canonical, ENT_QUOTES) ?>">


    <meta name="google-site-verification" content="4USrUmb19Z0YVYJqkaUI3pgEwwi8Ma9yXo-9gqbx9Q0">
    <meta name="google-site-verification" content=" NdBcxDI_-NXpTSSU12BEBBp_F7THLCq0G-zQDyaP3GI">


    <meta name="robots" content="index, follow">
    <meta name="color-scheme" content="light only">

    <link rel="icon" href="/assets/images/rosa-icon.png" type="image/png">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

    <!-- Bootstrap 4.6.2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">

    <!-- Font Awesome (Chỉ chọn phiên bản cao nhất bạn cần dùng, ở đây dùng 6.5.1) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Font Awesome 4.7.0 (nếu bạn cần các icon cũ không có ở bản mới) -->
    <link href="assets/plugins/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- Owl Carousel 2.2.1 -->
    <link rel="stylesheet" type="text/css" href="assets/plugins/OwlCarousel2-2.2.1/owl.carousel.css">
    <link rel="stylesheet" type="text/css" href="assets/plugins/OwlCarousel2-2.2.1/owl.theme.default.css">
    <link rel="stylesheet" type="text/css" href="assets/plugins/OwlCarousel2-2.2.1/animate.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Styles -->
    <link rel="stylesheet" href="style/header.css">

</head>

<body>
    <!-- Your content goes here -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap Bundle JS 4.6.2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script src="script/header.js"></script>

    <!-- Main Header -->
    <header class="header">
        <div class="container d-flex align-items-center justify-content-between py-2">
            <div class="logo_container">
                <div>
                    <a href="/">
                        <img src="/assets/images/rosa.webp" alt="Rosacomputer" width="150" height="50">
                    </a>
                </div>
            </div>

            <nav class="nav_container d-none d-md-block">
                <ul class="d-flex">
                    <!--<li><a href="/gioithieu.php">Giới thiệu</a></li>-->
                    <li><a href="gioithieu.php">Giới thiệu <i class="fas fa-chevron-down"></i></a>
                        <ul class="submenu">
                            <li class="has-submenu">
                                <a href="profile.php">Profile công ty</i></a>
                            </li>
                        </ul>
                    </li>
                    <li><a href="product.php">Sản phầm</a></li>
                    <li><a href="#">Giải pháp AI <i class="fas fa-chevron-down"></i></a>
                        <ul class="submenu">
                            <li class="has-submenu">
                                <a href="#">KHOÁ HỌC AI <i class="fas fa-chevron-right"></i></a>
                                <ul class="submenu">
                                    <li><a href="/ROSA-SW.php">ỨNG DỤNG ROSA</a></li>
                                    <li><a href="/courses/python-course.php">PYTHON CƠ BẢN</a></li>
                                    <li><a href="/courses/yolo-course.php">THỊ GIÁC MÁY TÍNH</a></li>
                                </ul>
                            </li>
                            <li class="has-submenu">
                                <a href="#">SMB <i class="fas fa-chevron-right"></i></a>
                                <ul class="submenu">
                                    <li><a href="/courses/ChatbotAI.php">CHATBOT AI</a></li>
                                    <li><a href="/courses/AIchamcong.php">CHẤM CÔNG CAMERA AI</a></li>
                                    <li><a href="/courses/Nextcloud.php">NEXCLOUND</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li><a href="/courses/palit.php">Chương trình</a></li>
                    <li><a href="/tintuc_test/template.php">Tin tức </a></li>
                    <li><a href="baohanh.php">Bảo hành </a></li>
                    <li><a href="check.php">Đơn hàng</a></li>
                </ul>
            </nav>
            <!-- TÌM KIẾM  -->
            <form class="search-box d-none d-md-flex" action="#" method="get">
                <i class="fas fa-search"></i>
                <input type="text" name="q" placeholder="Tìm kiếm">
            </form>

            <!-- 3 GẠCH TRÊN PHONE -->
            <div class="hamburger_container d-md-none">
                <i class="fa fa-bars"></i>
            </div>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div class="hamburger_menu">
        <div class="hamburger_close"><i class="fa fa-times" aria-hidden="true"></i></div>

        <!-- Logo trong menu -->
        <div class="menu_logo">
            <h1>
                <a href="/" title="Trang chủ Rosa Coffee">
                    <img src="/assets/images/rosa.png" alt="Rosacomputer" width="150" height="50" loading="lazy">
                </a>
            </h1>
        </div>
        <div class="hamburger_menu_content">
            <ul class="menu_top_nav">
                <li><a href="https://rosacomputer.vn/gioithieu.php">Giới thiệu</a></li>
                <li><a href="https://rosacomputer.vn/product.php">Sản phẩm</a></li>
                <li class="has-submenu">
                    <a href="javascript:void(0)" class="submenu-toggle">Giải pháp AI <i class="fas fa-chevron-down"></i></a>
                    <ul class="submenu">
                        <li class="has-submenu">
                            <a href="javascript:void(0)" class="submenu-toggle">KHÓA HỌC AI <i class="fas fa-chevron-down"></i></a>
                            <ul class="submenu">
                                <li><a href="ROSA-SW.php">ỨNG DỤNG ROSA</a></li>
                                <li><a href="/courses/python-course.php">PYTHON CƠ BẢN</a></li>
                                <li><a href="/courses/yolo-course.php">THỊ GIÁC MÁY TÍNH</a></li>
                            </ul>
                        </li>

                        <li class="has-submenu">
                            <a href="javascript:void(0)" class="submenu-toggle">SMB <i class="fas fa-chevron-down"></i></a>
                            <ul class="submenu">
                                <li><a href="/courses/ChatbotAI.php">CHATBOT AI</a></li>
                                <li><a href="/courses/AIchamcong.php">CHẤM CÔNG CAMERA AI</a></li>
                                <li><a href="/courses/Nextcloud.php">NEXTCLOUD</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li><a href="/courses/palit.php">Chương trình</a></li>
                <li><a href="/tintuc_test/template.php">Tin tức</a></li>
                <li><a href="baohanh.php">Bảo hành</a></li>
                <li><a href="check.php">Đơn hàng</a></li>
            </ul>
        </div>
    </div>
    </div>