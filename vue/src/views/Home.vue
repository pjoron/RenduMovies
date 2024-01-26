<script setup>
import { ref, onMounted } from "vue";
import Card from "../components/Card.vue";

const movies = ref([]);
const actors = ref([]);
onMounted(async () => {
	const usertoken = localStorage.getItem("token");

	if (!usertoken) {
		return (window.location.href = "/login");
	}

	// fetch movies
	const responseMovies = await fetch("http://127.0.0.1:8000/api/movies?page=1", {
		headers: {
			"Content-Type": "application/json",
			Accept: "application/json",
			Authorization: "Bearer " + usertoken,
		},
	});

	if (responseMovies.ok) {
		const moviesData = await responseMovies.json();
		movies.value = moviesData;
		console.log("Movies:", moviesData);
	} else if (responseMovies.status === 401) {
		localStorage.removeItem("token");
		window.location.href = "/login";
	} else {
		throw "Error while fetching movies";
	}

	// fetch ators
	const responseActors = await fetch("http://127.0.0.1:8000/api/actors?page=1", {
		headers: {
			"Content-Type": "application/json",
			Accept: "application/json",
			Authorization: "Bearer " + usertoken,
		},
	});

	if (responseActors.ok) {
		const actorsData = await responseActors.json();
		actors.value = actorsData;
		console.log("Actors:", actorsData);
	} else {
		throw "Error while fetching actors";
	}
});
</script>

<template>
	<main v-if="movies">
		<h2>Latest Movies</h2>
		<div class="row">
			<Card v-for="movie in movies.slice(0, 5)" :id="movie.id" :title="movie.title" type="movies" image="https://source.unsplash.com/random/150x200/?movie" />
		</div>

		<h2 id="actors-title">Best Actors</h2>
		<div class="row">
			<Card v-for="actor in actors.slice(0, 5)" :id="actor.id" :title="actor.firstname + ' ' + actor.lastname" type="actors" image="https://source.unsplash.com/random/150x200/?human" />
		</div>
	</main>
	<main v-else>
		<h2>Loading...</h2>
	</main>
</template>

<style scoped>
#actors-title {
	margin-top: 50px;
}

.row {
	margin: 20px 0;
	width: 100%;
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: space-between;
}

.column {
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	padding: 1rem;
	border-radius: 7px;
	transition: all 0.3s;
	cursor: pointer;
}

.column:hover {
	background-color: #ececec;
}

.row img {
	border-radius: 5px;
	height: 200px;
	width: 150px;
}

.row p {
	text-align: center;
	margin-top: 10px;
}
</style>
