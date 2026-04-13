<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Patrick Moz - Front-end Developer portfolio. Expert in web development, UI/UX design, and modern web technologies.">

    <!--=============== FAVICON ===============-->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.png') }}" type="image/x-icon">

    <!--=============== REMIXICONS ===============-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.0.0/fonts/remixicon.css" rel="stylesheet">

    <!--=============== CSS ===============-->
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">

    <title>Patrick Moz - Front-end Developer</title>
</head>

<body>
    <!--==================== HEADER ====================-->
    <header class="header" id="header">
        <nav class="nav container">
            <a href="#" class="nav__logo">
                Patrick <span>Moz</span>
            </a>

            <div class="nav__menu" id="nav-menu">
                <ul class="nav__list">
                    <li class="nav__item">
                        <a href="#home" class="nav__link active-link">Home</a>
                    </li>
                    <li class="nav__item">
                        <a href="#about" class="nav__link">About</a>
                    </li>
                    <li class="nav__item">
                        <a href="#services" class="nav__link">Services</a>
                    </li>
                    <li class="nav__item">
                        <a href="#projects" class="nav__link">Projects</a>
                    </li>
                    <li class="nav__item">
                        <a href="#contact" class="nav__link">Contact</a>
                    </li>
                </ul>

                <div class="nav__close" id="nav-close">
                    <i class="ri-close-line"></i>
                </div>
            </div>

            <div class="nav__toggle" id="nav-toggle">
                <i class="ri-menu-line"></i>
            </div>
        </nav>
    </header>

    <!--==================== MAIN ====================-->
    <main class="main">
        <!--==================== HOME ====================-->
        <section class="home section" id="home">
            <div class="home__container container grid">
                <div class="home__content grid">
                    <div class="home__data">
                        <span class="home__subtitle">Hello, <span>I'm</span></span>
                        <h1 class="home__title">Patrick Moz</h1>
                        <span class="home__education">Front-end Developer</span>
                        <p class="home__description">
                            With knowledge in web development and
                            design, I offer the best projects resulting
                            in quality work.
                        </p>
                        <a href="#contact" class="button">Let's Talk</a>
                    </div>

                    <div class="home__social">
                        <a href="https://github.com/" target="_blank" class="home__social-link">
                            <i class="ri-github-fill"></i>
                        </a>
                        <a href="https://dribbble.com/" target="_blank" class="home__social-link">
                            <i class="ri-dribbble-line"></i>
                        </a>
                        <a href="https://www.linkedin.com/" target="_blank" class="home__social-link">
                            <i class="ri-linkedin-box-fill"></i>
                        </a>
                    </div>
                </div>

                <div class="home__image">
                    <svg class="home__blob" viewBox="0 0 550 591" xmlns="http://www.w3.org/2000/svg">
                        <mask id="maskBlob" mask-type="alpha">
                            <path d="M263 47.1782C270.426 42.891 279.574 42.891 287 47.1782L501.214 
                                     169.322C508.64 173.609 513.214 181.591 513.214 
                                     190.165V434.835C513.214 443.409 508.64 451.391 501.214 
                                     455.678L287 577.822C279.574 582.109 270.426 582.109 263 
                                     577.822L48.786 455.678C41.3596 451.391 36.786 443.409 36.786 
                                     434.835V190.165C36.786 181.591 41.3596 173.609 48.786 
                                     169.322L263 47.1782Z"/>
                        </mask>

                        <g mask="url(#maskBlob)">
                            <path d="M263 47.1782C270.426 42.891 279.574 42.891 287 47.1782L501.214 
                                     169.322C508.64 173.609 513.214 181.591 513.214 
                                     190.165V434.835C513.214 443.409 508.64 451.391 501.214 
                                     455.678L287 577.822C279.574 582.109 270.426 582.109 263 
                                     577.822L48.786 455.678C41.3596 451.391 36.786 443.409 36.786 
                                     434.835V190.165C36.786 181.591 41.3596 173.609 48.786 
                                     169.322L263 47.1782Z"/>

                            <rect x="37" width="476" height="630" fill="url(#patternBlob)"/>
                        </g>

                        <defs>
                            <pattern id="patternBlob" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <use href="#imageBlob" transform="matrix(0.00143057 0 0 0.00143057 0.0404062 0)"/>
                            </pattern>

                            <image style="width: 700px; height: 700px;" id="imageBlob" href="{{ asset('assets/img/perfil.png') }}"/>
                        </defs>
                    </svg>
                </div>
            </div>
        </section>

        <!--==================== ABOUT ====================-->
        <section class="about section" id="about">
            <div class="about__container container grid">
                <div class="about__image">
                    <svg class="about__blob" viewBox="0 0 550 592" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <mask id="maskBorder" mask-type="alpha">
                            <path d="M263 48.1782C270.426 43.891 279.574 43.891 287 48.1782L501.214 
                                     170.322C508.64 174.609 513.214 182.591 513.214 
                                     191.165V435.835C513.214 444.409 508.64 452.391 501.214 
                                     456.678L287 578.822C279.574 583.109 270.426 583.109 263 
                                     578.822L48.786 456.678C41.3596 452.391 36.786 444.409 36.786 
                                     435.835V191.165C36.786 182.591 41.3596 174.609 48.786 
                                     170.322L263 48.1782Z"/>
                        </mask>

                        <g mask="url(#maskBorder)">
                            <rect x="37" width="476" height="630" fill="url(#patternBorder)"/>
                        </g>

                        <path d="M263 48.1782C270.426 43.891 279.574 43.891 287 48.1782L501.214 
                                 170.322C508.64 174.609 513.214 182.591 513.214 
                                 191.165V435.835C513.214 444.409 508.64 452.391 501.214 
                                 456.678L287 578.822C279.574 583.109 270.426 583.109 263 
                                 578.822L48.786 456.678C41.3596 452.391 36.786 444.409 36.786 
                                 435.835V191.165C36.786 182.591 41.3596 174.609 48.786 
                                 170.322L263 48.1782Z" stroke-width="5"/>

                        <defs>
                            <pattern id="patternBorder" patternContentUnits="objectBoundingBox" width="1" height="1">
                                <use href="#imageBorder" transform="matrix(0.00143057 0 0 0.00143057 0.0404062 0)"/>
                            </pattern>

                            <image style="width: 700px; height: 700px;" id="imageBorder" href="{{ asset('assets/img/perfil.png') }}"/>
                        </defs>
                    </svg>
                </div>

                <div class="about__data">
                    <span class="section__subtitle">My <span>Intro</span></span>
                    <h2 class="section__title">About Me</h2>

                    <div class="about__info grid">
                        <div class="about__box">
                            <i class='bx bx-award about__icon ri-award-fill'></i>
                            <h3 class="about__title">Experience</h3>
                            <span class="about__subtitle">5+ Years</span>
                        </div>
                        <div class="about__box">
                            <i class='bx bx-briefcase-alt about__icon ri-briefcase-fill'></i>
                            <h3 class="about__title">Completed</h3>
                            <span class="about__subtitle">20+ Projects</span>
                        </div>
                        <div class="about__box">
                            <i class='bx bx-support about__icon ri-customer-service-2-fill'></i>
                            <h3 class="about__title">Support</h3>
                            <span class="about__subtitle">Online 24/7</span>
                        </div>
                    </div>

                    <p class="about__description">
                        Frontend developer, I create web pages with
                        UI / UX user interface, I have years of
                        experience and many clients are happy with the
                        projects carried out.
                    </p>

                    <a href="#contact" class="button">Contact Me</a>
                </div>
            </div>
        </section>

        <!--==================== SKILLS ====================-->
        <section class="skills section" id="skills">
            <div class="skills__container container grid">
                <div class="skills__data">
                    <span class="section__subtitle">Favorite <span>Skills</span></span>
                    <h2 class="section__title">My Skills</h2>
                    <p class="skills__description">
                        See fully what skills I have and perform,
                        to develop the projects for you.
                    </p>
                    <a href="#projects" class="button">See Projects</a>
                </div>

                <div class="skills__content grid">
                    <ol class="skills__group">
                        <li class="skills__item">HTML &amp; CSS</li>
                        <li class="skills__item">JavaScript</li>
                        <li class="skills__item">Bootstrap</li>
                        <li class="skills__item">React</li>
                    </ol>
                    <ol class="skills__group">
                        <li class="skills__item">Git &amp; GitHub</li>
                        <li class="skills__item">Figma</li>
                        <li class="skills__item">Sketch</li>
                    </ol>
                </div>
            </div>
        </section>

        <!--==================== SERVICES ====================-->
        <section class="services section" id="services">
            <span class="section__subtitle">My <span>Services</span></span>
            <h2 class="section__title">What I Do</h2>

            <div class="services__container container grid">
                <div class="services__card">
                    <i class="ri-code-box-line services__icon"></i>
                    <h3 class="services__title">Web Developer</h3>
                    <p class="services__description">
                        Development of custom web pages.
                        Using current technologies and
                        libraries of the labor field.
                    </p>
                </div>

                <div class="services__card">
                    <i class="ri-layout-4-line services__icon"></i>
                    <h3 class="services__title">UI/UX Designer</h3>
                    <p class="services__description">
                        I offer design of web interfaces and
                        mobile applications, design made in
                        Figma, Adobe XD and Sketch.
                    </p>
                </div>

                <div class="services__card">
                    <i class="ri-pen-nib-line services__icon"></i>
                    <h3 class="services__title">Graphic Design</h3>
                    <p class="services__description">
                        I make designs at the client's request,
                        banner design, posters, digital
                        designs among others.
                    </p>
                </div>
            </div>
        </section>

        <!--==================== PROJECTS ====================-->
        <section class="projects section" id="projects">
            <span class="section__subtitle">My <span>Jobs</span></span>
            <h2 class="section__title">Recent Projects</h2>

            <div class="projects__container container grid">
                <article class="projects__card">
                    <img src="{{ asset('assets/img/project-img-1.jpg') }}" alt="Modern Website" class="projects__img">
                    <div class="projects__modal">
                        <span class="projects__subtitle">Web</span>
                        <h3 class="projects__title">Modern Website</h3>
                        <a href="#" class="projects__button" target="_blank">
                            View Demo <i class="ri-external-link-line"></i>
                        </a>
                    </div>
                </article>

                <article class="projects__card">
                    <img src="{{ asset('assets/img/project-img-2.jpg') }}" alt="Landing Page" class="projects__img">
                    <div class="projects__modal">
                        <span class="projects__subtitle">Web</span>
                        <h3 class="projects__title">Landing Page</h3>
                        <a href="#" class="projects__button" target="_blank">
                            View Demo <i class="ri-external-link-line"></i>
                        </a>
                    </div>
                </article>

                <article class="projects__card">
                    <img src="{{ asset('assets/img/project-img-3.jpg') }}" alt="E-commerce" class="projects__img">
                    <div class="projects__modal">
                        <span class="projects__subtitle">Web</span>
                        <h3 class="projects__title">E-commerce</h3>
                        <a href="#" class="projects__button" target="_blank">
                            View Demo <i class="ri-external-link-line"></i>
                        </a>
                    </div>
                </article>

                <article class="projects__card">
                    <img src="{{ asset('assets/img/project-img-4.jpg') }}" alt="Mobile App UI" class="projects__img">
                    <div class="projects__modal">
                        <span class="projects__subtitle">App</span>
                        <h3 class="projects__title">Mobile App UI</h3>
                        <a href="#" class="projects__button" target="_blank">
                            View Demo <i class="ri-external-link-line"></i>
                        </a>
                    </div>
                </article>

                <article class="projects__card">
                    <img src="{{ asset('assets/img/project-img-5.jpg') }}" alt="Dashboard Design" class="projects__img">
                    <div class="projects__modal">
                        <span class="projects__subtitle">Web</span>
                        <h3 class="projects__title">Dashboard Design</h3>
                        <a href="#" class="projects__button" target="_blank">
                            View Demo <i class="ri-external-link-line"></i>
                        </a>
                    </div>
                </article>

                <article class="projects__card">
                    <img src="{{ asset('assets/img/project-img-6.jpg') }}" alt="Portfolio Website" class="projects__img">
                    <div class="projects__modal">
                        <span class="projects__subtitle">Web</span>
                        <h3 class="projects__title">Portfolio Website</h3>
                        <a href="#" class="projects__button" target="_blank">
                            View Demo <i class="ri-external-link-line"></i>
                        </a>
                    </div>
                </article>
            </div>
        </section>

        <!--==================== CONTACT ====================-->
        <section class="contact section" id="contact">
            <span class="section__subtitle">Get In <span>Touch</span></span>
            <h2 class="section__title">Contact Me</h2>

            <div class="contact__container container grid">
                <form action="" class="contact__form" id="contact-form">
                    <div class="contact__group grid">
                        <div class="contact__form-div">
                            <label class="contact__form-tag">Name</label>
                            <input type="text" name="user_name" placeholder="Enter your name" class="contact__form-input">
                        </div>
                        <div class="contact__form-div">
                            <label class="contact__form-tag">Email</label>
                            <input type="email" name="user_email" placeholder="Enter your email" class="contact__form-input">
                        </div>
                    </div>

                    <div class="contact__form-div contact__form-area">
                        <label class="contact__form-tag">Message</label>
                        <textarea name="user_project" placeholder="Enter your message" class="contact__form-input"></textarea>
                    </div>

                    <p class="contact__form-message" id="contact-message"></p>

                    <button type="submit" class="button contact__button">Send Message</button>
                </form>
            </div>
        </section>
    </main>

    <!--==================== FOOTER ====================-->
    <footer class="footer">
        <div class="footer__container container grid">
            <div>
                <a href="#" class="footer__title">
                    Patrick <span>Moz</span>
                </a>
                <span class="footer__education">Front-end Developer</span>
            </div>

            <div class="footer__social">
                <a href="https://www.facebook.com/" target="_blank" class="footer__social-link">
                    <i class="ri-facebook-circle-fill"></i>
                </a>
                <a href="https://www.instagram.com/" target="_blank" class="footer__social-link">
                    <i class="ri-instagram-fill"></i>
                </a>
                <a href="https://twitter.com/" target="_blank" class="footer__social-link">
                    <i class="ri-twitter-x-fill"></i>
                </a>
            </div>

            <span class="footer__copy">
                &copy; Copyright Bedimcode. All rights reserved
            </span>
        </div>
    </footer>

    <!--========== SCROLL UP ==========-->
    <a href="#" class="scrollup" id="scroll-up">
        <i class="ri-arrow-up-line"></i>
    </a>

    <!--=============== SCROLLREVEAL ===============-->
    <script src="{{ asset('assets/js/scrollreveal.min.js') }}"></script>

    <!--=============== MAIN JS ===============-->
    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>

</html>
