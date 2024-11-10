document.addEventListener('DOMContentLoaded', function() {
    // Select popup elements
    const popup = document.querySelector('.popup-content');
    const popbtn = document.querySelector('.popbtn');

    // Toggle popup visibility on button click
    popbtn.addEventListener('click', function(e) {
        e.stopPropagation();
        popup.classList.toggle('show');
    });

    // Close popup when clicking outside
    document.addEventListener('click', function(event) {
        if (!popup.contains(event.target) && !popbtn.contains(event.target)) {
            popup.classList.remove('show');
        }
    });

    // Add click handler for language links
    document.querySelectorAll('.sls-language-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            // Set cookie for 30 days
            const language = this.getAttribute('data-language');
            const date = new Date();
            date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000));
            document.cookie = "sls_user_language=" + language + 
                            "; expires=" + date.toUTCString() + 
                            "; path=" + slsData.cookiePath + 
                            "; domain=" + slsData.cookieDomain;
        });
    });
});
