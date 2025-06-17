<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Usuarios</h1>
    <div class="flex justify-between items-center mb-4">
      <input v-model="search" @input="fetchUsers" type="text" placeholder="Buscar..." class="border p-2 rounded" />
      <button @click="goToCreate" class="bg-blue-600 text-white px-4 py-2 rounded">Crear Usuario</button>
    </div>

    <table class="w-full table-auto border">
      <thead>
        <tr>
          <th class="border px-4 py-2">Nombre</th>
          <th class="border px-4 py-2">Email</th>
          <th class="border px-4 py-2">Rol</th>
          <th class="border px-4 py-2">Estado</th>
          <th class="border px-4 py-2">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td class="border px-4 py-2">{{ user.name }}</td>
          <td class="border px-4 py-2">{{ user.email }}</td>
          <td class="border px-4 py-2">{{ user.role }}</td>
          <td class="border px-4 py-2">{{ user.status }}</td>
          <td class="border px-4 py-2">
            <button @click="goToEdit(user.id)" class="text-blue-600">Editar</button>
            <button @click="deleteUser(user.id)" class="text-red-600 ml-2">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from "../../plugins/axios";


const users = ref([]);
const search = ref('');
const router = useRouter();

const fetchUsers = async () => {
  const res = await axios.get('/api/v1/users', { params: { search: search.value } });
  users.value = res.data;
};

const deleteUser = async (id) => {
  if (confirm('¿Estás seguro de eliminar este usuario?')) {
    await axios.delete(`/api/v1/users/${id}`);
    fetchUsers();
  }
};

const goToCreate = () => router.push({ name: 'users.create' });
const goToEdit = (id) => router.push({ name: 'users.edit', params: { id } });

onMounted(fetchUsers);
</script>
