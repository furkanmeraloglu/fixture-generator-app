<template>
    <div class="container-fluid mt-4">
        <h1 class="text-center mb-4">League Results</h1>

        <div v-if="loadingFixtures || loadingTeams" class="text-center my-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">{{ loadingFixtures ? 'Simulating matches...' : 'Loading data...' }}</p>
        </div>
        <div v-else>
            <div class="row justify-content-center mb-4">
                <div class="col-lg-12 col-xl-11 d-flex justify-content-center align-items-center">
                    <TeamStandings :teams="teams" :loading="loadingTeams"/>
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
                <div class="col-12 text-center weekly-fixture-buttons">
                    <button @click="simulateAllMatches()"
                            class="btn btn-primary me-2"
                            :disabled="loadingFixtures || buttonInactive">
                        {{ loadingFixtures ? 'Simulating...' : 'Simulate All Season' }}
                    </button>
                    <button @click="resetFixtureAndGenerateNewOne()"
                            class="btn btn-danger me-2"
                            :disabled="loadingFixtures">
                        {{ loadingFixtures ? 'Resetting...' : 'Reset & Generate New' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Swal from 'sweetalert2';
import TeamStandings from "@/components/WeeklyScores/TeamStandings.vue";
import WeeklyFixtureCard from "@/components/Fixtures/WeeklyFixtureCard.vue";

export default {
    name: 'AllScores',
    components: {WeeklyFixtureCard, TeamStandings},
    data() {
        return {
            fixtures: [],
            teams: [],
            loadingFixtures: false,
            loadingTeams: false,
            buttonInactive: false,
        }
    },
    mounted() {
        this.fetchAllFixtures();
        this.fetchTeamStandings();
    },
    methods: {
        async fetchAllFixtures() {
            this.loadingFixtures = true;
            await axios.get('/api/fixtures')
                .then(response => {
                    console.log(response.data.data);
                    if (response.data.code === 200) {
                        this.fixtures = response.data.data;
                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'center',
                            icon: 'success',
                            title: response.data.message,
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'swal2-popup',
                                title: 'swal2-title',
                                content: 'swal2-content',
                                icon: 'swal2-icon'
                            }
                        });
                    }
                    this.loadingFixtures = false;
                })
                .catch(error => {
                    Swal.fire({
                        toast: true,
                        position: 'center',
                        icon: 'error',
                        title: error.message || 'Something went wrong!',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'swal2-popup',
                            title: 'swal2-title',
                            content: 'swal2-content',
                            icon: 'swal2-icon'
                        }
                    });
                    this.loadingFixtures = false;
                });
        },
        async simulateAllMatches() {
            this.loadingFixtures = true;
            await axios.post('/api/fixtures/simulate-all')
                .then(response => {
                    this.fixtures = response.data.data;
                    this.fetchTeamStandings();
                    this.loadingFixtures = false;
                    this.buttonInactive = true;
                })
                .catch(error => {
                    console.error('Error fetching scores:', error);
                    this.loadingFixtures = false;
                });
        },
        async fetchTeamStandings() {
            this.loadingTeams = true;
            await axios.get('/api/teams')
                .then(response => {
                    this.teams = response.data.data;
                    this.loadingTeams = false;
                })
                .catch(error => {
                    console.error('Error fetching teams:', error);
                    this.loadingTeams = false;
                });
        },
        async resetFixtureAndGenerateNewOne() {
            await axios.delete('/api/fixtures')
                .then(() => {
                    Swal.fire({
                        toast: true,
                        position: 'center',
                        icon: 'success',
                        title: 'Fixtures reset successfully. Redirecting to generate new fixtures...',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'swal2-popup',
                            title: 'swal2-title',
                            content: 'swal2-content',
                            icon: 'swal2-icon'
                        }
                    });
                    this.$router.push('/fixtures');
                })
                .catch(error => {
                    Swal.fire({
                        toast: true,
                        position: 'center',
                        icon: 'error',
                        title: error.message || 'Something went wrong!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'swal2-popup',
                            title: 'swal2-title',
                            content: 'swal2-content',
                            icon: 'swal2-icon'
                        }
                    });
                });
        },

    }
};

</script>
