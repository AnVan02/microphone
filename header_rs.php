<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ROSA COMPUTER</title>

  <!-- Các thư viện CSS giữ nguyên -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../../style/header.css">

</head>

<body>
  <header class="header">
    <div class="container d-flex align-items-center justify-content-between py-2">
      <!-- LOGO -->
      <div class="logo_container">
        <div class="site-logo">
          <a href="/"><img src="/assets/images/rosa.webp" width="150" height="50"></a>
        </div>
      </div>

      <!-- MENU CHÍNH (Giữ nguyên tiếng Việt, Google sẽ tự dịch) -->
      <nav class="nav_container d-none d-md-block">
        <ul class="d-flex align-items-center">
          <li><a href="ve-chung-toi.php">Giới thiệu</a></li>
          <li><a href="/product.php">Sản phẩm</a></li>
          <li><a href="#">Giải pháp AI</a></li>
          <li><a href="/courses/palit.php">Chương trình</a></li>
          <li><a href="/tintuc_test/template.php">Tin tức</a></li>
          <li><a href="tra-cuu-bao-hanh.php">Bảo hành</a></li>

          <!-- NÚT CHỌN NGÔN NGỮ (Tự chế) -->
          <li class="lang-switcher">
            <a onclick="doGTranslate('vi|vi'); return false;" href="#" title="Tiếng Việt"><img src="https://theme.hstatic.net/200000168421/1000614717/14/vi.jpg?v=1336" alt=""></a>
            |
            <a onclick="doGTranslate('vi|en'); return false;" href="#" title="English"><img src="https://theme.hstatic.net/200000168421/1000614717/14/en.jpg?v=1336" alt=""></a>
          </li>
        </ul>
      </nav>

      <!-- MENU MOBILE -->
      <div class="hamburger_container d-md-none"><i class="fa fa-bars"></i></div>
    </div>
  </header>

  <!-- MENU MOBILE (Rút gọn) -->
  <div class="hamburger_menu">
    <div class="hamburger_close"><i class="fa fa-times"></i></div>
    <div class="hamburger_menu_content">
      <ul>
        <li><a href="/ve-chung-toi.php">Giới thiệu</a></li>
        <li><a href="/product.php">Sản phẩm</a></li>
        <!-- Nút chọn ngôn ngữ Mobile -->
        <li style="text-align: center; padding: 15px;">
          <a onclick="doGTranslate('vi|vi'); return false;" href="#" title="Tiếng Việt"><img src="https://theme.hstatic.net/200000168421/1000614717/14/vi.jpg?v=1336" alt=""></a>
          |
          <a onclick="doGTranslate('vi|en'); return false;" href="#" title="English"><img src="https://theme.hstatic.net/200000168421/1000614717/14/en.jpg?v=1336" alt=""></a>

        </li>
      </ul>
    </div>
  </div>
  <!-- PHẦN XỬ LÝ DỊCH TỰ ĐỘNG -->
  <div id="google_translate_element"></div>

  <!-- 2. Script khởi tạo Google Translate -->
  <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({
        pageLanguage: 'vi',
        includedLanguages: 'en,vi',
        autoDisplay: false
      }, 'google_translate_element');
    }
  </script>
  <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

  <!-- 3. Script kết nối nút bấm của bạn với Google -->
  <script type="text/javascript">
    function doGTranslate(lang_pair) {
      try {
        if (document.querySelector('.goog-te-combo') != null) {
          var teCombo = document.querySelector('.goog-te-combo');
          var lang = lang_pair.split('|')[1];
          teCombo.value = lang;
          teCombo.dispatchEvent(new Event('change'));
        }
        // Nếu chưa load xong thì reload trang để Google chạy lại
        else {
          location.reload();
        }
      } catch (e) {
        console.log(e);
      }
    }
  </script>

  <!-- JS Thư viện -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../script/header.js"></script>
</body>

</html>

