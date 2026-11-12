import type { BlockCategory } from './types';

export type BlockClassificationStatus = 'confirmed' | 'provisional';

export type ComponentManifestEntry = {
  category: BlockCategory;
  status: BlockClassificationStatus;
  path?: string;
};

export function getComponentPath(name: string, entry: ComponentManifestEntry): string {
  return entry.path ?? `/src/components/registry/${name}.astro`;
}

/** Active portfolio section blocks. */
export const COMPONENT_MANIFEST: Record<string, ComponentManifestEntry> = {
  StackCraftHeroBlock: { category: 'core', status: 'confirmed' },
  ProjectsBlock: { category: 'core', status: 'confirmed' },
  ServicesHomeBlock: { category: 'core', status: 'confirmed' },
  TeamBlock: { category: 'core', status: 'confirmed' },
  ProcessIconStepsBlock: { category: 'core', status: 'confirmed' },
  Faq3Block: { category: 'core', status: 'confirmed' },
  TestimonialV2Block: { category: 'core', status: 'confirmed' },
  ContactSplitBlock: { category: 'core', status: 'confirmed' },
  StackCraftFooterBlock: { category: 'core', status: 'confirmed' },
};

const manifestEntries = Object.entries(COMPONENT_MANIFEST) as [string, ComponentManifestEntry][];

export const COMPONENT_CATEGORIES: Record<BlockCategory, string[]> = {
  core: manifestEntries
    .filter(([, entry]) => entry.category === 'core')
    .map(([name]) => name),
  catalog: manifestEntries
    .filter(([, entry]) => entry.category === 'catalog')
    .map(([name]) => name),
  legacy: manifestEntries
    .filter(([, entry]) => entry.category === 'legacy')
    .map(([name]) => name),
};
