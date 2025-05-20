let menu = document.querySelector('#menu-btn');
let navbar = document.querySelector('.navbar');

menu.onclick = () =>{
    menu.classList.toggle('fa-times');
    navbar.classList.toggle('active');
}
    // Changer entre formulaire de connexion et d'inscription
    document.querySelector('#switch-to-signup').onclick = () => {
      // Cacher le formulaire de connexion et afficher le formulaire d'inscription
      document.querySelector('#login-form').style.display = 'none';
      document.querySelector('#signup-form').style.display = 'block';
  };

  document.querySelector('#switch-to-login').onclick = () => {
      // Cacher le formulaire d'inscription et afficher le formulaire de connexion
      document.querySelector('#signup-form').style.display = 'none';
      document.querySelector('#login-form').style.display = 'block';
  };

  // Gérer l'affichage du formulaire de connexion lorsque l'on ferme
  document.querySelector('#close-login-form').onclick = () => {
      document.querySelector('.login-form-container').classList.remove('active');
  };
  
  // Afficher/masquer le formulaire de connexion
  document.querySelector('#login-btn').onclick = () => {
      document.querySelector('.login-form-container').classList.toggle('active');
  };

document.querySelector('.home').onmousemove = (e) =>{

  document.querySelectorAll('.home-parallax').forEach(elm =>{

    let speed = elm.getAttribute('data-speed');

    let x = (window.innerWidth - e.pageX * speed) / 90;
    let y = (window.innerHeight - e.pageY * speed) / 90;

    elm.style.transform = `translateX(${y}px) translateY(${x}px)`;

  });

};


document.querySelector('.home').onmouseleave = (e) =>{

  document.querySelectorAll('.home-parallax').forEach(elm =>{

    elm.style.transform = `translateX(0px) translateY(0px)`;

  });

};

var swiper = new Swiper(".vehicles-slider", {
  grabCursor: true,
  centeredSlides: true,  
  spaceBetween: 20,
  loop:true,
  autoplay: {
    delay: 9500,
    disableOnInteraction: false,
  },
  pagination: {
    el: ".swiper-pagination",
    clickable:true,
  },
  breakpoints: {
    0: {
      slidesPerView: 1,
    },
    768: {
      slidesPerView: 2,
    },
    1024: {
      slidesPerView: 3,
    },
  },
});

var swiper = new Swiper(".featured-slider", {
  grabCursor: true,
  centeredSlides: true,  
  spaceBetween: 20,
  loop:true,
  autoplay: {
    delay: 9500,
    disableOnInteraction: false,
  },
  pagination: {
    el: ".swiper-pagination",
    clickable:true,
  },
  breakpoints: {
    0: {
      slidesPerView: 1,
    },
    768: {
      slidesPerView: 2,
    },
    1024: {
      slidesPerView: 3,
    },
  },
});

// Testimonial Slider
var testimonialSwiper = new Swiper(".testimonial-slider", {
    grabCursor: true,
    centeredSlides: true,
    spaceBetween: 30,
    loop: true,
    autoplay: {
        delay: 8000,
        disableOnInteraction: false,
    },
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    breakpoints: {
        0: {
            slidesPerView: 1,
        },
        768: {
            slidesPerView: 2,
        },
        1024: {
            slidesPerView: 3,
        },
    },
});

// Header scroll effect
window.addEventListener('scroll', function() {
    const header = document.querySelector('.header');
    header.classList.toggle('active', window.scrollY > 0);
});