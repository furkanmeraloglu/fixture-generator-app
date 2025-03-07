<template>
    <div class="container mt-5 teams-page">
        <TeamsTable/>
    </div>
</template>
<script>
import TeamsTable from "@/components/Teams/TeamsTable.vue";

export default {
    name: 'Teams',
    components: {TeamsTable},
};
</script>
