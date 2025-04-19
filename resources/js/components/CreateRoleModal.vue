<template>
    <div>
      <!-- Botón para abrir el modal -->
      <button class="btn btn-uft" @click="showModal = true">
        <i class="fas fa-plus pr-2"></i>Crear Nuevo Rol
      </button>

      <!-- Modal -->
      <div v-if="showModal" class="modal fade show" tabindex="-1" style="display:block;">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Crear Nuevo Rol</h5>
              <button type="button" class="close" @click="closeModal">&times;</button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="createRole">
                <div class="form-group">
                  <label for="name">Nombre del Rol:</label>
                  <input type="text" v-model="role.name" class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="guard_name">Guard Name:</label>
                  <input type="text" v-model="role.guard_name" class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="description">Descripción:</label>
                  <textarea v-model="role.description" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-success">Guardar Rol</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Fondo oscuro para modal -->
      <div v-if="showModal" class="modal-backdrop fade show" @click="closeModal"></div>
    </div>
  </template>

<script>
export default {
  data() {
    console.log('CreateRoleModal');
    return {
      showModal: false,
    };
  },
  methods: {
    openModal() {
        console.log('CreateRoleModal');
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
    }
  }
};
</script>


  <style scoped>
  .modal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1050;
    background: white;
    padding: 20px;
    border-radius: 8px;
    width: 400px;
  }
  .modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1040;
  }
  </style>
