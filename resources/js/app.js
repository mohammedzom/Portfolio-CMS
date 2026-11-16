import Alpine from 'alpinejs';

import { architectureTrace } from './site/architecture';
import { toastStore } from './site/contact';
import { siteNavigation } from './site/navigation';
import { initPageEffects } from './site/page-effects';

Alpine.store('toast', toastStore());

Alpine.data('siteNavigation', siteNavigation);
Alpine.data('architectureTrace', architectureTrace);

window.Alpine = Alpine;
Alpine.start();

initPageEffects();
