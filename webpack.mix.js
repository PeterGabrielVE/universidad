const mix = require('laravel-mix');

// Configuración principal
mix.js('resources/js/app.js', 'public/js')
   .vue()
   .sass('resources/sass/app.scss', 'public/css') // Cambiado a Sass
