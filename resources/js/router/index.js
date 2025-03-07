import { createRouter, createWebHistory } from 'vue-router';
import WeeklyScores from '@/pages/WeeklyScores.vue';
import AllScores from '@/pages/AllScores.vue';
import Teams from '@/pages/Teams.vue';
import NotFound from '@/pages/NotFound.vue';
import Fixtures from "@/pages/Fixtures.vue";

const routes = [
    { path: '/', component: Teams },
    { path: '/fixtures', component: Fixtures },
    { path: '/weekly-fixture', component: WeeklyScores },
    { path: '/all-fixtures', component: AllScores },
    { path: '/:pathMatch(.*)*', component: NotFound },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
