<script setup>
import { onMounted, ref } from 'vue'
import Card from '../components/Card.vue';
import { useRoute } from 'vue-router'

const usertoken = localStorage.getItem('token')

if (!usertoken) {
    window.location.href = '/login'
}

const router = useRoute()
const query = router.query
const type = Object.keys(query)[0]
const search = Object.values(query)[0]

const searchQuery = ref('')
switch (type) {
    case "movies":
        searchQuery.value = 'title'
        break;

    case "categories":
        searchQuery.value = 'name'
        break;

    case "actors":
        searchQuery.value = 'lastName'
        break;

    default:
        throw ('Error while fetching search query')
}

const result = ref([])
onMounted(async () => {
    // fetch data
    console.log(`http://127.0.0.1:8000/api/${type}?page=1&${searchQuery.value}=${search}`)
    const response = await fetch(`http://127.0.0.1:8000/api/${type}?page=1&${searchQuery.value}=${search}`, {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + usertoken
        }
    })

    if (response.ok) {
        const data = await response.json()
        result.value = data
        console.log('Research result:', result.value)
    } else if (response.status === 401) {
        localStorage.removeItem('token')
        window.location.href = '/login'
    } else {
        throw (`Error while fetching data for ${type}`)
    }
})
</script>

<template>
    <main>
        <h2>Search results for "{{ search }}"</h2>
        <div class="row" v-if="result">
            <Card v-for="item in result" :id="item.id" :title="item.title" :type="type"
                :image='`https://source.unsplash.com/random/150x200/?${type}`' />
        </div>
    </main>
    <router-link :to="`/${type}`">Back</router-link>
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
    justify-content: flex-start;
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
</style>