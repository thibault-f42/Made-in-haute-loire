window.onload = () => {
    //variables a définire
    let stripe = Stripe('pk_test_51J6B95KScutnDNiWZTkXQWeHJ8lRgUooJl5YwTmmfXNGbVnxz4m0Skt4skKlDO5C7SZ34cCyYCzPT5ONC1ilsSxN00CR1KJ18p')
    let elements = stripe.elements()

    // TODO : CHANGER LE LIEN LORS DU PASSAGE ONLINE POUR PAGE ACCUEIL
    let redirect = "/Made-in-haute-loire/public/"

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

        console.log(clientSecret);
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


