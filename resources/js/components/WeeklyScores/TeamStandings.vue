<template>
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">League Table</h5>
            </div>
            <div class="card-body">
                <div v-if="loading" class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div v-else class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Teams</th>
                            <th>PTS</th>
                            <th>P</th>
                            <th>W</th>
                            <th>D</th>
                            <th>L</th>
                            <th>GD</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="team in teams" :key="team.id">
                            <td>{{ team.name }}</td>
                            <td>{{ team.points }}</td>
                            <td>{{ team.played_matches }}</td>
                            <td>{{ team.wins }}</td>
                            <td>{{ team.draws }}</td>
                            <td>{{ team.losses }}</td>
                            <td>{{ team.team_total_score_average }}</td>
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
    name: 'TeamStandings',
    data() {
        return {
            teams: [],
            loading: true,
        }
    },
    mounted() {
        this.fetchTeams();
    },
    methods: {
        async fetchTeams() {
            this.loading = true;
            await axios.get('/api/teams')
                .then(response => {
                    this.teams = response.data.data;
                    this.loading = false;
                })
                .catch(error => {
                    console.error('Error fetching teams:', error);
                    this.loading = false;
                });
        },
    }
};
</script>
