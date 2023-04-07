/* MENU BURGER */
var sidenav = document.getElementById("mySidenav");
var openBtn = document.getElementById("openBtn");
var closeBtn = document.getElementById("closeBtn");

openBtn.onclick = openNav;
closeBtn.onclick = closeNav;

/* Set the width of the side navigation to 250px */
function openNav() {
    sidenav.classList.add("active");
}

/* Set the width of the side navigation to 0 */
function closeNav() {
    sidenav.classList.remove("active");
}
/* MENU BURGER */

$(document).ready(function(){
    $('.num').counterUp({
        time: 1200
    });
});

/** Favoris **/

function toggleFavorite(catId) {

    var icon = document.getElementById("favorite-icon-" + catId);


    if (icon.classList.contains("fa-solid")) {

        fetch("/favorite/" + catId, {
            method: "DELETE",
        }).then(r => r.json()).then(data => {
            if (data.success) {
                icon.classList.remove("fa-solid");
                icon.classList.add("fa-regular");
            }
        }).then(()=>{
            location.reload();
        })
    } else {

        fetch("/favorite/" + catId, {
            method: "POST",
        }).then(r => r.json()).then(data => {
            icon.classList.remove("fa-regular");
            icon.classList.add("fa-solid");
        }).then(()=>{
            location.reload();
        });
    }
}

/** Autocompletion **/

const input = document.getElementById('city');
var autocomplete = new google.maps.places.Autocomplete(input);
google.maps.event.addListener(autocomplete, 'place_changed', function(){
    const place = autocomplete.getPlace();
    input.value = place.name
});