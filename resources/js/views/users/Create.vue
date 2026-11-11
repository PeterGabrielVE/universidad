<script setup>
import { ref } from "vue";
import api from "../../plugins/axios";

// Las opciones vienen de UserController::create (Blade), no de llamadas extra.
const props = defineProps({
  roles: { type: Array, default: () => [] },
  programs: { type: Object, default: () => ({}) },
  statuses: { type: Array, default: () => ["activo"] },
});

const emptyForm = () => ({
  name: "",
  last_name: "",
  identification_card: "",
  email: "",
  phone: "",
  password: "",
  role: "",
  doctorado_id: "",
  status: "activo",
});

const form = ref(emptyForm());
const errors = ref({});
const loading = ref(false);
const saved = ref("");

const submitForm = async () => {
  loading.value = true;
  errors.value = {};
  saved.value = "";
  try {
    const payload = { ...form.value, doctorado_id: form.value.doctorado_id || null };
    const { data } = await api.post("/api/v1/users", payload);
    saved.value = `Usuario ${data.name} ${data.last_name} creado.`;
    form.value = emptyForm();
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors ?? {};
    } else {
      errors.value = { general: ["No se pudo crear el usuario."] };
    }
  } finally {
    loading.value = false;
  }
};

const fieldError = (field) => errors.value[field]?.[0];
</script>

<template>
  <form @submit.prevent="submitForm">
    <div v-if="saved" class="alert alert-success">{{ saved }} <a href="/user">Volver al listado</a></div>
    <div v-if="fieldError('general')" class="alert alert-danger">{{ fieldError('general') }}</div>

    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="name">Nombre</label>
        <input id="name" v-model="form.name" class="form-control" :class="{ 'is-invalid': fieldError('name') }" required />
        <div class="invalid-feedback">{{ fieldError('name') }}</div>
      </div>
      <div class="form-group col-md-6">
        <label for="last_name">Apellido</label>
        <input id="last_name" v-model="form.last_name" class="form-control" :class="{ 'is-invalid': fieldError('last_name') }" required />
        <div class="invalid-feedback">{{ fieldError('last_name') }}</div>
      </div>
      <div class="form-group col-md-6">
        <label for="identification_card">Cédula</label>
        <input id="identification_card" v-model="form.identification_card" type="number" min="1" class="form-control" :class="{ 'is-invalid': fieldError('identification_card') }" required />
        <div class="invalid-feedback">{{ fieldError('identification_card') }}</div>
      </div>
      <div class="form-group col-md-6">
        <label for="email">Correo electrónico</label>
        <input id="email" v-model="form.email" type="email" class="form-control" :class="{ 'is-invalid': fieldError('email') }" required />
        <div class="invalid-feedback">{{ fieldError('email') }}</div>
      </div>
      <div class="form-group col-md-6">
        <label for="phone">Teléfono</label>
        <input id="phone" v-model="form.phone" class="form-control" :class="{ 'is-invalid': fieldError('phone') }" />
        <div class="invalid-feedback">{{ fieldError('phone') }}</div>
      </div>
      <div class="form-group col-md-6">
        <label for="password">Contraseña</label>
        <input id="password" v-model="form.password" type="password" minlength="8" autocomplete="new-password" class="form-control" :class="{ 'is-invalid': fieldError('password') }" required />
        <div class="invalid-feedback">{{ fieldError('password') }}</div>
      </div>
      <div class="form-group col-md-4">
        <label for="role">Rol</label>
        <select id="role" v-model="form.role" class="form-control" :class="{ 'is-invalid': fieldError('role') }" required>
          <option disabled value="">Seleccione un rol</option>
          <option v-for="role in props.roles" :key="role" :value="role">{{ role }}</option>
        </select>
        <div class="invalid-feedback">{{ fieldError('role') }}</div>
      </div>
      <div class="form-group col-md-4">
        <label for="doctorado_id">Programa</label>
        <select id="doctorado_id" v-model="form.doctorado_id" class="form-control" :class="{ 'is-invalid': fieldError('doctorado_id') }">
          <option value="">Sin programa</option>
          <option v-for="(name, id) in props.programs" :key="id" :value="id">{{ name }}</option>
        </select>
        <div class="invalid-feedback">{{ fieldError('doctorado_id') }}</div>
      </div>
      <div class="form-group col-md-4">
        <label for="status">Estado</label>
        <select id="status" v-model="form.status" class="form-control">
          <option v-for="status in props.statuses" :key="status" :value="status">{{ status }}</option>
        </select>
      </div>
    </div>

    <div class="d-flex justify-content-end">
      <a href="/user" class="btn btn-secondary mr-2">Cancelar</a>
      <button type="submit" :disabled="loading" class="btn btn-uft">
        {{ loading ? "Guardando…" : "Crear usuario" }}
      </button>
    </div>
  </form>
</template>
