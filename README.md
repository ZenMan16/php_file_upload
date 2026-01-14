# PHP Secure File Uploader (CI/CD Demo)

A professional-grade, containerized PHP application demonstrating a full SDLC (Software Development Life Cycle) pipeline.

## 🚀 Architecture
This project features a **Dual-Environment Stack** to simulate enterprise development:
* **Development Sandbox (Port 8082):** Apache-based environment for rapid iteration.
* **Staging Environment (Port 8081):** Nginx + PHP-FPM optimized stack.
* **CI/CD Controller:** Jenkins-orchestrated deployments with Docker-out-of-Docker (DooD) capabilities.

## 🛠 Tech Stack
* **Backend:** PHP 8.x
* **Web Servers:** Nginx, Apache
* **Orchestration:** Docker Compose
* **CI/CD:** Jenkins
* **VCS:** Git (following feature-branch naming conventions)

## 🔧 Key Features
* **Environment Isolation:** Uses Docker Project Names (`-p staging`) to prevent resource collisions.
* **Idempotent Deployments:** Automated cleanup of orphans and volumes during the build cycle.
* **Hardened Permissions:** Secure Docker socket management within the Jenkins container.

## 🚦 How to Run
1. Clone the repository.
2. Ensure Docker Desktop is running.
3. Run the deployment script:
   ```bash
   docker compose -p staging up -d --build
4. Access the app at http://localhost:8081.
