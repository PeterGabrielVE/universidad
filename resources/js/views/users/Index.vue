<template>
  <div class="container-fluid px-4 py-4 w-full">
    <!-- Título principal -->
    <h1 class="h3 mb-4 text-gray-800">Usuarios</h1>

    <!-- Encabezado con título y botón -->
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
      <h6 class="m-0 font-weight-bold text-uft">Listado de Usuarios</h6>
      <button @click="openModal()" class="btn btn-uft">
        <i class="fas fa-plus pr-2"></i>Crear Nuevo Usuarios
      </button>
    </div>

    <!-- Cuerpo con spinner y tabla -->
    <div class="card-body">
      <div v-if="loading" class="text-center py-4">
        <div class="spinner-border text-uft" role="status"></div>
      </div>

      <template v-else>
        <div class="table-responsive">
          <table class="table table-bordered w-100" cellspacing="0">
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
                      'text-success font-weight-bold': user.status === 'activo',
                      'text-danger font-weight-bold': user.status !== 'activo',
                    }"
                  >
                    {{ user.status }}
                  </span>
                </td>
                <td class="px-4 py-2 border text-center">
                  <button
                    @click="goToEdit(user.id)"
                    class="text-primary btn btn-sm btn-link"
                  >
                    Editar
                  </button>
                  <button
                    @click="deleteUser(user.id)"
                    class="text-danger btn btn-sm btn-link"
                  >
                    Eliminar
                  </button>
                </td>
              </tr>

              <tr v-if="users.length === 0">
                <td colspan="5" class="text-center py-4 text-muted">
                  No se encontraron usuarios.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </template>
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
