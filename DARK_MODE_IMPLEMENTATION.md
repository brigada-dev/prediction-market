# Dark Mode Implementation

This document explains how dark mode has been implemented in the PredictX application.

## Overview

The dark mode implementation uses Tailwind CSS's `darkMode: 'class'` strategy, which allows for manual control over when dark mode is active. The theme preference is stored in the browser's localStorage and persists across sessions.

## Key Components

### 1. Tailwind Configuration (`tailwind.config.js`)

```javascript
darkMode: 'class',
```

This enables class-based dark mode instead of media query-based dark mode.

### 2. Theme Toggle Component (`resources/views/components/theme-toggle.blade.php`)

A reusable component that provides:
- Two buttons for light and dark mode
- Alpine.js integration for reactive state management
- localStorage persistence
- Smooth transitions between themes

### 3. CSS Variables and Classes (`resources/css/app.css`)

Custom CSS variables and utility classes for:
- Gradient backgrounds (light and dark variants)
- Shadow effects (light and dark variants)
- Smooth transitions with `.transition-theme` class

### 4. Layout Updates

Both `app.blade.php` and `guest.blade.php` layouts include:
- Theme initialization script
- Dark mode classes on main containers
- Transition classes for smooth theme switching

## How It Works

### Theme Initialization

1. **Page Load**: The theme initialization script runs on every page load
2. **localStorage Check**: Retrieves the stored theme preference
3. **Class Application**: Adds `dark` class to `<html>` element if dark mode is selected

### Theme Switching

1. **User Action**: User clicks the theme toggle button
2. **Alpine.js Update**: Updates the theme state
3. **localStorage Update**: Saves the new preference
4. **Class Toggle**: Adds/removes `dark` class from `<html>` element
5. **CSS Transition**: Smooth transition between themes

### CSS Classes

The implementation uses Tailwind's dark mode classes extensively:

```html
<!-- Light mode -->
<div class="bg-white text-gray-900">

<!-- Dark mode -->
<div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
```

## Key Features

### 1. Persistence
- Theme preference is saved in localStorage
- Persists across browser sessions
- No server-side storage required

### 2. Smooth Transitions
- All theme changes include smooth transitions
- `.transition-theme` class provides consistent timing
- Prevents jarring visual changes

### 3. Comprehensive Coverage
- All major components support dark mode
- Navigation, cards, buttons, forms, etc.
- Consistent color scheme throughout

### 4. Accessibility
- High contrast ratios maintained
- Proper focus states for both themes
- Screen reader friendly

## Color Scheme

### Light Mode
- Background: `bg-gray-50` / `bg-white`
- Text: `text-gray-900` / `text-gray-600`
- Borders: `border-gray-200`
- Cards: `bg-white`

### Dark Mode
- Background: `bg-gray-900` / `bg-gray-800`
- Text: `text-white` / `text-gray-300`
- Borders: `border-gray-700`
- Cards: `bg-gray-800`

## Usage

### Adding Dark Mode to New Components

1. **Add dark mode classes**:
```html
<div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
```

2. **Include transition class**:
```html
<div class="transition-theme">
```

3. **Test both themes**:
- Use the theme toggle to verify appearance
- Check contrast ratios
- Ensure all interactive elements work

### Custom Components

For custom components, follow the same pattern:

```html
<div class="
    bg-white dark:bg-gray-800 
    border border-gray-200 dark:border-gray-700 
    text-gray-900 dark:text-white 
    transition-theme
">
    {{ $slot }}
</div>
```

## Browser Support

- Modern browsers with localStorage support
- Graceful degradation for older browsers
- No JavaScript required for basic functionality

## Performance

- Minimal performance impact
- CSS-only transitions
- No additional HTTP requests
- Efficient class-based switching

## Future Enhancements

Potential improvements:
1. System preference detection
2. Automatic theme switching based on time
3. Custom color schemes
4. Theme-specific assets (images, icons)
5. Server-side theme preference storage 