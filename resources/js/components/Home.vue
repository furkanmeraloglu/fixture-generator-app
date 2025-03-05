<template>
    <div>
        <h1>Home</h1>
        <router-link to="/fixture">Fixture</router-link>
    </div>
</template>

<script>
import Fixture from "./Fixture.vue";

export default {
    name: 'Home',
    components: {About: Fixture},
};
</script>
