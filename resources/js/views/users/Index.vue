<template>
  <div class="bg-white rounded shadow-md p-6 w-full">
    <h1 class="text-3xl font-semibold mb-6 text-gray-800">Usuarios</h1>

    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
      <input
        v-model="search"
        @input="fetchUsers"
        type="text"
        placeholder="Buscar por nombre o email..."
        class="border border-gray-300 rounded-md px-4 py-2 w-full md:w-1/3 focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
      <button
        @click="goToCreate"
        class="bg-blue-600 hover:bg-blue-700 transition text-white font-medium px-5 py-2 rounded-md shadow"
      >
        Crear Usuario
      </button>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-left border border-gray-300 rounded">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 border">Nombre</th>
            <th class="px-4 py-2 border">Email</th>
            <th class="px-4 py-2 border">Rol</th>
            <th class="px-4 py-2 border">Estado</th>
            <th class="px-4 py-2 border text-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="user in users"
            :key="user.id"
            class="hover:bg-gray-50 transition"
          >
            <td class="px-4 py-2 border">{{ user.name }}</td>
            <td class="px-4 py-2 border">{{ user.email }}</td>
            <td class="px-4 py-2 border">{{ user.role }}</td>
            <td class="px-4 py-2 border">
              <span
                :class="{
                  'text-green-600 font-semibold': user.status === 'activo',
                  'text-red-600 font-semibold': user.status !== 'activo',
                }"
              >
                {{ user.status }}
              </span>
            </td>
            <td class="px-4 py-2 border text-center">
              <button
                @click="goToEdit(user.id)"
                class="text-blue-600 hover:underline mr-2"
              >
                Editar
              </button>
              <button
                @click="deleteUser(user.id)"
                class="text-red-600 hover:underline"
              >
                Eliminar
              </button>
            </td>
          </tr>

          <tr v-if="users.length === 0">
            <td colspan="5" class="text-center py-4 text-gray-500">
              No se encontraron usuarios.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import api from "../../plugins/axios";

const users = ref([]);
const search = ref("");
const router = useRouter();

const fetchUsers = async () => {
    const token = localStorage.getItem('auth_token');
  try {
    const res = await api.get("/api/v1/users", {
      params: { search: search.value },
    });
    users.value = res.data;
  } catch (error) {
    console.error("Error fetching users:", error);
    // Redirigir a login si no está autenticado
    if (error.response?.status === 401) {
      //router.push('/login');
      console.log("Redirigiendo a login...");
    }
  }
};

const deleteUser = async (id) => {
  if (confirm("¿Estás seguro de eliminar este usuario?")) {
    await api.delete(`/api/v1/users/${id}`);
    fetchUsers();
  }
};

const goToCreate = () => router.push({ name: "users.create" });
const goToEdit = (id) => router.push({ name: "users.edit", params: { id } });

onMounted(fetchUsers);
</script>
