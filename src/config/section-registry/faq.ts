import type { SectionEntry } from './types';

const icon = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>';

export const faqSections: Record<string, SectionEntry> = {
  faq3: {
    id: 'faq3', label: 'FAQ', groupId: 'faq', hint: 'Frequently asked questions accordion', icon,
    defaultVariant: 'default',
    variants: { default: { component: 'Faq3Block', dataKey: 'faq3' } },
  },
};
