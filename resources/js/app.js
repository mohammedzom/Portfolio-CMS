import Alpine from 'alpinejs';

import { toastStore } from './site/contact';
import { initPageEffects } from './site/page-effects';

Alpine.store('toast', toastStore());

window.Alpine = Alpine;
Alpine.start();

initPageEffects();
