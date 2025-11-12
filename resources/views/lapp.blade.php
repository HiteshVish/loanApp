<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>lapp - Download</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        :root {
            --android-green: #3DDC84;
            --android-green-dark: #2DB572;
            --android-dark: #202124;
            --android-gray: #5F6368;
            --android-light-gray: #F8F9FA;
            --card-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            --hover-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
            background-color: #fff;
            color: var(--android-dark);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        header {
            background-color: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            font-size: 20px;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background-color: var(--android-green);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .app-header {
            padding: 40px 0 30px;
            background-color: var(--android-light-gray);
        }

        .app-info {
            display: flex;
            gap: 24px;
            align-items: flex-start;
        }

        .app-icon {
            width: 120px;
            height: 120px;
            background-color: white;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--card-shadow);
            flex-shrink: 0;
            padding: 8px;
        }

        .app-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .app-details {
            flex: 1;
        }

        .app-title {
            font-size: 28px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .app-tagline {
            font-size: 18px;
            color: var(--android-gray);
            margin-bottom: 12px;
        }

        .app-developer {
            font-size: 16px;
            color: var(--android-gray);
            margin-bottom: 20px;
        }

        .download-btn {
            background-color: var(--android-green);
            color: white;
            border: none;
            border-radius: 24px;
            padding: 12px 32px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: var(--card-shadow);
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .download-btn:hover {
            background-color: var(--android-green-dark);
            box-shadow: var(--hover-shadow);
            transform: translateY(-2px);
        }

        .app-specs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .spec-card {
            background-color: white;
            border-radius: 12px;
            padding: 16px;
            box-shadow: var(--card-shadow);
        }

        .spec-title {
            font-size: 14px;
            color: var(--android-gray);
            margin-bottom: 8px;
        }

        .spec-value {
            font-size: 16px;
            font-weight: 500;
        }

        .section {
            margin: 40px 0;
        }

        .section-title {
            font-size: 22px;
            font-weight: 500;
            margin-bottom: 20px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }

        .screenshots {
            display: flex;
            gap: 16px;
            overflow-x: auto;
            padding: 8px 0 16px;
            scrollbar-width: thin;
        }

        .screenshot {
            width: 240px;
            height: 426px;
            border-radius: 12px;
            background-color: #f0f0f0;
            flex-shrink: 0;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }

        .screenshot img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .content-card {
            background-color: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: var(--card-shadow);
            margin-bottom: 20px;
        }

        .content-card h3 {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .content-card p {
            margin-bottom: 16px;
            color: #444;
        }

        .checksum {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 16px;
            font-family: monospace;
            word-break: break-all;
            margin: 16px 0;
            font-size: 14px;
        }

        .integrity-note {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--android-gray);
            font-size: 14px;
        }

        .badges {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 20px;
        }

        .badge {
            background-color: #f1f3f4;
            border-radius: 16px;
            padding: 8px 16px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        footer {
            background-color: var(--android-light-gray);
            padding: 40px 0;
            margin-top: 60px;
            border-top: 1px solid #e8eaed;
        }

        .footer-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
        }

        .footer-badges {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .footer-badge {
            background-color: white;
            border-radius: 20px;
            padding: 12px 20px;
            font-weight: 500;
            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .copyright {
            color: var(--android-gray);
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .app-info {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .app-details {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .app-specs {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="material-icons">android</i>
                    </div>
                    <span>Android Apps</span>
                </div>
            </div>
        </div>
    </header>

    <section class="app-header">
        <div class="container">
            <div class="app-info">
                <div class="app-icon">
                    <img src="{{ asset('assets/ddfc2.png') }}" alt="lapp Icon">
                </div>
                <div class="app-details">
                    <h1 class="app-title">lapp</h1>
                    <p class="app-tagline">Quick and easy loan application</p>
                    <p class="app-developer">Developed by Sofftsolution technology</p>
                    <a href="{{ asset('assets/app-release.apk') }}" class="download-btn" download="lapp.apk">
                        <i class="material-icons">file_download</i>
                        Download APK (49.3 MB)
                    </a>
                </div>
            </div>
            <div class="app-specs">
                <div class="spec-card">
                    <div class="spec-title">Version</div>
                    <div class="spec-value">2.4.1</div>
                </div>
                <div class="spec-card">
                    <div class="spec-title">Size</div>
                    <div class="spec-value">49.3 MB</div>
                </div>
                <div class="spec-card">
                    <div class="spec-title">Last Updated</div>
                    <div class="spec-value">November 12, 2025</div>
                </div>
                <div class="spec-card">
                    <div class="spec-title">Android Required</div>
                    <div class="spec-value">8.0 and up</div>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <section class="section">
            <h2 class="section-title">Screenshots</h2>
            <div class="screenshots">
                <div class="screenshot">
                    <img src="{{ asset('assets/img1.jpeg') }}" alt="Screenshot 1">
                </div>
                <div class="screenshot">
                    <img src="{{ asset('assets/img2.jpeg') }}" alt="Screenshot 2">
                </div>
                <div class="screenshot">
                    <img src="{{ asset('assets/img3.jpeg') }}" alt="Screenshot 3">
                </div>
                <div class="screenshot">
                    <img src="{{ asset('assets/img4.jpeg') }}" alt="Screenshot 4">
                </div>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">About this app</h2>
            <div class="content-card">
                <h3>Description</h3>
                <p>lapp is a comprehensive loan application platform that makes applying for loans quick, easy, and secure. Get instant loan approvals, track your application status, and manage your loan payments all from your mobile device.</p>
                <p>Features include:</p>
                <ul style="margin-left: 20px; margin-bottom: 16px;">
                    <li>Quick and easy loan application process</li>
                    <li>Real-time application status tracking</li>
                    <li>Secure document upload and verification</li>
                    <li>Payment reminders and transaction history</li>
                    <li>User-friendly interface with intuitive navigation</li>
                </ul>
                <div class="badges">
                    <div class="badge">
                        <i class="material-icons" style="color: var(--android-green); font-size: 16px;">lock</i>
                        Secure & Encrypted
                    </div>
                    <div class="badge">
                        <i class="material-icons" style="color: var(--android-green); font-size: 16px;">account_circle</i>
                        Google Login
                    </div>
                    <div class="badge">
                        <i class="material-icons" style="color: var(--android-green); font-size: 16px;">cloud</i>
                        Internet Required
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">What's New</h2>
            <div class="content-card">
                <h3>Version 2.4.1</h3>
                <p>Latest updates and improvements:</p>
                <ul style="margin-left: 20px; margin-bottom: 16px;">
                    <li>Added support for Android 13's new permission system</li>
                    <li>Improved performance when loading loan applications</li>
                    <li>Fixed issue with Google login authentication on some devices</li>
                    <li>Added option to download loan statements as PDF</li>
                    <li>Minor UI improvements and bug fixes</li>
                </ul>
                <p><strong>Version 2.4.0</strong> - Added dark theme, improved payment processing, and added loan categories.</p>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">Security & Verification</h2>
            <div class="content-card">
                <h3>APK Integrity</h3>
                <p>Verify the integrity of your download using the SHA-256 checksum below:</p>
                <div class="checksum">
                    9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08
                </div>
                <div class="integrity-note">
                    <i class="material-icons" style="color: var(--android-green);">verified</i>
                    <span>This APK is signed and verified by Sofftsolution technology</span>
                </div>
            </div>
        </section>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-badges">
                    <div class="footer-badge">
                        <i class="material-icons" style="color: var(--android-green);">verified_user</i>
                        Signed APK
                    </div>
                    <div class="footer-badge">
                        <i class="material-icons" style="color: var(--android-green);">download</i>
                        Direct Download
                    </div>
                    <div class="footer-badge">
                        <i class="material-icons" style="color: var(--android-green);">apps</i>
                        Single App Distribution
                    </div>
                </div>
                <div class="copyright">
                    © 2023 Sofftsolution technology. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Simple screenshot carousel functionality
        document.addEventListener('DOMContentLoaded', function() {
            const screenshotsContainer = document.querySelector('.screenshots');
            let isDown = false;
            let startX;
            let scrollLeft;

            screenshotsContainer.addEventListener('mousedown', (e) => {
                isDown = true;
                screenshotsContainer.classList.add('active');
                startX = e.pageX - screenshotsContainer.offsetLeft;
                scrollLeft = screenshotsContainer.scrollLeft;
            });

            screenshotsContainer.addEventListener('mouseleave', () => {
                isDown = false;
                screenshotsContainer.classList.remove('active');
            });

            screenshotsContainer.addEventListener('mouseup', () => {
                isDown = false;
                screenshotsContainer.classList.remove('active');
            });

            screenshotsContainer.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - screenshotsContainer.offsetLeft;
                const walk = (x - startX) * 2;
                screenshotsContainer.scrollLeft = scrollLeft - walk;
            });

            // Download button animation
            const downloadBtn = document.querySelector('.download-btn');
            downloadBtn.addEventListener('click', function(e) {
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="material-icons">check</i> Download Started';
                this.style.backgroundColor = '#2DB572';
                
                // Reset after download starts
                setTimeout(() => {
                    this.innerHTML = originalHTML;
                    this.style.backgroundColor = '';
                }, 2000);
            });
        });
    </script>
</body>
</html>