<template>
    <button class="btn btn-danger" @click="logout">Logout</button>
</template>

<script>
import axios from 'axios';

export default {
    methods: {
        async logout() {
            try {
                await axios.post('/api/logout');
                console.log('Logged out successfully');

                // Remove the token from localStorage
                localStorage.removeItem('authToken');
                delete axios.defaults.headers.common['Authorization'];

                this.$router.push('/login');
            } catch (error) {
                alert('Logout failed. Please try again.');
            }
        }
    }
}
</script>
