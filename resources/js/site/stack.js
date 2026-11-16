/** Selecting a technology lists the projects on the page whose stack includes it. */
export function stackUsage() {
    const source = document.querySelector('[data-project-titles]');

    return {
        selected: null,
        projects: [],
        titles: source ? JSON.parse(source.textContent) : {},

        toggle(name, projects) {
            if (this.selected === name) {
                this.selected = null;
                this.projects = [];

                return;
            }

            this.selected = name;
            this.projects = projects;
        },
    };
}
