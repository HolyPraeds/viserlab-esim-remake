"use strict";
(function ($) {
  $(document).ready(function () {
    /* ===================== AOS animation active =================== */
    AOS.init({ offset: 0, once: true, });
    /* ===================== Underline Word JS Start =================== */
    $("[data-underline-word]").each((index, el) => {
      var text = $(el).text().trim();

      // Define the range (1-based index)
      var startWord =
        $(el).data("underline-start") || $(el).data("underline-word");
      var endWord = $(el).data("underline-word");

      // Split the string into words
      var words = text.split(" ");

      // Build the result
      var result = "";

      for (var i = 0; i < words.length; i++) {
        if (i === startWord - 1) {
          result += `
          <span class="underline-word">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 16.608">
              <path stroke-linecap="round" stroke-miterlimit="10" d="M5.3,11.307c0,0,160.071-11.682,190.106-3.544" />
            </svg>

          `;
        }

        result += words[i];

        if (i === endWord - 1) {
          result += "</span>";
        }

        // Add space between words (except after last word)
        if (i < words.length - 1) {
          result += " ";
        }
      }

      // Set the result to an element
      $(el).html(result);
    });
    /* ===================== Underline Word JS End ===================== */

    /* ===================== Height Calculation JS Start =================== */
    const headerHeight = document.querySelector(".header")?.offsetHeight || 0;
    const blogContentHeight =
      document.querySelector(".blog-section__content")?.offsetHeight || 0;
    document.documentElement.style.setProperty(
      "--header-height",
      `${headerHeight}px`
    );
    document.documentElement.style.setProperty(
      "--blog-post-height",
      `${blogContentHeight}px`
    );
    /* ===================== Height Calculation JS End ===================== */

    /* ===================== Header Navbar Collapse JS Start ===================== */
    function hideNavbarCollapse() {
      new bootstrap.Collapse($(".navbar-collapse")[0]).hide();
      $(".navbar-collapse").trigger("hide.bs.collapse");
    }

    $(".navbar-collapse").on({
      "show.bs.collapse": function () {
        $(".header").addClass("add-bg");
        $("body").addClass("scroll-hide");
        $(".body-overlay")
          .addClass("show")
          .off("click")
          .on("click", hideNavbarCollapse);
      },
      "hide.bs.collapse": function () {
        $("body").removeClass("scroll-hide");
        $(".body-overlay")
          .removeClass("show")
          .unbind("click", hideNavbarCollapse);
      },
      "hidden.bs.collapse": function () {
        $(".header").removeClass("add-bg");
      },
    });
    /* ==================== Header Navbar Collapse JS End ======================= */

    /* ==================== Offcanvas Sidebar JS Start ======================== */
    $('[data-toggle="offcanvas-sidebar"]').each(function (index, toggler) {
      let id = $(toggler).data("target");
      let sidebar = $(id);
      let sidebarClose = sidebar.find(".btn--close");
      let sidebarOverlay = $(".sidebar-overlay");

      let hideSidebar = function () {
        sidebar.removeClass("show");
        sidebarOverlay.removeClass("show");
        $(toggler).removeClass("active");
        $("body").removeClass("scroll-hide");
        $(document).unbind("keydown", EscSidbear);
      };

      let EscSidbear = function (e) {
        if (e.keyCode === 27) {
          hideSidebar();
        }
      };

      let showSidebar = function () {
        $(toggler).addClass("active");
        sidebar.addClass("show");
        sidebarOverlay.addClass("show");
        $("body").addClass("scroll-hide");
        $(document).on("keydown", EscSidbear);
      };

      $(toggler).off("click").on("click", showSidebar);
      $(sidebarOverlay).off("click").on("click", hideSidebar);
      $(sidebarClose).off("click").on("click", hideSidebar);
    });

    $(".offcanvas-sidebar__body").on("scroll", function () {
      if ($(this).scrollTop() > 0) {
        $(this).addClass("scrolling");
      } else {
        $(this).removeClass("scrolling");
      }
    });
    /* ==================== Offcanvas Sidebar JS End ========================== */

    /* ==================== Dynamically Add BG Image JS Start ====================== */
    $(".bg-img").css("background-image", function () {
      return `url(${$(this).data("background-image")})`;
    });
    /* ==================== Dynamically Add BG Image JS End ========================= */

    /* ==================== Dynamically Add Mask Image JS Start ====================== */
    $(".mask-img").css("mask-image", function () {
      return `url(${$(this).data("mask-image")})`;
    });
    /* ==================== Dynamically Add Mask Image JS End ======================== */

    /* ==================== Password Toggle JS Start ================================ */
    $(".input--group-password").each(function (index, inputGroup) {
      let inputGroupBtn = $(inputGroup).find(".input-group-btn");
      let formControl = $(inputGroup).find(".form-control.form--control");

      inputGroupBtn.on("click", function () {
        if (formControl.attr("type") === "password") {
          formControl.attr("type", "text");
          $(this).find("i").removeClass("fa-eye-slash").addClass("fa-eye");
        } else {
          formControl.attr("type", "password");
          $(this).find("i").removeClass("fa-eye").addClass("fa-eye-slash");
        }
      });
    });
    /* ==================== Password Toggle JS End ================================== */

    /* ==================== Highlight Word JS Start ================================ */
    $("[data-highlight]").each(function () {
      const $this = $(this);
      let originalText = $this.text().trim().split(" ");
      let textLength = originalText.length;
      const highlight = $this.data("highlight").toString();
      const highlight_class =
        $this.data("highlight-class")?.toString() || "text--base";
      const highlightToArray = highlight.split(",");
      // Loop through each highlight range
      $.each(highlightToArray, function (i, element) {
        const index = element.toString().split("_");
        var startIndex = index[0];
        var endIndex = index.length > 1 ? index[1] : startIndex;
        if (startIndex < 0) {
          startIndex = textLength - Math.abs(startIndex);
        }
        if (endIndex < 0) {
          endIndex = textLength - Math.abs(endIndex);
        }
        const startIndexValue = originalText[startIndex];
        const endIndexValue = originalText[endIndex];
        if (startIndex === endIndex) {
          originalText[
            startIndex
          ] = `<span class="${highlight_class}">${startIndexValue}</span>`;
        } else {
          originalText[
            startIndex
          ] = `<span class="${highlight_class}">${startIndexValue}`;
          originalText[endIndex] = `${endIndexValue}</span>`;
        }
      });
      $this.html(originalText.join(" "));
    });
    /* ==================== Highlight Word JS End ================================== */

    /* ==================== Bootstrap Tooltip Init Js Start ==================== */
    const tooltipTriggerList = document.querySelectorAll(
      '[data-bs-toggle="tooltip"]'
    );
    const tooltipList = [...tooltipTriggerList].map(
      (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
    );
    /* ==================== Bootstrap Tooltip Init Js End ==================== */

    /* ==================== Select2 Init Js Start ==================== */
    $(".select2").each((index, select) => {
      $(select)
        .wrap('<div class="select2-wrapper"></div>')
        .select2({
          dropdownParent: $(select).closest(".select2-wrapper"),
        });
    });
    /* ==================== Select2 Init Js End ==================== */

    /* ===================== Testimonial Slider JS Start ===================== */
    $(".testimonial-slider").slick({
      slidesToScroll: 1,
      autoplay: true,
      autoplaySpeed: 2000,
      speed: 1500,
      dots: true,
      pauseOnHover: true,
      slidesToShow: 1,
      arrows: false,
    });
    /* ===================== Testimonial Slider JS End ===================== */

    /* ===================== Client Slider JS Start ===================== */
    $(".client-slider").slick({
      slidesToShow: 8,
      slidesToScroll: 1,
      autoplay: true,
      autoplaySpeed: 2000,
      speed: 2000,
      arrows: false,
      pauseOnHover: true,
      responsive: [
        {
          breakpoint: 1400,
          settings: {
            slidesToShow: 7,
          },
        },
        {
          breakpoint: 1200,
          settings: {
            slidesToShow: 6,
          },
        },
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 5,
          },
        },
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 4,
          },
        },
        {
          breakpoint: 576,
          settings: {
            slidesToShow: 3,
          },
        },
        {
          breakpoint: 424,
          settings: {
            slidesToShow: 2,
          },
        },
      ],
    });
    /* ===================== Client Slider JS End ===================== */

    $(".showFilterBtn").on("click", function () {
      $(".responsive-filter-card").slideToggle();
    });

    function isOverflowing($el) {
      return $el[0].scrollHeight > $el.innerHeight() || $el[0].scrollWidth > $el.innerWidth();
    }

    if($('.choose-plan-item-container').length){
        if (isOverflowing($('.choose-plan-item-container'))) {
            $('.choose-plan-item-container').addClass('scrolling');
        } else {
            $('.choose-plan-item-container').removeClass('scrolling');
        }
    }
  });

  /* ==================== Header Fixed JS Start ========================= */
  $(window).on("scroll", function (e) {
    if ($(window).scrollTop() >= 300) {
      $(".header").addClass("fixed-header");
    } else {
      $(".header").removeClass("fixed-header");
    }
  });
  /* ==================== Header Fixed JS End ============================= */

  /* ==================== Scroll To Top Button JS Start =========================== */
  let scrollTopBtn = $(".scroll-top");

  if (scrollTopBtn.length) {
    let progressPath = scrollTopBtn.find(".scroll-top-progress path");
    let pathLength = progressPath[0].getTotalLength();
    let offset = 250;
    let duration = 550;

    progressPath.css({
      transition: "none",
      WebkitTransition: "none",
      strokeDasharray: `${pathLength} ${pathLength}`,
      strokeDashoffset: pathLength,
      transition: "stroke-dashoffset 10ms linear",
      WebkitTransition: "stroke-dashoffset 10ms linear",
    });

    function updateProgress() {
      let scroll = $(window).scrollTop();
      let height = $(document).height() - $(window).height();
      let progress = pathLength - (scroll * pathLength) / height;
      progressPath.css("strokeDashoffset", progress);
    }

    updateProgress();

    $(window).on("scroll", function () {
      updateProgress();
      if ($(this).scrollTop() > offset) {
        scrollTopBtn.addClass("active");
      } else {
        scrollTopBtn.removeClass("active");
      }
    });

    scrollTopBtn.on("click", function (e) {
      e.preventDefault();
      $("html, body").animate({ scrollTop: 0 }, duration);
      return false;
    });
  }
  /* ==================== Scroll To Top Button JS End ============================= */

  /* ==================== Preloader JS Start ====================================== */
  $(window).on("load", () => $(".preloader").fadeOut());
  /* ==================== Preloader JS End ======================================== */

  /* ==================== Odometer Initialization JS Start ============================= */
  function initOdometer() {
    $(".odometer").each(function (index, element) {
      var odometer = new Odometer({
        el: element,
        value: 0,
      });

      odometer.update($(element).data("odometer-stop"));

      $(element).isInViewport(function (status) {
        if (status === "entered") {
          odometer.update($(element).data("odometer-stop"));
        }

        if (status === "leaved") {
          odometer.update(0);
        }
      });
    });
  }

  $(window).on("load", initOdometer);
  /* ==================== Odometer Initialization JS End ================================== */
})(jQuery);
