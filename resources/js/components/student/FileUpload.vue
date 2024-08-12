<template>
    <Navigation/>
    <div class="container mt-5">

        <h3>
            File Upload
        </h3>
        <form @submit.prevent="uploadFile">
            <div class="form-group row justify-content-start border-0">
                <label for="student" class="col-sm-2"> Student</label>
                <select v-model="studentId" class="form-control col-sm-6 w-50">
                    <option disabled value="">Select Student</option>
                    <option v-for="student in students" :key="student.id" :value="student.id">
                        {{ student.first_name }} {{student.last_name}}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <input type="file" @change="onFileChange" accept=".docx" class="form-control" />
            </div>

            <button type="submit" class="w-25">Upload</button>

        </form>

        <div v-if="successMessage" class="alert alert-success mt-3">
            {{ successMessage }}
        </div>
    </div>
</template>

<script>
import Navigation from "../Navigation";
export default {
    components: {Navigation},
    data() {
        return {
            file: null,
            studentId: "",
            students: [],
            successMessage: ""
        };
    },
    methods: {
        onFileChange(e) {
            this.file = e.target.files[0];
        },
        async uploadFile() {
            let formData = new FormData();
            formData.append("file", this.file);
            formData.append("student_id", this.studentId);

            await axios.post("/api/upload-docx", formData, {
                headers: {
                    "Content-Type": "multipart/form-data"
                }
            }).then(response => {
                if (response.status === 200) {
                    this.successMessage = "File uploaded successfully!";
                    this.file = null; // Optionally clear the file input
                    setTimeout(() => {
                        this.$router.push('/student');
                    }, 2000);
                }

            });
        },
        async fetchStudents() {
            const response = await axios.get("/api/students");
            this.students = response.data;
        }
    },
    created() {
        this.fetchStudents();
    }
};
</script>
