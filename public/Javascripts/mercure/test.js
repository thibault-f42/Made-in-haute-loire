
//Récupération des notifications mercure
var userid ={ {user.id}};
console.log(userid);

const url = new URL('http://localhost:3000/.well-known/mercure');
url.searchParams.append('topic','http://localhost/Made-in-haute-loire/public/utilisateur');

const eventSource = new EventSource(url);
// Message pophup
eventSource.onmessage = e => {

    let message = 'Ping !'
    if (data.username){
        message = data.username
    }
    document.querySelector('header').insertAdjacentHTML('afterend', '<div class="alert alert-success">message</div>')
    window.setTimeout(() => {
        const $alert = document.querySelector('.alert')
        $alert.parentNode.removeChild($alert)
    }, 2000);
}