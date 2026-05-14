const slides = document.querySelectorAll(".simple-slide");
let index = 0;

const prevBtn = document.querySelector(".slider-prev");
const nextBtn = document.querySelector(".slider-next");

function showSlide(newIndex) {
  if (!slides.length) return;

  slides[index].classList.remove("active");
  index = (newIndex + slides.length) % slides.length;
  slides[index].classList.add("active");
}

if (prevBtn) {
  prevBtn.addEventListener("click", () => {
    showSlide(index - 1);
  });
}

if (nextBtn) {
  nextBtn.addEventListener("click", () => {
    showSlide(index + 1);
  });
}
