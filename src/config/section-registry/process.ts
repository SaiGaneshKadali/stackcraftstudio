import type { SectionEntry } from './types';

const icon = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>';

export const processSections: Record<string, SectionEntry> = {
  processIconSteps: {
    id: 'processIconSteps', label: 'Process', groupId: 'process', hint: '4-step workflow from discovery to support', icon,
    defaultVariant: 'default',
    variants: { default: { component: 'ProcessIconStepsBlock', dataKey: 'process-icon-steps' } },
  },
};
