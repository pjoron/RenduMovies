<script setup>
import { ref, onMounted } from "vue";
import moment from "moment";

const firstname = ref("Demon");
const lastname = ref("Salvadore");
const dob = ref("1997-03-06");
const nationality = ref("French");
const created_at = ref(moment().toISOString());

const requiredFields = [firstname, lastname, dob, nationality, created_at];

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
	const response = await fetch("http://127.0.0.1:8000/api/actors", {
		method: "POST",
		headers: {
			"Content-Type": "application/json",
			Accept: "application/json",
			Authorization: "Bearer " + usertoken,
		},
		body: JSON.stringify({
			lastname: lastname.value,
			firstname: firstname.value,
			dob: dob.value,
			nationality: nationality.value,
			createdAt: created_at.value,
		}),
	});

	if (response.status == 401) {
		localStorage.removeItem("token");
		window.location.href = "/login";
	}

	if (response.ok) {
		successMessage.value = "Actor created successfully";
	} else {
		const errorData = await response.json();
		errorMessage.value = "Error while creating actor: " + errorData.detail;
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
		<h2>Create a new actor</h2>

		<form action="">
			<div class="column">
				<label for="firstname">First Name</label>
				<input type="text" name="firstname" id="firstname" placeholder="Demon" v-model="firstname" />
			</div>

			<div class="column">
				<label for="lastname">Last Name</label>
				<input type="text" name="lastname" id="lastname" placeholder="Salvadore" v-model="lastname" />
			</div>

			<div class="column">
				<label for="dob">Date of Birth</label>
				<input type="date" name="dob" id="dob" placeholder="Date of Birth" v-model="dob" />
			</div>

			<div class="column">
				<label for="nationality">Nationality</label>
				<input type="text" name="nationality" id="nationality" placeholder="French" v-model="nationality" />
			</div>

			<button @click.prevent="addActor">
				{{ loading ? "Loading..." : "Create Actor" }}
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
	width: 100px;
	background-color: hsla(160, 100%, 37%, 1);
	border: none;
	border-radius: 5px;
	transition: all 0.3s;
	cursor: pointer;
	color: white;
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