<style>
  /* 1. Font chữ */
  @font-face {
    font-family: 'Montserrat';
    src: url('../font/Montserrat-VariableFont_wght.ttf') format('truetype');
    font-weight: 100 900;
    font-style: normal;
  }

  @font-face {
    font-family: 'Montserrat';
    src: url('../font/Montserrat-Italic-VariableFont_wght.ttf') format('truetype');
    font-weight: 100 900;
    font-style: italic;
  }

  .lang-switcher {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .lang-switcher a {
    text-decoration: none;
    color: #333;
    font-weight: 500;
    transition: color 0.3s ease;
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .lang-switcher a:hover {
    color: #007bff;
  }

  .lang-switcher img {
    max-width: 24px;
    height: auto;
    border-radius: 3px;
    border: 1px solid #ddd;
  }

  .lang-switcher .separator {
    color: #ccc;
    margin: 0 5px;
  }

  /* Mobile responsive */
  @media (max-width: 768px) {
    .lang-switcher img {
      max-width: 20px;
    }

    .lang-switcher a {
      font-size: 14px;
    }
  }

  /* 3. TUYỆT CHIÊU ẨN THANH GOOGLE (FIXED) */

  /* Ẩn iframe cũ */
  .goog-te-banner-frame.skiptranslate {
    display: none !important;
  }

  /* Ẩn thanh banner dạng DIV mới (Nguyên nhân chính gây lỗi hiện tại) */
  body>.skiptranslate {
    display: none !important;
  }

  /* Đẩy body lên trên cùng */
  body {
    top: 0px !important;
    position: static !important;
    margin-top: 0px !important;
  }

  /* Ẩn popup hiện ra khi rê chuột vào chữ */
  .goog-tooltip {
    display: none !important;
  }

  .goog-tooltip:hover {
    display: none !important;
  }

  /* Xóa màu nền xanh đỏ khi hover */
  .goog-text-highlight {
    background-color: transparent !important;
    border: none !important;
    box-shadow: none !important;
  }

  /* Ẩn công cụ chọn gốc */
  #google_translate_element {
    display: none !important;
  }

  /* 4. CSS Giao diện chính của bạn (Giữ nguyên) */
  body {
    font-family: 'Montserrat' !important;
    font-size: 18px;
    line-height: 1.6;
    color: #1C1D1D;
  }

  .header {
    position: sticky;
    top: 0;
    width: 100%;
    z-index: 1300;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    padding: 15px 0;
    font-size: 20px;
    font-family: Montserrat;
  }

  .logo_container img {
    max-width: 150px;
    height: auto
  }

  .nav_container {
    justify-content: center;
  }

  .nav_container ul {
    list-style: none;
    padding: 0;
    display: flex;
    align-items: center;
    /* Căn giữa dọc */
    margin-bottom: 0;
    /* Bootstrap fix */
  }

  .nav_container ul li {
    position: relative;
    margin: 0 15px;
    /* Giảm margin chút cho vừa nút lang */
  }

  .nav_container ul li a {
    text-decoration: none;
    color: #1C1D1D;
  }

  .nav_container ul li a:hover {
    color: #007bff;
  }

  /* Search Box */
  .search-box {
    border: 1px solid #ccc;
    border-radius: 25px;
    padding: 5px 20px;
    display: flex;
    align-items: center;
    background: white;
    max-width: 250px;
  }

  .search-box i {
    color: #666;
    margin-right: 8px;
    font-size: 14px;
  }

  .search-box input {
    border: none;
    outline: none;
    font-size: 14px;
    width: 100%;
    font-family: 'Montserrat';
  }

  /* Submenu CSS */
  .submenu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    list-style: none;
    padding: 0;
    margin: 0;
    min-width: 200px;
    z-index: 999;
  }

  .submenu li a {
    display: block;
    padding: 10px;
    color: #333;
    white-space: nowrap;
    font-size: 14px;
  }

  .submenu li a:hover {
    background: #f8f8f8;
    color: #007bff;
  }

  .nav_container ul li:hover>.submenu {
    display: block;
  }

  .has-submenu {
    position: relative;
  }

  .has-submenu .submenu {
    top: 0;
    left: 100%;
    display: none;
  }

  .has-submenu:hover>.submenu {
    display: block;
  }

  /* Hamburger & Mobile Menu CSS */
  .hamburger_container {
    display: none;
    font-size: 24px;
    cursor: pointer;
    padding: 8px;
    border-radius: 4px;
  }

  .hamburger_menu {
    display: none;
    position: fixed;
    top: 0;
    left: -280px;
    width: 280px;
    height: 100vh;
    background: #fff;
    z-index: 9999;
    transition: left 0.3s ease;
    overflow-y: auto;
    box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
  }

  .hamburger_menu.show {
    display: block;
    left: 0;
  }

  .hamburger_menu.show::after {
    content: '';
    position: fixed;
    top: 0;
    left: 280px;
    width: calc(100vw - 280px);
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: -1;
  }

  .hamburger_close {
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 24px;
    cursor: pointer;
    width: 40px;
    height: 40px;
    background: #f1f3f4;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .hamburger_menu_content {
    padding: 70px 0 20px 0;
  }

  .menu_logo {
    position: absolute;
    top: 15px;
    left: 20px;
    width: 80px;
    height: 40px;
  }

  .menu_logo img {
    max-width: 100%;
    max-height: 100%;
  }

  .hamburger_menu ul li {
    border-bottom: 1px solid #f0f0f0;
  }

  .hamburger_menu ul li>a {
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    color: #333;
    text-decoration: none;
    font-weight: 500;
  }

  /* Mobile Submenu */
  .hamburger_menu .submenu {
    position: static;
    box-shadow: none;
    background: #f8f9fa;
    display: none;
    /* JS will toggle */
  }

  .hamburger_menu .submenu.show {
    display: block;
  }

  .hamburger_menu .submenu li a {
    padding-left: 40px;
  }

  /* Responsive Media Queries */
  @media (max-width: 991px) {
    .hamburger_container {
      display: flex !important;
    }

    .nav_container,
    .search-box {
      display: none !important;
    }
  }

  @media (min-width: 769px) {
    .container {
      max-width: 1300px;
    }
  }
</style>