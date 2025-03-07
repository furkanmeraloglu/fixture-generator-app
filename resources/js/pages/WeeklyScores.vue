<template>
    <div class="container-fluid d-flex flex-column justify-content-center min-vh-100" style="padding-bottom: 5vh;">
        <div class="row mt-4 mb-4">
            <div class="col-12 text-center">
                <h3 class="fixture-title">Matches of the Week</h3>
            </div>
        </div>
        <div class="row justify-content-center weekly-scores-page">
            <div class="col-lg-10 col-xl-9 col-xxl-8 mb-4">
                <div class="row justify-content-center">
                    <TeamStandings :teams="teams" :loadingTeams="loadingTeams"/>
                    <WeekResults :currentWeek="currentWeek" :fixtures="fixtures" :loadingFixtures="loadingFixtures" @edit-fixture="openEditModal" />
                </div>
            </div>
        </div>
        <div v-if="predictionEnabled" class="row justify-content-center weekly-scores-page">
            <div class="col-lg-10 col-xl-9 col-xxl-8 mb-4">
                <div class="row justify-content-center">
                    <ChampionshipPrediction :currentWeek="currentWeek" :predictions="formattedPredictions" :loadingPredictions="loadingPredictions"/>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12 text-center weekly-fixture-buttons">
                <button @click="simulateNextWeekFixture()"
                        class="btn btn-primary me-2"
                        :disabled="loadingFixtures || buttonInactive">
                    {{ loadingFixtures ? 'Simulating...' : 'Simulate Next Week' }}
                </button>
                <router-link
                    :to="buttonInactive ? '#' : '/all-fixtures?simulateAll=true'"
                    class="btn btn-warning me-2"
                    :class="{ 'disabled': buttonInactive }"
                    @click.native.prevent="buttonInactive ? null : null">
                    Simulate All Season
                </router-link>
                <button @click="resetFixtureAndGenerateNewOne()"
                        class="btn btn-danger me-2"
                        :disabled="loadingFixtures">
                    {{ loadingFixtures ? 'Resetting...' : 'Reset & Generate New' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Match Modal -->
    <div class="modal fade" id="editMatchModal" tabindex="-1" aria-labelledby="editMatchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMatchModalLabel">Edit Match Result</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div v-if="selectedFixture" class="text-center">
                        <div class="mb-3">
                            <span class="team-name">{{ selectedFixture.home_team_name }}</span>
                            <div class="d-flex justify-content-center align-items-center mt-2">
                                <input type="number" v-model.number="homeScore" class="form-control form-control-lg text-center mx-2" style="max-width: 80px" min="0">
                                <span class="fs-5 mx-2">-</span>
                                <input type="number" v-model.number="awayScore" class="form-control form-control-lg text-center mx-2" style="max-width: 80px" min="0">
                            </div>
                            <span class="team-name mt-2">{{ selectedFixture.away_team_name }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" @click="saveEditedResult" :disabled="loadingEdit">
                        {{ loadingEdit ? 'Saving...' : 'Save Result' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TeamStandings from "@/components/WeeklyScores/TeamStandings.vue";
import WeekResults from "@/components/WeeklyScores/WeekResults.vue";
import Swal from 'sweetalert2';
import ChampionshipPrediction from "@/components/Predictions/ChampionshipPrediction.vue";

export default {
    name: 'Teams',
    components: {ChampionshipPrediction, WeekResults, TeamStandings},
    data() {
        return {
            fixtures: [],
            teams: [],
            predictions: {},
            loadingFixtures: true,
            loadingTeams: true,
            currentWeek: 1,
            postRequestWeek: 1,
            buttonInactive: false,
            predictionEnabled: false,
            loadingPredictions: false,
            selectedFixture: null,
            homeScore: 0,
            awayScore: 0,
            loadingEdit: false,
            modal: null
        }
    },
    computed: {
        formattedPredictions() {
            if (!this.predictions.predictions) {
                return { predictions: [] };
            }

            return {
                ...this.predictions,
                predictions: this.predictions.predictions.map(prediction => ({
                    ...prediction,
                    championship_percentage: Number(prediction.championship_percentage).toFixed(2)
                }))
            };
        }
    },
    methods: {
        async simulateNextWeekFixture() {
            await axios.post(`/api/fixtures/simulate-week`, {week: this.postRequestWeek})
                .then(response => {
                    if (response.data.code === 200 && response.data.data.length > 0) {
                        const week = response.data.data[0].week;
                        if (week !== 1) {
                            this.currentWeek = week;
                        }
                        if (this.currentWeek >= 4 && this.currentWeek !== 6) {
                            this.predictionEnabled = true;
                            this.fetchCurrentWeekPredictions();
                        }
                        if (this.currentWeek === 6) {
                            this.predictionEnabled = false;
                        }
                        this.postRequestWeek++;
                        this.fetchCurrentWeekLeagueFixture();
                        this.fetchTeamStandings();
                    } else {
                        this.buttonInactive = true;
                        Swal.fire({
                            toast: true,
                            position: 'center',
                            icon: 'success',
                            title: response.data.message,
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
                    }
                    this.loadingFixtures = false;
                })
                .catch(error => {
                    console.error('Error fetching fixtures:', error);
                    this.loadingFixtures = false;
                });
        },
        async fetchTeamStandings() {
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
        async fetchCurrentWeekLeagueFixture() {
            this.loadingFixtures = true;
            console.log('Fetch: ', this.currentWeek);
            await axios.get(`/api/fixtures/${this.currentWeek}`)
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
        async fetchCurrentWeekPredictions() {
            this.loadingPredictions = true;
            await axios.get(`/api/predictions/${this.currentWeek}`)
                .then(response => {
                    if (response.data.code === 200 && response.data.data) {
                        this.predictions = response.data.data;
                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'center',
                            icon: 'error',
                            title: response.data.message || 'No prediction data available',
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
                    }
                    this.loadingPredictions = false;
                })
                .catch(error => {
                    this.loadingPredictions = false;
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
        openEditModal(fixture) {
            this.selectedFixture = fixture;
            this.homeScore = fixture.home_team_goals;
            this.awayScore = fixture.away_team_goals;

            if (window.bootstrap && !this.modal) {
                this.modal = new window.bootstrap.Modal(document.getElementById('editMatchModal'));
            }

            if (this.modal) {
                this.modal.show();
            } else {
                console.error('Bootstrap modal could not be initialized');
            }
        },
        async saveEditedResult() {
            if (this.homeScore < 0 || this.awayScore < 0) {
                await Swal.fire({
                    icon: 'error',
                    title: 'Invalid Score',
                    text: 'Scores cannot be negative'
                });
                return;
            }

            this.loadingEdit = true;

            const fixtureId = this.selectedFixture.fixture_id;
            const payload = {
                home_team_goals: this.homeScore,
                away_team_goals: this.awayScore
            };

            try {
                await axios.patch(`/api/matches/${fixtureId}`, payload);

                this.fixtures = this.fixtures.map(fixture => {
                    if (fixture.fixture_id === fixtureId) {
                        return {
                            ...fixture,
                            home_team_goals: this.homeScore,
                            away_team_goals: this.awayScore
                        };
                    }
                    return fixture;
                });
                if (this.selectedFixture.week >= 4 && this.selectedFixture.week !== 6) {
                    console.log('hooppala');
                    await this.fetchCurrentWeekPredictions();
                }
                await this.fetchTeamStandings();
                await Swal.fire({
                    toast: true,
                    position: 'center',
                    icon: 'success',
                    title: 'Match result updated successfully',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });

                this.modal.hide();

                if (this.predictionEnabled) {
                    await this.fetchCurrentWeekPredictions();
                }
            } catch (error) {
                await Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update match result'
                });
            } finally {
                this.loadingEdit = false;
            }
        }
    },
    mounted() {
        this.fetchCurrentWeekLeagueFixture();
        this.fetchTeamStandings();
        this.$nextTick(() => {
            if (window.bootstrap) {
                this.modal = new window.bootstrap.Modal(document.getElementById('editMatchModal'));
            } else {
                console.error('Bootstrap is not loaded. Make sure to include bootstrap.js');
            }
        });
    }
};
</script>
