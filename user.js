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
 //when click a recent search keyword display the result


 const recentSearchesContainer = document.getElementById('recentSearchesContainer');
 const recentSearchButtons = document.querySelectorAll('.recent-search-btn');

 // Add click event listeners to each recent search button
 recentSearchButtons.forEach(button => {
     button.addEventListener('click', function() {
         // Display the recent searches container when any of the buttons is clicked
         recentSearchesContainer.style.display = 'block';
     });
 });