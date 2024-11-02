import { handleModal } from "./modal";

const btnAdd = document.getElementById("addPage");
btnAdd?.addEventListener("click", async (e) => {
  e.preventDefault();

  try {
    const request = await fetch(`ajax/page/form.php?noCache=${Date.now()}`, {
      method: "GET",
      headers: {
        "Cache-Control": "no-cache",
      },
    });
    if (request.ok) {
      const responseText = await request.text();
      document.body.insertAdjacentHTML("beforeend", responseText);
      handleModal();
    } else {
      alert("Erreur lors de la requête: ", request.statusText);
    }
  } catch (error) {
    alert("Erreur lors de la requête fetch:", error);
  }
});

const addPageForm = async (form) => {
  const formData = new FormData(form);
  try {
    const request = await fetch("ajax/page/add.php", {
      method: "POST",
      body: formData,
    });

    if (request.ok) {
      const contentType = request.headers.get("content-type");
      // Vérification si la réponse est en JSON
      if (contentType && contentType.indexOf("application/json") !== -1) {
        const response = await request.json();
        if (response.errors) {
          const alert = document.createElement("div");
          alert.classList.add("alert");
          response.errors.forEach((error) => {
            alert.innerHTML += `<p>${error}</p>`;
          });
          form.insertAdjacentElement("beforeend", alert);
        } else {
          // Si tout est OK, on peut recharger ou faire une action spécifique
          window.location.reload();
        }
      } else {
        // Si ce n'est pas du JSON, on traite comme du texte
        const responseText = await request.text();
        const alert = document.createElement("div");
        alert.classList.add("alert");
        alert.innerHTML = responseText;
        form.insertAdjacentElement("beforeend", alert);
      }
    } else {
      alert("Erreur lors de la requête: " + request.statusText);
    }
  } catch (error) {
    alert("Erreur lors de l'envoi du formulaire: " + error.message);
  }
};

const btnsActive = document.querySelectorAll("a[data-id][data-active]");
btnsActive.forEach((btn) => {
  btn.addEventListener("click", async (event) => {
    event.preventDefault();

    try {
      const formData = new FormData();
      formData.append("id", btn.getAttribute("data-id"));
      formData.append("active", btn.getAttribute("data-active"));

      const request = await fetch(`ajax/page/active.php`, {
        method: "POST",
        body: formData,
      });
      if (request.ok) {
        const response = await request.json();
        btn.innerHTML = response.icon;
        btn.setAttribute("data-active", response.active);
      } else {
        alert("Erreur lors de la requête: " + request.statusText);
      }
    } catch (error) {
      alert("Erreur lors de la requête fetch: " + error);
    }
  });
});

async function handlePrio(id, newPrio) {
  try {
    const formData = new FormData();
    formData.append("id", id);
    formData.append("newPrio", newPrio);

    const response = await fetch("ajax/page/prio.php", {
      method: "POST",
      body: formData,
    });

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

const prioButtons = document.querySelectorAll("a[data-id][data-new-prio]");
prioButtons.forEach((btn) => {
  btn.addEventListener("click", (event) => {
    event.preventDefault();
    const id = btn.getAttribute("data-id");
    const newPrio = btn.getAttribute("data-new-prio");

    handlePrio(id, newPrio);
  });
});

async function handleDelete(id) {
  try {
    const formData = new FormData();
    formData.append("id", id);
    formData.append("delete", 1);

    const response = await fetch("ajax/page/delete.php", {
      method: "POST",
      body: formData,
    });

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

const deleteButtons = document.querySelectorAll("a[data-id][data-delete]");
deleteButtons.forEach((btn) => {
  btn.addEventListener("click", (event) => {
    event.preventDefault();
    const id = btn.getAttribute("data-id");
    const name = btn.getAttribute("data-name");
    if (
      confirm(
        `Souhaitez vous supprimer la page ${name} ? Cette opération est irréversible.`
      )
    ) {
      handleDelete(id);
    }
  });
});

document.body.addEventListener("submit", (e) => {
  const target = e.target;
  if (target.getAttribute("name") === "addPage") {
    e.preventDefault();
    addPageForm(target);
  }
});
