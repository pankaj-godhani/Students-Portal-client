<template>
    <Navigation/>
    <div class="mt-5 container border-0">
        <div style="display: flex;justify-content: space-around">
            <h1> Welcome Back  {{userName}}!</h1>
        </div>
<!--        <div class="row">-->
<!--                <div class="d-flex col-sm-4 flex-column border border-2 rounded p-4 border-green text-center">-->
<!--                    <h3 class="text-lg">-->
<!--                        Students-->
<!--                    </h3>-->
<!--                    <h5 >-->
<!--                        {{studentsCount}}-->
<!--                    </h5>-->
<!--                </div>-->

<!--                <div class="d-flex col-sm-4 flex-column border border-2 rounded p-4 border-green text-center">-->
<!--                    <h3 class="text-lg">-->
<!--                        Templates-->
<!--                    </h3>-->
<!--                    <h5 >-->
<!--                        {{studentsCount}}-->
<!--                    </h5>-->
<!--                </div>-->

<!--        </div>-->

    </div>
</template>

<script>
import Logout from './Logout.vue';
import Navigation from "./Navigation";
import axios from "axios";

export default {
    components: {
        Navigation,
        Logout,
    },
    data() {
        return {
            studentsCount: 0,
            templateCount: 0,
            userName:''
        };
    },
    created() {
        axios.get('/api/students')
            .then(response => {
                const students = response.data;
                this.studentsCount = students.length;
            });
        axios.get('/api/user')
            .then(response => {
               this.userName = response.data.name;
            });

        axios.get('/api/report-templates')
            .then(response => {
                this.templateCount = response.data.length;
            });
    },
}
</script>
