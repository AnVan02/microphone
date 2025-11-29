<?php
$GLOBALS['page_specific_title'] = " Máy tính bộ, văn phòng, gaming, AI, mini pc ";
$GLOBALS['page_specific_description'] = "PC để bàn, máy tính đồng bộ, vi tính văn phòng giá rẻ, đa dạng cấu hình. Thương hiệu máy tính Việt Nam, trả góp, giao hàng nhanh, bảo hành chính hãng.";

?>
<?php require "header.php" ?>

<link rel="stylesheet" href="style/trangchu.css">

<!-- Banner -->
<div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">

  <!-- Các dấu chấm -->
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="0" class="active" aria-current="true"></button>
        <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="2"></button>
        <!--<button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="3"></button>-->
        <!--<button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="4"></button>-->
        <!--<button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="5"></button>-->
    </div>

    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="image/ROSA Palit 5080 size 1920 X 640.webp" class="d-block w-100" alt="Banner 1" onclick="window.location.href='https://rosacomputer.vn/courses/palit.php'">
        </div>
        <div class="carousel-item">
            <img src="image/ROSA MINIPC Series BANNER.webp" class="img-fluid" alt="Banner2">
        </div>
        <div class="carousel-item">
            <img src="image/banner news AI Agent 2.webp" class="img-fluid" alt="Banner 3" onclick="windown.location.href='https://rosacomputer.vn/product.php?may-tinh-ai'">
        </div>
        <!-- <div class="carousel-item">-->
        <!--    <img src="../image/Banner web 1.webp" class="img-fluid" alt="New Year Banner" onclick="window.location.href='product.php#vanphong'">-->
        <!--</div> -->
        <!--<div class="carousel-item">-->
        <!--    <img src="assets/images/Banner web 2.png" class="img-fluid" alt="Palit Banner" onclick="window.location.href='product.php'">-->
        <!--</div>-->
        
        <!--<div class="carousel-item">-->
        <!--    <img src="assets/images/Banner web 3.png" class="img-fluid" alt="New Year Banner" onclick="window.location.href='product.php#gaming'">-->
        <!--</div>-->

    </div>
</div>
</div>


<!-- Title -->
<br>
<section class="ai-section" style="margin-top: 40px;">
  <h1 class="ds-title">DÒNG SẢN PHẨM</h1>
  <p class="ds-subtitle">Đáp ứng đa dạng nhu cầu, mạnh mẽ, bền bỉ</p>

  <!-- Section: Dòng sản phẩm -->
<div class="ds-grid">
  <!-- Office -->
  <div class="ds-item">
    <a href="product.php?may-tinh-van-phong"
       onclick="activateCategory('vanphong'); return false;">
      <img src="image/RSa_office.webp" alt="Office PC" loading="lazy" width="800" height="600">
    </a>
  </div>

  <!-- AI -->
  <div class="ds-item">
    <a href="product.php?may-tinh-ai"
       onclick="activateCategory('ai'); return false;">
      <img src="image/RSa_ai.webp" alt="AI PC" loading="lazy" width="800" height="600">
    </a>
  </div>

  <!-- Gaming -->
  <div class="ds-item">
    <a href="product.php?may-tinh-gaming"
       onclick="activateCategory('gaming'); return false;">
      <img src="image/RSa_gaming.webp" alt="Gaming PC"loading="lazy" width="800" height="600">
    </a>
  </div>

  <!-- Mini PC -->
  <div class="ds-item">
    <a href="product.php?mini-pc"
       onclick="activateCategory('mini'); return false;">
      <img src="image/RSa_minipc.webp" alt="Mini PC" loading="lazy" width="800" height="600">
    </a>
  </div>
</div>

