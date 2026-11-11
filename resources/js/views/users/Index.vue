<template>
  <div class="container-fluid px-4 py-4 w-full">
    <h1 class="h3 mb-4 text-gray-800">{{ title }}</h1>

    <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
      <h6 class="m-0 font-weight-bold text-uft">Listado de usuarios</h6>
      <div class="d-flex align-items-center">
        <input
          v-model="search"
          type="search"
          class="form-control form-control-sm mr-2"
          placeholder="Buscar por nombre o correo"
          aria-label="Buscar usuarios"
          @input="onSearch"
        />
        <a v-if="canManage" href="/users/create" class="btn btn-uft text-nowrap">
          <i class="fas fa-plus pr-2"></i>Crear usuario
        </a>
      </div>
    </div>

    <div class="card-body">
      <div v-if="loading" class="text-center py-4">
        <div class="spinner-border text-uft" role="status"></div>
      </div>
      <div v-else-if="error" class="alert alert-danger">{{ error }}</div>
      <div v-else class="table-responsive">
        <table class="table table-bordered w-100">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Correo</th>
              <th>Rol</th>
              <th>Estado</th>
              <th v-if="canManage" class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id">
              <td>{{ user.name }} {{ user.last_name }}</td>
              <td>{{ user.email }}</td>
              <td>{{ user.role || '—' }}</td>
              <td>
                <span :class="user.status === 'activo' ? 'text-success font-weight-bold' : 'text-danger font-weight-bold'">
                  {{ user.status }}
                </span>
              </td>
              <td v-if="canManage" class="text-center">
                <a :href="`/user/${user.id}/edit`" class="btn btn-sm btn-link text-primary">Editar</a>
                <button class="btn btn-sm btn-link text-danger" @click="deleteUser(user)">Eliminar</button>
              </td>
            </tr>
            <tr v-if="users.length === 0">
              <td :colspan="canManage ? 5 : 4" class="text-center py-4 text-muted">No se encontraron usuarios.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "../../plugins/axios";

const props = defineProps({
  title: { type: String, default: "Usuarios" },
  // Nombre del rol para las vistas filtradas (p. ej. /usuario/directivo → "Coordinador").
  roleFilter: { type: String, default: "" },
  canManage: { type: Boolean, default: false },
});

const users = ref([]);
const search = ref("");
const loading = ref(true);
const error = ref("");
let searchTimer = null;

const fetchUsers = async () => {
  loading.value = true;
  error.value = "";
  try {
    const { data } = await api.get("/api/v1/users", {
      params: { role: props.roleFilter || undefined, search: search.value || undefined },
    });
    users.value = data;
  } catch (e) {
    if (e.response?.status === 401) {
      window.location.href = "/login";
      return;
    }
    error.value = "No se pudo cargar el listado de usuarios.";
  } finally {
    loading.value = false;
  }
};

const onSearch = () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(fetchUsers, 300);
};

const deleteUser = async (user) => {
  if (!confirm(`¿Eliminar a ${user.name} ${user.last_name ?? ""}?`)) {
    return;
  }
  try {
    await api.delete(`/api/v1/users/${user.id}`);
    await fetchUsers();
  } catch (e) {
    error.value = e.response?.data?.message ?? "No se pudo eliminar el usuario.";
  }
};

onMounted(fetchUsers);
</script>
