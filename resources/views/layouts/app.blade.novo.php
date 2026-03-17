<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HCL Licitações - Plataforma de Licitação</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        /* Header */
        .header {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .logo::before {
            content: "";
            width: 8px;
            height: 30px;
            background: linear-gradient(45deg, #00d4aa, #00bfa5);
            margin-right: 10px;
            border-radius: 4px;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-menu a {
            text-decoration: none;
            color: #666;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-menu a:hover {
            color: #00d4aa;
        }

        .nav-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn-login {
            color: #666;
            text-decoration: none;
            font-weight: 500;
        }

        .btn-register {
            background: #00d4aa;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-register:hover {
            background: #00bfa5;
            transform: translateY(-1px);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            padding: 120px 2rem 80px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-content h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 2rem;
            line-height: 1.2;
        }

        .hero-content h1 .highlight {
            color: #00d4aa;
        }

        .hero-features {
            list-style: none;
            margin-bottom: 2rem;
        }

        .hero-features li {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .hero-features li::before {
            content: "✓";
            color: #00d4aa;
            font-weight: bold;
            margin-right: 1rem;
            font-size: 1.2rem;
        }

        .btn-primary {
            background: #00d4aa;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: #00bfa5;
            transform: translateY(-2px);
        }

        .hero-dashboard {
            background: rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .dashboard-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .dashboard-card {
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.2);
            transition: all 0.3s;
        }

        .dashboard-card:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }

        .dashboard-card .icon {
            width: 40px;
            height: 40px;
            background: #00d4aa;
            border-radius: 8px;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .dashboard-card h4 {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Features Section */
        .features {
            padding: 80px 2rem;
            background: #f8f9fa;
        }

        .features-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .features-intro {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 4rem;
            margin-bottom: 60px;
            align-items: center;
        }

        .features-intro h2 {
            font-size: 2.5rem;
            color: #2c3e50;
            line-height: 1.3;
        }

        .features-intro p {
            color: #666;
            font-size: 1.1rem;
        }

        .benefits {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-bottom: 60px;
        }

        .benefit-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .benefit-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }

        .benefit-card .icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, #00d4aa, #00bfa5);
            border-radius: 12px;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .benefit-card h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }

        .benefit-card p {
            color: #666;
            line-height: 1.6;
        }

        /* Tools Section */
        .tools {
            padding: 80px 2rem;
            background: white;
        }

        .tools-container {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .tools h2 {
            font-size: 2.5rem;
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .tools p {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 60px;
        }

        .tools-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 3rem;
        }

        .tool-card {
            text-align: center;
            padding: 2rem;
            border-radius: 12px;
            transition: all 0.3s;
        }

        .tool-card:hover {
            background: #f8f9fa;
            transform: translateY(-3px);
        }

        .tool-card .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #00d4aa, #00bfa5);
            border-radius: 16px;
            margin: 0 auto 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
        }

        .tool-card h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
            font-size: 1.4rem;
        }

        .tool-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .tool-link {
            color: #00d4aa;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .tool-link:hover {
            color: #00bfa5;
        }

        /* Pricing Section */
        .pricing {
            padding: 80px 2rem;
            background: #f8f9fa;
        }

        .pricing-container {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .pricing h2 {
            font-size: 2.5rem;
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .pricing p {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 60px;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
        }

        .pricing-card {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            text-align: left;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transition: all 0.3s;
            position: relative;
        }

        .pricing-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }

        .pricing-card.featured {
            background: #2c3e50;
            color: white;
            transform: scale(1.05);
        }

        .pricing-card.featured:hover {
            transform: scale(1.08) translateY(-5px);
        }

        .pricing-card h3 {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            color: inherit;
        }

        .pricing-features {
            list-style: none;
            margin-bottom: 2rem;
        }

        .pricing-features li {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .pricing-features li::before {
            content: "✓";
            color: #00d4aa;
            font-weight: bold;
            margin-right: 0.75rem;
            width: 16px;
        }

        .pricing-features li.unavailable::before {
            content: "✗";
            color: #e74c3c;
        }

        .pricing-btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .pricing-card:not(.featured) .pricing-btn {
            background: #00d4aa;
            color: white;
        }

        .pricing-card.featured .pricing-btn {
            background: #00d4aa;
            color: white;
        }

        .pricing-btn:hover {
            background: #00bfa5;
            transform: translateY(-2px);
        }

        /* Footer */
        .footer {
            background: #2c3e50;
            color: white;
            padding: 60px 2rem 40px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
        }

        .footer-brand h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .footer-brand h3::before {
            content: "";
            width: 6px;
            height: 24px;
            background: linear-gradient(45deg, #00d4aa, #00bfa5);
            margin-right: 8px;
            border-radius: 3px;
        }

        .footer-brand p {
            color: #bdc3c7;
            line-height: 1.6;
        }

        .footer-section h4 {
            margin-bottom: 1.5rem;
            color: white;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: #bdc3c7;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: #00d4aa;
        }

        .footer-bottom {
            margin-top: 40px;
            padding-top: 40px;
            border-top: 1px solid #34495e;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .social-links {
            display: flex;
            gap: 1rem;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            background: #34495e;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #bdc3c7;
            text-decoration: none;
            transition: all 0.3s;
        }

        .social-links a:hover {
            background: #00d4aa;
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-container,
            .features-intro,
            .benefits,
            .tools-grid,
            .pricing-grid,
            .footer-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .hero-content h1 {
                font-size: 2rem;
            }

            .dashboard-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .nav-menu {
                display: none;
            }
        }
    </style>
</head>
<body>

    @yield('content')

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll effect to header
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(255, 255, 255, 0.95)';
                header.style.backdropFilter = 'blur(10px)';
            } else {
                header.style.background = 'white';
                header.style.backdropFilter = 'none';
            }
        });

        // Animate cards on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all cards
        document.querySelectorAll('.benefit-card, .tool-card, .pricing-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease-out';
            observer.observe(card);
        });

        // Dashboard cards hover effect
        document.querySelectorAll('.dashboard-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.05)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Button click effects
        document.querySelectorAll('.btn-primary, .pricing-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                // Create ripple effect
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;

                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.style.position = 'absolute';
                ripple.style.borderRadius = '50%';
                ripple.style.background = 'rgba(255, 255, 255, 0.3)';
                ripple.style.transform = 'scale(0)';
                ripple.style.animation = 'ripple 0.6s linear';
                ripple.style.pointerEvents = 'none';

                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);

                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Add CSS for ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
