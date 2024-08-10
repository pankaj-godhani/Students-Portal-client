
require('./bootstrap');

import { createApp } from 'vue';
import App from './components/App.vue';
import router from './router';
import axios from 'axios';
import StudentList from './components/student/StudentList.vue';
import StudentAdd from './components/student/StudentAdd.vue';
import SessionForm from './components/session/SessionForm.vue';
import Navigation from './components/Navigation.vue';

axios.defaults.withCredentials = true;

createApp(App).use(router).mount('#app').component(Navigation);
