
/**
 * Génère une map affichant les produits disponibles
 * @param produits [] --> tableau de produits + données gps pour générer les marqueurs
 */

function afficheProduits(produits)
{
    var maCarte = L.map('maCarte').setView([45.4333, 4.4], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
        attribution: 'données © <a href="//osm.org/copyright">OpenStreetMap</a>/ODbL - rendu <a href="//openstreetmap.fr">OSM France</a>',
        minZoom: 1,
        maxZoom: 13,
        ext: 'png',
    }).addTo(maCarte);

    var marqueurs = L.markerClusterGroup();
    var marqueursTab= [];

    for (produit in produits) {

        var icone = L.icon({

            iconSize: [50, 50],
            iconAnchor: [25, 50],
            popupAnchor: [0, -50]
        })

        //creation de marqueur et de popup ==> le mieux = un tableau
        var marqueur = L.marker([produits[produit].latitude, produits[produit].longitude]);
        // addTo(maCarte);
        marqueur.bindPopup(
            "<div>"+
            produits[produit].nomArticle+
            "<img src="+ "img/ProduitPhoto/" + produits[produit].urlFichier +">"+
            "</div>");
        marqueurs.addLayer(marqueur);
        //on ajoute les marqueurs au tableau marqueur
        marqueursTab.push(marqueur);
    }
    //on crée un groupe leaflet pour les marquzeurs
    var groupe = new L.featureGroup(marqueursTab)

    //On adapte le zoom de la map au groupe

    maCarte.fitBounds(groupe.getBounds())

    maCarte.addLayer(marqueurs);
}
