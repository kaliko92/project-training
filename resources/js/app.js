import './bootstrap';

window.Echo.channel('posts')
    .listen('post.created', (e) => {
        console.log('New post created:', e.post);
        alert(`New post created: ${e.post.title}`);
    });
