import { createRouter, createWebHistory } from "vue-router";

import Home from "../views/Home.vue";
import Movies from "../views/Movies.vue";
import Actors from "../views/Actors.vue";
import Categories from "../views/Categories.vue";
import MovieDetails from "../views/MovieDetails.vue";
import ActorDetails from "../views/ActorDetails.vue";
import Login from "../views/Login.vue";
import Logout from "../views/Logout.vue";
import Search from "../views/Search.vue";
import AddMovie from "../views/AddMovie.vue";
import CategoryDetails from "../views/CategoryDetails.vue";
import AddActor from "../views/AddActor.vue";
import AddCategory from "../views/AddCategory.vue";
import Profile from "../views/Profile.vue";

const router = createRouter({
	history: createWebHistory(import.meta.env.BASE_URL),
	routes: [
		{
			path: "/",
			component: Home,
		},
		{
			path: "/movies",
			component: Movies,
		},
		{
			path: "/actors",
			component: Actors,
		},
		{
			path: "/categories",
			component: Categories,
		},
		{
			path: "/categories/:id",
			component: CategoryDetails,
		},
		{
			path: "/movies/:id",
			component: MovieDetails,
		},
		{
			path: "/actors/:id",
			component: ActorDetails,
		},
		{
			path: "/login",
			component: Login,
		},
		{
			path: "/logout",
			component: Logout,
		},
		{
			path: "/search",
			component: Search,
		},
		{
			path: "/movies/add",
			component: AddMovie,
		},
		{
			path: "/actors/add",
			component: AddActor,
		},
		{
			path: "/categories/add",
			component: AddCategory,
		},
		{
			path: "/profile",
			component: Profile,
		},
	],
});

export default router;
