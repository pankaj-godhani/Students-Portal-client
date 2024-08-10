<template>
    <Navigation/>
    <div class="mt-5">
        <div style="display: flex;justify-content: space-around">
            <h1> Welcome Back !</h1>
        </div>


<!--        <div>-->
<!--            <student-list ref="studentList"></student-list>-->
<!--        </div>-->
<!--        <div>-->
<!--            <student-add  @student-added="onStudentAdded"></student-add>-->
<!--        </div>-->

<!--        <div>-->
<!--            <session-form ></session-form>-->
<!--        </div>-->
<!--        <div>-->
<!--            <session-rating></session-rating>-->
<!--        </div>-->
    </div>
</template>

<script>
import Logout from './Logout.vue';
import StudentList from "./student/StudentList";
import StudentAdd from "./student/StudentAdd";
import SessionForm  from "./session/SessionForm";
import SessionRating  from "./session/SessionRating";
import Navigation from "./Navigation";

export default {
    components: {
        Navigation,
        Logout,
        StudentList,
        StudentAdd,
        SessionForm,
        SessionRating
    },

}
</script>
