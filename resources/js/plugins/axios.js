import axios from 'axios';

// Configuración base de axios
axios.defaults.withCredentials = true; // Para enviar cookies
axios.defaults.baseURL = 'http://localhost:8000';

axios.get('/generate-token')
    .then(response => {
        localStorage.setItem('auth_token', response.data.token);
    })
    .catch(error => {
        console.error('Error al obtener el token:', error);
    });
const api = axios.create({
  baseURL:'http://localhost:8000',
  withCredentials: true,
});
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default api;
