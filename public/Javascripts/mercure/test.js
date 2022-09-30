
//Récupération des notifications mercure

const url = new URL('http://localhost:3000/.well-known/mercure');
url.searchParams.append('topic','http://localhost/Made-in-haute-loire/public/messages/');

const eventSource = new EventSource(url);

// Message pophup
eventSource.onmessage = e => {
    document.querySelector('header').insertAdjacentHTML('afterend', '<div class="alert alert-success">Ping !</div>')
    window.setTimeout(() => {
        const $alert = document.querySelector('.alert')
        $alert.parentNode.removeChild($alert)
    }, 2000);
}