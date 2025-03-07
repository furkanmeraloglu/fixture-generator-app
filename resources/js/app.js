import './bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import {createApp} from 'vue';
import App from './App.vue';
import router from './router';
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import * as bootstrap from 'bootstrap'

window.bootstrap = bootstrap

const options = {
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33'
};

const app = createApp(App);
app.use(router);
app.use(VueSweetalert2, options);
app.mount('#app');
