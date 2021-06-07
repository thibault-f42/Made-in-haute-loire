
window.onload = () => {
    //Gestion des liens supprimer
    let balises = document.querySelectorAll("[data-delete]")

    for (balise of balises) {

        balise.addEventListener("click", function (e){
            //on empèche la navigation
            e.preventDefault()

            // Confirmation
            if (confirm("Souhaitez vous supprimer cette photo ?")) {
                //On envoie une requete ajax vers le lien avec la methode "DELETE"
                fetch(this.getAttribute("href"), {
                    method:'DELETE',
                    headers:{
                        'X-Requested-With' : "XMLHttpRequest",
                        "Content-Type" : "application/json"},
                        body: JSON.stringify({"_token": this.dataset.token})
                }).then(
                    //récup de la reponse json
                    reponse => reponse.json()
                ).then( data => {
                    if (data.success){
                        console.log('ok');
                        this.parentElement.remove()
                    }
                    else {
                        alert(data.error)
                    }
                }).catch(e => alert(e))
            }
        })
    }
}


// /Supprimer/image{id}