<section class="article-section">
  <div id="article-content" class="collapsed">
    <h2>Máy Tính Để Bàn - Mạnh Mẽ, Đẳng Cấp</h2>
    <p>
      <a href="anh-seo/tongquat1.jpg">Máy tính để bàn</a> là thiết bị quen thuộc, giúp chúng ta xử lý công việc nhanh
      chóng, dễ dàng. Thế
      nhưng nhiều người vẫn chưa hiểu hết về sản phẩm, cũng như chưa biết nên mua loại nào hay mua ở đâu cho uy tín?
      Hãy cùng tìm hiểu máy tính để bàn thông qua bài viết này nhé!
    </p>
    <!-- PHẦN NỘI DUNG ẨN (More Content) -->
    <div class="more-content">
      <h2>1. Sơ Lược Về Máy Tính Để Bàn</h2>
      <p>
        Khác với laptop, máy tính để bàn sẽ có 6 bộ phận chính là: bo mạch chủ, chip xử lý trung tâm CPU, bộ nhớ RAM,
        card màn hình, ổ cứng và nguồn. Mỗi bộ phận sẽ đảm nhiệm những chức năng khác nhau,
        cấu thành một chiếc máy tính đặt bàn hoàn chỉnh.
      </p>
      <img src="anh-seo/tongquat2.png" alt="Cấu trúc máy tính để bàn">

      <h2>2. Các Dòng Máy Tính Để Bàn Phổ Biến Hiện Nay</h2>
      <h3>Máy Tính Để Bàn Học Tập - Văn Phòng</h3>
      <p>
        Dòng máy tính để bàn văn phòng này phù hợp với người dùng cơ bản: học sinh, nhân viên văn phòng, doanh nghiệp
        nhỏ. Cấu hình vừa phải, giá thành hợp lý nhưng vẫn đảm bảo mượt mà khi xử lý các tác vụ như Word, Excel, Zoom,
        Google Meet, Photoshop cơ bản.
      </p>
      <p>
        Tại ROSA COMPUTER, dòng sản phẩm này được tối ưu cho khả năng tiết kiệm điện năng, hoạt động ổn định 24/7, và dễ
        dàng bảo trì, nâng cấp khi cần.
      </p>
      <img src="anh-seo/tongquat3.png" alt="Máy tính văn phòng">

      <h3>Máy Tính Để Bàn Đồ Họa - Kỹ Thuật</h3>
      <p>
        Nếu bạn là dân thiết kế đồ họa, dựng phim, kỹ sư kiến trúc hay lập trình viên, thì dòng máy tính để bàn đồ họa –
        kỹ thuật là lựa chọn lý tưởng. Các mẫu máy ROSA được trang bị CPU Intel Core i7, AMD Ryzen, card đồ họa NVIDIA,
        PALIT hoặc AMD Radeon, RAM từ 32GB trở lên, cùng ổ SSD tốc độ cao.
      </p>
      <p>
        Điểm mạnh của ROSA COMPUTER là khả năng tùy chỉnh cấu hình linh hoạt, giúp tối ưu chi phí theo từng dự án, đồng
        thời bảo đảm hiệu năng và độ bền vượt trội.
      </p>
      <img src="anh-seo/tongquat4.png" alt="Máy tính đồ họa">

      <h3>Máy Tính Để Bàn Gaming</h3>
      <p>
        Game thủ chuyên nghiệp không thể bỏ qua dòng máy tính để bàn gaming của ROSA COMPUTER. Sở hữu thiết kế đậm chất
        gaming, hệ thống tản nhiệt cao cấp, card đồ họa mạnh mẽ và CPU đa nhân hiệu suất cao, dòng máy này giúp bạn
        chinh phục mọi tựa game từ Liên Minh Huyền Thoại, Valorant, FIFA Online 4 đến Cyberpunk 2077.
      </p>
      <p>
        Đặc biệt, ROSA Gaming PC còn tích hợp hệ thống đèn RGB, vỏ kính cường lực, đem lại trải nghiệm vừa mạnh mẽ, vừa
        đẳng cấp thẩm mỹ.
      </p>
      <img src="anh-seo/tongquat5.png" alt="Máy tính gaming">

      <h3>Mini PC - Nhỏ Gọn, Tiện Dụng</h3>
      <p>
        Với xu hướng làm việc linh hoạt, Mini PC của ROSA COMPUTER là giải pháp hoàn hảo cho không gian nhỏ. Kích thước
        chỉ bằng một cuốn sách, nhưng sức mạnh xử lý tương đương máy tính truyền thống, đặc biệt tiết kiệm điện năng và
        giảm tiếng ồn.
      </p>
      <p>
        Mini PC rất phù hợp cho doanh nghiệp SMB, văn phòng làm việc nhóm, hoặc quầy giao dịch cần hiệu suất cao mà vẫn
        gọn gàng.
      </p>
      <img src="anh-seo/tongquat6.png" alt="Mini PC">

      <h3>Máy Tính AI/SMB - Dòng Máy Tích Hợp Trí Tuệ Nhân Tạo</h3>
      <p>
        Đây là dòng máy tính để bàn chuyên biệt của ROSA COMPUTER, thiết kế cho doanh nghiệp thông minh, AI-ready. Các
        mẫu ROSA AI PC được tích hợp chatbot AI Rosa, nền tảng phân tích dữ liệu, và công nghệ NPU/AI accelerator, giúp
        doanh nghiệp tối ưu hiệu suất vận hành, giảm chi phí quản lý và cải thiện trải nghiệm người dùng. Đặc biệt, dòng
        máy tính bộ SMB ROSA hỗ trợ hạ tầng AI on-premise, dễ dàng kết nối Nextcloud Server, phục vụ giải pháp doanh
        nghiệp toàn diện.
      </p>
      <img src="anh-seo/tongquat7.png" alt="Máy tính AI">

      <h2>3. Tiêu Chí Chọn Mua Máy Tính Để Bàn Chất Lượng</h2>
      <h3>Màn Hình Máy Tính</h3>
      <p>
        Màn hình 19 - 24 inch sẽ giúp tiết kiệm được không gian làm việc. Bên cạnh đó nên chọn màn hình có độ phân giải
        Full HD đi kèm công nghệ như Công nghệ Anti Glare độ sáng khoảng 250 nits cho hình ảnh hiển thị rõ ràng. Tấm nền
        IPS mở rộng góc nhìn lên đến 178 độ giúp dễ dàng quan sát ở nhiều góc độ khác nhau, sẽ đảm bảo đáp ứng được các
        nhu cầu làm việc, giải trí cơ bản.
        Bên cạnh đó, nếu bạn làm việc liên quan đến thiết kế đồ họa, kỹ sư, cắt dựng phim,... thì bạn nên cân nhắc lựa
        chọn những loại màn hình có kích cỡ lớn và thông số độ phân giải cao hơn như 4.5K, độ sáng 500 nits để có tầm
        nhìn và độ chi tiết cao hơn, đáp ứng được yêu cầu công việc.
      </p>
      <img src="anh-seo/tongquat8.jpg" alt="Màn hình máy tính">

      <h3>Hệ Điều Hành Máy Tính</h3>
      <p>
        Hầu hết các dòng máy tính để bàn hiện có trên thị trường đều chạy hệ điều hành Microsoft Windows (chiếm thị phần
        nhiều nhất), MacOS của Apple Inc. và Linux (dành cho lập trình viên).
      </p>
      <p>
        Các nhà sản xuất như HP, Dell, Asus, Lenovo… đều sử dụng hệ điều hành Windows bởi sự thuận lợi, dễ sử dụng và
        khả năng tương thích cao với nhiều phân khúc giá, cho người dùng nhiều sự lựa chọn. Còn đối với MacOS, đây là sự
        lựa chọn ưu tiên cho iFan do Apple đã xây dựng một hệ sinh thái mà người dùng Apple có thể sử dụng các ứng dụng
        iPhone, iPad ngay trên chiếc Macbook của họ.
      </p>
      <img src="anh-seo/tongquat9.png" alt="Hệ điều hành">

      <h3>Bộ Vi Xử Lý CPU</h3>
      <p>
        CPU hay còn gọi là chip máy tính đóng vai trò là một bộ vi xử lý trung tâm. Chúng là chi tiết quan trọng giúp
        máy trở lên ổn định, chạy mượt và bền hơn, xử lý nhanh chóng các tác vụ trên máy tính.
      </p>
      <p>
        CPU của Intel hoặc AMD là hai loại người mua nên chọn bởi độ uy tín cũng như chất lượng. Dân thiết kế đồ họa,
        dựng phim, làm nhạc… nên lựa chọn máy có bộ vi xử lý hoạt động mạnh như CPU Core i5, i7.
      </p>
      <img src="anh-seo/tongquat10.png" alt="CPU">

      <h3>Bộ Nhớ RAM</h3>
      <p>
        RAM là bộ nhớ đệm của một chiếc máy tính. RAM càng lớn thì càng chứa được nhiều dữ liệu hơn, máy tính của bạn sẽ
        chạy càng mượt cũng như hiệu năng mạnh mẽ hơn. Bạn nên mua máy có RAM từ 8GB trở lên để truy xuất dữ liệu, xử lý
        tác vụ nhanh hơn, tránh tình trạng giật lag.
      </p>
      <img src="anh-seo/tongquat11.png" alt="RAM">

      <h3>Ổ Cứng</h3>
      <p>
        Ổ cứng là bộ nhớ lưu trữ các dữ liệu trên một chiếc máy tính để bàn như hình ảnh, video, âm thanh… và cả hệ điều
        hành. Nó quyết định đến mức độ bảo mật dữ liệu, khả năng truy xuất dữ liệu hay tốc độ khởi động của một chiếc
        máy tính. Hiện nay các thiết bị máy tính để bàn thường được trang bị 2 loại ổ cứng là SSD hoặc HDD. Hầu hết các
        laptop đều đang dần chuyển sang ổ SSD để có thể phục vụ người dùng tốt nhất.
      </p>
      <img src="anh-seo/tongquat12.png" alt="Ổ cứng">

      <h3>Thương Hiệu</h3>
      <p>
        Hiện nay, mỗi thương hiệu đều có những đặc trưng, thế mạnh và khách hàng trung thành riêng. Nếu nói về chất
        lượng thì cũng gần tương đương nhau vì dù là máy tính Apple hay Lenovo, HP, ASUS,.. tất cả đều thường sử dụng
        các thành phần linh kiện từ các đối tác thứ 3 như: Chip từ Intel, card đồ họa của AMD hay NVIDIA, chip nhớ/SSD
        Samsung,...
      </p>
      <img src="anh-seo/tongquat13.png" alt="Thương hiệu">

      <h3>Giá Cả</h3>
      <p>
        Tùy thuộc vào hệ điều hành, thương hiệu, CPU… mà mỗi máy tính để bàn có giá thành khác nhau. Tuy nhiên, chất
        lượng luôn đi đôi với giá thành, vậy nên bạn cần cân nhắc nhu cầu của bản thân cũng như khả năng tài chính để
        chọn mua máy phù hợp.
      </p>

      <h2>4. Tại Sao Nên Mua Máy Tính Để Bàn?</h2>
      <p>
        Mặc dù máy tính để bàn cồng kềnh, chiếm diện tích lớn, không có tính di động như laptop nhưng không thể phủ nhận
        việc không gì có thể thay thế được trong việc thực hiện những công việc phức tạp, những tác vụ trong công việc
        như máy tính để bàn. Nó sở hữu những ưu điểm vượt trội hơn so với laptop, máy tính bảng như:
      </p>
      <ol>
        <li>Giá thành thấp hơn.</li>
        <li>Sở hữu cấu hình cao, hoạt động mạnh mẽ và ổn định hơn, khả năng tản nhiệt hiệu quả.</li>
        <li>CPU sở hữu tốc độ cao với phần cứng có khả năng xử lý đồ họa tốt, lưu trữ được nhiều dữ liệu hơn.</li>
        <li>Màn hình lớn giúp mở rộng không gian giải trí, làm việc.</li>
        <li>Các linh kiện của máy tính để bàn có tuổi thọ bền, khó hư hỏng.</li>
      </ol>
      <img src="anh-seo/tongquat14.png" alt="Ưu điểm máy tính để bàn">
      
      <h2>5. Nên Mua Máy Tính Để Bàn Tốt Nhất Ở Đâu?</h2>
      <ol>
        <li>Bộ xử lý mạnh mẽ cùng card đồ họa hiệu suất vượt trội</li>
        <li>Dung lượng RAM và ổ cứng SSD giúp tăng tốc độ xử lý</li>
        <li>Trải nghiệm hình ảnh được nâng cao nhờ màn hình sắc nét</li>
        <li>Mainboard tiên tiến cùng nguồn điện tối ưu hiệu suất</li>
        <li>Khả năng kết nối đa dạng – Nâng cấp mạnh mẽ</li>
      </ol>

      <img src="anh-seo/tongquat15.png" alt="Mua máy tính ở đâu">
      <p>
        Bài viết đã chia sẻ với bạn những thông tin về máy tính để bàn. Hy vọng qua bài viết, bạn sẽ có thêm những thông
        tin hữu ích giúp bản thân lựa chọn được sản phẩm ưng ý nhé!
      </p>
    </div>
  </div>

  <div class="read-more-btn-container">
    <button id="read-more-btn" onclick="toggleContent()">Xem thêm</button>
  </div>
