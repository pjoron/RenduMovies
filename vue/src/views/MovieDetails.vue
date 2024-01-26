<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import moment from "moment";
import Card from "../components/Card.vue";

const router = useRoute();
const movie = ref(null);

const actors = ref([]);
onMounted(async () => {
	const usertoken = localStorage.getItem("token");

	if (!usertoken) {
		return (window.location.href = "/login");
	} else {
		console.log("User token:", usertoken);
	}

	const response = await fetch("http://127.0.0.1:8000/api/movies/" + router.params.id, {
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
		const movieData = await response.json();
		movie.value = movieData;
	} else {
		throw "Error while fetching movies";
	}

	movie.value.actors.forEach(async (actor) => {
		const response = await fetch("http://127.0.0.1:8000" + actor, {
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
			const actorsData = await response.json();
			actors.value.push(actorsData);
		} else {
			throw "Error while fetching actors";
		}
	});
});
</script>

<template>
	<main v-if="movie">
		<img src="https://source.unsplash.com/random/300x400/?movie" :alt="movie.title" />
		<h2>{{ movie.title }}</h2>
		<p>Description: {{ movie.description }}</p>
		<p>Release Date: {{ moment(movie.releaseDate).format("LLLL") }}</p>
		<p>Released : {{ moment().diff(moment(movie.releaseDate), "years") }} years ago</p>
		<p>Duration: {{ movie.duration }} minutes</p>

		<div v-if="movie.actors" id="actors">
			<h2>Actor(s)</h2>
			<div class="row">
				<Card v-for="actor in actors" :title="actor.firstname + ' ' + actor.lastname" image="https://source.unsplash.com/random/300x400/?human" type="actors" :id="actor.id" />
			</div>
		</div>

		<router-link to="/movies">Back</router-link>
	</main>
	<main v-else>
		<h2>Loading...</h2>
	</main>
</template>

<style scoped>
main {
	display: flex;
	flex-direction: column;
}

img {
	width: 300px;
	height: 400px;
	object-fit: cover;
	margin-bottom: 20px;
	border-radius: 10px;
}

h2 {
	margin-bottom: 20px;
}

p {
	margin-bottom: 10px;
}

.row {
	width: 100%;
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: flex-start;
	gap: 15px;
	width: 100vw;
	overflow-x: scroll;
}

#actors {
	margin: 20px 0;
}
</style>
