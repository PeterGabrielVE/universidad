<template>
    <div class="modal fade show" tabindex="-1" role="dialog" style="display: block;" v-if="show">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Nuevo Rol</h5>
                    <button type="button" class="close" @click="$emit('close')">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Formulario para crear rol -->
                    <form @submit.prevent="submitForm">
                        <div class="form-group">
                            <label>Nombre del Rol</label>
                            <input type="text" class="form-control" v-model="roleName" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        show: Boolean
    },
    data() {
        return {
            roleName: ''
        };
    },
    methods: {
        submitForm() {
            // Lógica para guardar el rol
            this.$emit('close');
        }
    }
};
</script>

<style scoped>
.modal {
    background-color: rgba(0,0,0,0.5);
}
</style>