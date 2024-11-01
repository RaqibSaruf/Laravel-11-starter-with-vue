import { createRouter, createWebHistory } from 'vue-router';

import home from '@/components/Home.vue';
import notFound from '@/components/NotFound.vue';

const routes = [
  { path: '/', component: home },
  { path: '/:pathMatch(.*)*', component: notFound },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
