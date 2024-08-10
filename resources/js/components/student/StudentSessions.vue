<!-- src/components/StudentSessions.vue -->
<template>
    <Navigation/>
    <div class="container mt-5">
        <h3>Sessions for {{ studentName }}</h3>
        <div class="d-flex justify-content-end mb-2">
            <router-link to="/student" class="btn btn-secondary mt-3">Back to Students</router-link>
        </div>

        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Target</th>
                <th>Is Repeated</th>
                <th>Rating</th>
                <th>Actions</th>

            </tr>
            </thead>
            <tbody>
            <tr v-for="session in sessions" :key="session.id">
                <td></td>
                <td>{{ session.session.start_time }}</td>
                <td>{{ session.session.end_time }}</td>
                <td>{{ session.session.target }}</td>
                <td>{{ session.session.is_repeated }}</td>
                <td>{{ session.rating }}</td>
                <td>
                    <button @click="openSessionsInNewTab(session.id)" class="btn btn-primary">
                        Give Rating
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
    props: {
        id: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            sessions: [],
            studentName: ''
        };
    },
    created() {
        this.fetchSessions();
    },
    methods: {
        fetchSessions() {
            axios.get(`/api/students/${this.id}/sessions`)
                .then(response => {
                    this.sessions = response.data.session;
                    this.studentName = response.data.student.full_name;  // Assuming the backend also returns the student's name
                });
        },
        goBack() {
            this.$router.push({ name: 'Students' });
        },
        openSessionsInNewTab(sessionID) {
            const route = this.$router.resolve({ name: 'SessionsRating', params: { sessionId: sessionID } });
            window.open(route.href);
        }
    }
}
</script>

<style scoped>
/* Add any specific styles here */
</style>
