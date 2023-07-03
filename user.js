 // Get the switch mode checkbox element
 const switchModeCheckbox = document.getElementById('switch-mode-checkbox');


 switchModeCheckbox.addEventListener('change', function() {

     document.body.classList.toggle('dark');
 });
 const searchInput = document.getElementById('search-input');
 const searchClearButton = document.getElementById('search-clear-button');

 // Clear button event listener
 searchClearButton.addEventListener('click', function() {
     searchInput.value = '';
     searchClearButton.style.display = 'none';
 });

 // Input event listener
 searchInput.addEventListener('input', function() {
     if (searchInput.value.trim() !== '') {
         searchClearButton.style.display = 'block';
     } else {
         searchClearButton.style.display = 'none';
     }
 });

 const profileLink = document.querySelector(".profile");
 profileLink.addEventListener("click", toggleSidebar);

 // Function to toggle the sidebar visibility
 function toggleSidebar() {
     const sidebarContainer = document.getElementById("sidebarContainer");
     sidebarContainer.classList.toggle("active");
 }


 // Add this function to display the notification
 function showNotification(message) {
     // Create a notification element
     var notification = document.createElement('div');
     notification.className = 'notification';
     notification.textContent = message;

     // Append the notification to the container
     var container = document.getElementById('notification-container');
     container.appendChild(notification);

     // Remove the notification after a certain time (e.g., 5 seconds)
     setTimeout(function() {
         container.removeChild(notification);
     }, 5000);
 }