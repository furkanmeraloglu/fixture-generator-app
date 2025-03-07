<template>
    <div class="col-md-6 mb-4">
        <WeeklyFixtureCard
            :fixtures="fixtures"
            :loadingFixtures="loadingFixtures"
            :currentWeek="currentWeek"
            @edit-fixture="handleEditFixture"
        />
    </div>
</template>

<script>
import WeeklyFixtureCard from "@/components/Fixtures/WeeklyFixtureCard.vue";

export default {
    name: 'WeekResults',
    components: {WeeklyFixtureCard},
    props: {
        currentWeek: {
            type: Number,
            default: 1
        },
        fixtures: {
            type: Array,
            default: () => []
        },
        loadingFixtures: {
            type: Boolean,
            default: true
        }
    },
    methods: {
        handleEditFixture(fixture) {
            this.$emit('edit-fixture', fixture);
        }
    }
};
</script>
