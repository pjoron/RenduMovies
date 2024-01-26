<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import Card from "../components/Card.vue";
import moment from "moment";

const router = useRoute();
const actor = ref(null);
const movies = ref([]);

onMounted(async () => {
	const usertoken = localStorage.getItem("token");

	if (!usertoken) {
		return (window.location.href = "/login");
	} else {
		console.log("User token:", usertoken);
	}

	const response = await fetch("http://127.0.0.1:8000/api/actors/" + router.params.id, {
		headers: {
			"Content-Type": "application/json",
			Accept: "application/json",
			Authorization: "Bearer " + usertoken,
		},
	});

	if (response.ok) {
		const actorData = await response.json();
		actor.value = actorData;
		console.log("Actor:", actorData);
	} else if (response.status === 401) {
		localStorage.removeItem("token");
		window.location.href = "/login";
	} else {
		throw "Error while fetching movies";
	}

	actor.value.movies.forEach(async (movie) => {
		const response = await fetch("http://127.0.0.1:8000" + movie, {
			headers: {
				"Content-Type": "application/json",
				Accept: "application/json",
				Authorization: "Bearer " + usertoken,
			},
		});

		if (response.ok) {
			const movieData = await response.json();
			movies.value.push(movieData);
		} else if (response.status === 401) {
			localStorage.removeItem("token");
			window.location.href = "/login";
		} else {
			throw "Error while fetching movies";
		}
	});
});
</script>

<template>
	<main v-if="actor">
		<img src="https://source.unsplash.com/random/300x400/?human" :alt="actor.firstname" />
		<h2>{{ actor.firstname }} {{ actor.lastname }}</h2>
		<p>Nationality: {{ actor.nationality }}</p>
		<p>Date of birth: {{ moment(actor.dob).format("YYYY/MM/DD") }}</p>
		<p>Added to database {{ moment(actor.created_at).days() }} days ago</p>

		<div v-if="movies" id="movies">
			<h2>Related Movie(s)</h2>
			<div class="row">
				<Card v-for="movie in movies" :title="movie.title" image="https://source.unsplash.com/random/300x400/?movie" type="movies" :id="movie.id" />
			</div>
		</div>

		<router-link to="/actors">Back</router-link>
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

img {
	width: 300px;
	height: 400px;
	object-fit: cover;
	margin-bottom: 20px;
}

h2 {
	margin-bottom: 20px;
}

p {
	margin-bottom: 10px;
}

router-link {
	margin-top: 20px;
	width: auto;
	align-self: flex-start;
}

#movies {
	margin: 30px 0;
}
</style>
