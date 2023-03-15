function sidebarToggleFunction() {
      // Le toogle de la sidebar
     let sidebarToggle = document.body.querySelector('#sidebarToggle');
      if (sidebarToggle) {
          // Pour garder le toogle de la sidebar en mémoire
          if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
              document.body.classList.toggle('sb-sidenav-toggled');
          }
          sidebarToggle.addEventListener('click', (event) => {
              event.preventDefault();
              document.body.classList.toggle('sb-sidenav-toggled');
              localStorage.setItem(
                  'sb|sidebar-toggle',
                  document.body.classList.contains('sb-sidenav-toggled')
              );
          });
      }
}

function backButtonFunction() {
    const back = document.body.querySelector('#back');
    if (back) {
        back.addEventListener('click', (event) => {
            event.preventDefault();
            window.history.back();
        });
    }
}

function toggleViewFunction() {
    let card = document.querySelector('#cardView');
    let table = document.querySelector('#tableView');
    let viewtable = document.querySelector('#viewtable');
    let viewcard = document.querySelector('#viewcard');
    let currentView = localStorage.getItem('currentView');
    
    // console.log(card, table, viewtable, viewcard, currentView);

    // Vérifier si les éléments existent
    if (card && table && viewtable && viewcard) {
        // par defaut on affiche la vue table
        if (currentView === null) {
            card.classList.remove('active');
            table.classList.add('active');
            viewtable.classList.remove('d-none');
            viewcard.classList.add('d-none');
            localStorage.setItem('currentView', 'table');
        } else if (currentView === 'card') {
            card.classList.add('active');
            table.classList.remove('active');
            viewcard.classList.remove('d-none');
            viewtable.classList.add('d-none');
        } else {
            card.classList.remove('active');
            table.classList.add('active');
            viewcard.classList.add('d-none');
            viewtable.classList.remove('d-none');
        }

        // Ajouter des écouteurs d'événements aux boutons de vue
        card.addEventListener('click', (event) => {
            event.preventDefault();
            card.classList.add('active');
            table.classList.remove('active');
            viewcard.classList.remove('d-none');
            viewtable.classList.add('d-none');
            localStorage.setItem('currentView', 'card');
        });

        table.addEventListener('click', (event) => {
            event.preventDefault();
            table.classList.add('active');
            card.classList.remove('active');
            viewtable.classList.remove('d-none');
            viewcard.classList.add('d-none');
            localStorage.setItem('currentView', 'table');
        });
    }
}

// On attend que le DOM soit chargé pour exécuter la fonction
document.addEventListener('DOMContentLoaded', toggleViewFunction);

function toogleViewFunctionArchived() {
    let cardArchived = document.querySelector('#cardViewArchived');
    let tableArchived = document.querySelector('#tableViewArchived');
    let viewtableArchived = document.querySelector('#viewtableArchived');
    let viewcardArchived = document.querySelector('#viewcardArchived');
    let currentViewArchived = localStorage.getItem('currentViewArchived');

    if (cardArchived && tableArchived && viewtableArchived && viewcardArchived) {
      if(currentViewArchived === null) {
        cardArchived.classList.remove('active');
        tableArchived.classList.add('active');
        viewtableArchived.classList.remove('d-none');
        viewcardArchived.classList.add('d-none');
        localStorage.setItem('currentViewArchived', 'table');
      } else if (currentViewArchived === 'card') {
        cardArchived.classList.add('active');
        tableArchived.classList.remove('active');
        viewcardArchived.classList.remove('d-none');
        viewtableArchived.classList.add('d-none');
      } else {
        cardArchived.classList.remove('active');
        tableArchived.classList.add('active');
        viewcardArchived.classList.add('d-none');
        viewtableArchived.classList.remove('d-none');
      }

        cardArchived.addEventListener('click', (event) => {
            event.preventDefault();
            cardArchived.classList.add('active');
            tableArchived.classList.remove('active');
            viewcardArchived.classList.remove('d-none');
            viewtableArchived.classList.add('d-none');
            localStorage.setItem('currentViewArchived', 'card');
        });

        tableArchived.addEventListener('click', (event) => {
            event.preventDefault();
            tableArchived.classList.add('active');
            cardArchived.classList.remove('active');
            viewtableArchived.classList.remove('d-none');
            viewcardArchived.classList.add('d-none');
            localStorage.setItem('currentViewArchived', 'table');
        });
}
}
document.addEventListener('DOMContentLoaded', toogleViewFunctionArchived);

let show = true;
// Pour afficher ou cacher le mot de passe
function showPassword() {
    if (show) {
        document
            .getElementById('registration_form_password')
            .setAttribute('type', 'text');
        document.getElementById('see_password').innerHTML = 'cacher';
        show = false;
    } else {
        document
            .getElementById('registration_form_password')
            .setAttribute('type', 'password');
        document.getElementById('see_password').innerHTML = 'afficher';
        show = true;
    }
}