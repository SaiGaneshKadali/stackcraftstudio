/**
 * Template layout registry.
 * Legal and system pages select layouts based on ACTIVE_TEMPLATE.
 */
import { ACTIVE_TEMPLATE } from "./template";

type TemplateId = string;

type LayoutImports = {
  legalLayout: () => Promise<{ default: any }>;
  header: () => Promise<{ default: any }>;
  footer: () => Promise<{ default: any }>;
};

const registry: Record<TemplateId, LayoutImports> = {
  default: {
    legalLayout: () => import("@layouts/LegalLayout.astro"),
    header: () => import("@components/ui/molecules/Navbar.astro"),
    footer: () => import("@components/registry/shell/StackCraftFooterBlock.astro"),
  },
};

const current: TemplateId = registry[ACTIVE_TEMPLATE] ? ACTIVE_TEMPLATE : "default";

export function getCurrentTemplate(): TemplateId {
  return current;
}

export async function getLegalLayout(): Promise<any> {
  const mod = await registry[current].legalLayout();
  return mod.default;
}

export async function getHeader(): Promise<any> {
  const mod = await registry[current].header();
  return mod.default;
}

export async function getFooter(): Promise<any> {
  const mod = await registry[current].footer();
  return mod.default;
}
