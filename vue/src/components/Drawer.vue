<script setup>
import { defineProps, ref, onMounted, watch } from "vue";

const usertoken = ref(null);
onMounted(() => {
	usertoken.value = localStorage.getItem("token");

	if (!usertoken) {
		return (window.location.href = "/login");
	}
});

const loading = ref(false);

const props = defineProps({
	isOpen: {
		type: Boolean,
		required: true,
	},
	id: {
		type: Number,
		required: false,
	},
	data: {
		type: Object,
		required: false,
	},
	type: {
		type: String,
		required: true,
	},
});

const itemData = ref(props.data);
const itemId = ref(props.id);
const isDrawerOpen = ref(props.isOpen);
const type = ref(props.type);

watch(props, () => {
	itemData.value = props.data;
	itemId.value = props.id;
	isDrawerOpen.value = props.isOpen;
	type.value = props.type;
});

const errorMessage = ref(null);
const body = ref("");
const handleSave = async () => {
	if (!itemId.value) return;
	errorMessage.value = "";

	if (type.value == "movies") {
		if (itemData.value.title.replace(/\s+/g, "") == "" || itemData.value.title == null) {
			errorMessage.value = "Title cannot be empty";
			return;
		}

		if (itemData.value.description.replace(/\s+/g, "") == "" || itemData.value.description == null) {
			errorMessage.value = "Description cannot be empty";
			return;
		}

		if (itemData.value.duration == "" || itemData.value.duration == null || itemData.value.duration == 0) {
			errorMessage.value = "Duration cannot be null";
			return;
		}

		body.value = JSON.stringify({
			title: itemData.value.title,
			description: itemData.value.description,
			duration: itemData.value.duration,
		});
	}

	if (type.value == "actors") {
		if (itemData.value.firstname.replace(/\s+/g, "") == "" || itemData.value.firstname == null) {
			errorMessage.value = "Fistname cannot be empty";
			return;
		}

		if (itemData.value.lastname.replace(/\s+/g, "") == "" || itemData.value.lastname == null) {
			errorMessage.value = "Lastname cannot be empty";
			return;
		}

		if (itemData.value.nationality.replace(/\s+/g, "") == "" || itemData.value.nationality == null) {
			errorMessage.value = "Nationality cannot be empty";
			return;
		}

		body.value = JSON.stringify({
			firstname: itemData.value.firstname,
			lastname: itemData.value.lastname,
			nationality: itemData.value.nationality,
		});
	}

	if (type.value == "categories") {
		if (itemData.value.name.replace(/\s+/g, "") == "" || itemData.value.name == null) {
			return (errorMessage.value = "Category name cannot be empty");
		}

		body.value = JSON.stringify({
			name: itemData.value.name,
		});
	}

	loading.value = true;
	const response = await fetch(`http://127.0.0.1:8000/api/${type.value}/${itemId.value}`, {
		method: "PATCH",
		headers: {
			"Content-Type": "application/merge-patch+json",
			Accept: "application/json",
			Authorization: "Bearer " + usertoken.value,
		},
		body: body.value,
	});

	if (response.ok) {
		const itemData = await response.json();
		props.data = itemData;
		console.log("done, new data are", itemData, "closing...");
		isDrawerOpen.value = false;
	} else if (response.status === 401) {
		localStorage.removeItem("token");
		window.location.href = "/login";
	} else {
		errorMessage.value("Error while saving");
		console.log("Error while saving");
	}
	loading.value = false;
};
</script>

<template>
	<div class="drawer" :class="{ active: isDrawerOpen }">
		<svg class="close-btn" @click="$emit('closeDrawer')" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12.0007 10.5865L16.9504 5.63672L18.3646 7.05093L13.4149 12.0007L18.3646 16.9504L16.9504 18.3646L12.0007 13.4149L7.05093 18.3646L5.63672 16.9504L10.5865 12.0007L5.63672 7.05093L7.05093 5.63672L12.0007 10.5865Z"></path></svg>

		<div class="column" v-if="type == 'movies'">
			<div class="column">
				<label for="title">Title</label>
				<input type="text" name="title" id="title-input" v-model="itemData.title" />
			</div>

			<div class="column">
				<label for="description">Description</label>
				<textarea name="description" id="description-input" v-model="itemData.description" />
			</div>

			<div class="column">
				<label for="duration">Duration</label>
				<input type="number" name="duration" id="duration-input" v-model="itemData.duration" />
			</div>

			<p v-if="errorMessage" class="error">{{ errorMessage }}</p>

			<button @click="handleSave">
				{{ loading ? "Saving..." : "Save" }}
			</button>
		</div>

		<div class="column" v-if="type == 'actors'">
			<div class="column">
				<label for="firstname">First name</label>
				<input type="text" name="firstname" id="firstname-input" v-model="itemData.firstname" />
			</div>

			<div class="column">
				<label for="lastname">Last name</label>
				<input type="text" name="lastname" id="lastname-input" v-model="itemData.lastname" />
			</div>

			<div class="column">
				<label for="nationality">Nationality</label>
				<input type="text" name="nationality" id="nationality-input" v-model="itemData.nationality" />
			</div>

			<p v-if="errorMessage" class="error">{{ errorMessage }}</p>

			<button @click="handleSave">
				{{ loading ? "Saving..." : "Save" }}
			</button>
		</div>

		<div class="column" v-if="type == 'categories'">
			<div class="column">
				<label for="name">Category Name</label>
				<input type="text" name="name" id="name-input" v-model="itemData.name" />
			</div>

			<p v-if="errorMessage" class="error">{{ errorMessage }}</p>

			<button @click="handleSave">
				{{ loading ? "Saving..." : "Save" }}
			</button>
		</div>
	</div>
</template>

<style scoped>
.drawer {
	position: fixed;
	top: 0;
	right: -500px;
	width: 400px;
	height: 100vh;
	background-color: #f5f5f5;
	transition: all 0.3s;
	box-shadow: -5px 0px 30px rgba(0, 0, 0, 0.1);
	z-index: 100;
	display: flex;
	justify-content: center;
	align-items: center;
	padding: 30px;
}

.drawer.active {
	right: 0;
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
</style>
