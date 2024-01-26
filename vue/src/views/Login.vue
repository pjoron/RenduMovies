<script setup>
import { ref, onMounted } from "vue";

const email = ref("paul@gmail.com");
const password = ref("test");

const message = ref("");
onMounted(() => {
	message.value = localStorage.getItem("message");

	if (message.value) {
		localStorage.removeItem("message");
	}
});

const loading = ref(false);
const handleLogin = () => {
	loading.value = true;

	const json = {
		username: email.value,
		password: password.value,
	};

	const request = fetch("http://127.0.0.1:8000/api/login_check", {
		method: "POST",
		headers: {
			"Content-Type": "application/json",
		},
		body: JSON.stringify(json),
	});

	request.then((response) => {
		if (response.status === 200) {
			response.json().then((data) => {
				console.log("login request data.token:", data);
				localStorage.setItem("token", data.token);
				window.location.href = "/";
			});
		} else if (response.status === 401) {
			message.value = "Wrong credentials";
		} else {
			response.json().then((data) => {
				throw new Error("An error occured when trying to connect user:", data.message);
			});
		}

		loading.value = false;
	});
};
</script>

<template>
	<p v-if="message" class="message">{{ message }}</p>

	<h2>Login</h2>

	<main>
		<div class="column">
			<label for="email">E-mail addess</label>
			<input type="email" name="email" id="email" placeholder="user1@mail.com" v-model="email" />
		</div>

		<div class="column">
			<label for="password">Password</label>
			<input type="password" name="password" id="password" placeholder="1234" v-model="password" />
		</div>

		<button @click="handleLogin">
			<template v-if="loading">Loading...</template>
			<template v-else>Login</template>
		</button>
	</main>
</template>

<style scoped>
h2 {
	margin-bottom: 30px;
}

.column {
	display: flex;
	flex-direction: column;
	gap: 5px;
	align-items: flex-start;
	margin-bottom: 30px;
}

input {
	height: 30px;
	width: 500px;
	background-color: transparent;
	border: none;
	outline: none;
	border-bottom: 1px #ececec solid;
	color: black;
	font-size: 16px;
}

button {
	height: 30px;
	width: 75px;
	background-color: hsla(160, 100%, 37%, 1);
	border: none;
	border-radius: 5px;
	transition: all 0.3s;
	cursor: pointer;
	color: white;
}

button:hover {
	background-color: hsla(160, 100%, 37%, 0.5);
}

.message {
	color: coral;
	padding-bottom: 10px;
}
</style>
