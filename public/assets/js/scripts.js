window.addEventListener('DOMContentLoaded', event => {

    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    console.log(sidebarToggle);
    if (sidebarToggle) {
        // icon toggle par défaut
        sidebarToggle.innerHTML = '<i class="fa fa-times"></i> Close Sidebar'
        // Uncomment Below to persist sidebar toggle between refreshes
        if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
            document.body.classList.toggle('sb-sidenav-toggled');
        }

        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            if (document.body.classList.contains('sb-sidenav-toggled')) {
                sidebarToggle.innerHTML = '<i class="fa fa-times"></i> Close Sidebar';
                // gestion du responsive de la sidebar
                if (document.body.classList.contains('sb-sidenav-toggled') && window.innerWidth < 992) {
                    sidebarToggle.innerHTML = '<i class="fa fa-times"></i> Close Sidebar';
                } else {
                    sidebarToggle.innerHTML = '<i class="fa fa-bars"></i> Open Sidebar';
                }

            } else if (!document.body.classList.contains('sb-sidenav-toggled') && window.innerWidth < 992) {
                sidebarToggle.innerHTML = '<i class="fa fa-bars"></i> Open Sidebar';
            } else {
                sidebarToggle.innerHTML = '<i class="fa fa-times"></i> Close Sidebar';
            }
            localStorage.setItem
            ('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }
});



