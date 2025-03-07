export default {
    data() {
        return {
            teams: [],
            loadingTeams: true,
        }
    },
    methods: {
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
        }
    }
};
