<template>
    <Navigation/>
    <div class="container mt-5">
        <h3>Rate Session</h3>
        <form @submit.prevent="rateSession" class="form">

            <div class="form-group row justify-content-center border-0">
                <label class="col-sm-2" for="rating">Rating</label>
                <input type="number" v-model="form.rating" min="1" max="10" class="form-control col-sm-4 w-50">
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="w-25">Rate</button>
            </div>
        </form>
    </div>
</template>

<script>
import Navigation from "../Navigation";
import axios from "axios";
export default {
    components: {Navigation},
    data() {
        return {
            form: {
                rating: '',
            },
        };
    },
    methods: {
        rateSession() {
            try {
                const sessionId = this.$route.params.id;
                axios.post(`/api/sessions/${sessionId}/rate`, this.form)
                    .then(response => {
                        this.openSessionsInNewTab(response.data.student_id)
                    });
            } catch (error) {
                console.error('Error rating session:', error);
            }
        },
        openSessionsInNewTab(studentId) {
            const route = this.$router.resolve({ name: 'StudentSessions', params: { id: studentId } });
            window.open(route.href);
        },
    },
};
</script>
