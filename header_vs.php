<?php
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    unset($_SESSION['account_email']);
    unset($_SESSION['account_id']);
    header('Location:index.php');
}
?>
<style>
    .voice-btn.recognizing .action__icon-on {
        display: block;
    }

    .voice-btn.recognizing .action__icon-off {
        display: none;
    }
</style>
<header class="header">
    <div class="header__topbar">
        <div class="container p-relative d-flex space-between align-center">
            <p class="h5" style="color: #ff0000">CÔNG TY CỔ PHẦN TIN HỌC VIẾT SƠN</p>
        </div>
    </div>
    <div class="header__main">
        <div class="container">
            <div class="header__container d-grid middle-left">
                <!-- menu button -->
                <div class="header__btn md-none d-flex align-center">
                    <div class="navbar__icons">
                        <div class="navbar__icon"></div>
                    </div>
                </div>
                <div class="header__logo d-flex justify-center align-center">
                    <a href="index.php" class="d-inline-block">
                        <img src="assets/images/logo/logoVS_new.png" alt="Footer Logo" style="width: 200px; height: auto; max-width: 100%;">
                    </a>
                </div>
                <nav class="header__nav space-between d-flex">
                    <ul class="nav__list md-flex">
                        <li class="nav__item md-none">
                            <a href="#" class="nav__anchor" style="content: ''"></a>
                        </li>

                        <li class="nav__item">
                            <a class="nav__anchor h7 d-flex align-center space-between" href="index.php">
                                TRANG CHỦ
                            </a>
                        </li>

                        <li class="nav__item nav__items h7">
                            <span class="nav__anchor p-relative h7 d-flex align-center space-between w-100 cursor-pointer">
                                VỀ CHÚNG TÔI
                                <img class="d-none md-block svg__icon" src="./assets/images/icon/icon-chevron-down.svg" alt="back" style="margin-left: 8px" />
                            </span>
                            <ul class="header__subnav">
                                <li class="nav__item">
                                    <a class="nav__anchor h7 d-flex align-center space-between" href="index.php?page=about">
                                        GIỚI THIỆU
                                        <span class="mobile-arrow md-none">›</span>
                                    </a>
                                </li>
                                <li class="nav__item">
                                    <a class="nav__anchor h7 d-flex align-center space-between" href="index.php?page=CSBH" style="text-decoration: none;color:#000000">
                                        CHÍNH SÁCH BẢO HÀNH - ĐỔI TRẢ
                                        <span class="mobile-arrow md-none">›</span>
                                    </a>
                                </li>
                                <li class="nav__item">
                                    <a class="nav__anchor h7 d-flex align-center space-between" href="https://www.vietsontdc.com/PSSD" style="text-decoration: none;color:#000000">
                                        CHƯƠNG TRÌNH
                                        <span class="mobile-arrow md-none">›</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav__item">
                            <a class="nav__anchor h7 d-flex align-center space-between" href="index.php?page=blog">
                                TIN TỨC
                            </a>
                        </li>

                        <li class="nav__item">
                            <a class="nav__anchor h7 d-flex align-center space-between" href="index.php?page=contact">
                                LIÊN HỆ
                            </a>
                        </li>

                        <li class="nav__item">
                            <a class="nav__anchor h7 d-flex align-center space-between" href="index.php?page=check">
                                BẢO HÀNH
                            </a>
                        </li>

                        <li class="nav__item nav__items h7">
                            <span class="nav__anchor p-relative h7 d-flex align-center space-between w-100 cursor-pointer">
                                SẢN PHẨM
                                <img class="d-none md-block svg__icon" src="./assets/images/icon/icon-chevron-down.svg" alt="back" style="margin-left: 8px" />
                            </span>
                            <ul class="header__subnav">
                                <li class="nav__item md-none h5">
                                    <span class="nav__anchor cursor-pointer d-flex align-center">
                                        <span class="mobile-arrow md-none" style="margin-right: 8px">‹</span>
                                        SẢN PHẨM
                                    </span>
                                </li>
                                <li class="nav__item">
                                    <a class="nav__anchor h7 d-flex align-center space-between" href="index.php?page=products">
                                        TẤT CẢ SẢN PHẨM
                                        <span class="mobile-arrow md-none">›</span>
                                    </a>
                                </li>

                                <!-- Intel -->
                                <li class="nav__item nav__items">
                                    <span class="nav__anchor h7 d-flex align-center space-between cursor-pointer">
                                        INTEL
                                        <span class="mobile-arrow md-none">›</span>
                                    </span>
                                    <ul class="nav__submenu">
                                        <li class="nav__item">
                                            <a class="nav__submenu-anchor" href="index.php?page=products&brand_id=10">VGA</a>
                                        </li>
                                        <li class="nav__item nav__items">
                                            <span class="nav__submenu-anchor d-flex align-center space-between cursor-pointer">
                                                CPU
                                                <span class="mobile-arrow md-none">›</span>
                                            </span>
                                            <ul class="nav__submenu">
                                                <li class="nav__item">
                                                    <a class="nav__submenu-anchor" href="index.php?page=products&brand_id=10">PENTIUM</a>
                                                </li>
                                                <li class="nav__item">
                                                    <a class="nav__submenu-anchor" href="index.php?page=product_detail&product_id=I3">I3</a>
                                                </li>
                                                <li class="nav__item">
                                                    <a class="nav__submenu-anchor" href="index.php?page=product_detail&product_id=10">I5</a>
                                                </li>
                                                <li class="nav__item">
                                                    <a class="nav__submenu-anchor" href="index.php?page=product_detail&product_id=10">I7</a>
                                                </li>
                                                <li class="nav__item">
                                                    <a class="nav__submenu-anchor" href="index.php?page=product_detail&product_id=10">I9</a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Các brand khác với cấu trúc tương tự -->
                                <li class="nav__item nav__items">
                                    <span class="nav__anchor h7 d-flex align-center space-between cursor-pointer">
                                        AMD
                                        <span class="mobile-arrow md-none">›</span>
                                    </span>
                                    <ul class="nav__submenu">
                                        <li class="nav__item nav__items">
                                            <span class="nav__submenu-anchor d-flex align-center space-between cursor-pointer">
                                                CPU
                                                <span class="mobile-arrow md-none">›</span>
                                            </span>
                                            <ul class="nav__submenu">
                                                <li class="nav__item">
                                                    <a class="nav__submenu-anchor" href="index.php?page=product_detail&product_id=240">ATHLON</a>
                                                </li>
                                                <li class="nav__item">
                                                    <a class="nav__submenu-anchor" href="index.php?page=product_detail&product_id=239">RYZEN 3</a>
                                                </li>
                                                <li class="nav__item">
                                                    <a class="nav__submenu-anchor" href="index.php?page=product_detail&product_id=299">RYZEN 5</a>
                                                </li>
                                                <li class="nav__item">
                                                    <a class="nav__submenu-anchor" href="index.php?page=product_detail&product_id=239">RYZEN 7</a>
                                                </li>
                                                <li class="nav__item">
                                                    <a class="nav__submenu-anchor" href="index.php?page=product_detail&product_id=296">RYZEN 9</a>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Các brand khác... -->

                            </ul>
                        </li>
                    </ul>

                    <div class="flex-1"></div>

                    <div class="header__footer md-none">
                        <!-- Phần footer của header -->
                    </div>
                </nav>

                <div class="header__action d-flex align-center">
                    <!-- Phần search và cart giữ nguyên -->
                    <div class="header__action--item d-flex align-center p-relative">
                        <form action="index.php?page=search" method="POST" class="d-flex align-center" id="search-box">
                            <input type="text" placeholder="Tìm kiếm sản phẩm ..." id="input-search" name="keyword" class="search__input" required>
                            <button type="submit" name="search" class="header__action--link search-btn p-absolute d-inline-block">
                                <img class="action__icon svg__icon d-block" src="./assets/images/icon/icon-search.svg" alt="search" />
                            </button>
                            <button type="button" class="header__action--link voice-btn p-absolute d-inline-block" id="search-btn" onclick="voiceInput();">
                                <img class="action__icon action__icon-off svg__icon d-block" src="./assets/images/icon/voice-icon.png" alt="search" />
                                <img class="action__icon action__icon-on svg__icon d-none" src="./assets/images/icon/mic-on.png" alt="search" />
                            </button>
                        </form>
                    </div>

                    <div class="header__action--item align-center d-none md-flex">
                        <a class="header__action--link d-inline-block" href="<?php if (isset($_SESSION['account_email'])) {
                                                                                    echo "index.php?page=my_account&tab=account_info";
                                                                                } else {
                                                                                    echo "index.php?page=login";
                                                                                } ?>">
                            <img class="action__icon svg__icon d-block" src="./assets/images/icon/icon-person.svg" alt="person" />
                        </a>
                    </div>

                    <div class="header__action--item d-flex align-center">
                        <a class="header__action--link d-inline-block" href="index.php?page=cart">
                            <?php
                            if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                            ?>
                                <div class="icon-cart d-flex align-center justify-center p-relative">
                                    <img class="action__icon svg__icon d-block" src="./assets/images/icon/cart-open.svg" alt="cart">
                                    <span class="cart-count p-absolute d-flex align-center justify-center h6"><?php echo count($_SESSION['cart']) ?></span>
                                </div>
                            <?php
                            } else {
                            ?>
                                <img class="action__icon svg__icon d-block" src="./assets/images/icon/icon-cart.svg" alt="cart">
                            <?php
                            }
                            ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-nav-overlay"></div>
    </div>
