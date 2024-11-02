const langs = document.querySelectorAll("a[data-lang]");
langs.forEach((lang) => {
  lang.addEventListener("click", (e) => {
    e.preventDefault();
    langs.forEach((l) => l.classList.remove("bg-blue-500", "text-white"));
    lang.classList.add("bg-blue-500", "text-white");
    loadFromByLocale(lang.getAttribute("data-lang"));
  });
});

async function loadFromByLocale(locale) {
  document
    .querySelectorAll(".ajax-form")
    .forEach((f) => (f.style.display = "none"));

  if (!document.getElementById(`detail_${locale}`)) {
    const formData = new URLSearchParams();
    const pageId = document.querySelector("input[name='id']");
    formData.set("locale", locale);
    formData.set("id", pageId.value);

    const url = `ajax/detail_zone/form.php?${formData.toString()}&noCache=${Date.now()}`;
    try {
      const response = await fetch(url, {
        method: "GET",
      });

      if (!response.ok) {
        throw new Error("Erreur de chargement des données");
      }

      const data = await response.text();
      if (data) {
        document
          .getElementById("ajax-form")
          .insertAdjacentHTML("beforeend", data);
      } else {
        alert("Une erreur est survenue dans le changement de la langue");
      }
    } catch (error) {
      alert("Erreur : " + error.message);
    }
  } else {
    document.getElementById(`detail_${locale}`).style.display = "block";
  }
}

const form = document.querySelector("form");
const errorContainer = document.createElement("div");
errorContainer.classList.add("error-container"); // Ajoute une classe CSS si nécessaire
form.insertAdjacentElement("beforebegin", errorContainer); // Place le conteneur au-dessus du formulaire

form.addEventListener("submit", async (e) => {
  e.preventDefault();
  const formData = new FormData(form);

  try {
    // Envoie les données via fetch en utilisant la méthode POST
    const response = await fetch("ajax/detail_zone/update.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();
    if (!result.success) {
      errorContainer.innerHTML = result.errorsHtml;
    } else {
      alert("La modification a été réalisée.");
      window.location.reload();
    }
  } catch (error) {
    console.error("Erreur:", error);
    alert("Erreur lors de la soumission du formulaire. Veuillez réessayer.");
  }
});

document.addEventListener("DOMContentLoaded", () => {
  ClassicEditor.create(document.querySelector("textarea"), {
    toolbar: [
      "heading",
      "|",
      "bold",
      "italic",
      "underline",
      "link",
      "|",
      "bulletedList",
      "numberedList",
      "|",
      "blockQuote",
      "insertTable",
      "|",
      "undo",
      "redo",
      "|",
      "sourceEditing", // Ajoute le bouton pour l'édition HTML
    ],
    heading: {
      options: [
        {
          model: "paragraph",
          title: "Paragraphe",
          class: "ck-heading_paragraph",
        },
        {
          model: "heading1",
          view: "h1",
          title: "Titre 1",
          class: "ck-heading_heading1",
        },
        {
          model: "heading2",
          view: "h2",
          title: "Titre 2",
          class: "ck-heading_heading2",
        },
      ],
    },
    extraPlugins: ["SourceEditing"],
  })
    .then((editor) => {
      console.log("CKEditor 5 est prêt !", editor);
    })
    .catch((error) => {
      console.error("Erreur avec CKEditor 5 :", error);
    });
});
