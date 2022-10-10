window.addEventListener("load",  () =>
{

    // On met un écouteur d'événements sur tous les boutons "répondre"
    document.querySelectorAll("[data-reponse]").forEach(element=>{
        console.log(element);
        element.addEventListener("click", function()
        {
            document.querySelector("#commentaires_parentid").value = this.dataset.id;

        })
    })
})