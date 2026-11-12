import type { SectionEntry } from './types';

const icon = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>';

export const portfolioSections: Record<string, SectionEntry> = {
  portfolio: {
    id: 'portfolio', label: 'Portfolio', groupId: 'portfolio', hint: 'Selected work showcase', icon,
    defaultVariant: 'stackcraftstudio',
    variants: {
      stackcraftstudio: { label: 'Selected work cards', component: 'ProjectsBlock', dataKey: 'stackcraftstudio-projects' },
    },
  },
};
