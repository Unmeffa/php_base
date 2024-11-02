export function handleModal() {
  document.body.style.overflow = "hidden";

  // S'assurer que l'événement est lié une seule fois au bouton de fermeture du modal
  const modal = document.getElementById("modal");
  if (!modal) return;

  const closeModalButton = document.getElementById("close-modal");
  closeModalButton?.removeEventListener("click", closeModalHandler); // Supprimer d'abord l'ancien écouteur s'il existe
  closeModalButton?.addEventListener("click", closeModalHandler);
}

function closeModalHandler() {
  const modal = document.getElementById("modal");
  modal.remove();
  document.body.style.overflow = "initial";
}
