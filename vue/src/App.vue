<script setup>
import { ref, onMounted } from "vue";

const token = ref("");
onMounted(() => {
	token.value = localStorage.getItem("token");
});
</script>

<template>
	<h1>My Amazing Application</h1>
	<nav v-if="token">
		<div>
			<router-link to="/">Home</router-link>
			<router-link to="/movies">Movies</router-link>
			<router-link to="/actors">Actors</router-link>
			<router-link to="/categories">Categories</router-link>
		</div>
		<div>
			<router-link to="/profile">Edit profile</router-link>
			<router-link to="/logout">Logout</router-link>
		</div>
	</nav>
	<nav v-else>
		<router-link to="/login">Login</router-link>
	</nav>

	<router-view></router-view>
</template>

<style scoped>
nav {
	display: flex;
	margin-bottom: 50px;
	margin-top: 15px;
	justify-content: space-between;
}

nav div {
	display: flex;
	gap: 20px;
	align-items: center;
}
</style>
