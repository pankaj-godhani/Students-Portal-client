<template>
    <Navigation/>
    <div class="container border-0">
        <h1>Students</h1>
        <div class="d-flex justify-content-end mb-2">
            <router-link to="/student/new" class="btn btn-primary mt-3">Add Student</router-link>
        </div>

        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Date of Birth</th>
                <th>Actions</th>

            </tr>
            </thead>
            <tbody>
                <tr v-for="student in students" :key="student.id">
                    <td></td>
                    <td>{{ student.full_name }}</td>
                    <td>{{ student.age }}</td>
                    <td>{{ student.gender }}</td>
                    <td>{{ student.date_of_birth }}</td>
                    <td>
                        <button @click="openSessionsInNewTab(student.id)" class="btn btn-primary">
                            Sessions
                        </button>
                        <button @click="generateReport(student.id)" class="btn btn-success ms-2">
                            Generate Report
                        </button>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
</template>

<script>
import axios from 'axios';
import Navigation from "../Navigation";

export default {
    components: {Navigation},
    data() {
        return {
            students: []
        };
    },
    created() {
        axios.get('/api/students')
            .then(response => {
                this.students = response.data;
            });
    },
    methods: {
        openSessionsInNewTab(studentId) {
            const route = this.$router.resolve({ name: 'StudentSessions', params: { id: studentId } });
            window.open(route.href, '_blank');
        },
        generateReport(studentId) {
            const route = this.$router.resolve({
                name: 'GenerateReport',
                params: { studentId }
            });
            window.open(route.href, '_blank');
        }
    }
}
</script>
<style>
/*.container{*/
/*    border: none;*/
/*}*/
</style>
