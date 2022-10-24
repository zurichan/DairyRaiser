let sidebar_wrapper = document.querySelector('.sidebar-wrapper');
let sidebar_btn = document.querySelector('#sidebar_btn');
let topbar_wrapper = document.querySelector('.topbar-wrapper');
let main_container = document.querySelector('.my-container');
let account_btn = document.querySelector('#account_btn');

sidebar_btn.addEventListener('click', function(e) {
    topbar_wrapper.classList.toggle('active');
    e.preventDefault();
    sidebar_wrapper.classList.toggle('active');
    main_container.classList.toggle('resize');
});

account_btn.addEventListener('click', function(e) {
    
    $(".admin_nav").find('i').toggleClass('bi bi-caret-right-fill');

    e.preventDefault();
    let admin_nav = document.querySelector('.admin_nav');
    let admin_config = document.querySelector('.admin_configs');

    admin_nav.classList.toggle('active');
    admin_config.classList.toggle('active');

    console.log('asd');
});