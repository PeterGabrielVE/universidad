<template>
  <div
    v-if="show"
    class="modal fade show d-block"
    tabindex="-1"
    style="background: rgba(0, 0, 0, 0.5)"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ isEditing ? 'Editar Rol' : 'Crear Nuevo Rol' }}</h5>
          <button
            type="button"
            class="close-btn"
            @click="closeModal"
            aria-label="Cerrar modal"
          >
            &times;
          </button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="roleName" class="form-label">Nombre del Rol</label>
            <input
              v-model="form.name"
              type="text"
              id="roleName"
              class="form-control"
              placeholder="Ingrese el nombre del rol"
            />
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-secondary"
            @click="closeModal"
          >
            Cancelar
          </button>
          <button type="button" class="btn btn-uft" @click="saveRole">
            {{ isEditing ? 'Actualizar' : 'Guardar' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    role: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      form: {
        name: '',
        guard_name: 'web'
      }
    };
  },
  computed: {
    isEditing() {
      return this.role !== null;
    }
  },
  watch: {
    role: {
      immediate: true,
      handler(newVal) {
        if (newVal) {
          this.form = {...newVal};
        } else {
          this.form = {
            name: '',
            guard_name: 'web'
          };
        }
      }
    }
  },
  methods: {
    closeModal() {
      this.$emit('close');
    },
    async saveRole() {
      if (!this.form.name.trim()) {
        Swal.fire({
          icon: "warning",
          title: "Campo requerido",
          text: "El nombre del rol no puede estar vacío",
        });
        return;
      }

      try {
        Swal.fire({
          title: this.isEditing ? "Actualizando..." : "Guardando...",
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          },
        });

        const url = this.isEditing 
          ? `/roles/${this.role.id}`
          : '/roles';
        
        const method = this.isEditing ? 'put' : 'post';
        
        await axios[method](url, this.form);

        Swal.close();
        
        Swal.fire({
          icon: "success",
          title: this.isEditing ? "Rol actualizado" : "Rol creado",
          text: this.isEditing 
            ? "El rol fue actualizado con éxito"
            : "El rol fue creado con éxito",
          timer: 2000,
          showConfirmButton: false,
        });
        
        this.$emit('saved');
        this.closeModal();
      } catch (error) {
        Swal.close();
        Swal.fire({
          icon: "error",
          title: "Error",
          text: error.response?.data?.message ||
            `Ocurrió un error al ${this.isEditing ? 'actualizar' : 'crear'} el rol`,
        });
      }
    }
  }
};
</script>

<style scoped>
.close-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  margin-left: auto;
  transition: all 0.3s ease;
  border-radius: 50%;
  width: 2.5rem;
  height: 2.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.8rem;
  line-height: 1;
  color: #6c757d;
}

.close-btn:hover {
  background-color: rgba(0, 0, 0, 0.05);
  color: #495057;
  transform: scale(1.1);
}

.close-btn:active {
  background-color: rgba(0, 0, 0, 0.1);
}
</style>