@php
$currentLocale = app()->getLocale();
$languages = [
    'en' => ['name' => 'English', 'flag' => '🇺🇸'],
    'sq' => ['name' => 'Shqip', 'flag' => '🇦🇱'],
    'de' => ['name' => 'Deutsch', 'flag' => '🇩🇪'],
];
@endphp

<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" 
            class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
        <span class="text-lg">{{ $languages[$currentLocale]['flag'] }}</span>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $languages[$currentLocale]['name'] }}</span>
        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 transform transition-transform" 
             :class="{ 'rotate-180': open }"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
        @foreach($languages as $locale => $language)
            <a href="{{ request()->fullUrlWithQuery(['locale' => $locale]) }}" 
               class="flex items-center space-x-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ $currentLocale === $locale ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300' }} {{ $loop->first ? 'rounded-t-lg' : '' }} {{ $loop->last ? 'rounded-b-lg' : '' }}">
                <span class="text-lg">{{ $language['flag'] }}</span>
                <span class="text-sm font-medium">{{ $language['name'] }}</span>
                @if($currentLocale === $locale)
                    <svg class="w-4 h-4 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                @endif
            </a>
        @endforeach
    </div>
</div>
