<!-- src/components/Navigation.vue -->
<template>
    <div class="bg-light">
        <nav class="navbar navbar-expand-lg custom-navbar">
            <a class="navbar-brand ms-auto" href="#">MyApp</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <router-link to="/" class="nav-link"  exact-active-class="active">Home</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/student" class="nav-link"  exact-active-class="active">Students</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/session-form" class="nav-link" exact-active-class="active">Session</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/report-template" class="nav-link" exact-active-class="active">Template</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/file-upload" class="nav-link" exact-active-class="active">File Upload</router-link>
                    </li>
                </ul>
            </div>
            <logout/>
        </nav>
    </div>

</template>

<script>
import Logout from "./Logout";
export default {
    name: 'Navigation',
    components: {Logout}
}
</script>

<style scoped>
.custom-navbar {
    max-width: 1200px; /* Adjust this value as needed */
    margin: 0 auto;
}
/* Add any specific styles for your navigation bar here */
</style>
