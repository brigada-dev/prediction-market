<div x-data="themeToggle()" class="flex items-center">
    <!-- Light Mode Button -->
    <button 
        @click="setTheme('light')"
        :class="{ 'bg-blue-100 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400': theme === 'light', 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600': theme !== 'light' }"
        class="theme-toggle light p-2 rounded-l-lg transition-all duration-200"
        title="Light Mode"
        onclick="fallbackSetTheme('light')"
    >
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
        </svg>
    </button>

    <!-- Dark Mode Button -->
    <button 
        @click="setTheme('dark')"
        :class="{ 'bg-gray-800 dark:bg-gray-600 text-white': theme === 'dark', 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600': theme !== 'dark' }"
        class="theme-toggle dark p-2 rounded-r-lg transition-all duration-200"
        title="Dark Mode"
        onclick="fallbackSetTheme('dark')"
    >
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
        </svg>
    </button>
</div>

<script>
function themeToggle() {
    return {
        theme: 'light',
        init() {
            // Get theme from localStorage or default to light
            this.theme = localStorage.getItem('theme') || 'light';
            
            // Apply theme immediately
            this.applyTheme(this.theme);
        },
        setTheme(newTheme) {
            this.theme = newTheme;
            localStorage.setItem('theme', newTheme);
            this.applyTheme(newTheme);
        },
        applyTheme(theme) {
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    }
}

// Fallback function in case Alpine.js fails
function fallbackSetTheme(theme) {
    try {
        localStorage.setItem('theme', theme);
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        
        // Update button states
        const buttons = document.querySelectorAll('.theme-toggle button');
        buttons.forEach(button => {
            button.classList.remove('bg-blue-100', 'dark:bg-blue-900/20', 'text-blue-600', 'dark:text-blue-400', 'bg-gray-800', 'dark:bg-gray-600', 'text-white');
            button.classList.add('bg-gray-100', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-400');
        });
        
        // Highlight active button
        if (theme === 'light') {
            buttons[0].classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-400');
            buttons[0].classList.add('bg-blue-100', 'dark:bg-blue-900/20', 'text-blue-600', 'dark:text-blue-400');
        } else {
            buttons[1].classList.remove('bg-gray-100', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-400');
            buttons[1].classList.add('bg-gray-800', 'dark:bg-gray-600', 'text-white');
        }
    } catch (error) {
        console.error('Theme toggle failed:', error);
    }
}
</script> 