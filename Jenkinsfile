pipeline {
    agent any
    
    environment {
        // Define environment variables
        DOCKER_HUB_CREDENTIALS = credentials('docker-hub-credentials')
        DOCKER_IMAGE_NAME = 'tpoor/apex'
        DOCKER_IMAGE_TAG = "${env.BUILD_NUMBER}"
        GIT_REPO_URL = 'https://github.com/TOR50/Apex-Ballistics'
    }
    
    triggers {
        // Enable webhook trigger
        githubPush()
    }
    
    stages {
        stage('Checkout') {
            steps {
                // Get code from GitHub repository
                checkout scm
                
                // Display information about the build
                sh 'echo "Building from repository: $GIT_REPO_URL"'
                sh 'echo "Branch: $GIT_BRANCH"'
                sh 'echo "Commit: $GIT_COMMIT"'
            }
        }
        
        stage('Build') {
            steps {
                // Build the Docker image
                sh "docker build -t ${DOCKER_IMAGE_NAME}:${DOCKER_IMAGE_TAG} -t ${DOCKER_IMAGE_NAME}:latest ."
            }
        }
        
        stage('Test') {
            steps {
                // Run tests inside a temporary container
                sh '''
                docker run --rm \
                -v "${WORKSPACE}:/var/www/html" \
                --entrypoint php \
                ${DOCKER_IMAGE_NAME}:${DOCKER_IMAGE_TAG} \
                artisan test
                '''
            }
        }
        
        stage('Push to Docker Hub') {
            steps {
                // Log in to Docker Hub
                sh 'echo $DOCKER_HUB_CREDENTIALS_PSW | docker login -u $DOCKER_HUB_CREDENTIALS_USR --password-stdin'
                
                // Push the Docker image
                sh "docker push ${DOCKER_IMAGE_NAME}:${DOCKER_IMAGE_TAG}"
                sh "docker push ${DOCKER_IMAGE_NAME}:latest"
                
                // Add tag information to build description
                script {
                    currentBuild.description = "Image: ${DOCKER_IMAGE_NAME}:${DOCKER_IMAGE_TAG}"
                }
            }
        }
        
        stage('Clean Up') {
            steps {
                // Remove local Docker images to save space
                sh "docker rmi ${DOCKER_IMAGE_NAME}:${DOCKER_IMAGE_TAG} ${DOCKER_IMAGE_NAME}:latest"
                sh 'docker logout'
            }
        }
    }
    
    post {
        always {
            // Clean up workspace
            cleanWs()
        }
        success {
            echo 'Build completed successfully!'
        }
        failure {
            echo 'Build failed!'
        }
    }
}
