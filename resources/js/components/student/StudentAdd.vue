<template>
    <Navigation/>
    <div class="container mt-5" >
        <h3>Add Student</h3>
        <form @submit.prevent="addStudent">
            <div class="row border-0">
                <div class="form-group col-sm-6">
                    <label for="first_name">First Name</label>
                    <input type="text" v-model="student.first_name" class="form-control" placeholder="Enter First Name" required>
                </div>
                <div class="form-group col-sm-6">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" v-model="student.middle_name" class="form-control" placeholder="Enter Middle Name">
                </div>
            </div>
            <div class="row border-0">
                <div class="form-group col-sm-6">
                    <label for="last_name">Last Name</label>
                    <input type="text" v-model="student.last_name" class="form-control" placeholder="Enter Last Name" required>
                </div>
                <div class="form-group col-sm-6" >
                    <label for="gender">Age</label>
                    <input type="number" v-model="student.age" class="form-control " placeholder="Enter Age" required>
                </div>
            </div>
            <div class="row border-0">
                <div class="form-group col-sm-6">
                    <label for="gender">Gender</label> <br/>
                    <input type="radio" id="one" value="Female" class="me-2" v-model="student.gender" />
                    <label for="one" class="me-2">  Female</label>
                    <input type="radio"  id="two" value="Male" class="me-2" v-model="student.gender"/>
                    <label for="two" class="me-2">  Male</label>
                </div>
                <div class="form-group col-sm-6 ">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" v-model="student.date_of_birth" required class="form-control" :min="minDate" :max="maxDate">
                </div>
            </div>
            <div class="row row-header">
                <div class="set-width">Days</div>
                <div  class="col-sm-6">Availability</div>
            </div>
            <div
                v-for="(day, index) in days"
                :key="index"
                class="row"
            >
                <div class="set-width">{{ day.name }}</div>
                <div  class="col-sm-6">
                    <input
                        type="checkbox"

                        v-model="student.availability[day.name.toLowerCase()]"
                    />
                </div>
            </div>

            <div class="legend">
                <div class="legend-item">
                    <input type="checkbox" checked disabled>
                    <span>This indicates available</span>
                </div>
                <div class="legend-item">
                    <input type="checkbox" disabled>
                    <span>This indicates not-available</span>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button class="btn btn-primary w-25" type="submit">Add Student</button>

            </div>
        </form>
    </div>
</template>

<script>
import axios from 'axios';
import Navigation from "../Navigation";

export default {
    components: {Navigation},
    data() {
        const today = new Date().toISOString().split('T')[0];
        return {
            student: {
                first_name: '',
                middle_name: '',
                last_name: '',
                date_of_birth: '',
                gender:'',
                age:'',
                availability: {
                    monday: false,
                    tuesday: false,
                    wednesday: false,
                    thursday: false,
                    friday: false,
                    saturday: false,
                    sunday: false
                }
            },
            days: [
                { name: "Monday", available: true },
                { name: "Tuesday", available: true },
                { name: "Wednesday", available: true },
                { name: "Thursday", available: true },
                { name: "Friday", available: true },
                { name: "Saturday", available: true },
                { name: "Sunday", available: false },
            ],
          selectedDate: null,
          maxDate: today
        };
    },
    methods: {
        addStudent() {
            axios.post('/api/students', this.student)
                .then(response => {

                     this.$emit('student-added', response.data);

                    this.student = {
                        first_name: '',
                        middle_name: '',
                        last_name: '',
                        date_of_birth: '',
                        gender: '',
                        age: '',
                        availability: {
                            monday: false,
                            tuesday: false,
                            wednesday: false,
                            thursday: false,
                            friday: false,
                            saturday: false,
                            sunday: false
                        }
                    };
                    this.$router.push('/student');
                });
        }
    }
}
</script>
<style>
.row {
    display: flex;
    justify-content: space-between;
    padding: 8px;
    border-bottom: 1px solid #ddd;
}

.row:last-child {
    border-bottom: none;
}

.row-header {
    font-weight: bold;
    background-color: #f9f9f9;
    border-bottom: 2px solid #ccc;
}

input[type="checkbox"] {
    width: 20px;
    height: 20px;
}

.legend {
    margin-top: 20px;
}

.legend span {
    margin-right: 10px;
}

.legend-item {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
}

.legend-item span {
    margin-left: 5px;
}
.set-width{
    width: 50%!important;
}
.row{
    flex-wrap: unset!important;
}

</style>
