<template>
    <div class="col-md-12 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Tournament Teams</h5>
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
                            <th>Overall</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="team in teams" :key="team.team_id">
                            <td>{{ team.name }}</td>
                            <td>{{ team.strength }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-4 p-4">
                <router-link to="/fixtures?generate=true" class="btn btn-primary">Generate League Fixtures</router-link>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'Teams',
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
