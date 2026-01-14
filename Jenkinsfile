pipeline {
    agent any

    stages {
        stage('Cleanup Environment') {
            steps {
                // clear the staging area to ensure a clean slate
                sh 'docker compose -p staging down -v --remove-orphans'
            }
        }
        stage('Build & Deploy') {
            steps {
                // build the images and start the containers
                sh 'docker compose -p staging up -d --build'
            }
        }
        stage('Verify Health') {
            steps {
                // check to see if containers are running
                sh 'docker ps'
            }
        }
    }
}