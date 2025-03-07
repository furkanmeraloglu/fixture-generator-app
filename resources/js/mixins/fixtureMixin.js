export default {
    data() {
        return {
            fixtures: [],
            loadingFixtures: true,
        }
    },
    methods: {
        async fetchLeagueFixtures() {
            this.loadingFixtures = true;
            await axios.get('/api/fixtures')
                .then(response => {
                    this.fixtures = response.data.data;
                    console.log(this.fixtures);
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
            this.fixtures = {};
            this.loadingFixtures = true;
            await axios.delete('/api/fixtures')
                .then(() => {
                    this.fetchLeagueFixtures();
                })
                .catch(error => {
                    console.error('Error resetting fixtures:', error);
                });
        }
    }
};
