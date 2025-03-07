<template>
    <div class="container-fluid d-flex flex-column justify-content-center min-vh-100" style="padding-bottom: 5vh;">
        <div class="row mt-4">
            <div class="col-12 text-center">
                <h3 class="fixture-title">League Fixtures</h3>
            </div>
        </div>
        <div class="row justify-content-center fixtures-page">
            <div class="col-lg-12 col-xl-11">
                <div class="row fixtures-container">
                    <div class="col-md-4 mb-5" v-for="(fixtureGroup, week) in fixtures" :key="week">
                        <WeeklyFixtureCard
                            :fixtures="fixtureGroup"
                            :loadingFixtures="loadingFixtures"
                            :currentWeek="parseInt(week)"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12 text-center fixtures-buttons">
                <router-link to="/weekly-fixture?simulate=true" class="btn btn-primary me-2">Start Simulation</router-link>
                <button @click="resetCurrentLeagueAndGenerateNewOne" class="btn btn-danger" :disabled="loadingFixtures">
                    {{ loadingFixtures ? 'Resetting...' : 'Reset Fixture and Generate New' }}
                </button>
            </div>
        </div>
    </div>
</template>
<script>
import WeeklyFixtureCard from "@/components/Fixtures/WeeklyFixtureCard.vue";

export default {
    name: 'Fixtures',
    components: {WeeklyFixtureCard},
    data() {
        return {
            fixtures: {},
            loadingFixtures: true,
            currentWeek: 1,
        }
    },
    mounted() {
        if (this.$route.query.generate === 'true') {
            this.generateLeagueFixtures();
        } else {
            this.fetchLeagueFixtures();
        }
    },
    methods: {
        async fetchLeagueFixtures() {
            this.loadingFixtures = true;
            await axios.get('/api/fixtures')
                .then(response => {
                    this.fixtures = response.data.data;
                })
                .catch(error => {
                    console.error('Error retrieving fixtures:', error);
                })
                .finally(() => {
                    this.loadingFixtures = false;
                });
        },
        async generateLeagueFixtures() {
            this.loadingFixtures = true;
            await axios.post('/api/fixtures')
                .then(response => {
                    this.fixtures = response.data.data;
                    this.$router.replace({path: this.$route.path});
                })
                .catch(error => {
                    console.error('Error generating fixtures:', error);
                })
                .finally(() => {
                    this.loadingFixtures = false;
                });
        },
        async resetCurrentLeagueAndGenerateNewOne() {
            this.fixtures = [];
            this.loadingFixtures = true;
            await axios.delete('/api/fixtures')
                .then(response => {
                    this.fetchLeagueFixtures();
                })
                .catch(error => {
                    console.error('Error resetting fixtures:', error);
                })
                .finally(() => {
                    this.loadingFixtures = false;
                });
        }
    }
};
</script>
