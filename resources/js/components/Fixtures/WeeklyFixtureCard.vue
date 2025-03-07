<template>
    <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Week {{ currentWeek }}</h5>
        </div>
        <div class="card-body">
            <div v-if="loadingFixtures" class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div v-else>
                <div v-for="fixture in fixtures" :key="fixture.fixture_id" class="fixture-row mb-2 p-2">
                    <div class="d-flex align-items-center">
                        <div class="fixture-team home-team text-end">
                <span :class="{'winner': fixture.home_team_goals > fixture.away_team_goals}">
                    {{ fixture.home_team.name }}
                </span>
                        </div>

                        <div class="fixture-score">
                <span class="score mx-2">
                    {{ fixture.home_team_goals }} - {{ fixture.away_team_goals }}
                </span>
                            <button class="btn btn-sm btn-outline-secondary edit-btn ms-2"
                                    @click="editFixture(fixture)">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                        </div>

                        <div class="fixture-team away-team">
                <span :class="{'winner': fixture.away_team_goals > fixture.home_team_goals}">
                    {{ fixture.away_team.name }}
                </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'WeeklyFixtureCard',
    props: {
        fixtures: {
            type: Array,
            default: () => []
        },
        loadingFixtures: {
            type: Boolean,
            default: false
        },
        currentWeek: {
            type: Number,
            default: 1
        }
    },
    methods: {
        editFixture(fixture) {
            this.$emit('edit-fixture', fixture);
        }
    }
};
</script>
