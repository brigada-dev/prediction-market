<!DOCTYPE html>
<html lang="en" class="transition-theme">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Debug</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
    <style>
        .transition-theme {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white transition-theme">
    <div class="min-h-screen p-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold mb-8">Theme Toggle Debug</h1>
            
            <!-- Theme Toggle -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Simple Theme Toggle Component</h2>
                <x-simple-theme-toggle />
            </div>
            
            <!-- Original Theme Toggle -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Original Theme Toggle Component (Alpine.js)</h2>
                <x-theme-toggle />
            </div>
            
            <!-- Manual Toggle -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Manual Toggle (Fallback)</h2>
                <div class="flex items-center space-x-2">
                    <button onclick="manualSetTheme('light')" class="px-4 py-2 bg-blue-500 text-white rounded">Light</button>
                    <button onclick="manualSetTheme('dark')" class="px-4 py-2 bg-gray-800 text-white rounded">Dark</button>
                </div>
            </div>
            
            <!-- Current Theme Display -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-4">Current Theme</h2>
                <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                    <p>Current theme: <span id="current-theme">Loading...</span></p>
                    <p>localStorage value: <span id="localstorage-value">Loading...</span></p>
                    <p>HTML class: <span id="html-class">Loading...</span></p>
                </div>
            </div>
            
            <!-- Test Content -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold mb-4">Test Card 1</h3>
                    <p class="text-gray-600 dark:text-gray-400">This is a test card to verify dark mode styling.</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold mb-4">Test Card 2</h3>
                    <p class="text-gray-600 dark:text-gray-400">Another test card with different content.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize theme
        (function() {
            try {
                const theme = localStorage.getItem('theme') || 'light';
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                updateDebugInfo();
            } catch (error) {
                console.warn('Theme initialization failed:', error);
                document.documentElement.classList.remove('dark');
            }
        })();

        // Manual theme setter
        function manualSetTheme(theme) {
            try {
                localStorage.setItem('theme', theme);
                if (theme === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                updateDebugInfo();
            } catch (error) {
                console.error('Manual theme set failed:', error);
            }
        }

        // Update debug information
        function updateDebugInfo() {
            const currentTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
            const localStorageValue = localStorage.getItem('theme') || 'not set';
            const htmlClass = document.documentElement.className;
            
            document.getElementById('current-theme').textContent = currentTheme;
            document.getElementById('localstorage-value').textContent = localStorageValue;
            document.getElementById('html-class').textContent = htmlClass;
        }

        // Update debug info every second
        setInterval(updateDebugInfo, 1000);
    </script>
</body>
</html> 