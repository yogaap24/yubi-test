import { createRouter, createWebHistory } from 'vue-router';
import SalesOrderForm from '../views/SalesOrderForm.vue';

const routes = [
  {
    path: '/',
    name: 'CreateSalesOrder',
    component: SalesOrderForm,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;