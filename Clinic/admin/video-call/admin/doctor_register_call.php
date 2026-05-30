<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Online Video Consultation</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: 'Segoe UI', sans-serif;
}

body{
    background: linear-gradient(135deg, #0a1931, #1a1a2e, #16213e);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    overflow-x: hidden;
    position: relative;
}

/* Particle Background */
.particles {
    position: absolute;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 0;
}

.particle {
    position: absolute;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: floatParticle 20s infinite linear;
}

/* Glowing Effects */
.glow {
    position: absolute;
    width: 500px;
    height: 500px;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.3;
    z-index: 0;
}

.glow-1 {
    background: #0d47a1;
    top: -200px;
    right: -200px;
    animation: glowMove1 15s infinite alternate ease-in-out;
}

.glow-2 {
    background: #1565c0;
    bottom: -200px;
    left: -200px;
    animation: glowMove2 15s infinite alternate-reverse ease-in-out;
}

.container{
    width:90%;
    max-width:1400px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 30px;
    overflow: hidden;
    box-shadow: 0 25px 70px rgba(0, 0, 0, 0.5);
    animation: fadeIn 1.5s ease-out;
    position: relative;
    z-index: 1;
    backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

/* HEADER */
.header{
    background: linear-gradient(90deg, rgba(13, 71, 161, 0.9), rgba(21, 101, 192, 0.9));
    padding: 60px 40px;
    color: #fff;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="rgba(255,255,255,0.1)"/></svg>');
    background-size: cover;
    animation: headerSlide 20s infinite linear;
}

.header h1{
    font-size: 3.5rem;
    margin-bottom: 15px;
    text-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    position: relative;
    z-index: 1;
    animation: textGlow 2s infinite alternate;
}

.header p{
    margin-top: 10px;
    opacity: 0.9;
    font-size: 1.2rem;
    position: relative;
    z-index: 1;
    animation: fadeInUp 1s ease-out 0.5s both;
}

/* MAIN */
.main{
    display: flex;
    padding: 60px 40px;
    gap: 40px;
    flex-wrap: wrap;
    position: relative;
    z-index: 1;
}

/* CARD */
.card{
    flex: 1;
    min-width: 320px;
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
    border-radius: 25px;
    padding: 40px 30px;
    text-align: center;
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transform-style: preserve-3d;
    perspective: 1000px;
}

.card::before{
    content:"";
    position:absolute;
    width: 150%;
    height: 5px;
    background: linear-gradient(90deg, #2196f3, #4caf50, #2196f3);
    top:0;
    left: -25%;
    animation: borderFlow 3s infinite linear;
    transform-origin: left;
}

.card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
    transform: translateX(-100%);
    transition: transform 0.6s;
}

.card:hover::after {
    transform: translateX(100%);
}

.card:hover{
    transform: translateY(-20px) rotateX(5deg) rotateY(5deg);
    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4),
                0 0 100px rgba(33, 150, 243, 0.3),
                inset 0 0 20px rgba(255, 255, 255, 0.1);
}

.card i{
    font-size: 80px;
    margin-bottom: 25px;
    position: relative;
    display: inline-block;
    animation: bounce 3s infinite ease-in-out;
}

.patient-icon {
    background: linear-gradient(45deg, #2196f3, #21cbf3);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: pulseGlow 2s infinite alternate;
}

.doctor-icon {
    background: linear-gradient(45deg, #4caf50, #8bc34a);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: pulseGlow 2s infinite alternate 0.5s;
}

.card h2{
    margin-bottom: 15px;
    color: #fff;
    font-size: 2.2rem;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.card p{
    color: rgba(255, 255, 255, 0.7);
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 25px;
}

.card button{
    margin-top: 20px;
    padding: 16px 35px;
    border: none;
    border-radius: 50px;
    background: linear-gradient(45deg, #1565c0, #2196f3);
    color: #fff;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    z-index: 1;
    box-shadow: 0 10px 20px rgba(33, 150, 243, 0.3);
}

.card button::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, #0d47a1, #1565c0);
    transform: translateX(-100%);
    transition: transform 0.4s;
    z-index: -1;
}

.card button:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 15px 30px rgba(33, 150, 243, 0.4),
                0 0 50px rgba(33, 150, 243, 0.2);
}

.card button:hover::before {
    transform: translateX(0);
}

.card button i {
    font-size: 18px;
    margin-left: 10px;
    animation: none;
}

/* FOOTER */
.footer{
    text-align: center;
    padding: 30px 20px;
    background: rgba(13, 71, 161, 0.8);
    color: white;
    position: relative;
    overflow: hidden;
}

.footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: repeating-linear-gradient(
        45deg,
        transparent,
        transparent 10px,
        rgba(255,255,255,0.05) 10px,
        rgba(255,255,255,0.05) 20px
    );
    animation: slideRight 20s infinite linear;
}

/* ANIMATIONS */
@keyframes fadeIn{
    from{opacity:0;transform:translateY(50px) scale(0.9);}
    to{opacity:1;transform:translateY(0) scale(1);}
}

@keyframes bounce{
    0%, 100%{transform:translateY(0) rotate(0deg);}
    50%{transform:translateY(-20px) rotate(5deg);}
}

@keyframes pulseGlow{
    0%{filter:drop-shadow(0 0 5px currentColor);}
    100%{filter:drop-shadow(0 0 20px currentColor);}
}

@keyframes borderFlow{
    0%{transform:translateX(-100%);}
    100%{transform:translateX(100%);}
}

@keyframes textGlow{
    0%{text-shadow:0 5px 15px rgba(0, 0, 0, 0.3);}
    100%{text-shadow:0 5px 25px rgba(33, 150, 243, 0.5),
                    0 0 50px rgba(33, 150, 243, 0.3);}
}

@keyframes floatParticle {
    0% {
        transform: translateY(100vh) translateX(0) rotate(0deg);
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    90% {
        opacity: 1;
    }
    100% {
        transform: translateY(-100px) translateX(100px) rotate(360deg);
        opacity: 0;
    }
}

@keyframes glowMove1 {
    0% { transform: translate(0, 0) scale(1); }
    100% { transform: translate(100px, 100px) scale(1.2); }
}

@keyframes glowMove2 {
    0% { transform: translate(0, 0) scale(1); }
    100% { transform: translate(-100px, -100px) scale(1.2); }
}

@keyframes headerSlide {
    0% { transform: translateX(0) translateY(0); }
    100% { transform: translateX(-100px) translateY(-50px); }
}

@keyframes slideRight {
    0% { transform: translateX(0); }
    100% { transform: translateX(100px); }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Floating Elements */
.floating-element {
    position: absolute;
    font-size: 24px;
    color: rgba(255, 255, 255, 0.1);
    animation: floatRandom 20s infinite linear;
    z-index: 0;
}

/* Responsive */
@media(max-width: 768px){
    .header{
        padding: 40px 20px;
    }
    
    .header h1{
        font-size: 2.5rem;
    }
    
    .main{
        padding: 40px 20px;
        flex-direction: column;
    }
    
    .card{
        min-width: 100%;
    }
    
    .card:hover{
        transform: translateY(-15px);
    }
}

/* Additional styles for extra effects */
.pulse-ring {
    position: absolute;
    border: 2px solid rgba(33, 150, 243, 0.6);
    border-radius: 50%;
    animation: ringPulse 2s infinite;
}

@keyframes ringPulse {
    0% {
        transform: scale(0.8);
        opacity: 1;
    }
    100% {
        transform: scale(2);
        opacity: 0;
    }
}

@keyframes floatRandom {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
    }
    25% {
        transform: translate(100px, 50px) rotate(90deg);
    }
    50% {
        transform: translate(50px, 100px) rotate(180deg);
    }
    75% {
        transform: translate(-50px, 50px) rotate(270deg);
    }
}
</style>
</head>

<body>

<!-- Background Elements -->
<div class="particles" id="particles"></div>
<div class="glow glow-1"></div>
<div class="glow glow-2"></div>

<!-- Floating Elements -->
<div class="floating-element" style="top:10%; left:5%;">❤️</div>
<div class="floating-element" style="top:20%; right:10%;">🩺</div>
<div class="floating-element" style="bottom:30%; left:15%;">⚕️</div>
<div class="floating-element" style="bottom:20%; right:20%;">💊</div>
<div class="floating-element" style="top:40%; left:20%;">🏥</div>

<div class="container">

    <div class="header">
        <h1><i class="fas fa-video-medical"></i> Online Video Consultation</h1>
        <p>Connect with healthcare professionals anytime, anywhere with secure HD video calls</p>
    </div>

    <div class="main">

        <!-- PATIENT -->
        <div class="card" id="patientCard">
            <div class="pulse-ring" style="width: 100px; height: 100px; top: 40px; left: 50%; transform: translateX(-50%);"></div>
            <i class="fas fa-user-injured patient-icon"></i>
            <h2>Patient Portal</h2>
            <p>Book instant appointments, consult with specialists, get e-prescriptions, and manage your health records securely.</p>
            <button onclick="animateButton(this); setTimeout(() => location.href='patient/login.php', 1000);">
                Access Patient Portal <i class="fas fa-arrow-right"></i>
            </button>
        </div>

        <!-- DOCTOR -->
        <div class="card" id="doctorCard">
            <div class="pulse-ring" style="width: 100px; height: 100px; top: 40px; left: 50%; transform: translateX(-50%); animation-delay: 1s;"></div>
            <i class="fas fa-user-md doctor-icon"></i>
            <h2>Doctor Portal</h2>
            <p>Manage your practice, conduct virtual consultations, access patient history, and collaborate with medical teams.</p>
            <button onclick="animateButton(this); setTimeout(() => location.href='doctor/login.php', 1000);">
                Access Doctor Portal <i class="fas fa-arrow-right"></i>
            </button>
        </div>

    </div>

    <div class="footer">
        <p>© 2026 Online Video Consultation System | Secure • Reliable • Professional</p>
        <p style="margin-top: 10px; font-size: 0.9rem; opacity: 0.8;">
            <i class="fas fa-shield-alt"></i> HIPAA Compliant • 
            <i class="fas fa-lock"></i> End-to-End Encrypted • 
            <i class="fas fa-heartbeat"></i> 24/7 Support
        </p>
    </div>

</div>

<script>
// Create particle background
function createParticles() {
    const particlesContainer = document.getElementById('particles');
    const particleCount = 30;
    
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.classList.add('particle');
        
        const size = Math.random() * 20 + 5;
        const left = Math.random() * 100;
        const duration = Math.random() * 20 + 10;
        const delay = Math.random() * 20;
        
        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        particle.style.left = `${left}vw`;
        particle.style.animationDuration = `${duration}s`;
        particle.style.animationDelay = `${delay}s`;
        
        // Random color
        const colors = [
            'rgba(33, 150, 243, 0.3)',
            'rgba(76, 175, 80, 0.3)',
            'rgba(255, 255, 255, 0.2)',
            'rgba(255, 193, 7, 0.3)'
        ];
        particle.style.background = colors[Math.floor(Math.random() * colors.length)];
        
        particlesContainer.appendChild(particle);
    }
}

// Button animation function
function animateButton(button) {
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Redirecting...';
    button.style.background = 'linear-gradient(45deg, #4caf50, #8bc34a)';
    
    // Add ripple effect
    const ripple = document.createElement('span');
    const rect = button.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size/2;
    const y = event.clientY - rect.top - size/2;
    
    ripple.style.cssText = `
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple 0.6s linear;
        width: ${size}px;
        height: ${size}px;
        top: ${y}px;
        left: ${x}px;
    `;
    
    button.appendChild(ripple);
    
    // Remove ripple after animation
    setTimeout(() => {
        ripple.remove();
    }, 600);
}

// Add ripple animation to CSS
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

// Card hover effects with sound (optional)
const cards = document.querySelectorAll('.card');
cards.forEach(card => {
    card.addEventListener('mouseenter', () => {
        card.style.transition = 'all 0.3s ease';
    });
    
    card.addEventListener('mousemove', (e) => {
        const rect = card.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        
        const rotateY = (x - centerX) / 25;
        const rotateX = (centerY - y) / 25;
        
        card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-20px)`;
    });
    
    card.addEventListener('mouseleave', () => {
        card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0)';
    });
});

// Initialize on load
document.addEventListener('DOMContentLoaded', () => {
    createParticles();
    
    // Add typing effect to header
    const headerText = document.querySelector('.header h1');
    const originalText = headerText.innerHTML;
    headerText.innerHTML = '';
    
    let i = 0;
    const typeWriter = () => {
        if (i < originalText.length) {
            headerText.innerHTML += originalText.charAt(i);
            i++;
            setTimeout(typeWriter, 50);
        }
    };
    
    // Start typing after 1 second
    setTimeout(typeWriter, 1000);
});

// Add floating animation to icons
document.querySelectorAll('.card i').forEach(icon => {
    icon.addEventListener('mouseenter', () => {
        icon.style.animation = 'bounce 0.5s ease';
    });
    
    icon.addEventListener('animationend', () => {
        icon.style.animation = 'bounce 3s infinite ease-in-out';
    });
});

// Add cursor effect
document.addEventListener('mousemove', (e) => {
    const cursor = document.querySelector('.cursor') || createCursor();
    cursor.style.left = e.clientX + 'px';
    cursor.style.top = e.clientY + 'px';
});

function createCursor() {
    const cursor = document.createElement('div');
    cursor.className = 'cursor';
    cursor.style.cssText = `
        position: fixed;
        width: 20px;
        height: 20px;
        border: 2px solid rgba(33, 150, 243, 0.8);
        border-radius: 50%;
        pointer-events: none;
        z-index: 9999;
        transform: translate(-50%, -50%);
        transition: width 0.2s, height 0.2s;
    `;
    document.body.appendChild(cursor);
    
    // Add trail
    const trail = document.createElement('div');
    trail.className = 'cursor-trail';
    trail.style.cssText = `
        position: fixed;
        width: 10px;
        height: 10px;
        background: rgba(33, 150, 243, 0.4);
        border-radius: 50%;
        pointer-events: none;
        z-index: 9998;
        transition: all 0.1s;
    `;
    document.body.appendChild(trail);
    
    return cursor;
}

// Update trail position
let trailX = 0, trailY = 0;
document.addEventListener('mousemove', (e) => {
    const trail = document.querySelector('.cursor-trail');
    if (trail) {
        setTimeout(() => {
            trail.style.left = e.clientX + 'px';
            trail.style.top = e.clientY + 'px';
        }, 50);
    }
});
</script>

</body>
</html>