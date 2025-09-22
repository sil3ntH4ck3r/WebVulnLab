const express = require('express');
const axios = require('axios');

const app = express();
app.set('etag', false);
const PORT = 3000;

app.use(express.static('public', {
  etag: false,
  lastModified: false,
  setHeaders: (res) => {
    res.setHeader('Cache-Control', 'public, max-age=31536000, immutable');
  }
}));

const defaultTranslations = {
  es: {
    title: "Web Cache Poisoning",
    subtitle: "Lleva tu negocio al siguiente nivel",
    nav_home: "Inicio",
    nav_services: "Servicios",
    nav_pricing: "Precios",
    nav_contact: "Contacto",
    hero_title: "Aumenta tus ventas con publicidad inteligente",
    hero_subtitle: "Conectamos tu marca con los clientes correctos en el momento perfecto",
    hero_cta: "Comenzar Ahora",
    services_title: "Nuestros Servicios",
    service1_title: "Publicidad en Google Ads",
    service1_desc: "Campañas optimizadas para máximo ROI",
    service2_title: "Marketing en Redes Sociales",
    service2_desc: "Presencia profesional en todas las plataformas",
    service3_title: "SEO y Contenido",
    service3_desc: "Posicionamiento orgánico y contenido de calidad",
    pricing_title: "Planes y Precios",
    plan1_name: "Básico",
    plan1_price: "€299/mes",
    plan1_feat1: "Google Ads básico",
    plan1_feat2: "Reporte mensual",
    plan1_feat3: "Soporte por email",
    plan2_name: "Profesional", 
    plan2_price: "€699/mes",
    plan2_feat1: "Google Ads + Facebook",
    plan2_feat2: "Reportes semanales",
    plan2_feat3: "Soporte telefónico",
    plan3_name: "Enterprise",
    plan3_price: "€1,299/mes",
    plan3_feat1: "Campañas completas",
    plan3_feat2: "Gerente dedicado",
    plan3_feat3: "Soporte 24/7",
    contact_title: "¿Listo para crecer?",
    contact_subtitle: "Contacta con nuestros expertos",
    contact_cta: "Solicitar Consulta Gratuita"
  },
  en: {
    title: "Web Cache Poisoning",
    subtitle: "Take your business to the next level",
    nav_home: "Home",
    nav_services: "Services", 
    nav_pricing: "Pricing",
    nav_contact: "Contact",
    hero_title: "Boost your sales with smart advertising",
    hero_subtitle: "We connect your brand with the right customers at the perfect moment",
    hero_cta: "Get Started",
    services_title: "Our Services",
    service1_title: "Google Ads Advertising",
    service1_desc: "Optimized campaigns for maximum ROI",
    service2_title: "Social Media Marketing",
    service2_desc: "Professional presence across all platforms",
    service3_title: "SEO & Content",
    service3_desc: "Organic positioning and quality content",
    pricing_title: "Plans & Pricing",
    plan1_name: "Basic",
    plan1_price: "$299/month",
    plan1_feat1: "Basic Google Ads",
    plan1_feat2: "Monthly report",
    plan1_feat3: "Email support",
    plan2_name: "Professional",
    plan2_price: "$699/month", 
    plan2_feat1: "Google Ads + Facebook",
    plan2_feat2: "Weekly reports",
    plan2_feat3: "Phone support",
    plan3_name: "Enterprise",
    plan3_price: "$1,299/month",
    plan3_feat1: "Full campaigns",
    plan3_feat2: "Dedicated account manager",
    plan3_feat3: "24/7 support",
    contact_title: "Ready to grow?",
    contact_subtitle: "Contact our experts",
    contact_cta: "Request Free Consultation"
  }
};

app.get('/', async (req, res) => {
  try {
    let translationsBaseUrl = 'http://webcachepoisoning.local';
    
    if (req.headers['x-forwarded-host']) {
      translationsBaseUrl = `http://${req.headers['x-forwarded-host']}`;
    }
    
    const html = generateHTML(translationsBaseUrl);
    
    res.setHeader('Content-Type', 'text/html; charset=utf-8');
    res.setHeader('Cache-Control', 'public, max-age=60, s-maxage=60, stale-while-revalidate=120');
    
    res.setHeader('X-Generated-At', new Date().toISOString());
    res.setHeader('X-Request-ID', Math.random().toString(36).substr(2, 9));
    
    res.send(html);
    
  } catch (error) {
    console.error('Error:', error);
    res.status(500).send('Error interno del servidor');
  }
});

