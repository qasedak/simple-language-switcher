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
});
