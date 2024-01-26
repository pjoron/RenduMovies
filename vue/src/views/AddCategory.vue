<script setup>
import { ref, onMounted } from "vue";

const name = ref("Anime");

const requiredFields = [name];

const successMessage = ref("");
const errorMessage = ref("");

const loading = ref(false);

onMounted(async () => {
	const usertoken = localStorage.getItem("token");

	if (!usertoken) {
		return (window.location.href = "/login");
	}
});

const addActor = async () => {
	errorMessage.value = "";

	const usertoken = localStorage.getItem("token");

	if (!usertoken) {
		return (window.location.href = "/login");
	}

	checkRequiredFields(requiredFields);

	loading.value = true;
	const response = await fetch("http://127.0.0.1:8000/api/categories", {
		method: "POST",
		headers: {
			"Content-Type": "application/json",
			Accept: "application/json",
			Authorization: "Bearer " + usertoken,
		},
		body: JSON.stringify({
			name: name.value,
		}),
	});

	if (response.status == 401) {
		localStorage.removeItem("token");
		window.location.href = "/login";
	}

	if (response.ok) {
		successMessage.value = `Category ${name.value} created successfully`;
	} else {
		const errorData = await response.json();
		errorMessage.value = "Error while creating category: " + errorData.detail;
	}

	loading.value = false;
};

const checkRequiredFields = (fields) => {
	fields.forEach((field) => {
		if (!field.value) {
			errorMessage.value = "Please fill all required fields";
			throw "Please fill all required fields";
		}
	});
};
</script>

<template>
	<main>
		<h2>Create a new category</h2>

		<form action="">
			<div class="column">
				<label for="name">Name</label>
				<input type="text" name="name" id="name" placeholder="Anime" v-model="name" />
			</div>

			<button @click.prevent="addActor">
				{{ loading ? "Loading..." : "Create Category" }}
			</button>

			<template v-if="errorMessage">
				<p class="error">{{ errorMessage }}</p>
			</template>

			<template v-if="successMessage">
				<p class="success">{{ successMessage }}</p>
			</template>
		</form>
	</main>
</template>

<style scoped>
main {
	display: flex;
	flex-direction: column;
	gap: 20px;
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
	width: auto;
	background-color: hsla(160, 100%, 37%, 1);
	border: none;
	border-radius: 5px;
	transition: all 0.3s;
	cursor: pointer;
	color: white;
	padding: 0 20px;
}

.error {
	padding-top: 10px;
	color: crimson;
}

.success {
	padding-top: 10px;
	color: green;
}
</style>
