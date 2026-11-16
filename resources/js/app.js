import Alpine from 'alpinejs';

import { architectureTrace } from './site/architecture';
import { toastStore } from './site/contact';
import { siteNavigation } from './site/navigation';
import { initPageEffects } from './site/page-effects';
import { projectDialog, projectGrid } from './site/projects';

Alpine.store('toast', toastStore());

Alpine.data('siteNavigation', siteNavigation);
Alpine.data('architectureTrace', architectureTrace);
Alpine.data('projectGrid', projectGrid);
Alpine.data('projectDialog', projectDialog);

window.Alpine = Alpine;
Alpine.start();

initPageEffects();
