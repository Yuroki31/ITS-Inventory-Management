document.querySelectorAll('.sidebar-link').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Get the URL from the link's href
        const url = new URL(this.getAttribute('href'), window.location.origin);
        const page = url.searchParams.get('page'); // Extract the 'page' query parameter
        
        // Update the URL in the browser (without reloading the page)
        history.pushState({ page: page }, '', url.href);

        // Load content dynamically based on the 'page' parameter
        fetch(`content/${page}.php`)  // Assuming content files are stored in the 'content/' directory
            .then(response => response.text())
            .then(html => {
                document.getElementById('dynamic-content').innerHTML = html;  // Update only the dynamic content
            })
            .catch(error => console.error('Error loading content:', error));
    });
});

// Handle back/forward navigation
window.addEventListener('popstate', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page') || 'item_list'; // Default to 'item_list' if no page parameter
    fetch(`content/${page}.php`)
        .then(response => response.text())+
        .then(html => {
            document.getElementById('dynamic-content').innerHTML = html;
        })
        .catch(error => console.error('Error loading content:', error));
});

// Load initial content based on the URL (in case the page is accessed directly)
window.addEventListener('load', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page') || 'item_list'; // Default to 'item_list' if no page parameter
    fetch(`content/${page}.php`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('dynamic-content').innerHTML = html;
        })
        .catch(error => console.error('Error loading content:', error));
});
