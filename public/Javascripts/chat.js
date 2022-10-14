
/**
 * Génère une requête Ajax
 * @param {string} selectorFormulaire Sur quel formulaire voulez-vous activer la requête Ajax.
 * @param {string} selectorElementAction Sur quel élément HTML souhaitez-vous placer le déclenchement de la requête Ajax.
 * @param {string} typeAction Type d'action mis en place sur le selectorElementAction (exemple : "click", "change", etc...).
 * @param {string} url url pour poster le message.
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
function scroll() {
   let messages = document.getElementById('messages');
   messages.scrollTop = messages.scrollHeight;
}

/**
 * @param {string} chemin Adresse du topique mercure
 */
function mercure(chemin) {
    element = document.getElementById('messages-chat');
    element.scrollTop = element.scrollHeight;
    const url = new URL('http://localhost:3000/.well-known/mercure');

    url.searchParams.append('topic','http://localhost/Made-in-haute-loire/public/'+ chemin);

    const eventSource = new EventSource(url);
    // Message pophup
    eventSource.onmessage = e => {
        const data = JSON.parse(e.data)
        let message = 'Ping !'
        if (data.username){
            message = data.username
        }
        location.reload(true);//todo Provisoire
        // document.querySelector('header').insertAdjacentHTML('afterend', '<div class="alert alert-success">message</div>')
        // console.log('message');
        // window.setTimeout(() => {
        //     const $alert = document.querySelector('.alert')
        //     $alert.parentNode.removeChild($alert)
        // }, 2000);
    }
}

