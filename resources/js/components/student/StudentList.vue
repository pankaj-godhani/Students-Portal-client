<template>
    <Navigation/>
    <div class="container border-0">
        <h1>Students</h1>
        <div class="d-flex justify-content-end mb-2">
            <router-link to="/student/new" class="btn btn-primary mt-3">+ Add Student</router-link>
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
            <tr v-for="student in paginatedStudents" :key="student.id">
                <td>{{student.id}}</td>
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

        <!-- Pagination Controls -->
        <div class="d-flex justify-content-end mt-4">
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li class="page-item" :class="{ disabled: currentPage === 1 }">
                        <button class="page-link" @click="prevPage">Previous</button>
                    </li>
                    <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: currentPage === page }">
                        <button class="page-link" @click="goToPage(page)">{{ page }}</button>
                    </li>
                    <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                        <button class="page-link" @click="nextPage">Next</button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Navigation from "../Navigation";

export default {
    components: { Navigation },
    data() {
        return {
            students: [],
            currentPage: 1,
            perPage: 10,
        };
    },
    computed: {
        paginatedStudents() {
            const start = (this.currentPage - 1) * this.perPage;
            const end = start + this.perPage;
            return this.students.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.students.length / this.perPage);
        }
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
        },
        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
            }
        },
        goToPage(page) {
            this.currentPage = page;
        }
    }
}
</script>

<style>
/* Add custom styles here */
</style>
