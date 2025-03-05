import { createRouter, createWebHistory } from 'vue-router';
import Home from '../components/Home.vue';
import Fixture from '../components/Fixture.vue';
import NotFound from '../components/NotFound.vue';

const routes = [
    { path: '/', component: Home },
    { path: '/fixture', component: Fixture },
    { path: '/:pathMatch(.*)*', component: NotFound },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
