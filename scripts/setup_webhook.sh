#!/bin/bash

# GitHub webhook setup script
# This script provides guidance on setting up GitHub webhook for Jenkins integration

echo "====== GitHub Webhook Setup Guide ======"
echo
echo "1. Go to your GitHub repository: https://github.com/TOR50/Apex-Ballistics"
echo "2. Click on 'Settings' tab"
echo "3. Select 'Webhooks' from the left menu"
echo "4. Click on 'Add webhook'"
echo
echo "5. Configure the webhook with these settings:"
echo "   - Payload URL: https://jenkins.yourdomain.com/github-webhook/"
echo "   - Content type: application/json"
echo "   - Secret: <create a secure secret>"
echo "   - Events: Select 'Just the push event'"
echo "   - Active: Check this box"
echo
echo "6. Click 'Add webhook' to save"
echo
echo "====== Jenkins GitHub Plugin Setup ======"
echo
echo "1. Open Jenkins: http://localhost:8080"
echo "2. Go to 'Manage Jenkins' > 'Configure System'"
echo "3. Find the 'GitHub' section"
echo "4. Add GitHub Server:"
echo "   - Name: GitHub"
echo "   - API URL: https://api.github.com"
echo "   - Credentials: Add your GitHub credentials"
echo "5. Save the configuration"
echo
echo "====== Pushing Code to GitHub - Step by Step ======"
echo
echo "First time setup:"
echo "1. Initialize Git repository (if not already done):"
echo "   $ cd /path/to/your/project"
echo "   $ git init"
echo
echo "2. Configure Git identity (if not already done):"
echo "   $ git config --global user.name \"Your Name\""
echo "   $ git config --global user.email \"your.email@example.com\""
echo
echo "3. Add the remote repository:"
echo "   $ git remote add origin https://github.com/TOR50/Apex-Ballistics.git"
echo
echo "For every push:"
echo "1. Check the status of your changes:"
echo "   $ git status"
echo
echo "2. Add files to staging area:"
echo "   $ git add ."                        # Add all files"
echo "   $ git add specific-file.txt         # Add specific file"
echo
echo "3. Commit your changes:"
echo "   $ git commit -m \"Your commit message describing the changes\""
echo
echo "4. Push to GitHub:"
echo "   $ git push -u origin main           # First time push to main branch"
echo "   $ git push                          # Subsequent pushes"
echo
echo "If you're working with branches:"
echo "1. Create and switch to a new branch:"
echo "   $ git checkout -b feature-branch-name"
echo
echo "2. Push the branch to GitHub:"
echo "   $ git push -u origin feature-branch-name"
echo
echo "3. Create a Pull Request on GitHub to merge your changes"
echo
echo "Your Jenkins instance will now automatically build when code is pushed to GitHub!"

# Make the script executable
chmod +x "$0"
