<template>
    <Navigation/>
    <div class="container">

        <div class="text-center">
            File Upload
        </div>
        <form @submit.prevent="uploadFile">
            <div class="form-group row justify-content-center border-0 col-sm-6">
                <label for="student" class="col-sm-2"> Student</label>
                <select v-model="studentId" class="form-control">
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
    </div>
</template>

<script>
import Navigation from "../Navigation";
export default {
    components: {Navigation},
    data() {
        return {
            file: null,
            studentId: null,
            students: []
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
