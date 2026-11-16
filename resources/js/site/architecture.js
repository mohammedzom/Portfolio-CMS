import { escapeHtml, prefersReducedMotion, wait } from './motion';

const SVG_NS = 'http://www.w3.org/2000/svg';
const ARROW = '<span aria-hidden="true">→</span>';

/** Wires between nodes; the third value marks the dashed deploy wire. */
const EDGES = [
    ['client', 'nginx'],
    ['nginx', 'laravel'],
    ['laravel', 'redis'],
    ['laravel', 'mysql'],
    ['ci', 'laravel', 'dashed'],
];

/**
 * The hero's request trace: Client → Nginx → Laravel → Redis / MySQL inside a
 * Linux VPS, with GitHub Actions deploying into it. The first trace misses the
 * cache, the second hits it, and a deploy clears it again.
 */
export function architectureTrace() {
    // DOM references stay outside Alpine's reactive proxy: native SVG methods
    // such as getTotalLength() throw when called through a Proxy.
    const paths = {};
    let selected = null;

    return {
        state: 'idle',
        stateText: 'idle',
        hint: 'First request misses the cache. Run it twice.',
        busy: false,
        warm: false,
        runs: 0,
        canDeploy: false,

        init() {
            this.drawWires();

            if ('ResizeObserver' in window) {
                new ResizeObserver(() => this.drawWires()).observe(this.$refs.stage);
            } else {
                window.addEventListener('resize', () => this.drawWires());
            }

            document.fonts?.ready.then(() => this.drawWires());

            this.$refs.stage.querySelectorAll('[data-node]').forEach((element) => {
                const show = () => this.showNote(element);
                element.addEventListener('mouseenter', show);
                element.addEventListener('focus', show);
                element.addEventListener('click', () => this.select(element));
            });

            // Ambient wire flow only runs while the figure is on screen.
            new IntersectionObserver(([entry]) => {
                this.$root.classList.toggle('is-visible', entry.isIntersecting);
            }).observe(this.$root);

            window.addEventListener('hero-intro-finished-soon', () => {
                setTimeout(() => {
                    if (! this.runs) {
                        this.trace();
                    }
                }, 1600);
            }, { once: true });
        },

        node(key) {
            return this.$refs.stage.querySelector(`[data-node="${key}"]`);
        },

        drawWires() {
            const stage = this.$refs.stage;
            const svg = this.$refs.wires;
            const stageBox = stage.getBoundingClientRect();

            svg.setAttribute('viewBox', `0 0 ${stageBox.width} ${stageBox.height}`);
            svg.replaceChildren();

            EDGES.forEach(([from, to, dashed]) => {
                const a = this.node(from).getBoundingClientRect();
                const b = this.node(to).getBoundingClientRect();
                const ax = a.left - stageBox.left;
                const ay = a.top - stageBox.top;
                const bx = b.left - stageBox.left;
                const by = b.top - stageBox.top;
                const acx = ax + a.width / 2;
                const acy = ay + a.height / 2;
                const bcx = bx + b.width / 2;
                const bcy = by + b.height / 2;
                const horizontalGap = Math.abs(bcx - acx) - (a.width + b.width) / 2;
                const verticalGap = Math.abs(bcy - acy) - (a.height + b.height) / 2;
                let d;

                if (horizontalGap > verticalGap) {
                    const direction = bcx > acx ? 1 : -1;
                    const x1 = direction > 0 ? ax + a.width : ax;
                    const x2 = direction > 0 ? bx : bx + b.width;
                    const mx = (x1 + x2) / 2;
                    d = `M${x1} ${acy} C${mx} ${acy} ${mx} ${bcy} ${x2} ${bcy}`;
                } else {
                    const direction = bcy > acy ? 1 : -1;
                    const y1 = direction > 0 ? ay + a.height : ay;
                    const y2 = direction > 0 ? by : by + b.height;
                    const my = (y1 + y2) / 2;
                    d = `M${acx} ${y1} C${acx} ${my} ${bcx} ${my} ${bcx} ${y2}`;
                }

                const base = document.createElementNS(SVG_NS, 'path');
                base.setAttribute('d', d);
                base.setAttribute('class', `wire${dashed ? ' wire--dashed' : ''}`);
                svg.appendChild(base);

                if (! dashed) {
                    const flow = document.createElementNS(SVG_NS, 'path');
                    flow.setAttribute('d', d);
                    flow.setAttribute('class', 'wire wire--flow');
                    svg.appendChild(flow);
                }

                paths[`${from}>${to}`] = base;
            });
        },

        showNote(element) {
            const info = this.$refs.info;
            info.classList.remove('is-swap');
            void info.offsetWidth;
            info.classList.add('is-swap');
            info.innerHTML = `<p class="arch__info-title">${escapeHtml(element.dataset.noteTitle)}</p><p class="arch__info-body">${escapeHtml(element.dataset.noteBody)}</p>`;
        },

        select(element) {
            this.$refs.stage.querySelectorAll('.is-selected').forEach((selected) => selected.classList.remove('is-selected'));
            (element.dataset.node === 'host' ? element.closest('.host') : element).classList.add('is-selected');
            selected = element;
            this.showNote(element);
        },

        restoreSelection() {
            if (selected) {
                this.showNote(selected);
            }
        },

        travel(from, to, duration) {
            let path = paths[`${from}>${to}`];
            let reverse = false;

            if (! path) {
                path = paths[`${to}>${from}`];
                reverse = true;
            }

            if (! path) {
                return Promise.resolve();
            }

            const svg = this.$refs.wires;
            const length = path.getTotalLength();
            const halo = document.createElementNS(SVG_NS, 'circle');
            const dot = document.createElementNS(SVG_NS, 'circle');
            halo.setAttribute('r', 9);
            halo.setAttribute('class', 'packet-halo');
            dot.setAttribute('r', 4);
            dot.setAttribute('class', 'packet');
            path.classList.add('is-live');
            svg.append(halo, dot);

            const total = prefersReducedMotion() ? 1 : duration;
            let start = null;

            return new Promise((resolve) => {
                const frame = (time) => {
                    start ??= time;
                    const progress = Math.min(1, (time - start) / total);
                    const eased = progress < 0.5 ? 2 * progress * progress : 1 - Math.pow(-2 * progress + 2, 2) / 2;
                    const point = path.getPointAtLength((reverse ? 1 - eased : eased) * length);
                    dot.setAttribute('cx', point.x);
                    dot.setAttribute('cy', point.y);
                    halo.setAttribute('cx', point.x);
                    halo.setAttribute('cy', point.y);

                    if (progress < 1) {
                        requestAnimationFrame(frame);
                    } else {
                        dot.remove();
                        halo.remove();
                        setTimeout(() => path.classList.remove('is-live'), 250);
                        resolve();
                    }
                };

                requestAnimationFrame(frame);
            });
        },

        heat(key) {
            const element = this.node(key);
            element.classList.add('is-hot');
            setTimeout(() => element.classList.remove('is-hot'), 700);
        },

        log(html) {
            const log = this.$refs.log;
            log.querySelector('.arch__log-empty')?.remove();

            const line = document.createElement('span');
            line.className = 'log-line';
            const match = /^<b>([^<]+)<\/b>\s+([\s\S]*)$/.exec(html);

            if (match && match[1] !== '$ curl') {
                line.innerHTML = `<b>${match[1]}</b><span>${match[2]}</span>`;
            } else {
                line.classList.add('is-plain');
                line.innerHTML = html;
            }

            log.appendChild(line);
            log.scrollTop = log.scrollHeight;
        },

        setState(state, text) {
            this.state = state;
            this.stateText = text;
        },

        async hop(from, to, line, duration = 520) {
            await this.travel(from, to, duration);
            this.heat(to);

            if (line) {
                this.log(line);
            }

            await wait(140);
        },

        async trace() {
            if (this.busy) {
                return;
            }

            this.busy = true;
            this.runs++;
            this.$refs.log.replaceChildren();
            this.setState('running', 'tracing');
            this.log('<b>$ curl</b> -H "Authorization: Bearer …" /api/projects');
            this.heat('client');
            await wait(220);
            await this.hop('client', 'nginx', `<b>nginx</b>    TLS terminated ${ARROW} proxy to php-fpm`);
            await this.hop('nginx', 'laravel', '<b>laravel</b>  route matched, auth:sanctum passed');

            if (! this.warm) {
                await this.hop('laravel', 'redis', '<b>redis</b>    projects.index — cache miss');
                await this.hop('redis', 'laravel', null, 380);
                await this.hop('laravel', 'mysql', '<b>mysql</b>    select from projects (indexed)');
                await this.hop('mysql', 'laravel', '<b>laravel</b>  JsonResource built, stored in cache', 420);
            } else {
                await this.hop('laravel', 'redis', '<b>redis</b>    projects.index — <span class="ok">cache hit</span>');
                await this.hop('redis', 'laravel', '<b>laravel</b>  database skipped', 380);
            }

            await this.hop('laravel', 'nginx', null, 420);
            await this.hop('nginx', 'client', `<b>client</b>   <span class="ok">200 OK</span> application/json${this.warm ? ', served from cache' : ''}`, 420);

            this.hint = this.warm
                ? 'Served from Redis. Ship a deploy to clear the cache.'
                : 'Cache is warm now. Trace again to see the shorter path.';

            if (this.warm) {
                this.canDeploy = true;
            }

            this.warm = true;
            this.setState('ok', 'done');
            this.busy = false;
        },

        async deploy() {
            if (this.busy) {
                return;
            }

            this.busy = true;
            this.$refs.log.replaceChildren();
            this.setState('running', 'deploying');
            this.heat('ci');
            this.log('<b>actions</b>  push to main — running tests');
            await wait(700);
            this.log('<b>actions</b>  <span class="ok">tests passed</span>, deploying release');
            await this.travel('ci', 'laravel', 700);
            this.heat('laravel');
            this.log('<b>laravel</b>  migrate, config:cache, queue:restart');
            await wait(260);
            await this.hop('laravel', 'redis', '<b>redis</b>    cache cleared for the new release');
            this.log('<b>systemd</b>  php-fpm reloaded, workers back up');
            this.warm = false;
            this.hint = 'New release live, cache is cold again.';
            this.setState('ok', 'deployed');
            this.busy = false;
        },
    };
}
