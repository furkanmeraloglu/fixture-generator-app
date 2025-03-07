<template>
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Fixtures - Week {{ currentWeek }}</h5>
            </div>
            <div class="card-body">
                <div v-if="loadingFixtures" class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div v-else-if="fixtures.length === 0" class="text-center">
                    <p>No fixtures available</p>
                </div>
                <div v-else class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Home Team</th>
                            <th>Score</th>
                            <th>Away Team</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="fixture in fixtures" :key="fixture.fixture_id">
                            <td class="text-end">{{ fixture.home_team.name }}</td>
                            <td class="text-center">
                                <span v-if="fixture.is_played">{{ fixture.home_team_goals }} - {{ match.away_team_goals }}</span>
                                <span v-else>vs</span>
                            </td>
                            <td>{{ fixture.away_team.name }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'WeekResults',
    data() {
        return {
            fixtures: [],
            loadingFixtures: true,
            currentWeek: 1,
        }
    },
    mounted() {
        this.fetchCurrentWeekFixtures();
    },
    methods: {
        async fetchCurrentWeekFixtures() {
            this.loadingFixtures = true;
            await axios.get(`/api/fixtures/${this.currentWeek}`)
                .then(response => {
                    if (response.data.data && response.data.data.length > 0) {
                        this.currentWeek = response.data.data[0].week || 1;
                        this.fixtures = response.data.data;
                    } else {
                        this.fixtures = [];
                    }
                    this.loadingFixtures = false;
                })
                .catch(error => {
                    console.error('Error fetching fixtures:', error);
                    this.loadingFixtures = false;
                });
        }
    }
};
</script>
