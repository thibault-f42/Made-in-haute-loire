window.onload = () => {

    // On met un écouteur d'événements sur tous les boutons "répondre"

    document.querySelector("[data-reponse]").forEach(element=>{
        element.addEventListener("click", function ()
        {
            document.querySelector("#commentaires_parentid").value = this.dataset.id;
        })
    })
}