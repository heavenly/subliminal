document.addEventListener('DOMContentLoaded', function() {
    const backToTopButton = document.getElementById('back-to-top');
    const isPostView = window.blogData && window.blogData.currentPost;
    const toc = document.querySelector('.toc');
    const tocLinks = document.querySelectorAll('.toc a[href^="#"]');

    function smoothScrollTo(targetPosition, offset = 0) {
        window.scrollTo({
            top: targetPosition + offset,
            behavior: 'smooth'
        });
    }


    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopButton.classList.add('visible');
        } else {
            backToTopButton.classList.remove('visible');
        }

        if (toc && window.pageYOffset > 100) {
            toc.classList.add('scrolled');
        } else if (toc) {
            toc.classList.remove('scrolled');
        }

        if (isPostView) {
            updateActiveTOCLink();
        }
    });

    backToTopButton.addEventListener('click', function() {
        smoothScrollTo(0);
    });

    if (isPostView) {
        tocLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);

                if (targetElement) {
                    const headerOffset = 80;
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    smoothScrollTo(offsetPosition);
                } else {
                    console.log('Target element not found:', targetId);
                }
            });
        });

        function updateActiveTOCLink() {
            const sections = document.querySelectorAll('h1[id], h2[id], h3[id]');

            let currentSection = '';
            const scrollPosition = window.pageYOffset + 50;


            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (scrollPosition >= sectionTop) {
                    currentSection = section.getAttribute('id');
                }
            });

            tocLinks.forEach(link => {
                link.classList.remove('active');
            });

            if (currentSection) {
                const activeLink = document.querySelector(`.toc a[href="#${currentSection}"]`);
                if (activeLink) {
                    activeLink.classList.add('active');


                    if (toc) {
                        const containerRect = toc.getBoundingClientRect();
                        const linkRect = activeLink.getBoundingClientRect();
                        const containerTop = containerRect.top + toc.scrollTop;
                        const linkTop = linkRect.top + tocContainer.scrollTop;

                        if (linkTop < containerTop || linkTop > containerTop + containerRect.height) {
                            tocContainer.scrollTo({
                                top: linkTop - containerTop - containerRect.height / 2,
                                behavior: 'smooth'
                            });
                        }
                    }
                }
            }
        }
    }


    function calculateReadingTime() {
        const content = document.querySelector('.post-content') || document.querySelector('.content');
        if (!content) return 0;

        const text = content.textContent || content.innerText || '';
        const wordsPerMinute = 200;
        const words = text.trim().split(/\s+/).length;
        const readingTime = Math.ceil(words / wordsPerMinute);

        return readingTime;
    }

    const readingTimeElement = document.querySelector('.reading-time');
    if (readingTimeElement) {
        const readingTime = calculateReadingTime();
        readingTimeElement.textContent = `${readingTime} min read`;
    }

    const codeBlocks = document.querySelectorAll('pre code');
    codeBlocks.forEach(codeBlock => {
        const pre = codeBlock.parentElement;
        const copyButton = document.createElement('button');
        copyButton.textContent = 'Copy';
        copyButton.classList.add('copy-code-btn');
        copyButton.style.cssText = `
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: #c54b99;
            color: white;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            font-size: 0.75rem;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.2s ease;
        `;

        pre.appendChild(copyButton);

        pre.addEventListener('mouseenter', () => {
            copyButton.style.opacity = '1';
        });

        pre.addEventListener('mouseleave', () => {
            copyButton.style.opacity = '0';
        });

        copyButton.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(codeBlock.textContent);
                copyButton.textContent = 'Copied!';
                setTimeout(() => {
                    copyButton.textContent = 'Copy';
                }, 2000);
            } catch (err) {
                console.error('Failed to copy code:', err);
            }
        });
    });

    if (isPostView) {
        updateActiveTOCLink();
    }
});
function initializeTheme() {

    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const htmlElement = document.documentElement;

    if (systemPrefersDark) {
        htmlElement.setAttribute('data-theme', 'dark');
    } else {
        htmlElement.setAttribute('data-theme', 'light');
    }
}


window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    const htmlElement = document.documentElement;

    if (e.matches) {
        htmlElement.setAttribute('data-theme', 'dark');
    } else {
        htmlElement.setAttribute('data-theme', 'light');
    }
});

initializeTheme();