app.get('/api/translations', (req, res) => {
  const lang = req.query.lang || 'es';
  const translations = defaultTranslations[lang] || defaultTranslations['es'];
  
  res.json(translations);
});

function generateHTML(translationsBaseUrl) {
  return `<!DOCTYPE html>
<html lang="es" id="htmlLang">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="pageTitle">Web Cache</title>
    <link href="/bootstrap.min.css" rel="stylesheet">
    <link href="/all.min.css" rel="stylesheet">
    <style>
        .hero-section { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            padding: 100px 0; 
        }
        .service-card { transition: transform 0.3s; }
        .service-card:hover { transform: translateY(-5px); }
        .navbar-brand { font-weight: bold; font-size: 1.5rem; }
        .pricing-card { border: 2px solid #e9ecef; transition: all 0.3s; }
        .pricing-card:hover { border-color: #007bff; transform: translateY(-5px); }
        .lang-selector { margin-left: 20px; }
        .loading { opacity: 0.5; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                PubliPro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="#home" data-i18n="nav_home">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services" data-i18n="nav_services">Servicios</a></li>
                    <li class="nav-item"><a class="nav-link" href="#pricing" data-i18n="nav_pricing">Precios</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact" data-i18n="nav_contact">Contacto</a></li>
                </ul>
                <div class="lang-selector">
                    <select class="form-select form-select-sm" onchange="changeLanguage(this.value)" style="width: auto;">
                        <option value="es">🇪🇸 Español</option>
                        <option value="en">🇺🇸 English</option>
                    </select>
                </div>
            </div>
        </div>
    </nav>

    <section id="home" class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4" data-i18n="hero_title">Aumenta tus ventas con publicidad inteligente</h1>
            <p class="lead mb-4" data-i18n="hero_subtitle">Conectamos tu marca con los clientes correctos en el momento perfecto</p>
            <a href="#contact" class="btn btn-light btn-lg px-5" data-i18n="hero_cta">Comenzar Ahora</a>
        </div>
    </section>

    <section id="services" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5" data-i18n="services_title">Nuestros Servicios</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 service-card">
                        <div class="card-body text-center">
                            <i class="fas fa-search fa-3x text-primary mb-3"></i>
                            <h5 class="card-title" data-i18n="service1_title">Publicidad en Google Ads</h5>
                            <p class="card-text" data-i18n="service1_desc">Campañas optimizadas para máximo ROI</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 service-card">
                        <div class="card-body text-center">
                            <i class="fas fa-share-alt fa-3x text-primary mb-3"></i>
                            <h5 class="card-title" data-i18n="service2_title">Marketing en Redes Sociales</h5>
                            <p class="card-text" data-i18n="service2_desc">Presencia profesional en todas las plataformas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 service-card">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line fa-3x text-primary mb-3"></i>
                            <h5 class="card-title" data-i18n="service3_title">SEO y Contenido</h5>
                            <p class="card-text" data-i18n="service3_desc">Posicionamiento orgánico y contenido de calidad</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="pricing" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5" data-i18n="pricing_title">Planes y Precios</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card pricing-card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title" data-i18n="plan1_name">Básico</h5>
                            <h3 class="text-primary" data-i18n="plan1_price">€299/mes</h3>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> <span data-i18n="plan1_feat1">Google Ads básico</span></li>
                                <li><i class="fas fa-check text-success"></i> <span data-i18n="plan1_feat2">Reporte mensual</span></li>
                                <li><i class="fas fa-check text-success"></i> <span data-i18n="plan1_feat3">Soporte por email</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card pricing-card h-100 border-primary">
                        <div class="card-body text-center">
                            <h5 class="card-title" data-i18n="plan2_name">Profesional</h5>
                            <h3 class="text-primary" data-i18n="plan2_price">€699/mes</h3>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> <span data-i18n="plan2_feat1">Google Ads + Facebook</span></li>
                                <li><i class="fas fa-check text-success"></i> <span data-i18n="plan2_feat2">Reportes semanales</span></li>
                                <li><i class="fas fa-check text-success"></i> <span data-i18n="plan2_feat3">Soporte telefónico</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card pricing-card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title" data-i18n="plan3_name">Enterprise</h5>
                            <h3 class="text-primary" data-i18n="plan3_price">€1,299/mes</h3>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> <span data-i18n="plan3_feat1">Campañas completas</span></li>
                                <li><i class="fas fa-check text-success"></i> <span data-i18n="plan3_feat2">Gerente dedicado</span></li>
                                <li><i class="fas fa-check text-success"></i> <span data-i18n="plan3_feat3">Soporte 24/7</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="mb-3" data-i18n="contact_title">¿Listo para crecer?</h2>
            <p class="lead mb-4" data-i18n="contact_subtitle">Contacta con nuestros expertos</p>
            <a href="mailto:info@webcacheposoning.local" class="btn btn-light btn-lg" data-i18n="contact_cta">Solicitar Consulta Gratuita</a>
        </div>
    </section>

    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0
        </div>
    </footer>

    <script src="/bootstrap.bundle.min.js"></script>
    <script>
        const DEFAULT_TRANSLATIONS_URL = 'http://webcachepoisoning.local';
        let currentLang = 'es';
        
        async function getTranslationsBaseUrl() {
            try {
                const resp = await fetch('/api/debug/headers', {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                });
                if (!resp.ok) throw new Error('HTTP ' + resp.status);
                const data = await resp.json();
                const poisonedBase = data && data.translationsBaseUrl ? data.translationsBaseUrl : null;
                const xfh = data && data.headers ? data.headers['x-forwarded-host'] : null;
                if (xfh && poisonedBase) {
                    return poisonedBase;
                }
            } catch (e) {
                console.warn('Could not fetch translationsBaseUrl from /api/debug/headers:', e.message);
            }
            return window.location.origin;
        }
        
        document.addEventListener('DOMContentLoaded', async function() {
            const baseUrl = await getTranslationsBaseUrl();
            const translationsUrl = baseUrl + '/api/translations?lang=es';
            loadTranslations('es', translationsUrl);
        });
        
        async function changeLanguage(lang) {
            if (lang !== currentLang) {
                currentLang = lang;
                const baseUrl = await getTranslationsBaseUrl();
                const translationsUrl = baseUrl + '/api/translations?lang=' + lang;
                loadTranslations(lang, translationsUrl);
            }
        }
        
        async function loadTranslations(lang, translationsUrl) {
            try {
                document.body.classList.add('loading');
                
                const response = await fetch(translationsUrl, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(\`HTTP \${response.status}: \${response.statusText}\`);
                }
                
                const translations = await response.json();
                
                if (typeof translations !== 'object' || translations === null) {
                    throw new Error('Invalid translations format received');
                }
                
                applyTranslations(translations, lang);
                
            } catch (error) {
                console.error('[ERROR]:', error);
                
                showErrorMessage(\`Error loading translations from: \${new URL(translationsUrl).origin}, check the browser console\`);
                
                if (!translationsUrl.includes(DEFAULT_TRANSLATIONS_URL)) {
                    const fallbackUrl = DEFAULT_TRANSLATIONS_URL + '/api/translations?lang=' + lang;
                    await loadTranslations(lang, fallbackUrl);
                }
            } finally {
                document.body.classList.remove('loading');
            }
        }
        
        function applyTranslations(translations, lang) {
            const elements = document.querySelectorAll('[data-i18n]');
            elements.forEach(element => {
                const key = element.getAttribute('data-i18n');
                if (translations[key]) {
                    element.innerHTML = translations[key];
                }
            });
            
            if (translations.title) {
                document.title = translations.title;
                document.getElementById('pageTitle').textContent = translations.title;
            }
            
            document.getElementById('htmlLang').setAttribute('lang', lang);
            
            const select = document.querySelector('.lang-selector select');
            select.value = lang;
        }
        
        function showErrorMessage(message) {
            let errorDiv = document.getElementById('error-message');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.id = 'error-message';
                errorDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed';
                errorDiv.style.cssText = 'top: 70px; right: 20px; z-index: 9999; max-width: 400px;';
                document.body.appendChild(errorDiv);
            }
            
            errorDiv.innerHTML = \`
                <strong>Error:</strong><br>
                <small>\${message}</small>
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            \`;
        }
    </script>
</body>
</html>`;
}

app.get('/api/debug/headers', (req, res) => {
  const headers = {
    'x-forwarded-host': req.headers['x-forwarded-host'] || null,
    'host': req.headers['host'],
    'user-agent': req.headers['user-agent'],
    'x-forwarded-for': req.headers['x-forwarded-for'] || null,
    'x-real-ip': req.headers['x-real-ip'] || null
  };
  
  const translationsBaseUrl = req.headers['x-forwarded-host'] 
    ? `http://${req.headers['x-forwarded-host']}`
    : 'http://webcachepoisoning.local';
  
  res.json({
    headers: headers,
    translationsBaseUrl: translationsBaseUrl,
    timestamp: new Date().toISOString()
  });
});

app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});