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

    /* Mobile Menu Styles */
    .mobile-menu-toggle {
        display: none;
        flex-direction: column;
        justify-content: space-between;
        width: 30px;
        height: 21px;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 0;
        z-index: 1001;
        margin-left: 15px;
    }

    .mobile-menu-toggle span {
        display: block;
        height: 3px;
        width: 100%;
        background-color: #333;
        border-radius: 3px;
        transition: all 0.3s ease;
    }

    .mobile-menu-toggle.active span:nth-child(1) {
        transform: rotate(45deg) translate(6px, 6px);
    }

    .mobile-menu-toggle.active span:nth-child(2) {
        opacity: 0;
    }

    .mobile-menu-toggle.active span:nth-child(3) {
        transform: rotate(-45deg) translate(6px, -6px);
    }

    .mobile-menu-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 998;
    }

    .mobile-menu {
        display: none;
        position: fixed;
        top: 0;
        right: -100%;
        width: 80%;
        max-width: 300px;
        height: 100%;
        background: white;
        z-index: 999;
        overflow-y: auto;
        transition: right 0.3s ease;
        box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .mobile-menu.active {
        right: 0;
    }

    .mobile-menu-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #ff0000;
    }

    .mobile-menu-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: white;
    }

    .mobile-nav__list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .mobile-nav__item {
        border-bottom: 1px solid #eee;
    }

    .mobile-nav__anchor {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        text-decoration: none;
        color: #333;
        font-weight: 500;
        font-size: 14px;
    }

    .mobile-nav__subnav {
        display: none;
        background: #f8f9fa;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .mobile-nav__subnav.active {
        display: block;
    }

    .mobile-nav__subitem {
        border-bottom: 1px solid #e9ecef;
    }

    .mobile-nav__subanchor {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 20px 12px 30px;
        text-decoration: none;
        color: #666;
        font-size: 13px;
    }

    .mobile-nav__submenu {
        display: none;
        background: #f1f3f4;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .mobile-nav__submenu.active {
        display: block;
    }

    .mobile-nav__subsubanchor {
        display: block;
        padding: 10px 20px 10px 40px;
        text-decoration: none;
        color: #666;
        font-size: 12px;
        border-bottom: 1px solid #e9ecef;
    }

    .mobile-menu-toggle-icon {
        transition: transform 0.3s ease;
        width: 16px;
        height: 16px;
    }

    .mobile-menu-toggle-icon.active {
        transform: rotate(180deg);
    }

    /* Header Layout for Mobile */
    .header__container {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .header__logo {
        flex: 1;
        display: flex;
        justify-content: center;
    }

    .header__action {
        display: flex;
        align-items: center;
        margin-left: auto;
    }

    /* Responsive Styles */
    @media (max-width: 991px) {
        .mobile-menu-toggle {
            display: flex;
            order: 3;
            /* Đặt nút menu ở vị trí thứ 3 (bên phải) */
        }

        .header__nav {
            display: none !important;
        }

        /* Sắp xếp thứ tự các phần tử trong header */
        .header__container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header__logo {
            order: 2;
            flex: 1;
            justify-content: center;
        }

        .header__action {
            order: 1;
            margin-left: 0;
        }

        .mobile-menu-toggle {
            order: 3;
        }

        /* Hide desktop menu on mobile */
        .nav__list.md-flex {
            display: none !important;
        }

        /* Show mobile menu when active */
        .mobile-menu-overlay.active,
        .mobile-menu.active {
            display: block;
        }

        /* Điều chỉnh search box trên mobile */
        .search__input {
            width: 150px;
            font-size: 12px;
        }
    }

    @media (max-width: 768px) {
        .header__logo img {
            width: 150px;
        }

        .search__input {
            width: 120px;
        }

        .header__topbar .h5 {
            font-size: 12px;
        }

        .header__action--item {
            margin-left: 10px;
        }
    }

    @media (max-width: 480px) {
        .header__logo img {
            width: 120px;
        }

        .search__input {
            width: 100px;
            font-size: 11px;
        }

        .header__topbar .h5 {
            font-size: 10px;
        }

        .mobile-menu {
            width: 85%;
        }
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
            <div class="header__container">
                <!-- Logo ở giữa -->
                <div class="header__logo">
                    <a href="index.php" class="d-inline-block">
                        <img src="assets/images/logo/logoVS_new.png" alt="Footer Logo" style="width: 200px; height: auto; max-width: 100%;">
                    </a>
                </div>

                <!-- Action icons bên trái -->
                <div class="header__action">
                    <div class="header__action--item d-flex align-center p-relative">
                        <form action="index.php?page=search" method="POST" class="d-flex align-center" id="search-box">
                            <input type="text" placeholder="Tìm kiếm..." id="input-search" name="keyword" class="search__input" required>
                            <button type="submit" name="search" class="header__action--link search-btn p-absolute d-inline-block">
                                <img class="action__icon svg__icon d-block" src="./assets/images/icon/icon-search.svg" alt="search" />
                            </button>
                        </form>
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

                <!-- Mobile Menu Toggle bên phải -->
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Desktop Navigation (Hidden on Mobile) -->
    <nav class="header__nav space-between d-flex">
        <ul class="nav__list md-flex">
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
                <ul class="header__subnav p-absolute">
                    <li class="nav__item">
                        <a class="nav__anchor h7 d-flex align-center space-between" href="index.php?page=about">
                            GIỚI THIỆU
                        </a>
                    </li>
                    <li class="nav__item">
                        <a class="nav__anchor h7 d-flex align-center space-between" href="index.php?page=CSBH">
                            CHÍNH SÁCH BẢO HÀNH - ĐỔI TRẢ
                        </a>
                    </li>
                    <li class="nav__item">
                        <a class="nav__anchor h7 d-flex align-center space-between" href="https://www.vietsontdc.com/PSSD">
                            CHƯƠNG TRÌNH
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
                <ul class="header__subnav p-absolute">
                    <!-- Các mục sản phẩm... -->
                </ul>
            </li>
        </ul>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-header">
            <img src="assets/images/logo/logoVS_new.png" alt="Logo" style="height: 30px; filter: brightness(0) invert(1);">
            <button class="mobile-menu-close" id="mobileMenuClose">×</button>
        </div>

        <ul class="mobile-nav__list">
            <li class="mobile-nav__item">
                <a class="mobile-nav__anchor" href="index.php">
                    TRANG CHỦ
                </a>
            </li>

            <li class="mobile-nav__item mobile-nav__items">
                <a class="mobile-nav__anchor" href="javascript:void(0)">
                    VỀ CHÚNG TÔI
                    <img class="mobile-menu-toggle-icon" src="./assets/images/icon/icon-chevron-down.svg" alt="toggle">
                </a>
                <ul class="mobile-nav__subnav">
                    <li class="mobile-nav__subitem">
                        <a class="mobile-nav__subanchor" href="index.php?page=about">
                            GIỚI THIỆU
                        </a>
                    </li>
                    <li class="mobile-nav__subitem">
                        <a class="mobile-nav__subanchor" href="index.php?page=CSBH">
                            CHÍNH SÁCH BẢO HÀNH
                        </a>
                    </li>
                    <li class="mobile-nav__subitem">
                        <a class="mobile-nav__subanchor" href="https://www.vietsontdc.com/PSSD">
                            CHƯƠNG TRÌNH
                        </a>
                    </li>
                </ul>
            </li>

            <li class="mobile-nav__item">
                <a class="mobile-nav__anchor" href="index.php?page=blog">
                    TIN TỨC
                </a>
            </li>

            <li class="mobile-nav__item">
                <a class="mobile-nav__anchor" href="index.php?page=contact">
                    LIÊN HỆ
                </a>
            </li>

            <li class="mobile-nav__item">
                <a class="mobile-nav__anchor" href="index.php?page=check">
                    BẢO HÀNH
                </a>
            </li>

            <li class="mobile-nav__item mobile-nav__items">
                <a class="mobile-nav__anchor" href="javascript:void(0)">
                    SẢN PHẨM
                    <img class="mobile-menu-toggle-icon" src="./assets/images/icon/icon-chevron-down.svg" alt="toggle">
                </a>
                <ul class="mobile-nav__subnav">
                    <li class="mobile-nav__subitem">
                        <a class="mobile-nav__subanchor" href="index.php?page=products">
                            TẤT CẢ SẢN PHẨM
                        </a>
                    </li>

                    <li class="mobile-nav__subitem">
                        <a class="mobile-nav__subanchor" href="index.php?page=products&brand_id=10">
                            INTEL
                        </a>
                    </li>

                    <li class="mobile-nav__subitem">
                        <a class="mobile-nav__subanchor" href="index.php?page=products&brand_id=14">
                            AMD
                        </a>
                    </li>

                    <li class="mobile-nav__subitem">
                        <a class="mobile-nav__subanchor" href="index.php?page=products&brand_id=15">
                            ASUS
                        </a>
                    </li>

                    <li class="mobile-nav__subitem">
                        <a class="mobile-nav__subanchor" href="index.php?page=products&brand_id=7">
                            LEXAR
                        </a>
                    </li>

                    <li class="mobile-nav__subitem">
                        <a class="mobile-nav__subanchor" href="index.php?page=products&brand_id=3">
                            KINGSTON
                        </a>
                    </li>

                    <li class="mobile-nav__subitem">
                        <a class="mobile-nav__subanchor" href="index.php?page=products&brand_id=5">
                            PALIT
                        </a>
                    </li>

                    <li class="mobile-nav__subitem">
                        <a class="mobile-nav__subanchor" href="index.php?page=products&brand_id=1">
                            POWER COLOR
                        </a>
                    </li>

                    <li class="mobile-nav__subitem">
                        <a class="mobile-nav__subanchor" href="index.php?page=products&brand_id=13">
                            ASROCK
                        </a>
                    </li>

                    <li class="mobile-nav__subitem">
                        <a class="mobile-nav__subanchor" href="index.php?page=products&brand_id=11">
                            AOC
                        </a>
                    </li>

                    <li class="mobile-nav__subitem">
                        <a class="mobile-nav__subanchor" href="index.php?page=products&brand_id=8">
                            GSKILL
                        </a>
                    </li>

                    <li class="mobile-nav__subitem">
                        <a class="mobile-nav__subanchor" href="index.php?page=products&brand_id=4">
                            ROSA
                        </a>
                    </li>
                </ul>
            </li>

            <!-- User Account -->
            <li class="mobile-nav__item">
                <a class="mobile-nav__anchor" href="<?php if (isset($_SESSION['account_email'])) {
                                                        echo "index.php?page=my_account&tab=account_info";
                                                    } else {
                                                        echo "index.php?page=login";
                                                    } ?>">
                    👤 TÀI KHOẢN
                </a>
            </li>
        </ul>
    </div>
</header>

<script>
    // Mobile Menu Functionality
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
        const mobileMenuClose = document.getElementById('mobileMenuClose');

        // Toggle mobile menu
        function toggleMobileMenu() {
            mobileMenuToggle.classList.toggle('active');
            mobileMenu.classList.toggle('active');
            mobileMenuOverlay.classList.toggle('active');
            document.body.style.overflow = mobileMenu.classList.contains('active') ? 'hidden' : '';
        }

        mobileMenuToggle.addEventListener('click', toggleMobileMenu);
        mobileMenuOverlay.addEventListener('click', toggleMobileMenu);
        mobileMenuClose.addEventListener('click', toggleMobileMenu);

        // Mobile menu dropdown functionality
        const mobileMenuItems = document.querySelectorAll('.mobile-nav__items');

        mobileMenuItems.forEach(item => {
            const anchor = item.querySelector('.mobile-nav__anchor');
            const subnav = item.querySelector('.mobile-nav__subnav');
            const icon = item.querySelector('.mobile-menu-toggle-icon');

            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                subnav.classList.toggle('active');
                icon.classList.toggle('active');
            });
        });

        // Voice input function
        function voiceInput() {
            var inputSearch = document.getElementById('input-search');
            var searchBtn = document.querySelector('.voice-btn');
            const recognition = new webkitSpeechRecognition();
            recognition.lang = 'vi-VN';
            recognition.continuous = false;

            recognition.onresult = function(event) {
                const transcript = event.results[0][0].transcript;
                inputSearch.value = transcript;
            };

            recognition.onstart = function() {
                searchBtn.classList.add('recognizing');
            };

            recognition.onend = function() {
                searchBtn.classList.remove('recognizing');
            };

            recognition.onerror = function(event) {
                console.error(event.error);
            };

            recognition.start();
        }

        window.voiceInput = voiceInput;
    });
</script>