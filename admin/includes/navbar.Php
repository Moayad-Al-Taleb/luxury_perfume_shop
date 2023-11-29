<nav>
  <div class="menu-icon">
    <i id="menuIcon" class="fa-solid fa-bars"></i>
    <h5><?php echo $page_title ?></h5>
  </div>

  <div>
    <a href="../logout.php">تسجيل الخروج</a>
  </div>
</nav>

<script>
  const menuIcon = document.getElementById("menuIcon");
  menuIcon.onclick = () => {
    document.getElementById("sidebar").classList.remove("active");
    document.getElementById("sidebar-container").classList.remove("active");
  };
</script>