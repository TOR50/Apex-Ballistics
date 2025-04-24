# Setting up Cloudflare Tunnel for Jenkins

This guide will walk you through setting up Cloudflare Tunnel to expose your local Jenkins server securely to the internet using Cloudflare's free temporary domains.

## Prerequisites

1. A Cloudflare account (free)
2. Jenkins installed and running locally on port 8080
3. cloudflared CLI installed on your machine

## Step 1: Install cloudflared

### Windows
```powershell
# Using chocolatey
choco install cloudflared

# Or download the installer from Cloudflare website
```

### Mac
```bash
brew install cloudflare/cloudflare/cloudflared
```

### Linux
```bash
# Download the latest version
curl -L --output cloudflared.deb https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64.deb

# Install the package
sudo dpkg -i cloudflared.deb
```

## Step 2: Start a quick tunnel

Instead of creating a permanent tunnel with a custom domain, we can use Cloudflare's free temporary domains:

```bash
cloudflared tunnel --url http://localhost:8080
```

This command will create a tunnel and generate a random subdomain on `trycloudflare.com` that points to your local Jenkins instance running on port 8080. The output will look something like:

```
2023-05-10T12:34:56Z INF Cannot determine default configuration path. No file [config.yml config.yaml] in [~/.cloudflared ~/.cloudflare-warp ~/cloudflare-warp /etc/cloudflared /usr/local/etc/cloudflared]
2023-05-10T12:34:56Z INF Starting tunnel tunnelID=xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
2023-05-10T12:34:56Z INF Version 2023.5.0
2023-05-10T12:34:56Z INF GOOS: windows, GOVersion: go1.20.3, GoArch: amd64
2023-05-10T12:34:56Z INF Settings: map[url:http://localhost:8080]
2023-05-10T12:34:56Z INF Generated Connector ID: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
2023-05-10T12:34:56Z INF Initial protocol quic
2023-05-10T12:34:56Z INF Starting metrics server on 127.0.0.1:38173/metrics
2023-05-10T12:34:56Z INF Connection established connIndex=0 connection=xxxxxxxxxxxxxxx
2023-05-10T12:34:56Z INF Register tunnel connection connIndex=0 connection=xxxxxxxxx
2023-05-10T12:34:58Z INF Tunnel registration complete
2023-05-10T12:34:58Z INF Connected to https://random-words.trycloudflare.com
```

The URL (like `https://random-words.trycloudflare.com`) is your public Jenkins address.

## Step 3: Setting up GitHub webhooks with temporary domains

Since the domain changes each time you start the tunnel, you'll need to update your GitHub webhook URL after each restart:

1. Start your Cloudflare tunnel
2. Copy the generated domain (e.g., `https://random-words.trycloudflare.com`)
3. Go to your GitHub repository: https://github.com/TOR50/Apex-Ballistics
4. Navigate to Settings → Webhooks
5. Update the Payload URL to: `https://random-words.trycloudflare.com/github-webhook/`
6. Set content type to `application/json`
7. Save the webhook

## Step 4: Run as a background process

### Windows
To run the tunnel in the background, you can create a simple batch file:

```batch
@echo off
start /b cloudflared tunnel --url http://localhost:8080 > tunnel.log 2>&1
echo Tunnel started! Check tunnel.log for the generated URL.
```

### Linux/Mac
```bash
nohup cloudflared tunnel --url http://localhost:8080 > tunnel.log 2>&1 &
echo "Tunnel started! Check tunnel.log for the generated URL."
```

## Step 5: Creating a persistent tunnel (optional)

If you frequently restart tunnels and need to avoid updating webhooks, consider creating a script to:
1. Start a tunnel
2. Extract the generated domain
3. Automatically update your webhook using the GitHub API

## Security Recommendations

1. Enable Jenkins security
2. Set up proper authentication
3. Use the GitHub webhook secret
4. Configure proper permissions in Jenkins
5. Remember that while convenient, the generated domains are public - anyone with the URL can access your Jenkins
