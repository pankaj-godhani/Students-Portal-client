<template style="position: relative;width: 100%;height: 100%">
    <div class="login-container" style="position: absolute;top:50%;left: 50%;  transform: translate(-50%, -50%);padding:18px">
        <h1>Login</h1>
        <form @submit.prevent="login">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" v-model="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" v-model="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            email: '',
            password: ''
        };
    },
    methods: {
        async login() {
            try {
                const response = await axios.post('/api/login', {
                    email: this.email,
                    password: this.password
                });

                // Log the entire response for debugging
                console.log('Login response:', response);

                // Store the token if it exists
                const token = response.data.token;
                if (token) {
                    console.log('Token received:', token);
                    localStorage.setItem('authToken', token);
                    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
                    this.$router.push('/');
                } else {
                    console.error('Token not found in response');
                }
            } catch (error) {
                console.error('Login failed:', error);
                this.error = 'Invalid credentials';
            }
        }
    }
}
</script>

<style scoped>
.login-container {
    max-width: 500px;
    margin: auto;
    padding: 1rem;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 20%;
}
.form-group {
    margin-bottom: 1rem;
}
form button {
    display: block;
    width: 100%;
    padding: 0.5rem;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
form button:hover {
    background-color: #0056b3;
}
</style>
