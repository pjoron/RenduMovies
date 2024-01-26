<script setup>
import { ref, onMounted } from "vue";

const title = ref("Amazing Film");
const actors = ref([]);
const releaseDate = ref("2019-03-06");
const boxOffice = ref(1150000000);
const categories = ref(["http://127.0.0.1:8000/api/categories/84"]);
const description = ref("Set in 1995, the story follows Danvers as she tries to uncover the secrets of her past while harnessing her special superpowers to end the war with the evil Skrulls. With the help of Nick Fury, she tries to uncover the secrets of her past while harnessing her special superpowers to end the war with the Skrulls.");
const duration = ref(120);
const note = ref(7);
const budget = ref(152000000);
const director = ref("Anna Boden");
const website = ref("https://www.imdb.com/title/tt4154664/");
const poster = ref("");

const requiredFields = [title, actors, releaseDate, boxOffice, categories, description, duration, note, budget, director, website, poster];

const successMessage = ref("");
const errorMessage = ref("");

const actorsList = ref([]);
const categoriesList = ref([]);
onMounted(async () => {
	const usertoken = localStorage.getItem("token");

	if (!usertoken) {
		return (window.location.href = "/login");
	}

	const getActors = await fetch("http://127.0.0.1:8000/api/actors", {
		method: "GET",
		headers: {
			"Content-Type": "application/json",
			Accept: "application/json",
			Authorization: "Bearer " + usertoken,
		},
	});

	if (getActors.ok) {
		const actorsData = await getActors.json();
		actorsList.value = actorsData;

		console.log("Actors:", actorsData);
	} else if (getActors.status === 401) {
		localStorage.removeItem("token");
		window.location.href = "/login";
	} else {
		throw "Error while getting actors";
	}

	const getCategories = await fetch("http://127.0.0.1:8000/api/categories", {
		method: "GET",
		headers: {
			"Content-Type": "application/json",
			Accept: "application/json",
			Authorization: "Bearer " + usertoken,
		},
	});

	if (getCategories.ok) {
		const categoriesData = await getCategories.json();
		categoriesList.value = categoriesData;

		console.log("Categories:", categoriesData);
	} else if (getCategories.status === 401) {
		localStorage.removeItem("token");
		window.location.href = "/login";
	} else {
		throw "Error while getting categories";
	}
});

const addMovie = async () => {
	errorMessage.value = "";

	const usertoken = localStorage.getItem("token");

	if (!usertoken) {
		return (window.location.href = "/login");
	}

	checkRequiredFields(requiredFields);

	const selectedActors = [];
	selectedActors.push("http://127.0.0.1:8000/api/actors/" + actors.value);

	const selectedCategories = [];
	selectedCategories.push("http://127.0.0.1:8000/api/categories/" + categories.value);

	const responseUploadPoster = await fetch("http://127.0.0.1:8000/api/media_objects", {
		method: "POST",
		headers: {
			"Content-Type": "application/json",
			Accept: "application/json",
			Authorization: "Bearer " + usertoken,
		},
		body: JSON.stringify({
			file: poster.value,
		}),
	});

	if (responseUploadPoster.ok) {
		console.log("Poster uploaded successfully");
	} else if (responseUploadPoster.status === 401) {
		localStorage.removeItem("token");
		window.location.href = "/login";
	} else {
		errorMessage.value = "Error while creating movie: unable to upload poster";
		const errorData = await responseUploadPoster.json();
		throw errorData.detail;
	}

	const responseCreateMovie = await fetch("http://127.0.0.1:8000/api/movies", {
		method: "POST",
		headers: {
			"Content-Type": "application/json",
			Accept: "application/json",
			Authorization: "Bearer " + usertoken,
		},
		body: JSON.stringify({
			title: title.value,
			actors: selectedActors,
			releaseDate: releaseDate.value,
			boxOffice: boxOffice.value,
			categories: selectedCategories,
			description: description.value,
			duration: duration.value,
			note: note.value,
			budget: budget.value,
			director: director.value,
			website: website.value,
		}),
	});

	if (responseCreateMovie.ok) {
		successMessage.value = "Movie created successfully";
	} else if (responseCreateMovie.status === 401) {
		localStorage.removeItem("token");
		window.location.href = "/login";
	} else {
		errorMessage.value = "Error while creating movie";
		const errorData = await responseCreateMovie.json();
		console.log(errorData.detail);
	}
};

const posterOnChange = (event) => {
	const file = event.target.files[0];
	poster.value = file;
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
	<main v-if="actorsList">
		<h2>Create a new movie</h2>

		<form action="">
			<div class="column">
				<label for="title">Poster</label>
				<input type="file" name="poster" id="poster" @change="posterOnChange" />
			</div>

			<div class="column">
				<label for="title">Title</label>
				<input type="text" name="title" id="title" placeholder="Amazing Movie" v-model="title" />
			</div>

			<div class="column">
				<label for="actors">Actors</label>
				<select name="actors" id="actors" v-model="actors">
					<option v-for="actor in actorsList" :key="actor.id" :value="actor.id">{{ actor.firstname + " " + actor.lastname }}</option>
				</select>
			</div>

			<div class="column">
				<label for="releaseDate">Release Date</label>
				<input type="date" name="releaseDate" id="releaseDate" placeholder="Amazing Release Date" v-model="releaseDate" />
			</div>

			<div class="column">
				<label for="boxOffice">Box Office</label>
				<input type="number" name="boxOffice" id="boxOffice" placeholder="Amazing Box Office" v-model="boxOffice" />
			</div>

			<div class="column">
				<label for="categories">Categories</label>
				<select name="categories" id="categories" v-model="categories">
					<option v-for="category in categoriesList" :key="category.id" :value="category.id">{{ category.name }}</option>
				</select>
			</div>

			<div class="column">
				<label for="description">Description</label>
				<input type="text" name="description" id="description" placeholder="Amazing Description" v-model="description" />
			</div>

			<div class="column">
				<label for="duration">Duration</label>
				<input type="number" name="duration" id="duration" placeholder="Amazing Duration" v-model="duration" />
			</div>

			<div class="column">
				<label for="note">Note</label>
				<input type="number" name="note" id="note" placeholder="Amazing Note" v-model="note" />
			</div>

			<div class="column">
				<label for="budget">Budget</label>
				<input type="number" name="budget" id="budget" placeholder="Amazing Budget" v-model="budget" />
			</div>

			<div class="column">
				<label for="director">Director</label>
				<input type="text" name="director" id="director" placeholder="Amazing Director" v-model="director" />
			</div>

			<div class="column">
				<label for="website">Website</label>
				<input type="text" name="website" id="website" placeholder="Amazing Website" v-model="website" />
			</div>

			<button @click.prevent="addMovie">Create Movie</button>

			<template v-if="errorMessage">
				<p class="error">{{ errorMessage }}</p>
			</template>

			<template v-if="successMessage">
				<p class="success">{{ successMessage }}</p>
			</template>
		</form>
	</main>
	<main v-else>
		<h2>Loading...</h2>
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

select {
	width: 500px;
	background-color: transparent;
	border: none;
	outline: none;
	border-bottom: 1px #ececec solid;
	color: black;
	font-size: 16px;
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
