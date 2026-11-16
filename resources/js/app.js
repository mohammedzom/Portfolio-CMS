import Alpine from 'alpinejs';

import { architectureTrace } from './site/architecture';
import { copyEmail, toastStore } from './site/contact';
import { expertiseTabs } from './site/expertise';
import { siteNavigation } from './site/navigation';
import { initPageEffects } from './site/page-effects';
import { projectDialog, projectGrid } from './site/projects';
import { stackUsage } from './site/stack';

Alpine.store('toast', toastStore());

Alpine.data('siteNavigation', siteNavigation);
Alpine.data('architectureTrace', architectureTrace);
Alpine.data('projectGrid', projectGrid);
Alpine.data('projectDialog', projectDialog);
Alpine.data('expertiseTabs', expertiseTabs);
Alpine.data('stackUsage', stackUsage);
Alpine.data('copyEmail', copyEmail);

window.Alpine = Alpine;
Alpine.start();

initPageEffects();
