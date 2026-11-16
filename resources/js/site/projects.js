import { prefersReducedMotion } from './motion';

/** Category filters for the bento grid. Cards are rendered by Blade; this only toggles them. */
export function projectGrid() {
    return {
        status: '',

        apply(filter) {
            const bento = this.$refs.bento;
            const cards = [...bento.querySelectorAll('.card')];
            const chips = [...this.$root.querySelectorAll('[data-filter]')];
            const shown = [];

            chips.forEach((chip) => {
                const active = chip.dataset.filter === filter;
                chip.classList.toggle('is-active', active);
                chip.setAttribute('aria-pressed', String(active));
            });

            bento.classList.toggle('is-filtered', filter !== 'all');

            cards.forEach((card) => {
                const visible = filter === 'all' || card.dataset.category === filter;
                card.classList.toggle('is-hidden', ! visible);
                card.classList.remove('is-entering', 'is-last-odd');

                if (visible) {
                    shown.push(card);
                }
            });

            if (filter !== 'all' && shown.length % 2 === 1) {
                shown[shown.length - 1].classList.add('is-last-odd');
            }

            void bento.offsetWidth;

            shown.forEach((card, index) => {
                card.style.setProperty('--d', `${index * 0.06}s`);
                card.classList.add('is-entering', 'is-in');
            });

            const label = chips.find((chip) => chip.dataset.filter === filter)?.firstChild.textContent.trim();
            this.status = `${shown.length} project${shown.length === 1 ? '' : 's'} shown${filter === 'all' ? '' : ` in ${label}`}`;
        },

        open(slug, event) {
            const card = event.currentTarget;
            this.$dispatch('open-project', { slug, trigger: card.querySelector('.card__trigger') ?? card });
        },
    };
}

/**
 * Native <dialog> with the project details: focus moves in, Escape and the
 * backdrop close it (animated), ←/→ step through projects, and focus returns
 * to whatever opened it.
 */
export function projectDialog() {
    const source = document.getElementById('project-data');
    const projects = source ? JSON.parse(source.textContent) : [];
    let lastTrigger = null;

    return {
        projects,
        index: 0,
        imageIndex: 0,
        imageFailed: false,

        get project() {
            return this.projects[this.index] ?? null;
        },

        get image() {
            return this.project?.images[this.imageIndex] ?? null;
        },

        show(slug, trigger) {
            const index = this.projects.findIndex((project) => project.slug === slug);

            if (index === -1) {
                return;
            }

            lastTrigger = trigger ?? document.activeElement;
            this.fill(index);

            if (! this.$root.open) {
                this.$root.classList.remove('is-closing');
                this.$root.showModal();
                document.body.style.overflow = 'hidden';
            }

            this.$nextTick(() => this.$refs.close.focus());
        },

        fill(index) {
            this.index = index;
            this.imageIndex = 0;
            this.imageFailed = false;
            this.$refs.body.scrollTop = 0;
        },

        step(direction) {
            this.fill((this.index + direction + this.projects.length) % this.projects.length);
        },

        stepImage(direction) {
            const count = this.project?.images.length ?? 0;

            if (count > 1) {
                this.imageIndex = (this.imageIndex + direction + count) % count;
                this.imageFailed = false;
            }
        },

        close() {
            const dialog = this.$root;

            if (! dialog.open || dialog.classList.contains('is-closing')) {
                return;
            }

            const done = () => {
                dialog.classList.remove('is-closing');
                dialog.close();
            };

            if (prefersReducedMotion()) {
                done();
            } else {
                dialog.classList.add('is-closing');
                setTimeout(done, 240);
            }
        },

        restoreFocus() {
            document.body.style.overflow = '';

            if (lastTrigger && document.contains(lastTrigger) && ! lastTrigger.closest('.is-hidden')) {
                lastTrigger.focus();
            }
        },

        onKeydown(event) {
            if (event.target.closest('a, input, .gallery')) {
                return;
            }

            if (event.key === 'ArrowRight') {
                event.preventDefault();
                this.step(1);
            }

            if (event.key === 'ArrowLeft') {
                event.preventDefault();
                this.step(-1);
            }
        },
    };
}
