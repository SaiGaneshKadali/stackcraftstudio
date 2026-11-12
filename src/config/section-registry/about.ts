import type { SectionEntry } from './types';

const icon = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>';

export const aboutSections: Record<string, SectionEntry> = {
  about: {
    id: 'about', label: 'About', groupId: 'about', hint: 'Studio overview and values', icon,
    defaultVariant: 'default',
    variants: { default: { component: 'TeamBlock', dataKey: 'stackcraftstudio-team' } },
  },
};
