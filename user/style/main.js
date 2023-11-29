/*=============== SWIPER JS GALLERY ===============*/
let swiperCards = new Swiper(".gallery-cards", {
  loop: true,
  loopedSlides: 5,
  cssMode: true,
  effect: "fade",
});

let swiperThumbs = new Swiper(".gallery-thumbs", {
  loop: true,
  loopedSlides: 5,
  slidesPerView: 3,
  centeredSlides: true,
  slideToClickedSlide: true,

  pagination: {
    el: ".swiper-pagination",
    type: "fraction",
  },
});

swiperThumbs.controller.control = swiperCards;
