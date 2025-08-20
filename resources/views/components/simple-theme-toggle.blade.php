<div class="flex items-center" id="simple-theme-toggle">
    <!-- Light Mode Button -->
    <button 
        id="light-btn"
        class="theme-toggle light p-2 rounded-l-lg transition-all duration-200 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600"
        title="Light Mode"
        onclick="simpleSetTheme('light')"
    >
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
        </svg>
    </button>

    <!-- Dark Mode Button -->
    <button 
        id="dark-btn"
        class="theme-toggle dark p-2 rounded-r-lg transition-all duration-200 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600"
        title="Dark Mode"
        onclick="simpleSetTheme('dark')"
    >
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
        </svg>
    </button>
</div>

<script>
// Simple theme toggle without Alpine.js
function simpleSetTheme(theme) {
    try {
        // Save to localStorage
        localStorage.setItem('theme', theme);
        
        // Apply theme to document
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        
        // Update button states
        updateButtonStates(theme);
        
        console.log('Theme set to:', theme);
    } catch (error) {
        console.error('Failed to set theme:', error);
    }
}

function updateButtonStates(activeTheme) {
    const lightBtn = document.getElementById('light-btn');
    const darkBtn = document.getElementById('dark-btn');
    
    // Reset both buttons
    lightBtn.className = 'theme-toggle light p-2 rounded-l-lg transition-all duration-200 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600';
    darkBtn.className = 'theme-toggle dark p-2 rounded-r-lg transition-all duration-200 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600';
    
    // Highlight active button
    if (activeTheme === 'light') {
        lightBtn.className = 'theme-toggle light p-2 rounded-l-lg transition-all duration-200 bg-blue-100 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400';
    } else {
        darkBtn.className = 'theme-toggle dark p-2 rounded-r-lg transition-all duration-200 bg-gray-800 dark:bg-gray-600 text-white';
    }
}

// Initialize theme on page load
(function() {
    try {
        const theme = localStorage.getItem('theme') || 'light';
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        updateButtonStates(theme);
    } catch (error) {
        console.warn('Theme initialization failed:', error);
        document.documentElement.classList.remove('dark');
        updateButtonStates('light');
    }
})();
</script> 