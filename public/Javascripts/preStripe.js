window.onload = () => {
    //variables a définire
    let stripe = Stripe('METTRE LA CLE PUBLIQUE')
    let elements = stripe.elements()

    // TODO : CHANGER LE LIEN LORS DU PASSAGE ONLINE POUR PAGE ACCUEIL
    let redirect = "/Made-in-haute-loire/public/commande/CommandeValidee"

    //chargement des objets de la page
    let cardHolderName = document.getElementById('cardholder-name')
    let cardButton = document.getElementById('cardButton')
    let clientSecret = cardButton.dataset.secret;

    //creation des éléments du formulaire de carte
    //on crée une carte
    let card = elements.create("card")
    card.mount('#card-element')

    //on gere la saisie (messages d'erreur)
    card.addEventListener("change", (event) => {
        let displayError =   document.getElementById("card-errors")
        if(event.error) {
            displayError.textContent = event.error.message;
        }
        else {
            displayError.textContent ="";
        }
    })

    //on gere le paiement en JS UNIQUEMENT

    cardButton.addEventListener("click", ()=>{

        document.location.href = redirect
        stripe.handleCardPayment(
            clientSecret,
            card,
            { payment_method_data : {
                    billing_details : {name : cardHolderName.value}
                }
            }
        ).then((result)=>{
            if (result.error) {
                document.getElementById('errors').innerText = result.error.message
            }else {
                document.location.href = redirect
            }
        })
    })

}


