<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instituto AtWell | Instituto Bicultural</title>
    <style>
        :root {
            --color-principal: #0d4a97; /* Azul oscuro del logo */
            --color-secundario: #6c757d; /* Gris para texto */
            --color-fondo: #f8f9fa; /* Fondo claro */
            --color-texto-claro: #ffffff;
            --color-resaltado: #e7367c; /* Rosa/rojo del logo */
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--color-fondo);
            color: #333;
            line-height: 1.6;
        }

        /* Contenedor principal para centrar el contenido */
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 0;
            position: relative; /* Para posicionar elementos dentro si es necesario */
        }

        /* Header (Encabezado) */
        header {
            background-color: var(--color-principal); /* Usamos el azul oscuro del logo */
            color: var(--color-texto-claro);
            padding: 15px 0;
            text-align: center;
            position: relative;
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header .logo {
            flex-grow: 1; /* Permite que el logo ocupe el espacio central */
            text-align: center;
        }

        header .logo img {
            height: 60px; /* Ajusta el tamaño del logo */
            width: auto;
        }

        header .login-link {
            position: absolute; /* Posicionamiento absoluto para el login */
            right: 5%; /* Ajusta la posición desde la derecha */
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.1em;
        }

        header .login-link a {
            color: var(--color-texto-claro);
            text-decoration: none;
            padding: 8px 15px;
            border: 1px solid var(--color-texto-claro);
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        header .login-link a:hover {
            background-color: var(--color-texto-claro);
            color: var(--color-principal);
        }

        /* Hero Section (Sección principal y CTA) */
        .hero {
            background-color: var(--color-texto-claro);
            padding: 80px 0;
            text-align: center;
            border-bottom: 5px solid var(--color-resaltado); /* Usamos el color resaltado del logo */
        }

        .hero h2 {
            font-size: 3em;
            color: var(--color-principal);
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.5em;
            color: var(--color-secundario);
            margin-bottom: 40px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Botón CTA principal */
        .cta-button {
            display: inline-block;
            background-color: #28a745; /* Verde llamativo para la acción */
            color: var(--color-texto-claro);
            padding: 15px 30px;
            text-decoration: none;
            font-size: 1.4em;
            font-weight: bold;
            border-radius: 8px;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .cta-button:hover {
            background-color: #218838;
        }

        /* Section (Características/Beneficios) */
        .features {
            padding: 60px 0;
            text-align: center;
        }

        .features h3 {
            font-size: 2em;
            color: var(--color-principal);
            margin-bottom: 40px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .feature-item {
            background-color: var(--color-texto-claro);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            text-align: left;
        }

        .feature-item h4 {
            color: var(--color-principal);
            margin-top: 0;
            font-size: 1.3em;
        }

        /* Footer (Pie de página) */
        footer {
            background-color: #343a40; /* Gris oscuro */
            color: var(--color-texto-claro);
            text-align: center;
            padding: 20px 0;
            margin-top: 40px;
        }

        footer a {
            color: #adb5bd;
            text-decoration: none;
            margin: 0 10px;
        }

        footer a:hover {
            text-decoration: underline;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            header .container {
                flex-direction: column;
                padding-bottom: 15px; /* Ajuste para espacio cuando el login se reposiciona */
            }

            header .login-link {
                position: static; /* Vuelve a posicionamiento normal en móviles */
                transform: none;
                margin-top: 15px; /* Espacio debajo del logo */
                text-align: center;
                width: 100%;
            }

            header .login-link a {
                display: block; /* Ocupa todo el ancho */
                width: fit-content;
                margin: 0 auto;
            }

            .hero h2 {
                font-size: 2.2em;
            }

            .hero p {
                font-size: 1.2em;
            }
        }
    </style>
</head>
<body>
<header>
    <div class="container">
        <div class="login-link">
            <a href="/login">Entrar a plataforma</a>
        </div>
    </div>
</header>
<img src="/images/atwell_header.png" alt="Instituto AtWell" style="width: 100%; height: auto;">

<main>
    <section class="hero">
        <div class="container">
            <h2>Instituto AtWell: Tu Puente Hacia el Bilingüismo y el Éxito</h2>
            <p>Descubre nuestros programas biculturales y bilingües diseñados para transformar tu potencial. Inicia tu camino hacia la fluidez y el dominio cultural.</p>
            <a href="https://www.facebook.com/institutoatwell/?locale=es_LA" target="_blank" class="cta-button">
                Explora Nuestra actividad en Facebook
            </a>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <h3>¿Por Qué Elegir Instituto AtWell?</h3>
            <div class="feature-grid">
                <div class="feature-item">
                    <h4>🌍 Enfoque Bicultural Inmersivo</h4>
                    <p>No solo aprendes un idioma, vives una cultura. Nuestros programas te sumergen en experiencias lingüísticas y culturales auténticas.</p>
                </div>
                <div class="feature-item">
                    <h4>👩‍🏫 Metodología de Vanguardia</h4>
                    <p>Clases dinámicas y herramientas interactivas que garantizan un aprendizaje efectivo y divertido. </p>
                </div>
                <div class="feature-item">
                    <h4>🤝 Modelo Educativo</h4>
                    <p>Nuestro modelo educativo está centrado en el alumno y en promover al máximo cada una de sus habilidad.</p>
                </div>
            </div>
        </div>
    </section>


</main>

<footer>
    <div class="container">
        <p>&copy; 2024 Instituto AtWell. Todos los derechos reservados.</p>
        <p>
            <a href="https://www.facebook.com/institutoatwell/?locale=es_LA" target="_blank">Visítanos en Facebook</a> |
            <a href="/politica-privacidad">Política de Privacidad</a>
        </p>
    </div>
</footer>

</body>
</html>
