<template>
    <div v-if="isOpen" class="modal fade show d-block" tabindex="-1" style="background: rgba(0, 0, 0, 0.5);">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Crear Nuevo Rol</h5>
            <button type="button" class="btn-close" @click="$emit('close')"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="roleName" class="form-label">Nombre del Rol</label>
              <input
                v-model="roleName"
                type="text"
                id="roleName"
                class="form-control"
                placeholder="Ingrese el nombre del rol"
              />
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="$emit('close')">Cancelar</button>
            <button type="button" class="btn btn-primary" @click="createRole">Guardar</button>
          </div>
        </div>
      </div>
    </div>
  </template>

  <script>
  export default {
    props: {
      isOpen: {
        type: Boolean,
        required: true
      }
    },
    data() {
      return {
        roleName: ''
      };
    },
    methods: {
      createRole() {
        console.log('Rol a crear:', this.roleName);
        // Aquí puedes emitir el nombre del rol para guardarlo
        this.$emit('close');
      }
    }
  };
  </script>