</section>

<style>
  /* Ẩn tất cả danh mục lúc đầu để tránh nhấp nháy */
  #vanphong, #gaming, #mini, #ai {
    display: none;
  }
  
  /* Typography */
  h2, h3, h4, li {
    color: #222;
    text-align: left;
    font-family: 'Montserrat';
  }

  h2 {
    font-size: 25px;
    font-weight: 700;
    margin-top: 30px;
    margin-bottom: 15px;
    line-height: 1.4;
  }

  h3 {
    font-size: 20px;
    font-weight: 600;
    margin-top: 25px;
    margin-bottom: 12px;
    line-height: 1.4;
  }

  h4 {
    font-size: 18px;
    font-weight: 600;
    margin-top: 20px;
    margin-bottom: 10px;
  }

  p, strong {
    color: #333;
    font-size: 15px;
    line-height: 1.8;
    margin-bottom: 15px;
    text-align: left;
    font-family: 'Montserrat', sans-serif;
  }

  /* Container chính */
  .article-section {
    line-height: 1.8;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f5f5f5;
  }

  /* Article content */
  #article-content {
    background-color: #ffffff;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  }

  /* Collapsed và Expanded states */
  .collapsed .more-content {
    display: none;
  }

  .expanded .more-content {
    display: block;
    animation: fadeIn 0.4s ease-in-out;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* Read more button */
  .read-more-btn-container {
    margin-top: 25px;
    text-align: center;
  }

  #read-more-btn {
    background: linear-gradient(135deg, #0073b3 0%, #005a8d 100%);
    color: white;
    border: none;
    padding: 12px 35px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: 'Montserrat', sans-serif;
    box-shadow: 0 4px 12px rgba(0, 115, 179, 0.3);
  }

  #read-more-btn:hover {
    background: linear-gradient(135deg, #005a8d 0%, #004670 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 115, 179, 0.4);
  }

  #read-more-btn:active {
    transform: translateY(0);
  }

  /* Images */
  img {
    width: 100%;
    max-height: 800px;
    object-fit: cover;
    margin: 20px 0;
    border-radius: 8px;
    background-color: #eee;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  }

  /* Links */
  a {
    color: #0073b3;
    text-decoration: none;
    transition: color 0.3s ease;
  }

  a:hover {
    color: #005a8d;
    text-decoration: underline;
  }

  /* Lists */
  ol, ul {
    margin: 15px 0;
    padding-left: 25px;
  }

  li {
    margin-bottom: 10px;
    line-height: 1.8;
  }

  /* Table styles */
  .table-wrap {
    overflow-x: auto;
    margin: 25px 0;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    background-color: white;
  }

  th, td {
    border: 1px solid #ddd;
    padding: 14px;
    text-align: left;
  }

  th {
    background-color: #f8f9fa;
    font-weight: 700;
    color: #222;
    font-family: 'Montserrat', sans-serif;
  }

  td {
    color: #333;
    font-family: 'Montserrat', sans-serif;
  }

  .price {
    color: #6893f0;
    font-weight: 700;
    font-size: 16px;
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .article-section {
      padding: 15px;
    }

    #article-content {
      padding: 20px;
      border-radius: 8px;
    }
    
    h2 {
      font-size: 22px;
      margin-top: 25px;
    }

    h3 {
      font-size: 18px;
      margin-top: 20px;
    }

    h4 {
      font-size: 16px;
    }

    p, li {
      font-size: 14px;
    }

    #read-more-btn {
      padding: 10px 28px;
      font-size: 15px;
    }
    
    table {
      font-size: 13px;
    }
    
    th, td {
      padding: 10px;
    }

    img {
      max-height: 400px;
      margin: 15px 0;
    }
  }

  @media (max-width: 480px) {
    .article-section {
      padding: 10px;
    }

    #article-content {
      padding: 15px;
    }

    h2 {
      font-size: 20px;
    }

    h3 {
      font-size: 17px;
    }

    p, li {
      font-size: 14px;
    }

    table {
      font-size: 12px;
    }

    th, td {
      padding: 8px;
    }
  }

  /* Smooth scrolling */
  html {
    scroll-behavior: smooth;
  }

  /* Selection color */
  ::selection {
    background-color: #0073b3;
    color: white;
  }

  ::-moz-selection {
    background-color: #0073b3;
    color: white;
  }
