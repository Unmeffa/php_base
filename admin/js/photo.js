import { handleModal } from "./modal";

const btnDetails = document.querySelectorAll("[data-details]");
btnDetails.forEach((btnDetail) => {
  btnDetail.addEventListener("click", async (e) => {
    e.preventDefault();
    const id = btnDetail.getAttribute("data-id");
    try {
      const request = await fetch(
        `ajax/photo/form.php?id=${id}&noCache=${Date.now()}`,
        {
          method: "GET",
          headers: {
            "Cache-Control": "no-cache",
          },
        }
      );
      if (request.ok) {
        const responseText = await request.text();
        document.body.insertAdjacentHTML("beforeend", responseText);
        handleModal();
        addDetailForm(id, "fr");
      } else {
        alert("Erreur lors de la requête: ", request.statusText);
      }
    } catch (error) {
      alert("Erreur lors de la requête fetch:", error);
    }
  });
});

async function handlePrio(id, newPrio) {
  try {
    const formData = new FormData();
    formData.append("id", id);
    formData.append("newPrio", newPrio);

    /*const response = await fetch("ajax/page/prio.php", {
      method: "POST",
      body: formData,
    });*/

    if (response.ok) {
      const result = await response.json();
      if (result.success) {
        window.location.reload(); // Recharger la page si tout est OK
      } else {
        alert(result.message); // Afficher une alerte en cas d'erreur
      }
    } else {
      alert("Erreur lors de la requête.");
    }
  } catch (error) {
    alert("Erreur : " + error);
  }
}

async function handleDelete(id) {
  try {
    const formData = new FormData();
    formData.append("id", id);
    formData.append("delete", 1);

    const response = await fetch("ajax/photo/delete.php", {
      method: "POST",
      body: formData,
    });

    if (response.ok) {
      const result = await response.json();
      if (result.success) {
        window.location.reload();
      } else {
        alert(result.message); // Afficher une alerte en cas d'erreur
      }
    } else {
      alert("Erreur lors de la requête.");
    }
  } catch (error) {
    alert("Erreur : " + error);
  }
}

const deleteButtons = document.querySelectorAll("a[data-id][data-delete]");
deleteButtons.forEach((btn) => {
  btn.addEventListener("click", (event) => {
    event.preventDefault();
    const id = btn.getAttribute("data-id");
    if (
      confirm(
        `Souhaitez vous supprimer la photo ? Cette opération est irréversible.`
      )
    ) {
      handleDelete(id);
    }
  });
});

const addDetailForm = async (id, locale = "fr") => {
  const url = `ajax/detail_photo/form.php?locale=${encodeURIComponent(
    locale
  )}&id=${encodeURIComponent(id)}&${Date.now()}`;

  try {
    const request = await fetch(url, {
      method: "GET",
    });

    const ajaxContent = document.querySelector("#detailForm");
    const response = await request.text();
    if (ajaxContent) {
      ajaxContent.insertAdjacentHTML("beforeend", response);
      document
        .querySelectorAll(".ajax-form")
        .forEach((f) => (f.style.display = "none"));
      document.querySelector("#detail_" + locale).style.display = "flex";
    }
  } catch (error) {
    alert("Erreur lors de l'envoi du formulaire: " + error.message);
  }
};

const handleDetails = async (form) => {
  const formData = new FormData(form);

  try {
    // Envoie les données via fetch en utilisant la méthode POST
    const response = await fetch("ajax/detail_photo/update.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();
    if (!result.success) {
      alert(result.errorsHtml);
    } else {
      alert("La modification a été réalisée.");
      window.location.reload();
    }
  } catch (error) {
    console.error("Erreur:", error);
    alert("Erreur lors de la soumission du formulaire. Veuillez réessayer.");
  }
};

document.body.addEventListener("submit", (e) => {
  const target = e.target;
  if (target.getAttribute("name") === "addDetails") {
    e.preventDefault();
    handleDetails(target);
  }
});

document.body.addEventListener("change", (e) => {
  const target = e.target;
  if (target.getAttribute("name") === "langs") {
    e.preventDefault();
    const idInput = document.querySelector('input[name="id"]');
    if (idInput) {
      const id = idInput.value;
      addDetailForm(id, target.value);
    }
  }
});

document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("photo-container");

  // Configuration de SortableJS
  Sortable.create(container, {
    animation: 150, // Animation lors du déplacement
    ghostClass: "sortable-ghost", // Classe appliquée à l'élément pendant le drag
    onEnd: async (evt) => {
      // Récupérer l'ordre actuel des éléments après le drag & drop
      const orderedItems = Array.from(container.children).map(
        (item, index) => ({
          id: item.getAttribute("data-id"),
          prio: index + 1,
        })
      );

      // Envoi de l'ordre par AJAX
      try {
        const response = await fetch("ajax/photo/prio.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(orderedItems),
        });

        const data = await response.json();
        if (!data.success) {
          alert("Erreur lors de la mise à jour de l'ordre");
        } else {
          console.log("Ordre mis à jour avec succès");
        }
      } catch (error) {
        console.error("Erreur:", error);
      }
    },
  });
});
