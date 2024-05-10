// https://leafletjs.com/index.html



$(document).ready(function () {
    // Verifica se il browser supporta l'API Geolocation
    if ("geolocation" in navigator) {
        // Ottieni la posizione corrente dell'utente
        navigator.geolocation.getCurrentPosition(function (position) {
            // Ottieni latitudine e longitudine dalla posizione
            let lat = position.coords.latitude;
            let lng = position.coords.longitude;
            let zoom = 10;
            let map = L.map('map').setView([lat, lng], zoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            // L.marker([lat, lng]).addTo(map)
            //     .bindPopup('A pretty CSS3 popup.<br> Easily customizable.')
            //     .openPopup();

            map.on('click', function (e) {
                let lat = e.latlng.lat;
                let lng = e.latlng.lng;

                console.log('You clicked the map at latitude: ' + lat + ' and longitude: ' + lng);
            });
        }, function (error) {
            // Gestisci eventuali errori durante il recupero della posizione
            console.error("Errore durante il recupero della posizione:", error);
        });
    } else {
        // Il browser non supporta l'API Geolocation
        console.error("Il browser non supporta l'API Geolocation.");
    }


});
