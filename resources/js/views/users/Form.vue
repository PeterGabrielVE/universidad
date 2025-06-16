<template>
  <div class="p-6">
    <h2 class="text-xl font-bold mb-4">{{ isEdit ? 'Editar Usuario' : 'Crear Usuario' }}</h2>
    <form @submit.prevent="submitForm" class="grid gap-4">
      <input v-model="form.name" type="text" placeholder="Nombre" class="border p-2 rounded" required />
      <input v-model="form.email" type="email" placeholder="Email" class="border p-2 rounded" required />
      <input v-if="!isEdit" v-model="form.password" type="password" placeholder="Contraseña" class="border p-2 rounded" required />
      <select v-model="form.role" class="border p-2 rounded" required>
        <option disabled value="">Seleccione Rol</option>
        <option value="admin">Administrador</option>
        <option value="docente">Docente</option>
        <option value="estudiante">Estudiante</option>
        <option value="representante">Representante</option>
      </select>
      <select v-model="form.status" class="border p-2 rounded" required>
        <option value="activo">Activo</option>
        <option value="inactivo">Inactivo</option>
      </select>
      <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Guardar</button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from '@/plugins/axios';

const route = useRoute();
const router = useRouter();
const isEdit = !!route.params.id;

const form = ref({
  name: '',
  email: '',
  password: '',
  role: '',
  status: 'activo'
});

const submitForm = async () => {
  if (isEdit) {
    await axios.put(`/api/v1/users/${route.params.id}`, form.value);
  } else {
    await axios.post('/api/v1/users', form.value);
  }
  router.push({ name: 'users.index' });
};

onMounted(async () => {
  if (isEdit) {
    const res = await axios.get(`/api/v1/users/${route.params.id}`);
    form.value = res.data;
  }
});
</script>
