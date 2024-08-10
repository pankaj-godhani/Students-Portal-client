import { createRouter, createWebHistory } from 'vue-router';

import Login from './components/Login.vue';
import Home from './components/Home.vue';
import StudentList from './components/student/StudentList.vue';
import axios from 'axios';
import StudentAdd from "./components/student/StudentAdd";
import StudentSessions from "./components/student/StudentSessions";
import SessionRating from "./components/session/SessionRating";
import FileUpload from "./components/student/FileUpload";
import ReportTemplate from "./components/ReportTemplate";
import GenerateReport from "./components/GenerateReport";
import SessionForm from "./components/session/SessionForm";
axios.defaults.headers.common['Authorization'] = `Bearer ${localStorage.getItem('authToken')}`;


const checkAuth = async (to, from, next) => {
    try {
        const response = await axios.get('/api/user');
        const isAuthenticated = !!response.data;
        if (to.path !== '/login' && !isAuthenticated) {
            next('/login');
        } else {
            next();
        }
    } catch (error) {
        // User is not authenticated
        if (to.path !== '/login') {
            next('/login');
        } else {
            next();
        }
    }
};

const routes = [
    { path: '/login', component: Login },
    { path: '/', component: Home ,beforeEnter: checkAuth },
    { path: '/student', component: StudentList ,beforeEnter: checkAuth },
    { path: '/student/new', component: StudentAdd ,beforeEnter: checkAuth },
    { path :'/session-form',component: SessionForm,beforeEnter: checkAuth},
    {
        path: '/students/:id/sessions',
        name: 'StudentSessions',
        component: StudentSessions,
        props: true
    },
    {
        path: '/sessions/:id/rating',
        name: 'SessionsRating',
        component: SessionRating,
        props: true
    },
    { path: '/file-upload', component: FileUpload ,beforeEnter: checkAuth },
    { path: '/report-template', component: ReportTemplate ,beforeEnter: checkAuth },
    {
        path: '/student/:studentId/report',
        name: 'GenerateReport',
        component: GenerateReport
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router;
