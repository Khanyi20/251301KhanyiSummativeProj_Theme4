/* Toggle between adding and removing the "responsive" class to topnav when the user clicks on the icon */
function myFunction() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}

// function myFunction() {
//   const myTopnav = document.getElementById("myTopnav");
//   myTopnav.classList.toggle("responsive");
// }



// slideshow controls



let slideIndex = 1;
showSlides(slideIndex);

// Next/previous controls
function plusSlides(n) {
  showSlides(slideIndex += n);
}

// Thumbnail image controls
function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  let i;
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dot");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " active";
}



// signup form controls




document.addEventListener("DOMContentLoaded", () => {
    const search = document.querySelector("#animal-search");
    const species = document.querySelector("#species-filter");
    const cards = document.querySelectorAll(".animal-card");

    function filterAnimals() {
        const query = (search?.value || "").toLowerCase().trim();
        const selectedSpecies = species?.value || "";

        cards.forEach((card) => {
            const text = card.textContent.toLowerCase();
            const cardSpecies = card.dataset.species || "";

            const matchesSearch = text.includes(query);
            const matchesSpecies =
                !selectedSpecies || cardSpecies === selectedSpecies;

            card.hidden = !(matchesSearch && matchesSpecies);
        });
    }

    search?.addEventListener("input", filterAnimals);
    species?.addEventListener("change", filterAnimals);
});
