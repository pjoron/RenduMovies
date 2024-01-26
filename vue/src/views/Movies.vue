<script setup>
import { ref, onMounted, computed } from "vue";
import Card from "../components/Card.vue";
import Searchbar from "../components/Searchbar.vue";
import { useRoute } from "vue-router";

const route = useRoute();
const page = ref(route.query.page || 1);
const filter = ref(route.query.title || null);

const isNextPageDisabled = ref(true);

const movies = ref([]);
onMounted(async () => {
	const usertoken = localStorage.getItem("token");

	if (!usertoken) {
		return (window.location.href = "/login");
	}

	const responseMovies = await fetch(`http://127.0.0.1:8000/api/movies?page=${page.value}${filter.value ? "&title=" + filter.value : ""}`, {
		headers: {
			"Content-Type": "application/json",
			Accept: "application/json",
			Authorization: "Bearer " + usertoken,
		},
	});

	if (responseMovies.ok) {
		const moviesData = await responseMovies.json();

		if (moviesData.length <= 0) {
			return (window.location.href = "/movies?page=1");
		}

		movies.value = moviesData;
		console.log("Movies:", moviesData);
	} else if (responseMovies.status === 401) {
		localStorage.removeItem("token");
		window.location.href = "/login";
	} else {
		throw "Error while fetching movies";
	}

	if (!filter.value) {
		const tempPage = page.value + 1;
		const responseNextMovies = await fetch(`http://127.0.0.1:8000/api/movies?page=${tempPage}`, {
			headers: {
				"Content-Type": "application/json",
				Accept: "application/json",
				Authorization: "Bearer " + usertoken,
			},
		});

		if (responseNextMovies.status === 401) {
			localStorage.removeItem("token");
			window.location.href = "/login";
		}

		if (responseNextMovies.ok) {
			const nextMovies = await responseNextMovies.json();
			if (nextMovies.length > 0) {
				isNextPageDisabled.value = false;
			}
		}
	}
});

const previousPage = () => {
	page.value--;
	window.location.href = `/movies?page=${page.value}`;
};

const nextPage = async () => {
	if (isNextPageDisabled.value) {
		return;
	}

	page.value++;
	window.location.href = `/movies?page=${page.value}`;
};
</script>

<template>
	<main v-if="movies">
		<h2>All Movies</h2>

		<Searchbar type="movies" />

		<div class="row">
			<router-link to="/movies/add">Add movie</router-link>
		</div>

		<div class="row">
			<div class="column" v-for="movie in movies">
				<Card :show-actions="true" :id="movie.id" :data="movie" :title="movie.title" type="movies" image="https://source.unsplash.com/random/150x200/?movie" />
			</div>
		</div>

		<div class="row" id="pagination">
			<button class="btn btn-primary" @click="previousPage" :disabled="page <= 1">Previous</button>
			<button class="btn btn-primary" @click="nextPage" :disabled="isNextPageDisabled">Next</button>
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
	flex-wrap: wrap;
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

.column {
	width: 150px;
	display: flex;
	flex-direction: column;
	gap: 5px;
	align-items: center;
	margin: 20px;
}

.row#pagination {
	justify-content: center;
	gap: 10px;
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

button:disabled {
	background-color: hsla(160, 100%, 37%, 0.5);
	cursor: not-allowed;
}
</style>
