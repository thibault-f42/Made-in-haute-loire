
/**
 * Génère une requête Ajax
 * @param {string} selectorFormulaire Sur quel formulaire voulez-vous activer la requête Ajax.
 * @param {string} selectorElementAction Sur quel élément HTML souhaitez-vous placer le déclenchement de la requête Ajax.
 * @param {string} typeAction Type d'action mis en place sur le selectorElementAction (exemple : "click", "change", etc...).
 * @param {string} url Dans quel élément HTML souhaitez-vous que le bloc réponse s'affiche.
 */
function requeteAjaxPost(selectorFormulaire, selectorElementAction, typeAction, url) {
    document.querySelector(selectorElementAction).addEventListener('click', (event) => {
        event.preventDefault();
        const Form = new FormData(document.querySelector(selectorFormulaire));
        const Params = new URLSearchParams();
        Form.forEach((value, key) => {
            if (value.trim()) {
                Params.append(key, value);
            }
            else {
                throw 'Les champs sont obligatoires';
            }
        });
        const Url = new URL(window.location.href);
        fetch(url, {
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            },method: "POST", body: Params
        })
            .catch(function(res){ console.log(res) })
    });
}
