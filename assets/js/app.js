/**
 * Football Manager Pro - Script principal
 * Version: 1.0.0
 * Description: Gestion des fonctionnalités de l'application Football Manager Pro
 */

$(document).ready(function () {
  "use strict";

  /* ==========================================================================
     Configuration & Constantes
     ========================================================================== */
  const CONFIG = {
    URLS: {
      GET_PLAYERS: "ajax/get_players.php",
      ADD_PLAYER: "ajax/add_player.php",
      UPDATE_PLAYER: "ajax/update_player.php",
      DELETE_PLAYER: "ajax/delete_player.php",
      PLAYER_DETAILS_TEMPLATE: "templates/components/player_details.html",
      PLAYER_CARD_TEMPLATE: "templates/components/player_card.html",
    },
    VALIDATION: {
      AGE: {
        MIN: 15,
        MAX: 45,
      },
      IMAGE: {
        ALLOWED_EXTENSIONS: /\.(jpg|jpeg|png|webp|gif)$/i,
      },
    },
    TOAST: {
      DURATION: 3000,
      POSITION: {
        top: "1rem",
        right: "1rem",
      },
    },
  };

  /* ==========================================================================
     État de l'application
     ========================================================================== */
  const State = {
    players: [],
    currentPlayerId: null,
    currentFilters: {
      team: "",
      position: "",
    },
    playerDetailsTemplate: "",
    playerCardTemplate: "",

    // Setters
    setPlayers(newPlayers) {
      this.players = newPlayers;
    },
    setCurrentPlayerId(id) {
      this.currentPlayerId = id;
    },
    setFilters(filters) {
      this.currentFilters = { ...this.currentFilters, ...filters };
    },
    setTemplate(type, template) {
      this[`${type}Template`] = template;
    },
  };

  /* ==========================================================================
     Gestionnaire de thème
     ========================================================================== */
  const ThemeManager = {
    init() {
      this.themeToggle = $("#themeToggle");
      this.prefersDarkScheme = window.matchMedia(
        "(prefers-color-scheme: dark)"
      );
      this.initializeTheme();
      this.bindEvents();
    },

    initializeTheme() {
      const savedTheme =
        localStorage.getItem("theme") ||
        (this.prefersDarkScheme.matches ? "dark" : "light");
      document.documentElement.setAttribute("data-bs-theme", savedTheme);
      this.updateThemeIcon(savedTheme);
    },

    bindEvents() {
      this.themeToggle.on("click", () => this.toggleTheme());
    },

    toggleTheme() {
      const currentTheme =
        document.documentElement.getAttribute("data-bs-theme");
      const newTheme = currentTheme === "dark" ? "light" : "dark";
      document.documentElement.setAttribute("data-bs-theme", newTheme);
      localStorage.setItem("theme", newTheme);
      this.updateThemeIcon(newTheme);
    },

    updateThemeIcon(theme) {
      this.themeToggle
        .find("i")
        .toggleClass("fa-sun", theme === "light")
        .toggleClass("fa-moon", theme === "dark");
    },
  };

  /* ==========================================================================
     Gestionnaire de joueurs
     ========================================================================== */
  const PlayerManager = {
    init() {
      this.container = $("#players-container");
      this.bindEvents();
      // Charger d'abord le template, puis les joueurs
      $.get(CONFIG.URLS.PLAYER_CARD_TEMPLATE, (template) => {
        State.setTemplate("playerCard", template);
        this.loadPlayers();
      });
    },

    bindEvents() {
      // Boutons d'action
      $(document).on("click", ".details-btn", (e) =>
        this.showDetails($(e.currentTarget).data("id"))
      );
      $(document).on("click", ".edit-btn", (e) =>
        this.loadForEdit($(e.currentTarget).data("id"))
      );
      $(document).on("click", ".delete-btn", (e) => {
        State.setCurrentPlayerId($(e.currentTarget).data("id"));
        $("#deleteModal").modal("show");
      });
    },

    loadPlayers(filters = {}) {
      $.ajax({
        url: CONFIG.URLS.GET_PLAYERS,
        method: "GET",
        data: filters,
        beforeSend: () => {
          this.showLoader();
        },
        success: (response) => {
          if (response.success) {
            State.setPlayers(response.players);
            this.displayPlayers(response.players);
            if (!filters.team) this.updateTeamFilter(response.players);
          } else {
            UIManager.showToast(
              response.error || "Erreur lors du chargement des joueurs",
              "danger"
            );
          }
        },
        error: (xhr) => {
          const response = xhr.responseJSON;
          UIManager.showToast(
            response?.error || "Erreur lors du chargement des joueurs",
            "danger"
          );
        },
      });
    },

    showLoader() {
      this.container.html(
        '<div class="text-center mt-5"><div class="spinner-border" role="status"></div></div>'
      );
    },

    displayPlayers(players) {
      this.container.empty();

      if (!players?.length) {
        this.showNoPlayersMessage();
        return;
      }

      players.forEach((player, index) => {
        this.container.append(this.createPlayerCard(player, index));
      });
    },

    showNoPlayersMessage() {
      this.container.html(
        '<div class="col-12 text-center"><p class="fw-bolder fs-4 text-danger">Aucun joueur trouvé</p></div>'
      );
    },

    createPlayerCard(player, index) {
      const $card = $(State.playerCardTemplate);

      // Mise à jour des données du joueur
      $card.attr("data-aos-delay", index * 100);
      $card.find(".card-header h5").text(player.name);
      $card.find(".player-team").text(player.team);
      $card.find(".player-position").text(player.position);

      // Mise à jour de l'image
      const $imageContainer = $card.find(".player-image");
      if (player.image_url) {
        $imageContainer.html(
          `<img src="${player.image_url}" alt="${player.name}" class="img-fluid rounded"/>`
        );
      } else {
        $imageContainer.replaceWith(
          `<div class="player-avatar">${player.name.charAt(0)}</div>`
        );
      }

      // Mise à jour des boutons d'action
      $card.find(".details-btn").attr("data-id", player.id);
      $card.find(".edit-btn").attr("data-id", player.id);
      $card.find(".delete-btn").attr("data-id", player.id);

      return $card;
    },

    updateTeamFilter(players) {
      if (!Array.isArray(players)) return;
      const teams = [...new Set(players.map((p) => p.team))].sort();
      const teamFilter = $("#teamFilter");
      teamFilter.find("option:not(:first)").remove();
      teams.forEach((team) => {
        teamFilter.append(`<option value="${team}">${team}</option>`);
      });
    },

    showDetails(playerId) {
      const player = State.players.find((p) => p.id === playerId);
      if (!player) return;

      // Helper pour obtenir la première lettre
      Handlebars.registerHelper("firstLetter", function (str) {
        return str.charAt(0);
      });

      // Helper pour valeur par défaut
      Handlebars.registerHelper("default", function (value, defaultValue) {
        return value || defaultValue;
      });

      const template = Handlebars.compile(State.playerDetailsTemplate);
      const modalContent = template(player);

      $("#playerDetailsContent").html(modalContent);
      $("#playerDetailsModal").modal("show");
    },

    loadForEdit(playerId) {
      const player = State.players.find((p) => p.id === playerId);
      if (!player) return;

      $("#editPlayerId").val(player.id);
      $("#editPlayerName").val(player.name);
      $("#editPlayerPosition").val(player.position);
      $("#editPlayerTeam").val(player.team);
      $("#editPlayerAge").val(player.age);
      $("#editPlayerNationality").val(player.nationality);
      $("#editPlayerGoals").val(player.goals_scored);
      $("#editPlayerImage").val(player.image_url || "");

      $("#editPlayerModal").modal("show");
    },
  };

  /* ==========================================================================
     Gestionnaire de formulaires
     ========================================================================== */
  const FormManager = {
    init() {
      this.bindEvents();
    },

    bindEvents() {
      $("#playerForm").on("submit", (e) => this.handleAddPlayer(e));
      $("#editPlayerForm").on("submit", (e) => this.handleEditPlayer(e));
      $("#updatePlayerBtn").on("click", () => $("#editPlayerForm").submit());

      // Réinitialisation des formulaires
      $("#addPlayerModal").on("hidden.bs.modal", () =>
        this.resetForm("playerForm")
      );
      $("#editPlayerModal").on("hidden.bs.modal", () =>
        this.resetForm("editPlayerForm", true)
      );
    },

    handleAddPlayer(e) {
      e.preventDefault();
      const formData = this.getFormData("player");

      if (!this.validateForm(formData, "player")) return;

      this.submitPlayer(
        formData,
        CONFIG.URLS.ADD_PLAYER,
        "Joueur ajouté avec succès"
      );
    },

    handleEditPlayer(e) {
      e.preventDefault();
      const formData = this.getFormData("editPlayer");

      if (!this.validateForm(formData, "editPlayer")) return;

      this.submitPlayer(
        formData,
        CONFIG.URLS.UPDATE_PLAYER,
        "Joueur mis à jour avec succès"
      );
    },

    getFormData(prefix) {
      return {
        id: $(`#${prefix}Id`)?.val(),
        name: $(`#${prefix}Name`).val().trim(),
        position: $(`#${prefix}Position`).val(),
        team: $(`#${prefix}Team`).val().trim(),
        age: parseInt($(`#${prefix}Age`).val()) || 0,
        nationality: $(`#${prefix}Nationality`).val().trim(),
        goals_scored: parseInt($(`#${prefix}Goals`).val()) || 0,
        image_url: $(`#${prefix}Image`).val().trim() || null,
      };
    },

    validateForm(formData, formPrefix) {
      const errors = {};

      if (!formData.name) errors.Name = "Le nom est requis";
      if (!formData.position) errors.Position = "La position est requise";
      if (!formData.team) errors.Team = "L'équipe est requise";
      if (!formData.nationality)
        errors.Nationality = "La nationalité est requise";

      if (!formData.age) {
        errors.Age = "L'âge est requis";
      } else if (
        formData.age < CONFIG.VALIDATION.AGE.MIN ||
        formData.age > CONFIG.VALIDATION.AGE.MAX
      ) {
        errors.Age = `L'âge doit être compris entre ${CONFIG.VALIDATION.AGE.MIN} et ${CONFIG.VALIDATION.AGE.MAX} ans`;
      }

      if (formData.image_url && !this.isValidImageUrl(formData.image_url)) {
        errors.Image = "L'URL de l'image n'est pas valide";
      }

      this.displayFormErrors(errors, formPrefix);
      return Object.keys(errors).length === 0;
    },

    isValidImageUrl(url) {
      try {
        const parsedUrl = new URL(url);
        return CONFIG.VALIDATION.IMAGE.ALLOWED_EXTENSIONS.test(
          parsedUrl.pathname
        );
      } catch (e) {
        return false;
      }
    },

    displayFormErrors(errors, formPrefix) {
      $(`#${formPrefix}Form input, #${formPrefix}Form select`).removeClass(
        "is-invalid"
      );
      $(`#${formPrefix}Form .invalid-feedback`).remove();

      Object.keys(errors).forEach((field) => {
        const input = $(`#${formPrefix}${field}`);
        input.addClass("is-invalid");
        input.after(`<div class="invalid-feedback">${errors[field]}</div>`);
      });
    },

    submitPlayer(formData, url, successMessage) {
      $.ajax({
        url: url,
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify(formData),
        success: () => {
          $(`#${formData.id ? "edit" : "add"}PlayerModal`).modal("hide");
          if (!formData.id) $("#playerForm")[0].reset();
          PlayerManager.loadPlayers(State.currentFilters);
          UIManager.showToast(successMessage, "success");
        },
        error: () => {
          UIManager.showToast(
            `Erreur lors de ${
              formData.id ? "la mise à jour" : "l'ajout"
            } du joueur`,
            "danger"
          );
        },
      });
    },

    resetForm(formId, isEdit = false) {
      if (!isEdit) $(`#${formId}`)[0].reset();
      $(`#${formId} .is-invalid`).removeClass("is-invalid");
      $(`#${formId} .invalid-feedback`).remove();
    },
  };

  /* ==========================================================================
     Gestionnaire d'interface utilisateur
     ========================================================================== */
  const UIManager = {
    init() {
      this.bindEvents();
      this.initializeAOS();
    },

    bindEvents() {
      $("#addPlayerBtn").on("click", () => $("#addPlayerModal").modal("show"));
      $("#confirmDelete").on("click", () => this.handleDelete());

      // Filtres et recherche
      $("#teamFilter, #positionFilter").on("change", () =>
        this.handleFilters()
      );
      $("#searchButton, #searchInput").on("click keypress", (e) =>
        this.handleSearch(e)
      );
    },

    initializeAOS() {
      AOS.init({
        duration: 800,
        once: true,
      });
    },

    handleDelete() {
      if (!State.currentPlayerId) return;

      $.ajax({
        url: CONFIG.URLS.DELETE_PLAYER,
        method: "POST",
        contentType: "application/json",
        data: JSON.stringify({ id: State.currentPlayerId }),
        success: (response) => {
          if (response.success) {
            $("#deleteModal").modal("hide");
            PlayerManager.loadPlayers(State.currentFilters);
            this.showToast(
              response.message || "Joueur supprimé avec succès",
              "success"
            );
          } else {
            this.showToast(
              response.error || "Erreur lors de la suppression",
              "danger"
            );
          }
        },
        error: (xhr) => {
          const response = xhr.responseJSON;
          this.showToast(
            response?.error || "Erreur lors de la suppression",
            "danger"
          );
        },
      });
    },

    handleFilters() {
      State.setFilters({
        team: $("#teamFilter").val(),
        position: $("#positionFilter").val(),
      });
      PlayerManager.loadPlayers(State.currentFilters);
    },

    handleSearch(e) {
      if (e.type === "click" || e.which === 13) {
        const searchTerm = $("#searchInput").val().trim().toLowerCase();
        const filteredPlayers = !searchTerm
          ? State.players
          : State.players.filter(
              (player) =>
                player.name.toLowerCase().includes(searchTerm) ||
                player.team.toLowerCase().includes(searchTerm)
            );
        PlayerManager.displayPlayers(filteredPlayers);
      }
    },

    showToast(message, type = "success") {
      $(".toast-container").remove();

      const toastContainer = $(
        '<div class="toast-container position-fixed top-0 end-0 p-3"></div>'
      );
      const toastId = "toast-" + Date.now();

      const toast = $(`
        <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="${CONFIG.TOAST.DURATION}">
          <div class="toast-header bg-${type} text-white">
            <strong class="me-auto">Notification</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
          <div class="toast-body">${message}</div>
        </div>
      `);

      $("body").append(toastContainer.append(toast));
      new bootstrap.Toast(document.getElementById(toastId)).show();
    },
  };

  /* ==========================================================================
     Initialisation de l'application
     ========================================================================== */
  function initializeApp() {
    // Chargement des templates
    $.get(CONFIG.URLS.PLAYER_DETAILS_TEMPLATE, (template) => {
      State.setTemplate("playerDetails", template);
    }).fail(() => {
      console.error("Erreur lors du chargement du template des détails");
    });

    // Initialisation des gestionnaires
    ThemeManager.init();
    PlayerManager.init();
    FormManager.init();
    UIManager.init();
  }

  // Démarrage de l'application
  initializeApp();
});
