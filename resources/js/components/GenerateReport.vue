<template>
    <Navigation/>
    <div class="generate-report container mt-5">
        <h3>Generate Report for Student ID: {{ studentId }}</h3>
        <div class="form-group d-flex col-sm-6">
            <label>Select Template:</label>
            <select v-model="selectedTemplateId" class="form-control ">
                <option v-for="template in templates" :key="template.id" :value="template.id">
                    {{ template.name || 'Template ' + template.id }}
                </option>
            </select>
        </div>
        <div class="form-group d-flex gap-4">
            <label>Select Date Range:</label>
            <input type="date" class="form-control" style="width: 30%" v-model="startDate">
            <input type="date" class="form-control" style="width: 30%" v-model="endDate">
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
            selectedTemplateId: null,
            startDate: '',
            endDate: '',
            studentId: null
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
                // responseType: "blob", // important
            }, { responseType: 'blob' })
                .then(response => {


                    const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', `student_report_${this.studentId}.pdf`);
                    document.body.appendChild(link);
                    link.click();
                    link.remove();



                    // const url = window.URL.createObjectURL(new Blob([response.data]));
                    // const link = document.createElement('a');
                    // link.href = url;
                    // link.setAttribute('download', `student_report_${this.studentId}.pdf`);
                    // document.body.appendChild(link);
                    // link.click();
                    // link.remove();

                    // const pdfUrl = response.data.pdf_url;
                    // console.log(pdfUrl);

                    // Create a link element and click it to download the PDF
                    // const link = document.createElement('a');
                    // link.href = pdfUrl;
                    // link.download = `student_report_${this.studentId}.pdf`;
                    // document.body.appendChild(link);
                    // link.click();

                    // const url = window.URL.createObjectURL(new Blob([response.data],{ type: 'application/pdf' }));
                    // console.log(url);
                    // const link = document.createElement("a");
                    // link.href = url;
                    // link.setAttribute("download", `student_report_${this.studentId}.pdf`);
                    // document.body.appendChild(link);
                    // link.click();
                    // link.remove();



                    // Remove the link element after clicking
                    // document.body.removeChild(link);

                })
                .catch(error => {
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
