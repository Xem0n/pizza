"use strict";

let form;

function openModal($el) {
  $el.classList.add("is-active");
}

function closeModal($el) {
  $el.classList.remove("is-active");
}

function closeAllModals() {
  (document.querySelectorAll(".modal") || []).forEach(($modal) => {
    closeModal($modal);
  });
}

// (document.querySelectorAll(".js-modal-trigger") || []).forEach(($trigger) => {
(document.querySelectorAll("form") || []).forEach(($trigger) => {
  const modal = $trigger.dataset.target;
  const $target = document.getElementById(modal);

  $trigger.addEventListener("submit", event => {
    event.preventDefault();

    form = $trigger;

    openModal($target);
  });
});

(
  document.querySelectorAll(
    ".modal-background, .modal-close, .modal-card-head .delete, .modal-card-foot .button"
  ) || []
).forEach(($close) => {
  const $target = $close.closest(".modal");

  $close.addEventListener("click", () => {
    closeModal($target);
  });
});

document.addEventListener("keydown", (event) => {
  const e = event || window.event;

  if (e.keyCode === 27) {
    closeAllModals();
  }
});

document.getElementById("sendForm").addEventListener("click", () => {
    if (!form) {
        return;
    }

    const password = document.querySelector(".modal input").cloneNode();
    password.id = "verify_password";
    password.classList.add("is-hidden")
    form.appendChild(password);

    form.submit();
});