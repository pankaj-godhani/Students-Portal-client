<template>
    <Navigation/>
    <div class="container mt-5">
        <h3>Schedule a Session</h3>

        <div v-if="successMessage" class="alert alert-success mt-3">
            {{ successMessage }}
        </div>
        <div v-if="alertMessage" class="alert alert-danger mt-3">
            {{ alertMessage }}
        </div>

        <form @submit.prevent="scheduleSession">
            <div class="form-group col-sm-6">
                <label for="student">Student</label>
                <select v-model="form.student_id" class="form-control">
                    <option v-for="student in students" :key="student.id" :value="student.id" >
                        {{ student.full_name }}
                    </option>
                </select>
            </div>

            <div class="row border-0">
                <div class="form-group col-sm-6">
                    <label for="start_time">Start Time</label>
                    <input type="datetime-local" v-model="form.start_time" class="form-control">
                </div>

                <!--            <div class="form-group">-->
                <!--                <label for="end_time">End Time</label>-->
                <!--                <input type="datetime-local" v-model="form.end_time" class="form-control">-->
                <!--            </div>-->

                <div class="form-group col-sm-6">
                    <label for="target">Target</label>
                    <input type="number" v-model="form.target" class="form-control">

                </div>
            </div>
            <div class="form-group">
                <label for="is_repeated" class="me-3">Repeat Daily?</label>
                <input type="checkbox" v-model="form.is_repeated" class="mt-1">
            </div>
            <div class="d-flex justify-content-end">
                <button class="btn btn-primary w-25" type="submit">Schedule</button>

            </div>
        </form>
    </div>
</template>

<script>
import axios from "axios";
import Navigation from "../Navigation";

export default {
    components: {Navigation},
    data() {
        return {
            form: {
                student_id: '',
                start_time: '',
                // end_time: '',
                target: '',
                is_repeated: false,
                successMessage:'',
                alertMessage:'',
            },
            students: [],
        };
    },
    created() {
        axios.get('/api/students')
            .then(response => {
                this.students = response.data;
            });
    },
    methods: {

        scheduleSession() {
            axios.post('/api/sessions', this.form)
                .then(response => {
                    console.log(response);
                    if(response.status === 200){
                        this.successMessage = "Session Added successfully!";
                       const route= this.$router.resolve({ name: 'StudentSessions', params: { id: this.form.student_id } });
                        window.open(route,'_blank');
                    }
                    else{
                        this.alertMessage = "Something went wrong!";
                    }
                });
        }

        // async scheduleSession() {
        //     try {
        //         await fetch('/api/sessions', {
        //             method: 'POST',
        //             headers: {
        //                 'Content-Type': 'application/json',
        //                 'Authorization': `Bearer  ${localStorage.getItem('authToken')}`,
        //             },
        //             body: JSON.stringify(this.form),
        //         });
        //         alert('Session scheduled successfully!');
        //     } catch (error) {
        //         console.error('Error scheduling session:', error);
        //         alert('Error scheduling session.');
        //     }
        // },
    },
    // mounted() {
    //     this.fetchStudents();
    // },
};
</script>
