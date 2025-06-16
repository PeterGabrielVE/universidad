import UserIndex from '@/views/users/Index.vue';
import UserForm from '@/views/users/Form.vue';

const routes = [
  { path: '/usuarios', name: 'users.index', component: UserIndex },
  { path: '/usuarios/crear', name: 'users.create', component: UserForm },
  { path: '/usuarios/:id/editar', name: 'users.edit', component: UserForm, props: true },
];
