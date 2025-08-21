# Parashiko.com – Brand Guide

## Core Identity
- **Meaning**: “Parashiko” = predict (Albanian)
- **Concept**: Futuristic, data-driven prediction markets with Kosovo roots
- **Personality**: Bold, trustworthy, energetic, forward-looking

## Logo System
- **Primary**: Wordmark + “P + chart” emblem (neon gradient)
- **Secondary**: Circular emblem only (app icon, favicon, social)
- **Monochrome**: Solid white/black for minimal contexts

Usage rules:
- Clear space = height of emblem circle around the logo
- Minimum sizes: emblem 16 px, lockup 120 px width
- Do not rotate, distort, or recolor outside brand palette

## Color Palette
- **Primary**
  - Midnight Blue `#0A0F2C`
  - Neon Cyan `#00F0FF`
- **Secondary**
  - Neon Magenta `#FF00A8`
  - Electric Green `#39FF14`
- **Neutrals**
  - Off-White `#F2F2F2`
  - Graph Gray `#1A1A1A`

Notes:
- Dark UI by default; neon as accents for CTAs, data highlights
- Gradients: blue → cyan → purple

## Typography
- **Logo/Headlines**: Bold geometric sans (Inter, Satoshi, Neue Haas Grotesk)
- **Body**: Inter / IBM Plex Sans
- **Numbers/Code**: Space Mono / JetBrains Mono (charts, tickers)

## CSS Tokens (in `resources/css/app.css`)
- Palette variables: `--brand-midnight`, `--brand-cyan`, `--brand-magenta`, `--brand-electric-green`, `--brand-off-white`, `--brand-graph-gray`
- Gradients: `--gradient-primary`, `--gradient-primary-dark`, `--gradient-cyber`
- Utilities:
  - `.brand-gradient` – background gradient
  - `.brand-cyber` – conic cyber gradient
  - `.text-brand-gradient` – gradient-filled text
  - `.neon-glow` – cyan/magenta glow shadow
  - `.btn-brand` – branded CTA button
  - `.badge-cyan` – subtle cyan badge

Example usage:
```html
<button class="btn-brand neon-glow">Predict Now</button>
<h1 class="text-4xl font-extrabold text-brand-gradient">parashiko.com</h1>
<div class="brand-gradient rounded-xl p-6"></div>
```

## Graphic Language
- Icons: nodes, circuit lines, connected dots
- Charts: glowing line graphs with data points
- Shapes: circles, arcs, futuristic grids
- Effects: subtle neon glow; avoid heavy blur that harms readability

## Applications
1. Website
   - Dark by default; neon cyan buttons with hover glow
   - Hero: animated chart or glowing P emblem
2. Social
   - Emblem-only avatar
   - Motion posts with glowing charts
3. Marketing
   - Cyberpunk posters, tees; data-heavy visuals
4. App Icon / Favicon
   - Circular “P + chart” with neon gradient

## Brand Voice
- **Tone**: Confident, direct, forward-looking
- **Keywords**: Predict. Anticipate. Win.
- **Copy**: Short, sharp, futuristic

Tagline examples:
- “Predict the future.”
- “Markets in motion.”
- “What’s next?”
