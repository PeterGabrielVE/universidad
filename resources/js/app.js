/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries.
 */

import 'toastr/toastr.js';
import 'toastr/toastr.scss';
import { createApp } from 'vue';
import CreateRoleModal from './components/CreateRoleModal.vue';
import Swal from 'sweetalert2';

require('./bootstrap');

// Configuración global de Toastr si es necesario
window.toastr = require('toastr');
toastr.options = {
  "closeButton": true,
  "progressBar": true,
  "positionClass": "toast-top-right"
};

// Creamos la aplicación Vue
const app = createApp({
  data() {
    return {
      showModal: false,
      currentRole: null,
      roles: window.initialRoles || [] // Datos iniciales pasados desde Blade
    };
  },
  methods: {
    openModal(role = null) {
      this.currentRole = role ? {...role} : null;
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
      this.currentRole = null;
    },
    editRole(role) {
        this.currentRole = {...role};
        this.showModal = true;
      },
    handleRoleSaved() {
      window.location.reload(); // O podrías actualizar la lista de roles via AJAX
    },
    confirmDelete(id) {
      Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esta acción!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          this.deleteRole(id);
        }
      });
    },
    async deleteRole(id) {
      try {
        const response = await axios.delete(`/roles/${id}`);
        Swal.fire('¡Eliminado!', 'El rol ha sido eliminado.', 'success');
        window.location.reload();
      } catch (error) {
        Swal.fire('Error', 'Ocurrió un error al eliminar el rol.', 'error');
      }
    }
  },
  mounted() {
    console.log('Aplicación Vue montada correctamente');
  }
});

// Registramos componentes globales
app.component('create-role-modal', CreateRoleModal);

// Montamos la aplicación
app.mount('#app');

// Opcional: Hacer disponible para accesos directos en consola
window.VueApp = app;