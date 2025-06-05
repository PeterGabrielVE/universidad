import './bootstrap';
import { createApp } from 'vue';
import axios from 'axios';
import toastr from 'toastr';
import Swal from 'sweetalert2';
import CreateRoleModal from './components/CreateRoleModal.vue';

// Configura Toastr
window.toastr = toastr;
toastr.options = {
  "closeButton": true,
  "progressBar": true,
  "positionClass": "toast-top-right"
};

// Configura Axios
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Crea la aplicación Vue
const app = createApp({
  data() {
    return {
      showModal: false,
      currentRole: null,
      roles: window.initialRoles || [],
      loading: false
    };
  },
  methods: {
    openModal(role = null) {
      this.currentRole = role ? {...role} : null;
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
    },
    editRole(role) {
      this.openModal(role);
    },
    async loadRoles() {
      this.loading = true;
      try {
        const response = await axios.get('/api/roles');
        this.roles = response.data;
      } catch (error) {
        toastr.error('Error al cargar roles');
        console.error(error);
      } finally {
        this.loading = false;
      }
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
        await axios.delete(`/api/roles/${id}`);
        toastr.success('Rol eliminado');
        this.loadRoles();
      } catch (error) {
        toastr.error('Error al eliminar rol');
        console.error(error);
      }
    },
    handleRoleSaved() {
      this.loadRoles();
      this.closeModal();
      toastr.success('Rol guardado correctamente');
    }
  },
  mounted() {
    console.log('Aplicación montada', this.roles);
    if (this.roles.length === 0) {
      this.loadRoles();
    }
  }
});

// Registra el componente
app.component('create-role-modal', CreateRoleModal);

// Monta la aplicación
app.mount('#app');
