<script setup>
import { defineProps, onMounted, ref } from "vue";
import { useRouter } from "vue-router";
const router = useRouter();

const props = defineProps({
	type: {
		type: String,
		required: true,
	},
});

const search = ref("");

const handleSearch = () => {
	if (props.type === "movies") {
		return (window.location.href = `/movies?title=${search.value}`);
	}

	if (props.type === "actors") {
		return (window.location.href = `/actors?lastname=${search.value}`);
	}

	if (props.type === "categories") {
		return (window.location.href = `/categories?name=${search.value}`);
	}
};

onMounted(() => {
	search.value = router.currentRoute.value.query.lastname || router.currentRoute.value.query.name || router.currentRoute.value.query.title || "";
});
</script>

<template>
	<div class="row searchbar">
		<input type="text" v-model="search" :placeholder="'Search a ' + props.type.slice(0, -1) + ' by name'" />
		<button @click="handleSearch" @keyup.enter="handleSearch">Search</button>
	</div>
</template>

<style scoped>
.row {
	margin: 20px 0;
	width: 100%;
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: space-between;
	flex-wrap: wrap;
}

.row.searchbar {
	justify-content: flex-start;
	gap: 15px;
}

.row.row.searchbar input {
	height: 30px;
	width: 500px;
	background-color: transparent;
	border: none;
	outline: none;
	border-bottom: 1px #ececec solid;
	color: black;
	font-size: 16px;
}

.row.row.searchbar button {
	height: 30px;
	width: 75px;
	background-color: hsla(160, 100%, 37%, 1);
	border: none;
	border-radius: 5px;
	transition: all 0.3s;
	cursor: pointer;
	color: white;
}

.row.row.searchbar button:hover {
	background-color: hsla(160, 100%, 37%, 0.5);
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
