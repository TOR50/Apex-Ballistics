# Apex Ballistics CI/CD Pipeline

This repository contains a complete CI/CD pipeline for the Apex Ballistics project using Jenkins, Docker, and GitHub integration.

## Architecture

- **GitHub Repository**: [https://github.com/TOR50/Apex-Ballistics](https://github.com/TOR50/Apex-Ballistics)
- **Docker Hub Repository**: [https://hub.docker.com/repository/docker/tpoor/apex/general](https://hub.docker.com/repository/docker/tpoor/apex/general)
- **CI/CD Tool**: Jenkins (local instance exposed via Cloudflare Tunnel)
- **Build Trigger**: GitHub webhooks

## Pipeline Steps

1. **Checkout**: Pull the latest code from GitHub
2. **Build**: Create a Docker image for the application
3. **Test**: Run automated tests within the container
4. **Push**: Upload the Docker image to Docker Hub
5. **Clean Up**: Remove temporary resources

## Setup Instructions

### Jenkins Plugins

Ensure you have these plugins installed in Jenkins:
- Docker Pipeline
- Git Integration
- GitHub Integration
- Pipeline
- Credentials Binding

### Jenkins Credentials

1. Add Docker Hub credentials:
   - Kind: Username with password
   - ID: docker-hub-credentials
   - Username: Your Docker Hub username
   - Password: Your Docker Hub password/token

2. Add GitHub credentials:
   - Kind: Username with password
   - ID: github-credentials
   - Username: Your GitHub username
   - Password: Your GitHub personal access token

### Cloudflare Tunnel

Follow the instructions in `docs/cloudflare_tunnel_setup.md` to set up Cloudflare Tunnel.

### GitHub Webhook

Run the script `scripts/setup_webhook.sh` for instructions on setting up the GitHub webhook.

## Usage

Once set up, the pipeline will automatically trigger whenever code is pushed to the GitHub repository. You can also manually trigger builds from the Jenkins dashboard.

## Troubleshooting

Common issues:
- GitHub webhook not working: Check webhook logs in GitHub repository settings
- Jenkins not receiving webhooks: Verify Cloudflare Tunnel configuration
- Docker build failures: Check Docker daemon status and disk space
- Docker push failures: Verify Docker Hub credentials in Jenkins

## Maintenance

- Regularly update Jenkins plugins
- Rotate credentials periodically
- Monitor disk space on the Jenkins server
- Keep Docker and Cloudflared up to date
