<template>
  <div>
    <div class="container mt-5">
      <div class="row justify-content-center">
        <!-- League Table -->
        <TeamStandings/>
        <WeekResults/>
      </div>
    </div>
  </div>
</template>

<script>
import TeamStandings from "./weeklyScores/TeamStandings.vue";
import WeekResults from "./weeklyScores/WeekResults.vue";

export default {
  name: 'Teams',
  components: {WeekResults, TeamStandings},
};
</script>
