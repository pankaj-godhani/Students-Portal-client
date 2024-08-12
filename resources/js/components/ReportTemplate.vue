<template>
    <Navigation/>
    <div class="report-template container mt-5">
        <h3>Template</h3>
        <div v-if="successMessage" class="alert alert-success mt-3">
            {{ successMessage }}
        </div>
        <div ref="editor"></div>
        <button @click="saveTemplate" class="btn btn-primary mt-3">Save report template</button>
    </div>
</template>

<script>
import axios from 'axios';
import Navigation from "./Navigation";
import Quill from 'quill';
import 'quill/dist/quill.snow.css';



export default {
    components: {Navigation},
    data() {
        return {
            templateContent: `
        <p>Student improvement report card</p>
        <p>Student name: @student_full_name</p>
        <p>Session date: @session_date</p>
        <p>Report card for period: @target_start_date to @target_end_date</p>
        <p>Target: @target</p>
        <p>Achieved: @session_rating</p>
        <p>Lorem Ipsum is simply @student_full_name text of the printing and typesetting industry.</p>
      `,
            successMessage:''
        };
    },
    mounted() {
        this.editor = new Quill(this.$refs.editor, {
            theme: 'snow'
        });
        this.editor.clipboard.dangerouslyPasteHTML(this.templateContent);
    },
    methods: {
        saveTemplate() {
            const templateContent = this.editor.root.innerHTML;
            axios.post('/api/report-template', {
                template_content: templateContent
            })
                .then(response => {
                    this.successMessage = 'Template saved successfully!';
                })
                .catch(error => {
                    console.error('There was an error saving the template:', error);
                });
        }
    }
};
</script>

<style scoped>
.report-template {
    border: 1px solid #ccc;
    padding: 20px;
    width: 100%;
}
textarea {
    width: 100%;
    margin-bottom: 10px;
}
button {
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    border: none;
    cursor: pointer;
}
button:hover {
    background-color: #0056b3;
}
</style>
