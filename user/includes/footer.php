<footer class="container">
    <div class="footer-logo">
        <img src="../assets/logo.png" alt="">
        <h3>متجر العطور الفاخرة</h3>
    </div>
    <div class="footer-copyright">
        <span>Copyright 2023 luxury perfume shop – All rights reserved</span>
    </div>
</footer>
<!--=============== SWIPER JS ===============-->
<script src="style/swiper-bundle.min.js"></script>

<!--=============== MAIN JS ===============-->
<script src="style/main.js"></script>

<script src="https://kit.fontawesome.com/d8ee9aaa2f.js" crossorigin="anonymous"></script>
<script>
    const swiper = new Swiper('.home-swiper', {
        // Optional parameters
        direction: 'vertical',
        loop: true,
        effect: "slide",
        speed: 1000,
        // Navigation arrows
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        autoplay: {
            delay: 2000,
        },
    });
</script>
</body>

</html>