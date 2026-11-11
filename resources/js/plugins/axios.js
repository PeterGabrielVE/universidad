import axios from 'axios';

/**
 * Cliente para la API interna. Las pantallas Vue viven dentro de páginas Blade
 * con sesión iniciada, así que Sanctum autentica con la cookie de sesión
 * (modo SPA) y axios envía el token CSRF desde la cookie XSRF-TOKEN.
 * Las URLs son relativas: funciona en cualquier host y puerto.
 */
const api = axios.create({
  withCredentials: true,
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
    Accept: 'application/json',
  },
});

export default api;