</style>


<script>
  function toggleContent() {
    const content = document.getElementById("article-content");
    const button = document.getElementById("read-more-btn");

    if (content.classList.contains("collapsed")) {
      content.classList.remove("collapsed");
      content.classList.add("expanded");
      button.textContent = "Thu gọn";
    } else {
      content.classList.remove("expanded");
      content.classList.add("collapsed");
      button.textContent = "Xem thêm";
      content.scrollIntoView({ behavior: "smooth" });
    }
  }
</script>

<!--=== LOGO thương hiệu ===-->
<!-- Owl Carousel CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

<!-- Logo Partners Section -->
<section class="logo-section">
    <div class="container">
        <div class="section-title text-center">
            <h2 style="font-size: 28px;font-weight: bold;color:#000">ĐỐI TÁC THƯƠNG HIỆU</h2>
            <p class="ds-subtitle">Tự hào đồng hành cùng các khách hàng chiến lược trên toàn cầu</p>

        </div>
        <div class="logo-container">
            <div id="owl-brands-slider" class="owl-carousel owl-theme">
                <div class="item">
                    <a class="text-center">
                        <img src="image/logo/AMD.webp" alt="AMD" loading="lazy" width="900" height="600">
                    </a>
                </div>
                <div class="item">
                    <a class="text-center">
                        <img src="image/logo/INTEl.webp" alt="INTEL "loading="lazy" width="900" height="600">
                    </a>
                </div>
               
                <div class="item">
                    <a class="text-center">
                        <img src="image/logo/ASUS.webp" alt="ASUS" loading="lazy" width="900" height="600">
                    </a>
                </div>
                <div class="item">
                    <a class="text-center">
                        <img src="image/logo/KINGSTON.webp" alt="KINGSTON" loading="lazy" width="900" height="600">
                    </a>
                </div>
                <div class="item">
                    <a class="text-center">
                        <img src="image/logo/LEXAR.webp" alt="LEXAR"loading="lazy" width="900" height="600">
                    </a>
                </div>
                <div class="item">
                    <a class="text-center">
                        <img src="image/logo/ASROCK.webp" alt="ASROCK"loading="lazy" width="900" height="600">
                    </a>
                </div>
                <div class="item">
                    <a class="text-center">
                        <img src="image/logo/AOC.webp" alt="AOC"loading="lazy" width="900" height="600">
                    </a>
                </div>
                <div class="item">
                    <a class="text-center">
                        <img src="image/logo/PALIT.webp" alt="PALIT"loading="lazy" width="900" height="600">
                    </a>
                </div>
                <div class="item">
                    <a class="text-center">
                        <img src="image/logo/GSKILL.webp" alt="GSKILL"loading="lazy" width="900" height="600">
                    </a>
                </div>
                <div class="item">
                    <a class="text-center">
                        <img src="image/logo/POWERCOLOR.webp" alt="POWERCOLOR"loading="lazy" width="900" height="600">
                    </a>
                </div>
                
                <div class="item">
                    <a class="text-center">
                        <img src="image/logo/NVIDIA.webp" alt="NVIDIA"loading="lazy" width="900" height="600">
                    </a>
                </div>

                <div class="item">
                    <a class="text-center">
                        <img src="image/logo/MICROSOFT.webp" alt="MICROSOFT"loading="lazy" width="900" height="600">
                    </a>
                </div>
           
            </div>
        </div>
    </div>
