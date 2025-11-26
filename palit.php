<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="/assets/images/rosa-icon.png" type="image/png">

    <title></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

</head>

<body>


    <section class="image-container">
        <img src="ssd-kingston.webp" alt=" GeForce RTX 50 Series" alt="dblock w-100" style="width:100%; height:auto;" />
    </section>



</body>

</html>

<style>
    body {
        font-family: 'Arial';
        background-color: #000;
        color: #fff;
        font-size: 18px;
        overflow-x: hidden;
    }

    .container {
        margin: 0 auto;
        padding: 0 20px;
    }

    .content {
        margin: 0 auto;
        padding: 0 20px;
        justify-content: center;
        margin-inline: 20px;
    }

    h2 {
        justify-content: center;
        font-size: 20px;

    }

    header {
        background-color: #fff;
        padding: 12px 0;
        /* border-bottom: 1px solid #ddd; */
    }

    header .container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    .hero-banner {
        width: 100%;
        height: auto;
        image-rendering: -webkit-optimize-contrast;
        image-rendering: crisp-edges;
    }


    .logo img {
        height: 48px;
    }

    .image-container {
        width: 100vw;
        /* rộng bằng toàn bộ viewport */
        margin: 0;
        /* bỏ margin */
        padding: 0;
        /* bỏ padding */
    }

    .main-content-image {
        width: 100%;
        height: auto;
        display: block;
        /* bỏ khoảng trống dưới ảnh */
    }


    /* thah 3  */
    /* Mobile nav */
    .mobile-nav {
        display: flex;
        flex-direction: column;
        /* Xếp theo chiều dọc */
        gap: 20px;
        /* Khoảng cách giữa các mục */
        margin-top: 30px;
    }

    .mobile-nav a {
        color: #fff;
        text-decoration: none;
        font-size: 1.2rem;
        font-weight: bold;
        display: block;
        padding: 10px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }


    nav {
        display: flex;
        gap: 16px;
    }

    nav a {
        text-decoration: none;
        color: #333;
        font-weight: 500;
        font-size: 1rem;
    }

    header {
        background-color: #111;
        padding: 20px 0;
    }

    header .container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        color: red;
        font-weight: bold;
        font-size: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .logo img {
        height: 65px;
    }

    .logo span {
        font-size: 16px;
        font-weight: 500;
        color: #fff;
        letter-spacing: 1px;
    }

    /* hình  */
    nav a {
        color: #fff;
        text-decoration: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 15px;
        letter-spacing: 0.5px;
        padding: 4px 12px;
        margin: 0;
    }

    .hero {
        padding: 40px 0;

    }

    .hero-content {
        position: relative;
        min-height: 420px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
    }

    .hero-bg {
        position: absolute;
        inset: 0;
        z-index: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #1a1a1a 0%, #000 50%, #1a1a1a 100%);
    }

    .hero-text {
        position: relative;
        z-index: 1;
        flex: 1;
        padding: 20px;
    }

    .hero-text h2 {
        color: #FFF;
        font-size: 28px;
        margin-bottom: 15px;
    }

    .hero-text p {
        font-size: 17px;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .hero-text .btn {
        background-color: #7fff00;
        color: #000;
        padding: 12px 24px;
        border: none;
        font-weight: bold;
        cursor: pointer;

        text-transform: uppercase;
    }

    .hero-image {
        position: relative;
        z-index: 1;
        display: flex;
        gap: 32px;
        align-items: center;
        justify-content: center;
        margin-top: 24px;
        flex: 1;
        padding: 20px;
    }

    .hero-image img {
        max-width: 80%;
        height: auto;
    }

    .logo-partners {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 20px 0;
    }

    .logo-partners img {
        max-height: 75px;
    }

    .btn-buy {
        background: #76b900;
        color: #fff;
        font-weight: 700;
        padding: 12px 32px;
        font-size: 1.1rem;
        text-decoration: none;
        display: inline-block;
        margin-bottom: 32px;
    }

    .logo-wrap {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .logo-wrap img {
        height: 80px;
    }

    /* hedder tổng */

    /* Desktop Banner */
    /* .rtx-section {
    background: #000 url("../palit/Rectangle 3.png") no-repeat center center;
    background-size: cover;
    color: #fff;
    padding: 60px 80px;
    font-family: Arial, sans-serif;
} */


    .rtx-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 40px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .rtx-text {
        flex: 1;
    }

    .rtx-text h2 {
        font-size: 32px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .rtx-text p {
        font-size: 16px;
        margin-bottom: 15px;
        line-height: 1.6;
    }

    .btn-buy {
        display: inline-block;
        padding: 12px 24px;
        background: #76b900;
        /* xanh NVIDIA */
        color: #000;
        font-weight: bold;
        text-decoration: none;
        margin-bottom: 20px;
    }

    .logo-wrap {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-top: 20px;
    }

    .logo-wrap img {
        height: 40px;
        object-fit: contain;
    }


    .rtx-image {
        flex: 1;
        text-align: right;
    }

    .rtx-image img {
        max-width: 100%;
        height: auto;
    }

    .palit-mobile-card {
        background: #000;
        /* nền đen */
        color: #fff;
        font-family: Arial, sans-serif;
        padding: 0;
        margin: 0;
    }

    .palit-mobile-card-inner {
        display: flex;
        flex-direction: column;
        align-items: center;
    }



    .palit-mobile-card-content {
        padding: 16px;
        text-align: left;
    }

    .palit-mobile-card h3 {
        font-size: 22px;
        font-weight: bold;
        margin-bottom: 4px;
    }

    .palit-mobile-card-sub {
        font-size: 16px;
        font-weight: 500;
        margin-bottom: 12px;
    }

    .palit-mobile-card-desc {
        font-size: 14px;
        line-height: 1.5;
        margin-bottom: 16px;
    }

    .palit-mobile-card-btn {
        display: inline-block;
        background: #9c0;
        /* xanh NVIDIA */
        color: #000;
        font-weight: bold;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 4px;
        margin-bottom: 16px;
    }

    .palit-mobile-card-logos {
        display: flex;
        justify-content: flex-start;
        gap: 12px;
    }

    .palit-mobile-card-logos img {
        height: 28px;
        width: auto;
    }


    @media (min-width: 769px) {
        .palit-mobile-card {
            display: none !important;
        }
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    @media (max-width: 768px) {
        .palit-mobile-card {
            display: flex;
            justify-content: center;
            align-items: center;
            background: #000;
            padding: 24px 0 32px 0;
            width: 100vw;
            /* min-height: 100vh; */
            box-sizing: border-box;
            z-index: 100;
        }
    }

    .palit-mobile-card-inner {
        background: #000;
        box-shadow: 0 4px 24px 0 rgba(0, 0, 0, 0.25);
        max-width: 350px;
        width: 100%;
        margin: 0 auto;
        padding: 0 0 24px 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        overflow: hidden;
    }

    .palit-mobile-card {
        background: #000;
        color: #fff;
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }


    html,
    body {
        margin: 0;
        padding: 0;
    }

    .palit-mobile-card {
        margin: 0;
        padding: 0;
        height: 100vh;
        /* full chiều cao màn hình */
    }

    .palit-mobile-card-inner {
        margin: 0;
        padding: 0;

    }

    .palit-mobile-card {
        margin: 0;
        padding: 0;
    }

    .palit-mobile-card-inner {
        margin: 0;
        padding: 0;

    }

    @media (max-width: 768px) {
        .palit-mobile-card-img {
            width: 100%;
            height: 100vh;
            /* full chiều cao màn hình */
            object-fit: cover;
            /* tự cắt ảnh cho vừa khung */
            object-position: center;
            /* canh giữa ảnh */
            display: block;
            margin: 0;
            padding: 0;
        }
    }





    .palit-mobile-card-content h3 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 8px 0;
        line-height: 1.1;
    }

    .palit-mobile-card-sub {
        font-size: 1.1rem;
        color: #bdbdbd;
        margin-bottom: 12px;
        font-weight: 500;
    }

    .palit-mobile-card-desc {
        font-size: 1rem;
        color: #e0e0e0;
        margin-bottom: 33px;
        line-height: 1.5;
    }

    .palit-mobile-card-btn {
        padding: 12px 24px;
        /* display: block; */
        width: 100%;
        background: #76b900;
        color: #fff;
        font-weight: 700;
        text-align: center;
        padding: 14px 0;

        text-decoration: none;
        font-size: 1.1rem;
        margin-bottom: 18px;
        transition: background 0.2s;
    }



    .palit-mobile-card-logos {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        gap: 16px;
        margin-top: 8px;
    }

    .palit-mobile-card-logos img {
        height: 55px;
        /* background: #fff; */
        padding: 2px 6px;
    }

    .palit-mobile-card {
        background: #000;
        /* đen hẳn */
    }

    @media (max-width: 768px) {
        .hero-content {
            flex-direction: column;
        }

        /* Ẩn các phần desktop khi ở mobile */
        .hero,
        .logo-partners {
            display: none ! important;

        }


        .hero-image {
            order: -1;
            /* ép ảnh lên trên text */
            margin-top: 0;
            margin-bottom: 20px;
        }

        .hero-image img {
            max-width: 95%;
            height: auto;
        }

        .hero-text {
            text-align: left;
            /* chữ căn trái */
            padding: 0 15px;
            /* chừa khoảng cách hai bên cho dễ đọc */
        }

        .hero-text h2 {
            font-size: 22px;
        }

        .hero-text p {
            font-size: 14px;
            line-height: 1.6;
        }

        .hero-text {
            order: 2;
            /* chữ xuống dưới */
        }

        .hero-image {
            order: 1;
            /* hình ở trên */
        }

        .mobile-menu {
            position: fixed;
            top: 0;
            right: -100%;
            /* ban đầu ẩn ngoài màn hình */
            width: 80%;
            height: 100%;
            background: white;
            transition: right 0.3s ease;
            z-index: 999;
        }

        .mobile-menu.active {
            right: 0;
            /* khi có class active thì hiện ra */
        }
    }


    /* Mobile Banner */
    .hero-mobile {
        display: none;
        padding: 20px;
        background: #000;
        color: #fff;
    }

    .hero-mobile h2 {
        font-size: 1.8rem;
        margin-bottom: 8px;
    }

    .hero-mobile .sub-title {
        font-size: 1.2rem;
        margin-bottom: 16px;
    }

    .hero-mobile p {
        font-size: 1rem;
        line-height: 1.6;
    }

    /* Footer Desktop */
    .footer {
        background: #000;
        padding: 20px;
        text-align: center;
    }

    .footer img {
        height: 50px;
        margin: 0 10px;
    }

    /* Footer Mobile */
    .footer-mobile {
        display: none;
        background: #000;
        padding: 16px;
        text-align: center;
    }

    .footer-mobile img {
        height: 40px;
        margin: 0 8px;
    }

    /* Responsive */
    /*@media (max-width: 768px) {*/
    /*    .hero {*/
    /*        display: none;*/
    /*    }*/

    /*    .footer {*/
    /*        display: none;*/
    /*    }*/

    /*    .hero-mobile {*/
    /*        display: block;*/
    /*    }*/

    /*    .footer-mobile {*/
    /*        display: block;*/
    /*    }*/
    /*}*/

    /* ===== PRODUCT SECTION ===== */
    .product-section {
        text-align: center;
    }

    .product-section h2 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 60px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }

    .product-list {
        display: flex;
        justify-content: center;
        gap: 40px;
        flex-wrap: wrap;
    }

    .product {
        background: #000;
        padding: 30px;
        width: 350px;
        transition: all 0.3s;
    }

    .product:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
    }

    .product img {
        width: 115%;
        height: auto;
        margin-bottom: 25px;
    }

    .product h3 {
        font-size: 22px;
        margin-bottom: 20px;
    }

    .product h3 a {
        color: #fff;
        text-decoration: none;
        transition: color 0.3s;
    }

    .product h3 a:hover {
        color: #76b900;
    }

    .product .btn {
        font-size: 16px;
        padding: 12px 30px;
    }

    /* ==== ảnh thêm nên đen ==== */

    .image-container {
        background: url(palit_img/banner.png) center center / cover no-repeat;
    }

    /* ===== sản phẩm ====  */
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .feature-card {
        background: linear-gradient(145deg, #2a2a2a, #1e1e1e);
        padding: 30px 25px;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(118, 185, 0, 0.1);
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }

    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(118, 185, 0, 0.1), transparent);
        transition: left 0.5s;
    }

    .feature-card:hover::before {
        left: 100%;
    }

    .feature-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px rgba(118, 185, 0, 0.2);
        border-color: rgba(118, 185, 0, 0.4);
    }

    .icon {
        width: 60px;
        height: 60px;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, #76b900, #5a9200);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        position: relative;
        z-index: 1;
    }

    .feature-card:hover .icon {
        animation: pulse 0.6s infinite alternate;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        100% {
            transform: scale(1.1);
        }
    }

    .title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 15px;
        line-height: 1.3;
        color: #ffffff;
    }

    .description {
        font-size: 14px;
        color: #b0b0b0;
        line-height: 1.4;
        font-weight: 500;
    }

    .nvidia-brand {
        color: #76b900;
        font-weight: 700;
    }

    .header {
        text-align: center;
        margin-bottom: 40px;
    }

    .header h1 {
        font-size: 3rem;
        background: linear-gradient(135deg, #76b900, #a8d900);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 10px;
    }

    .header p {
        font-size: 1.2rem;
        color: #888;
    }

    /* hiện thị tren mobi */
    desktop-header {
        background: #fff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 1000;
    }

    .desktop-header .container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        flex-direction: row;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .logo img {
        height: 40px;
        width: auto;
    }

    .desktop-nav {
        display: flex;
        gap: 30px;
    }

    .desktop-nav a {
        text-decoration: none;
        color: #FFF;
        font-weight: 500;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }

    /* Hamburger Menu Button */
    .hamburger-btn {
        display: none;
        flex-direction: column;
        cursor: pointer;
        padding: 10px;
        background: none;
        border: none;
    }

    .hamburger-btn span {
        width: 25px;
        height: 3px;
        background: #333;
        margin: 3px 0;
        transition: 0.3s;

    }

    .hamburger-btn.active span:nth-child(1) {
        transform: rotate(-45deg) translate(-5px, 6px);
    }

    .hamburger-btn.active span:nth-child(2) {
        opacity: 0;
    }

    .hamburger-btn.active span:nth-child(3) {
        transform: rotate(45deg) translate(-5px, -6px);
    }

    /* Mobile Menu */
    .mobile-menu {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background: rgba(0, 0, 0, 0.9);
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .mobile-menu.active {
        opacity: 1;
        visibility: visible;
    }

    .mobile-menu-content {
        position: absolute;
        top: 0;
        right: 0;
        width: 300px;
        height: 100vh;
        background: white;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        overflow-y: auto;
    }

    .mobile-menu.active .mobile-menu-content {
        transform: translateX(0);
    }

    .mobile-menu-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #eee;
    }

    .mobile-close-btn {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #666;
    }

    .mobile-nav {
        padding: 20px;
    }

    .mobile-nav a {
        display: block;
        text-decoration: none;
        color: #333;
        padding: 15px 0;
        border-bottom: 1px solid #eee;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .mobile-nav a:hover {
        color: #007bff;
    }

    .mobile-nav a:last-child {
        border-bottom: none;
    }

    /* Demo Content */
    .demo-content {
        padding: 50px 0;
        text-align: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        min-height: 100vh;
    }

    .demo-content h1 {
        font-size: 3rem;
        margin-bottom: 20px;
    }

    .demo-content p {
        font-size: 1.2rem;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .desktop-nav {
            display: none;
        }

        .hamburger-btn {
            display: flex;
        }

        .mobile-menu {
            display: block;
        }

        .demo-content h1 {
            font-size: 2rem;
        }

        .demo-content p {
            font-size: 1rem;
            padding: 0 20px;
        }
    }

    @media (max-width: 480px) {
        .mobile-menu-content {
            width: 100%;
        }
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .grid {
            grid-template-columns: 1fr;
        }

        .header h1 {
            font-size: 2rem;
        }

        .feature-card {
            padding: 25px 20px;
        }
    }

    /* Animation on load */
    .feature-card {
        opacity: 0;
        transform: translateY(30px);
        animation: slideInUp 0.6s ease-out forwards;
    }

    .feature-card:nth-child(1) {
        animation-delay: 0.1s;
    }

    .feature-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .feature-card:nth-child(3) {
        animation-delay: 0.3s;
    }

    .feature-card:nth-child(4) {
        animation-delay: 0.4s;
    }

    .feature-card:nth-child(5) {
        animation-delay: 0.5s;
    }

    .feature-card:nth-child(6) {
        animation-delay: 0.6s;
    }

    .feature-card:nth-child(7) {
        animation-delay: 0.7s;
    }

    .feature-card:nth-child(8) {
        animation-delay: 0.8s;
    }

    @keyframes slideInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ===== BLACKWELL SECTION ===== */
    .blackwell-section {
        /* padding: 100px 0; */
        text-align: center;
    }

    .blackwell-section h2 {
        font-size: 42px;
        font-weight: 700;
        margin-bottom: 15px;
        text-transform: uppercase;
    }

    .blackwell-section h3 {
        font-size: 24px;
        font-weight: 400;
        margin-bottom: 60px;
        color: rgba(255, 255, 255, 0.8);
    }

    .blackwell-features {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 80px;
        margin-bottom: 80px;
        flex-wrap: wrap;
    }

    .feature-text {
        max-width: 350px;
        text-align: center;
    }

    .feature-text h4 {
        font-size: 24px;
        margin-bottom: 15px;
        color: #f6f6f6;
    }

    .feature-text p {
        font-size: 16px;
        color: rgba(255, 255, 255, 0.8);
    }

    .blackwell-chip-image {
        width: 220px;
        height: 220px;
        object-fit: contain;
    }

    .section-description {
        max-width: 900px;
        margin: 0 auto 60px;
        text-align: center;
    }

    .section-description h4 {
        font-size: 28px;
        margin-bottom: 20px;
        color: #fff;
    }

    .section-description p {
        font-size: 18px;
        color: rgba(255, 255, 255, 0.8);
    }

    .main-content-image {
        width: 100%;
        max-width: 1300px;
        height: auto;
        margin: 0 auto 60px;
        display: block;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
    }

    /* Đồng bộ kích thước ảnh trong features grid */

    .features-section-title {
        display: flex;
        justify-content: center;
        margin-bottom: 32px;
    }

    .features-section-title h4 {
        font-size: 28px;
        margin-bottom: 8px;
        text-align: center;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto 48px auto;
    }

    .features-grid .feature-item {
        /* background: #222; */

        padding: 28px 24px 18px 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .features-grid img {
        width: 100%;
        max-width: 600px;
        height: 280px;
        object-fit: cover;

        /* box-shadow: 0 2px 16px #222; */
    }

    .features-grid .feature-title {
        font-size: 20px;
        font-weight: 600;
        color: #fff;
        margin-bottom: 8px;
        text-align: left;
        width: 100%;
    }

    .features-grid .feature-description {
        color: #ccc;
        font-size: 15px;
        text-align: left;
        width: 100%;
    }

    /* Đồng bộ kích thước ảnh chip Blackwell */
    .blackwell-chip-image {
        width: 188px;
        height: 188px;
        object-fit: cover;

        box-shadow: 0 2px 16px #222;
    }

    hr {
        border-bottom: 1px solid #ddd;


    }


    /* ===== FOOTER ===== */
    footer {
        /* background: #111; */
        padding: 50px 0;
        text-align: center;
    }

    .footer-logos {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 60px;
        margin-bottom: 30px;
    }

    .footer-logos img {
        height: 70px;
        object-fit: contain;
    }

    /* Dòng kẻ trên footer */
    .footer-line {
        border: none;
        border-top: 1px solid #444;
        /* màu xám đậm */
        margin: 0 auto 20px auto;
        /* căn giữa + cách logo 20px */
        width: 70%;
        /* độ rộng 90% màn hình */
    }

    /* Footer */
    footer {
        width: 100%;
        background-color: #000;
        /* nền đen */
        padding: 20px 0;
        text-align: center;
        color: #fff;
        font-size: 12px;
        line-height: 1.5;
    }

    .footer-logos {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 30px;
        /* khoảng cách giữa logo */
        margin-bottom: 15px;
    }

    .footer-logos img {
        max-height: 40px;
        /* chỉnh chiều cao logo */
        object-fit: contain;
    }


    /* Responsive cho mobile */
    @media (max-width: 768px) {
        .footer-logos {
            flex-direction: column;
            gap: 20px;
        }
    }


    /* ===== RESPONSIVE ===== */

    @media (max-width: 450px) {
        .dlss-text {
            font-size: 15px;
            flex-direction: 20px;

        }
    }

    @media (max-width: 768px) {

        header .container {
            flex-direction: column;
            align-items: center;
        }

        .logo {
            margin-bottom: 12px;
        }

        nav a {
            font-size: 1rem;
            padding: 8px 0;
        }

        /* Container padding and layout */
        .container {
            padding: 0 16px;
            max-width: 100%;
            box-sizing: border-box;
        }

        /* Header */
        header .container {
            flex-direction: column;
            align-items: center;
        }

        nav a {
            font-size: 0.9rem;
            padding: 6px;
        }

        /* Hero section */
        .hero-content {
            flex-direction: column !important;
            text-align: center;
            padding: 16px;
        }

        .hero-left {
            padding: 16px !important;
        }

        .hero-left h2 {
            font-size: 1.5rem;
        }

        .hero-left p {
            font-size: 0.95rem;
        }

        .hero-image img {
            width: 60%;
            max-width: 90vw;
            height: auto;
        }

        .btn {
            padding: 10px 20px;
            font-size: 1rem;
            display: inline-block;
        }

        /* Product section */
        .product-list {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .product {
            margin-bottom: 24px;
            text-align: center;
        }

        .product img {
            width: 90%;
            height: auto;
        }

        /* Blackwell sections */
        .blackwell-features {
            flex-direction: column;
            gap: 24px;
            text-align: center;
        }

        .blackwell-features img {
            width: 90%;
            max-width: 250px;
            height: auto;
        }

        .section-description h3,
        .section-description h4 {
            font-size: 1.4rem !important;
        }

        .section-description p {
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Image containers */
        .image-container img.main-content-image {
            width: 100%;
            max-width: 100%;
            height: auto;

        }

        /* Features grid */
        .features-grid {
            display: flex;
            flex-direction: column;
            gap: 24px;
            padding: 0 12px;
        }

        .feature-item {
            text-align: center;
        }

        .feature-title {
            font-size: 1.1rem;
            margin: 8px 0;
        }

        .feature-description {
            font-size: 0.95rem;
            line-height: 1.6;
        }

        /* Footer logos */
        .footer-logos {
            display: flex;
            flex-direction: column;
            gap: 16px;
            align-items: center;
            margin-top: 32px;
        }

        .footer-logos img {
            max-width: 100px;
            height: auto;
        }

        .footer-logos {
            flex-direction: column;
            /* xếp dọc */
            gap: 15px;
            /* khoảng cách ngắn hơn */
        }

        .footer-logos img {
            max-height: 50px;
            /* logo to hơn chút cho dễ nhìn */
        }

        footer p {
            font-size: 11px;
            /* chữ nhỏ gọn hơn */
            padding: 0 10px;
            /* chừa khoảng cách hai bên */
        }

    }
</style>