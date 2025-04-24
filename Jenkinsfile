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
                bat 'echo Building from repository: %GIT_REPO_URL%'
                bat 'echo Branch: %GIT_BRANCH%'
                bat 'echo Commit: %GIT_COMMIT%'
            }
        }
        
        stage('Build') {
            steps {
                // Build the Docker image
                bat "docker build -t %DOCKER_IMAGE_NAME%:%DOCKER_IMAGE_TAG% -t %DOCKER_IMAGE_NAME%:latest ."
            }
        }
        
    stage('Test') {
    steps {
        // Build a test image with dev dependencies
        bat "docker build -t %DOCKER_IMAGE_NAME%:%DOCKER_IMAGE_TAG%-test -f Dockerfile.test ."
        
        // Run tests using this image
        bat 'docker run --rm --entrypoint ./vendor/bin/phpunit %DOCKER_IMAGE_NAME%:%DOCKER_IMAGE_TAG%-test'
    }
}
        
        stage('Push to Docker Hub') {
            steps {
                // Log in to Docker Hub
                bat 'echo %DOCKER_HUB_CREDENTIALS_PSW% | docker login -u %DOCKER_HUB_CREDENTIALS_USR% --password-stdin'
                
                // Push the Docker image
                bat "docker push %DOCKER_IMAGE_NAME%:%DOCKER_IMAGE_TAG%"
                bat "docker push %DOCKER_IMAGE_NAME%:latest"
                
                // Add tag information to build description
                script {
                    currentBuild.description = "Image: ${DOCKER_IMAGE_NAME}:${DOCKER_IMAGE_TAG}"
                }
            }
        }
        
        stage('Clean Up') {
            steps {
                // Remove local Docker images to save space
                bat "docker rmi %DOCKER_IMAGE_NAME%:%DOCKER_IMAGE_TAG% %DOCKER_IMAGE_NAME%:latest"
                bat 'docker logout'
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