</section>

<!-- ==== Giai pháp AI ==== -->
<section class="ai-section">
    <h2 class="ds-title">GIẢI PHÁP AI</h2>
    <p class="ds-subtitle">Giải pháp toàn diện cho công việc và cuộc sống</p>
</section>

<div class="ai_solution">
    <div class="card">
        <a href="https://rosacomputer.vn/smb/chatbot-ai.php">
            <img src="image/botai.webp" alt="Chat bot ai">
        </a>
    </div>

    <div class="card">
        <a href="https://rosacomputer.vn/smb/giai-phap-cham-cong-ip-camera.php">
            <img src="image/chamcong.webp" alt="Chấm công IP Camera">
        </a>
    </div>

    <div class="card">
        <a href="https://rosacomputer.vn/smb/Nextcloud.php">
            <img src="image/nextcloud.webp" alt="Chương trình nextclaud">
        </a>
    </div>
</div>

<div class="banner"style="margin-top: 40px;">
    <div class="row">
        <div class="col-lg-12 mb-3">
            <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner position-relative">
                    <div class="carousel-item active">
                        <img src="image/background python.webp" class="img-fluid" alt="python" onclick="window.location.href='product.php?may-tinh-ai'">
                    </div>
                    <div class="hero-text">
                        <button class="button-style" onclick="window.location.href='https://rosacomputer.vn/ROSA-SW.php'">ỨNG DỤNG</button>
                        <h2 style="color:#FFF" >KHÓA HỌC ROSA</h2>
                        <p style="color:#FFF">Chương trình đào tạo bài bản, được chứng nhận bởi tổ chức uy tín</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<style>
