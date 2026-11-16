import { prefersReducedMotion } from './motion';

/**
 * ARIA tablist (arrow keys, Home, End). The active pipeline lights one step at
 * a time while a signal travels along it.
 */
export function expertiseTabs() {
    let timer = null;

    return {
        active: null,
        lit: 0,
        animated: ! prefersReducedMotion(),

        init() {
            this.active = this.tabs()[0]?.dataset.tab ?? null;
            this.startPipeline();
        },

        destroy() {
            clearInterval(timer);
        },

        tabs() {
            return [...this.$root.querySelectorAll('[role="tab"]')];
        },

        select(key, focus = false) {
            this.active = key;
            this.startPipeline();

            if (focus) {
                this.$root.querySelector(`[data-tab="${key}"]`)?.focus();
            }
        },

        startPipeline() {
            clearInterval(timer);
            this.lit = 0;

            if (! this.animated) {
                return;
            }

            const steps = this.$root.querySelectorAll(`[data-panel="${this.active}"] .flow__step`).length;

            if (steps > 1) {
                timer = setInterval(() => {
                    this.lit = (this.lit + 1) % steps;
                }, 900);
            }
        },

        onKeydown(event) {
            const tabs = this.tabs();
            const current = tabs.findIndex((tab) => tab.dataset.tab === this.active);
            const targets = {
                ArrowDown: current + 1,
                ArrowRight: current + 1,
                ArrowUp: current - 1,
                ArrowLeft: current - 1,
                Home: 0,
                End: tabs.length - 1,
            };

            if (! (event.key in targets)) {
                return;
            }

            event.preventDefault();
            const next = tabs[(targets[event.key] + tabs.length) % tabs.length];
            this.select(next.dataset.tab, true);
        },
    };
}
