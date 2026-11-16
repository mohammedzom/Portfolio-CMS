/** Shared motion helpers: every animation respects prefers-reduced-motion. */
export const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

export const prefersReducedMotion = () => reducedMotion.matches;

/** A delay that collapses to (almost) nothing when the visitor prefers reduced motion. */
export const wait = (ms) => new Promise((resolve) => setTimeout(resolve, prefersReducedMotion() ? Math.min(ms, 60) : ms));

/** Escape text for the few places where markup is assembled in JS. */
export const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[character]);
