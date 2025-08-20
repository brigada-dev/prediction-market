<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alpine.js Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Alpine.js Test</h1>
        
        <!-- Simple Alpine.js test -->
        <div x-data="{ count: 0 }" class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Alpine.js Counter Test</h2>
            <p class="mb-4">Count: <span x-text="count"></span></p>
            <button @click="count++" class="px-4 py-2 bg-blue-500 text-white rounded">Increment</button>
        </div>
        
        <!-- Theme toggle test -->
        <div x-data="{ theme: 'light' }" class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Alpine.js Theme Test</h2>
            <p class="mb-4">Current theme: <span x-text="theme"></span></p>
            <button @click="theme = 'light'" class="px-4 py-2 bg-blue-500 text-white rounded mr-2">Light</button>
            <button @click="theme = 'dark'" class="px-4 py-2 bg-gray-800 text-white rounded">Dark</button>
        </div>
        
        <!-- Status -->
        <div class="bg-white p-4 rounded-lg border">
            <h3 class="font-semibold mb-2">Status</h3>
            <p id="alpine-status">Checking Alpine.js...</p>
        </div>
    </div>

    <script>
        // Check if Alpine.js is loaded
        setTimeout(() => {
            const status = document.getElementById('alpine-status');
            if (window.Alpine) {
                status.textContent = '✅ Alpine.js is loaded and working!';
                status.className = 'text-green-600';
            } else {
                status.textContent = '❌ Alpine.js is not loaded!';
                status.className = 'text-red-600';
            }
        }, 1000);
    </script>
</body>
</html> 