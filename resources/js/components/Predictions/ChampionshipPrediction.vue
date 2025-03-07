<template>
    <div class="col-md-12 mb-4">
        <div class="card prediction-card">
            <div class="card-header bg-gray text-black">
                <h5 class="mb-0">Championship Predictions - Week {{ currentWeek }}</h5>
            </div>
            <div class="card-body">
                <div v-if="loadingPredictions" class="text-center p-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div v-else-if="!predictions.predictions.length" class="text-center p-3">
                    No predictions available for this week.
                </div>
                <div v-else class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Team</th>
                            <th>Championship Chance</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(prediction, index) in predictions.predictions" :key="index">
                            <td>{{ prediction.team }}</td>
                            <td>{{ prediction.championship_percentage }}%</td>
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
    name: 'ChampionshipPrediction',
    props: {
        currentWeek: {
            type: Number,
            required: true
        },
        predictions: {
            type: Object,
            default: () => {}
        },
        loadingPredictions: {
            type: Boolean,
            default: false
        }
    },
};
</script>
