# Fixture Generator App

## Project Overview

Fixture Generator App is a football fixture simulation application. By default, it automatically generates a double round-robin fixture for 4 teams with different overall strength ratings (you can adjust the number of teams through DatabaseSeeder.php according to your preferences). The application simulates matches based on these fixtures and calculates championship probability percentages for each team starting from the 4th week (based on the default 4-team setup).

### Key Features

- **Automatic Fixture Generation**: Creates a balanced double round-robin tournament schedule
- **Match Simulation**: Simulates matches based on multiple factors
- **Championship Probability Calculator**: Predicts each team's chances of winning the championship

### Simulation Factors

The match simulation algorithm takes into account several factors:
- Team overall strength ratings
- Home/away fan support multiplier
- Teams' previous performance (based on goals scored and conceded)
- Goalkeeper performance probability (poor/normal/excellent)

These factors collectively determine the number of scoring opportunities for both home and away teams, leading to the final match result prediction.

### Championship Prediction Algorithm

Championship expectation percentages are calculated based on:
- Teams' performance in previous weeks
- Points gap with the leading team
- Simulation of potential results in remaining fixtures

This comprehensive approach provides realistic predictions that evolve as the tournament progresses.

### With Fixture Generator App, you can:

- **Custom Team Selection**: Create double-round robin fixtures with your selected teams
- **Flexible Match Simulation**: Simulate matches week by week or all at once
- **Manual Result Adjustment**: Manually change any match result as needed
- **Reset Functionality**: Reset the fixture and create a new one after all matches have been simulated

## Tech Stack
### Backend (API)
- PHP 8.2+
- Laravel 12.0
### Frontend
- Vue.js 3.5.13
- Vue Router 4.5.0
- Vite 6.2.0
### Development Tools
- Composer
- NPM
- Laravel Sail
- PHPUnit 11.5.3
## Requirements
- PHP 8.2 or higher
- Composer
- Node.js and NPM

## Installation

### Clone the repository
```bash
    git clone git@github.com:furkanmeraloglu/fixture-generator-app.git
    cd fixture-generator-app
```

### Install Composer Dependencies
```bash
    composer install
```

### Set Up .env File
```bash
    cp .env.example .env
```

### Install Laravel Sail
```bash
    ./vendor/bin/sail install
    ./vendor/bin/sail up -d # Start the development server
```

### Migrate the DB and Seed Dummy Data
```bash
    ./vendor/bin/sail artisan migrate:fresh --seed
```

### Generate Application Key
```bash
    ./vendor/bin/sail artisan key:generate
```

### Install NPM Dependencies
```bash
    npm install
    npm run build
    npm run dev
```

### To Run Unit Tests
```bash
    ./vendor/bin/sail artisan test
```
