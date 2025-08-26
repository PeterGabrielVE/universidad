<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

// Datos del formulario
const form = ref({
  name: "",
  email: "",
  password: "",
  role_id: "",
});

// Lista de roles
const roles = ref([]); 
const loading = ref(false);
const errors = ref({}); 

// Cargar roles
const fetchRoles = async () => {
  try {
    const { data } = await axios.get("/api/roles");
    roles.value = data;
  } catch (error) {
    console.error("Error cargando roles", error);
  }
};

// Enviar formulario
const submitForm = async () => {
  loading.value = true;
  errors.value = {};
  try {
    await axios.post("/api/users", form.value);
    alert("Usuario creado con éxito");
    form.value = { name: "", email: "", password: "", role_id: "" };
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
    } else {
      console.error("Error creando usuario", error);
    }
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchRoles();
});
</script>
<template>
  <div class="p-6 max-w-lg mx-auto bg-white rounded-xl shadow-md">
    <h2 class="text-xl font-bold mb-4">Registrar Usuario</h2>

    <form @submit.prevent="submitForm" class="space-y-4">
      <!-- Nombre -->
      <div>
        <label class="block font-medium">Nombre</label>
        <input
          v-model="form.name"
          type="text"
          class="w-full border p-2 rounded"
          placeholder="Nombre completo"
        />
        <span v-if="errors.name" class="text-red-500 text-sm">
          {{ errors.name[0] }}
        </span>
      </div>

      <!-- Email -->
      <div>
        <label class="block font-medium">Email</label>
        <input
          v-model="form.email"
          type="email"
          class="w-full border p-2 rounded"
          placeholder="ejemplo@correo.com"
        />
        <span v-if="errors.email" class="text-red-500 text-sm">
          {{ errors.email[0] }}
        </span>
      </div>

      <!-- Password -->
      <div>
        <label class="block font-medium">Contraseña</label>
        <input
          v-model="form.password"
          type="password"
          class="w-full border p-2 rounded"
          placeholder="********"
        />
        <span v-if="errors.password" class="text-red-500 text-sm">
          {{ errors.password[0] }}
        </span>
      </div>

      <!-- Rol -->
      <div>
        <label class="block font-medium">Rol</label>
        <select v-model="form.role_id" class="w-full border p-2 rounded">
          <option disabled value="">Seleccione un rol</option>
          <option v-for="role in roles" :key="role.id" :value="role.id">
            {{ role.name }}
          </option>
        </select>
        <span v-if="errors.role_id" class="text-red-500 text-sm">
          {{ errors.role_id[0] }}
        </span>
      </div>

      <!-- Botón -->
      <div class="flex justify-end">
        <button
          type="submit"
          :disabled="loading"
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          <span v-if="loading">Guardando...</span>
          <span v-else>Registrar</span>
        </button>
      </div>
    </form>
  </div>
</template>
