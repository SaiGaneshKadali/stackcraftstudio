import type { SectionEntry } from './types';

const icon = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>';

export const servicesSections: Record<string, SectionEntry> = {
  services: {
    id: 'services', label: 'Services', groupId: 'services', hint: 'Web development and maintenance services', icon,
    defaultVariant: 'default',
    variants: { default: { component: 'ServicesHomeBlock', dataKey: 'stackcraftstudio-services' } },
  },
};
