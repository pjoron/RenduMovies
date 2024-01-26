<script setup>
import { ref, onMounted } from "vue";
import Card from "../components/Card.vue";
import Searchbar from "../components/Searchbar.vue";
import { useRoute } from "vue-router";

const route = useRoute();
const page = ref(route.query.page || 1);
const filter = ref(route.query.name || null);

const isNextPageDisabled = ref(true);

const categories = ref([]);
onMounted(async () => {
	const usertoken = localStorage.getItem("token");

	if (!usertoken) {
		return (window.location.href = "/login");
	}

	const responseCategories = await fetch(`http://127.0.0.1:8000/api/categories?page=${page.value}${filter.value ? "&name=" + filter.value : ""}`, {
		headers: {
			"Content-Type": "application/json",
			Accept: "application/json",
			Authorization: "Bearer " + usertoken,
		},
	});

	if (responseCategories.ok) {
		const categoriesData = await responseCategories.json();

		if (categoriesData.length <= 0) {
			return (window.location.href = "/categories?page=1");
		}

		categories.value = categoriesData;
	} else if (responseCategories.status === 401) {
		localStorage.removeItem("token");
		window.location.href = "/login";
	} else {
		throw "Error while fetching categories";
	}

	if (!filter.value) {
		const tempPage = page.value + 1;
		const responseNextCategories = await fetch(`http://127.0.0.1:8000/api/categories?page=${tempPage}`, {
			headers: {
				"Content-Type": "application/json",
				Accept: "application/json",
				Authorization: "Bearer " + usertoken,
			},
		});

		if (responseNextCategories.status === 401) {
			localStorage.removeItem("token");
			window.location.href = "/login";
		}

		if (responseNextCategories.ok) {
			const nextCategories = await responseNextCategories.json();
			if (nextCategories.length > 0) {
				isNextPageDisabled.value = false;
			}
		}
	}
});

const previousPage = () => {
	page.value--;
	window.location.href = `/categories?page=${page.value}`;
};

const nextPage = async () => {
	if (isNextPageDisabled.value) {
		return;
	}

	page.value++;
	window.location.href = `/categories?page=${page.value}`;
};
</script>

<template>
	<main v-if="categories">
		<h2>All Categories</h2>

		<Searchbar type="categories" />

		<div class="row">
			<router-link to="/categories/add">Add Category</router-link>
		</div>

		<div class="row">
			<Card v-for="item in categories" :show-actions="true" :data="item" :id="item.id" :title="item.name" type="categories" image="https://source.unsplash.com/random/150x200/?film" />
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
#categories-title {
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
