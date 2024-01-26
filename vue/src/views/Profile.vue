<script setup>
import { ref, onMounted } from "vue";

const errorMessage = ref("");
const succesMessage = ref("");

const user = ref(null);

const loading = ref(false);

onMounted(async () => {
	const usertoken = localStorage.getItem("token");

	if (!usertoken) {
		return (window.location.href = "/login");
	}

	// fetch user
	const response = await fetch("http://127.0.0.1:8000/api/me", {
		headers: {
			"Content-Type": "application/json",
			Accept: "application/json",
			Authorization: "Bearer " + usertoken,
		},
	});

	if (response.status === 401) {
		localStorage.removeItem("token");
		window.location.href = "/login";
	}

	if (response.ok) {
		const userData = await response.json();
		console.log("User:", userData);
		user.value = userData;
	} else {
		throw "Error while fetching movies";
	}
});

const handleSave = async () => {
	loading.value = true;
	errorMessage.value = "";
	succesMessage.value = "";

	const usertoken = localStorage.getItem("token");
	if (!usertoken) {
		return (window.location.href = "/login");
	}

	const body = JSON.stringify({
		email: user.value.email,
		firstname: user.value.firstname,
		lastname: user.value.lastname,
	});

	console.log(body);

	const response = await fetch(`http://127.0.0.1:8000/api/users/${user.value.id}`, {
		method: "PATCH",
		headers: {
			"Content-Type": "application/merge-patch+json",
			Accept: "application/json",
			Authorization: "Bearer " + usertoken,
		},
		body: body,
	});

	if (response.status === 401) {
		localStorage.removeItem("token");
		window.location.href = "/login";
	}

	if (response.ok) {
		const userData = await response.json();
		console.log("User:", userData);
		succesMessage.value = "Profile updated succesfully";
		user.value = userData;
	} else {
		errorMessage.value = "Error while updating profile";
		throw "Error while fetching movies";
	}

	loading.value = false;
};

const isModified = ref(false);
const setModified = () => {
	isModified.value = true;
};
</script>

<template>
	<main v-if="user">
		<h2>Edit Profile</h2>

		<p v-if="errorMessage" class="error">{{ errorMessage }}</p>
		<p v-if="succesMessage" class="success">{{ succesMessage }}</p>

		<div class="column">
			<label for="email">Email Address</label>
			<input type="email" id="email" v-model="user.email" @input="setModified" />
		</div>

		<div class="column">
			<label for="firstname">First Name</label>
			<input type="text" id="firstname" v-model="user.firstname" @input="setModified" />
		</div>

		<div class="column">
			<label for="lastname">Last Name</label>
			<input type="text" id="lastname" v-model="user.lastname" @input="setModified" />
		</div>

		<button @click="handleSave" :disabled="!isModified || loading">
			{{ loading ? "Saving..." : "Save" }}
		</button>
	</main>
	<main v-else>
		<h2>Loading...</h2>
	</main>
</template>

<style scoped>
h2 {
	margin-bottom: 30px;
}

.column {
	width: 100%;
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

textarea {
	height: 100px;
	width: 100%;
	background-color: transparent;
	border: none;
	outline: none;
	border-bottom: 1px #ececec solid;
	color: black;
	font-size: 16px;
}

button {
	margin-top: 10px;
	height: 30px;
	width: 75px;
	background-color: hsla(160, 100%, 37%, 1);
	border: none;
	border-radius: 5px;
	transition: all 0.3s;
	cursor: pointer;
	color: white;
}

button:disabled {
	background-color: hsla(160, 100%, 37%, 0.5);
	cursor: not-allowed;
}

.close-btn {
	position: absolute;
	top: 30px;
	left: 30px;
	height: 35px;
	width: 35px;
	cursor: pointer;
	transition: all 0.3s;
}

.close-btn:hover {
	fill: hsla(160, 100%, 37%, 1);
	transform: rotate(-90deg);
}

.error {
	color: crimson;
}

.success {
	color: green;
}
</style>