</header>
<!-- js seace -->
<script>
    function voiceInput() {
        var inputSearch = document.getElementById('input-search');
        var searchBtn = document.querySelector('.voice-btn');
        // Tạo một đối tượng SpeechRecognition
        const recognition = new webkitSpeechRecognition();
        // Đặt thuộc tính cho đối tượng recognition
        recognition.lang = 'vi-VN'; // Ngôn ngữ được nhận dạng
        recognition.continuous = false; // Nhận dạng liên tục (true) hoặc một lần (false)

        // Sự kiện khi nhận dạng giọng nói thành công
        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            inputSearch.value = transcript; // In ra kết quả nhận dạng giọng nói
        };

        recognition.onstart = function() {
            searchBtn.classList.add('recognizing'); // Thêm class để áp dụng hiệu ứng khi bắt đầu nhận dạng
        };

        recognition.onend = function() {
            searchBtn.classList.remove('recognizing'); // Xóa class khi kết thúc nhận dạng
        };

        // Sự kiện khi xảy ra lỗi trong quá trình nhận dạng
        recognition.onerror = function(event) {
            console.error(event.error);
        };

        // Bắt đầu ghi âm và nhận dạng giọng nói
        recognition.start();
    }
</script>
<style>
    /* Định dạng cơ bản cho menu */
    .nav__item {
        position: relative;
        list-style-type: none;
    }

    /* Liên kết của menu */
    .nav__anchor {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 15px;
        text-decoration: none;
        color: #333;
        transition: background-color 0.5s ease;
    }

    /* Menu con (ẩn theo mặc định) */
    nav ul ul {
        display: none;
        position: absolute;
        top: 100%;
        /* Menu con sẽ xuất hiện ngay dưới menu cha */
        left: 0;
        background-color: #333;
        padding: 0;
        min-width: 150px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    /* Hiển thị menu con khi hover vào menu cha */
    nav ul li:hover>ul {
        display: block;
        opacity: 1;
    }

    /* Định dạng menu con */
    .nav__submenu {
        display: none;
        position: absolute;
        top: 0;
        left: 100%;
        /* Đẩy menu con sang phải */
        background-color: #fff;
        padding: 0;
        list-style-type: none;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 10;
        white-space: nowrap;
        /* Giữ menu không bị xuống dòng */
    }

    /* Hiển thị menu con khi hover vào mục cha */
    .nav__item:hover>.nav__submenu {
        display: block;
    }

    /* Định dạng liên kết trong menu con */
    .nav__submenu-anchor {
        padding: 10px 20px;
        display: block;
        text-decoration: none;
        color: #333;
        background-color: #f7f7f7;
        transition: background-color 0.3s ease;
    }

    /* Thay đổi màu khi hover vào liên kết trong menu con */
    .nav__submenu-anchor:hover {
        background-color: #007bff;
        color: #fff;
    }

    /* Khi ở chế độ di động */
    @media (max-width: 768px) {
        .nav__item {
            display: block;
        }

        .nav__submenu {
            position: static;
            /* Menu con sẽ xuất hiện bên dưới menu cha */
            display: none;
            /* Ẩn menu con theo mặc định */
            background-color: #fff;
            box-shadow: none;
            /* Không cần đổ bóng trên mobile */
        }

        /* Hiển thị menu con khi nhấn vào (trên di động) */
        .nav__item.active .nav__submenu {
            display: block;
        }

        /* Hiển thị nút toggle trên màn hình nhỏ */
        .nav__toggle {
            display: block;
        }
    }
</style>

<!-- js header -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lấy tất cả các mục menu
        const menuItems = document.querySelectorAll('.nav__item.nav__items');

        menuItems.forEach(item => {
            const link = item.querySelector('.nav__anchor');

            link.addEventListener('click', function(e) {
                e.preventDefault(); // Ngăn chặn hành động mặc định của liên kết

                // Đóng tất cả các menu khác
                menuItems.forEach(i => {
                    if (i !== item) {
                        i.classList.remove('active'); // Xóa lớp active
                        const submenu = i.querySelector('.header__subnav');
                        if (submenu) submenu.style.display = 'none'; // Ẩn submenu
                    }
                });

                // Mở hoặc đóng menu được nhấp
                const submenu = item.querySelector('.header__subnav');
                if (submenu) {
                    submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block'; // Chuyển đổi hiển thị
                }
            });
        });

        // Đóng menu khi nhấn bên ngoài
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.nav__item')) {
                menuItems.forEach(i => {
                    i.classList.remove('active');
                    const submenu = i.querySelector('.header__subnav');
                    if (submenu) submenu.style.display = 'none'; // Ẩn tất cả submenus
                });
            }
        });
    });
</script>