.container {
    max-width: 1200px; margin: 0 auto; padding: 20px;
}
.ai-section { text-align: center; margin-bottom: 30px; }
.ds-title { font-size: 28px; font-weight: 700; color: #1a365d; margin-bottom: 8px; }
.ds-subtitle { font-size: 16px; color: #4a5568; }

.tabs {
    display: flex; justify-content: center; align-items: center;
    margin-bottom: 25px; border-bottom: 1px solid #e2e8f0;
    overflow-x: auto; white-space: nowrap;
}
.tab {
    padding: 10px 20px; background: none; border: none; font-size: 15px;
    font-weight: 600; color: #718096; cursor: pointer; transition: .3s;
}
.tab.active { color: #3182ce; position: relative; }
.tab.active::after {
    content: ''; position: absolute; bottom: -1px; left: 0;
    width: 100%; height: 3px; background-color: #3182ce;
}

.news-layout {
    display: grid; grid-template-columns: 2fr 1fr; gap: 25px;
}

/* Featured */
.featured-news,
.news-item {
    background: white; border-radius: 10px; overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.featured-news img { width: 100%; height: 220px; object-fit: cover; }
.featured-content { padding: 20px; }
.featured-title a {
    font-size: 20px; font-weight: 700; color: #2d3748; text-decoration: none;
}
.featured-meta { font-size: 13px; color: #718096; margin: 8px 0; }
.featured-desc { font-size: 14px; color: #4a5568; }

/* List */
.news-list { display: flex; flex-direction: column; gap: 15px; }
.news-item { display: flex; }
.news-item img { width: 100px; height: 80px; object-fit: cover; }
.news-item-content { padding: 12px; flex: 1; }
.news-item-title a {
    font-size: 14px; font-weight: 600; color: #2d3748; text-decoration: none;
}
.news-item-meta { font-size: 11px; color: #718096; }
.news-item-desc { font-size: 12px; color: #4a5568; }

@media (max-width: 768px) {
    .news-layout { grid-template-columns: 1fr; }
    .news-item { flex-direction: column; }
    .news-item img { width: 100%; height: 120px; }
}
</style>

<?php
function getArticles($where, $limit) {
    $conn = new mysqli("localhost", "root", "", "");
    $conn->set_charset("utf8");

    $sql = "SELECT article_title, article_date, article_image, article_link, article_content 
            FROM article 
            WHERE $where 
            ORDER BY article_date DESC 
            LIMIT $limit";
    return $conn->query($sql);
}

function imgSrc($img) {
    return (preg_match('/^https?:\/\//', trim($img))) ? $img : '/tintuc_test/admin/modules/blog/uploads/' . $img;
}
?>

<div class="container">
    <section class="ai-section">
        <h2 class="ds-title">TIN TỨC</h2>
        <p class="ds-subtitle">Cập nhật tin tức công nghệ và khuyến mãi</p>
    </section>

    <div class="tabs">
        <button class="tab active" data-tab="tab1">Tin công nghệ</button>
        <button class="tab" data-tab="tab2">Tin giáo dục</button>
    </div>

    <!-- TAB 1 -->
    <div id="tab1-content">
        <?php 
        $whereTech = "article_tag LIKE '%cong-nghe%' OR article_tag LIKE '%technology%' OR article_tag LIKE '%cloud%'";
        $featured = getArticles($whereTech, "1");
        $list = getArticles($whereTech, "1,3");
        ?>
        
        <div class="news-layout">
            <!-- Featured -->
            <div class="featured-news">
                <?php if ($row = $featured->fetch_assoc()): ?>
                    <a href="/tintuc/<?= htmlspecialchars($row['article_link']) ?>">
                        <img src="<?= imgSrc($row['article_image']) ?>" alt="">
                    </a>
                    <div class="featured-content">
                        <div class="featured-title">
                            <a href="/tintuc/<?= $row['article_link'] ?>">
                                <?= htmlspecialchars($row['article_title']) ?>
                            </a>
                        </div>
                        <p class="featured-meta">Ngày đăng: <?= date("d/m/Y", strtotime($row['article_date'])) ?></p>
                        <p class="featured-desc"><?= strip_tags(substr($row['article_content'],0,300)) ?>...</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- List -->
            <div class="news-list">
                <?php while ($item = $list->fetch_assoc()): ?>
                <div class="news-item">
                    <a href="/tintuc/<?= $item['article_link'] ?>">
                        <img src="<?= imgSrc($item['article_image']) ?>" alt="">
                    </a>
                    <div class="news-item-content">
                        <div class="news-item-title">
                            <a href="/tintuc/<?= $item['article_link'] ?>"><?= htmlspecialchars($item['article_title']) ?></a>
                        </div>
                        <p class="news-item-meta">Ngày đăng: <?= date("d/m/Y", strtotime($item['article_date'])) ?></p>
                        <p class="news-item-desc"><?= strip_tags(substr($item['article_content'],0,200)) ?>...</p>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- TAB 2 -->
    <div id="tab2-content" style="display:none">
        <?php 
        $whereEdu = "article_tag LIKE '%giao-duc%' OR article_tag LIKE '%promotion%' OR article_tag LIKE '%ưu đãi%' OR article_tag LIKE '%sale%'";
        $featured2 = getArticles($whereEdu, "1");
        $list2 = getArticles($whereEdu, "1,3");
        ?>

        <div class="news-layout">
            <div class="featured-news">
                <?php if ($row = $featured2->fetch_assoc()): ?>
                    <a href="/tintuc/<?= $row['article_link'] ?>">
                        <img src="<?= imgSrc($row['article_image']) ?>" alt="">
                    </a>
                    <div class="featured-content">
                        <div class="featured-title">
                            <a href="/tintuc/<?= $row['article_link'] ?>"><?= htmlspecialchars($row['article_title']) ?></a>
                        </div>
                        <p class="featured-meta">Ngày đăng: <?= date("d/m/Y", strtotime($row['article_date'])) ?></p>
                        <p class="featured-desc"><?= strip_tags(substr($row['article_content'],0,300)) ?>...</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="news-list">
                <?php while ($item = $list2->fetch_assoc()): ?>
                <div class="news-item">
                    <a href="/tintuc/<?= $item['article_link'] ?>">
                        <img src="<?= imgSrc($item['article_image']) ?>" alt="">
                    </a>
                    <div class="news-item-content">
                        <div class="news-item-title">
                            <a href="/tintuc/<?= $item['article_link'] ?>"><?= htmlspecialchars($item['article_title']) ?></a>
                        </div>
                        <p class="news-item-meta">Ngày đăng: <?= date("d/m/Y", strtotime($item['article_date'])) ?></p>
                        <p class="news-item-desc"><?= strip_tags(substr($item['article_content'],0,200)) ?>...</p>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll(".tab").forEach(btn => {
    btn.addEventListener("click", () => {
        document.querySelectorAll(".tab").forEach(t => t.classList.remove("active"));
        btn.classList.add("active");

        document.getElementById("tab1-content").style.display =
            btn.dataset.tab === "tab1" ? "block" : "none";
        document.getElementById("tab2-content").style.display =
            btn.dataset.tab === "tab2" ? "block" : "none";
    });
});
</script>
<br>
<section class="ai-section" style="margin-top: 40px;">
    <h2 class="ds-title">CÂU HỎI THƯỜNG GẶP</h2>
    <p class="ds-subtitle">Xem giải đáp nhanh thắc mắc phổ biến</p>
</section>

<div class="faq-container">
    <div class="faq-buttons">
        <button class="faq-button active" data-category="tuvan">TƯ VẤN</button>
        <button class="faq-button" data-category="baohanh">BẢO HÀNH</button>
        <button class="faq-button" data-category="giaohang">GIAO HÀNG</button>
        <button class="faq-button" data-category="thanhtoan">THANH TOÁN</button>
        <button class="faq-button" data-category="sanpham">SẢN PHẨM</button>
        <button class="faq-button" data-category="cauhoi">CÂU HỎI</button>
    </div>
    <hr>
    
    <div class="faq-content">
        
        <!-- Tư vấn -->

        <div class="faq-item active" data-category="tuvan">
            <div class="faq-question">ROSA hiện tại có chi nhánh không?</div>
            <div class="faq-answer">
                Hiện tại ROSA tự hào có mặt trên 34 tỉnh thành trên toàn quốc: <br>
                Văn phòng đại diện tại:150 Ter, đường Bùi Thị Xuân, phường Bến Thành, TP. Hồ Chí Minh.<br>
                Quý khách vui lòng đến trực tiếp địa chỉ này để được tư vấn và trải nghiệm sản phẩm với đầy đủ dịch vụ hỗ trợ.
            </div>
        </div>
        
        <div class="faq-item" data-category="tuvan">
            <div class="faq-question">Giờ làm việc của ROSA</div>
            <div class="faq-answer">
                ROSA hoạt động từ Thứ 2 đến Thứ 7 trong khung giờ hành chính.<br>
                Để đảm bảo phục vụ tốt nhất và kịp thời hỗ trợ đầy đủ, quý khách có thể kiểm tra thông tin giờ làm việc chi tiết tại website chính thức: <a href="https://rosacomputer.vn/" class="website-link">https://rosacomputer.vn/</a>
            </div>
        </div>

        <div class="faq-item" data-category="tuvan">
            <div class="faq-question">ROSA có bán hàng trên các sàn thương mại điện tử không?</div>
            <div class="faq-answer">
                Hiện tại ROSA phân phối sản phẩm thông qua nhiều kênh như cửa hàng online, hệ thống đại lý và một số sàn thương mại điện tử. Tuy nhiên, để đảm bảo quyền lợi, chất lượng sản phẩm và hỗ trợ đầy đủ, chúng tôi khuyến khích quý khách:<br>
                - Mua trực tiếp tại showroom chính thức của ROSA.<br>
                - Hoặc đặt hàng qua website: <a href="https://rosacomputer.vn/" class="website-link" >https://rosacomputer.vn/</a>
            </div>
        </div>

        <!-- Bảo hành -->

        <div class="faq-item" data-category="baohanh">
            <div class="faq-question">Thời gian bảo hành của sản phẩm là bao lâu?</div>
            <div class="faq-answer">
                Đa số sản phẩm của chúng tôi được bảo hành trong vòng 3 năm kể từ ngày mua. Vui lòng kiểm tra phiếu bảo hành đi kèm để biết thời gian bảo hành cụ thể.
            </div>
        </div>
        <div class="faq-item" data-category="baohanh">
            <div class="faq-question">Máy bộ ROSA bảo hành bao lâu?</div>
            <div class="faq-answer">
                Tất cả máy bộ ROSA được bảo hành 3 năm theo quy định từ nhà sản xuất         
            </div>
        </div>
        <div class="faq-item" data-category="baohanh">
            <div class="faq-question">Số điện thoại trung tâm bảo hành ROSA là gì?</div>
            <div class="faq-answer">
                (028) 3926 0996       
            </div>
        </div>

        <!-- Giao hàng -->

        <div class="faq-item" data-category="giaohang">
            <div class="faq-question">Thời gian giao hàng mất bao lâu?</div>
            <div class="faq-answer">
                Thời gian giao hàng tùy thuộc vào vị trí và khu vực nhận hàng ROSA luôn cố gắng giao nhanh nhất có thể 
            </div>
        </div>

        <!-- Thanh toán -->

        <div class="faq-item" data-category="thanhtoan">
            <div class="faq-question">ROSA hổ trợ những phương thức thanh toán nào?</div>
            <div class="faq-answer">
                Rosa hỗ trợ hai hình thức thanh toán: chuyển khoản qua ngân hàng và thanh toán tiền mặt
            </div>
        </div>

        <!-- Sản phẩm -->

        <div class="faq-item" data-category="sanpham">
            <div class="faq-question">Sản phẩm có sẵn hàng không?</div>
            <div class="faq-answer">
                Sản phẩm của chúng tôi có sẵn tại showroom hoặc có thể đặt hàng trực tuyến. Vui lòng kiểm tra tại website <a href="https://rosacomputer.vn/" class="website-link">https://rosacomputer.vn/</a>
            </div>
        </div>
        <div class="faq-item" data-category="sanpham">
            <div class="faq-question">ROSA hiện đang cung cấp những sản phẩm gì?</div>
            <div class="faq-answer">
                Rosa tập trung vào dòng máy bộ PC với nhiều cấu hình phù hợp nhu cầu học tập, văn phòng, gaming, lập trình
            </div>
        </div>
        <div class="faq-item" data-category="sanpham">
            <div class="faq-question">Các dòng máy bộ chính của ROSA gồm những gì?</div>
            <div class="faq-answer">
                ROSA AI, ROSA VĂN PHÒNG, ROSA GAMER           
            </div>
        </div>
        
        <div class="faq-item" data-category="sanpham">
            <div class="faq-question">ROSA AI là gì?</div>
            <div class="faq-answer">
                Dòng máy ROSA AI được thiết kế dành riêng cho lập trình và phát triển trí tuệ nhân tạo. Tích hợp cấu hình mạnh, cài sẵn công cụ AI, sẵn sàng cho hành trình sáng tạo của bạn
            </div>
        </div>

        <!-- Câu hỏi -->

        <div class="faq-item" data-category="cauhoi">
            <div class="faq-question">Làm thế nào để đặt hàng qua website?</div>
            <div class="faq-answer">
                Để đặt hàng qua website, hãy truy cập <a href="https://rosacomputer.vn/product.php" class="website-link" >https://rosacomputer.vn/sanpham</a>, chọn sản phẩm bạn muốn, thêm vào giỏ hàng và thanh toán theo hướng dẫn.
            </div>
        </div>
    </div>
</div>

<!-- ==== loggoo ==== -->
<!-- jQuery và Owl Carousel JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<!-- Initialize Owl Carousel -->
<script>
$(document).ready(function(){
    // Khởi tạo Owl Carousel cho logo brands
    $("#owl-brands-slider").owlCarousel({
        loop: true,
        margin: 30,
        nav: false,
        dots: false,
        autoplay: true,
        autoplayTimeout: 2000,
        autoplayHoverPause: true,
        responsive: {
            0: {
                items: 2
            },
            600: {
                items: 4
            },
            1000: {
                items: 6
            }
        }
    });
});
</script>

<script src="script/trangchu.js"></script>

<?php require "footer.php" ?>