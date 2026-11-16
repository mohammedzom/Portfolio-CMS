/** Mobile menu: toggle, close on link / Escape / resize to desktop, focus handling. */
export function siteNavigation() {
    return {
        open: false,

        toggle() {
            this.open = ! this.open;

            if (this.open) {
                this.$nextTick(() => this.$refs.menu.querySelector('a')?.focus());
            }
        },

        close(returnFocus = false) {
            if (! this.open) {
                return;
            }

            this.open = false;

            if (returnFocus) {
                this.$refs.toggle.focus();
            }
        },

        closeOnDesktop() {
            if (window.innerWidth > 860) {
                this.close();
            }
        },
    };
}
