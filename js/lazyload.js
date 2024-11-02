const options = {
  root: null,
  rootMargin: "10px",
  threshold: 0,
};

const lazyImgObserver = new IntersectionObserver((elements, observer) => {
  elements.forEach((element) => {
    const target = element.target;
    target.setAttribute("src", target.getAttribute("data-src"));
    target.removeAttribute("data-src");
    observer.unobserve(element.target);
  });
}, options);

const lazyTargets = document.querySelectorAll("[data-src]");
lazyTargets.forEach((element) => {
  lazyImgObserver.observe(element);
});

const photoObserver = new IntersectionObserver((elements, observer) => {
  elements.forEach((element) => {
    if (element.isIntersecting) {
      getPhoto(element, observer);
      observer.unobserve(element.target);
    }
  });
}, options);

const lazyElements = document.querySelectorAll(".lazy");
lazyElements.forEach((element) => {
  photoObserver.observe(element);
});

const getPhoto = async (element, observer) => {
  const target = element.target;
  const width = target.offsetWidth;
  const height = target.classList.contains("contain")
    ? null
    : target.offsetHeight;
  const id = target.getAttribute("data-id");
  const resasoftPhoto = target.classList.contains("photo-resasoft");
  const response = await fetch(root + "/ajax/ajax-photo.php", {
    method: "POST",
    mode: "same-origin",
    cache: "default",
    credentials: "same-origin",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ id, width, height, resasoftPhoto }),
  });
  const data = await response.json();
  if (target.classList.contains("paralax")) {
    target.style.backgroundImage = "url(" + data.src + ")";
  } else {
    target?.querySelector("img")?.setAttribute("src", data.src);
    target?.querySelector("img")?.setAttribute("width", data.width);
    target?.querySelector("img")?.setAttribute("height", data.height);
  }

  target.classList.remove("lazy");
};
