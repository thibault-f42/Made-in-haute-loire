
/**
 * Génère une map affichant les produits disponibles
 * @param produits [] --> tableau de produits + données gps pour générer les marqueurs
 */

function afficheEntreprise(produits)
{
    var map = L.map('map').setView([45.4333, 4.4], 5);
    L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
        attribution: 'données © <a href="//osm.org/copyright">OpenStreetMap</a>/ODbL - rendu <a href="//openstreetmap.fr">OSM France</a>',
        minZoom: 1,
        maxZoom: 13,
        ext: 'png',

    }).addTo(map);

    var marqueurs = L.markerClusterGroup();
    var marqueursTab= [];

    for (produit in produits) {

         //creation de marqueur et de popup ==> le mieux = un tableau
        var marqueur = L.marker([produits[produit].latitude, produits[produit].longitude]);
        // addTo(map);
        marqueur.bindPopup(
            "<div>"+
            produits[produit].nomArticle+
            "</div>");
        marqueurs.addLayer(marqueur);
        //on ajoute les marqueurs au tableau marqueur
        marqueursTab.push(marqueur);
    }
    //on crée un groupe leaflet pour les marquzeurs
    var groupe = new L.featureGroup(marqueursTab)

    //On adapte le zoom de la map au groupe

    map.fitBounds(groupe.getBounds())

    map.addLayer(marqueur);

}





