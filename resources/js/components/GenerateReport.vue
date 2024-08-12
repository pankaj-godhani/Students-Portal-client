<template>
    <Navigation/>
    <div class="generate-report container mt-5">
        <h3>Generate Report for Student ID: {{ studentId }}</h3>

        <div v-if="successMessage" class="alert alert-success mt-3">
            {{ successMessage }}
        </div>
        <div v-if="alertMessage" class="alert alert-danger mt-3">
            {{ alertMessage }}
        </div>
        <div class="form-group ">
            <label>Template:</label>
            <select v-model="selectedTemplateId" class="form-control w-50">
                <option disabled value="">Select Template</option>
                <option v-for="template in templates" :key="template.id" :value="template.id">
                    {{ template.name || 'Template ' + template.id }}
                </option>
            </select>
        </div>
        <div class="row border-0">
            <div class="form-group col-sm-6">
                <label>From Date:</label>
                    <input type="date" class="form-control" v-model="startDate">

            </div>
            <div class="form-group  col-sm-6">
                <label>To Date:</label>
                    <input type="date" class="form-control" v-model="endDate">

            </div>
        </div>

        <div>
            <label for="splitSession">Split Session in Minutes</label>
            <select v-model="splitMinutes" id="splitSession" class="form-control w-50">
                <option value="15">15 min</option>
                <option value="10">10 min</option>
                <option value="5">5 min</option>
                <option value="2">2 min</option>
            </select>
        </div>
        <button class="btn btn-primary" @click="generateReport">Generate Report</button>
    </div>
</template>

<script>
import axios from 'axios';
import Navigation from "./Navigation";

export default {
    components: {Navigation},
    data() {
        return {
            templates: [],
            selectedTemplateId: "",
            startDate: '',
            endDate: '',
            splitMinutes: 15,
            studentId: null,
            alertMessage: '',
            successMessage: '',
        };
    },
    methods: {
        fetchTemplates() {
            axios.get('/api/report-templates')
                .then(response => {
                    this.templates = response.data;
                })
                .catch(error => {
                    console.error('Error fetching templates:', error);
                });
        },
        generateReport() {
            axios.post('/api/generate-report', {
                student_id: this.studentId,
                template_id: this.selectedTemplateId,
                start_date: this.startDate,
                end_date: this.endDate,
                splitMinutes: this.splitMinutes,
                // responseType: "blob", // important
            }, { responseType: 'blob' })
                .then(response => {
                    console.log(response.status);

                    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/zip' }));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', `student_reports_${this.studentId}.zip`);
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                    this.successMessage = "Report generate successfully!";
                    this.file = null; // Optionally clear the file input
                    setTimeout(() => {
                    }, 2000);
                })
                .catch(error => {

                    this.alertMessage ='Something went wrong';
                    console.error('Error generating report:', error);
                });
        }
    },
    mounted() {
        this.fetchTemplates();
        this.studentId = this.$route.params.studentId;
    }
};
</script>

<style scoped>
.generate-report {
    border: 1px solid #ccc;
    padding: 20px;
    width: 100%;
}
button {
    padding: 10px 20px;
    margin-top: 10px;
    background-color: #007bff;
    color: #fff;
    border: none;
    cursor: pointer;
}
button:hover {
    background-color: #0056b3;
}
</style>
