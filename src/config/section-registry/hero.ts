import type { SectionEntry } from './types';

const icon = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>';

export const heroSections: Record<string, SectionEntry> = {
  hero: {
    id: 'hero', label: 'Hero', groupId: 'hero', hint: 'Primary portfolio hero', icon,
    defaultVariant: 'stackcraftstudio',
    variants: {
      stackcraftstudio: { label: 'Portfolio hero', component: 'StackCraftHeroBlock', dataKey: 'stackcraftstudio-hero' },
    },
  },
};
