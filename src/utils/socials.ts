import type { CompanyConfig } from '@/types/config';

export type SocialEntry = {
  key: string;
  label: string;
  icon: string;
  href: string;
};

export const SOCIAL_META: Record<string, { label: string; icon: string }> = {
  github: { label: 'GitHub', icon: 'ph-github-logo' },
  linkedin: { label: 'LinkedIn', icon: 'ph-linkedin-logo' },
  whatsapp: { label: 'WhatsApp', icon: 'ph-whatsapp-logo' },
  facebook: { label: 'Facebook', icon: 'ph-facebook-logo' },
  instagram: { label: 'Instagram', icon: 'ph-instagram-logo' },
  youtube: { label: 'YouTube', icon: 'ph-youtube-logo' },
  twitter: { label: 'X (Twitter)', icon: 'ph-x-logo' },
};

/** Returns social profiles with a valid non-empty URL (skipping empty strings and "#"). */
export function getEnabledSocialLinks(company: CompanyConfig | Record<string, unknown>): SocialEntry[] {
  const socials = (company as any)?.socials;
  if (!socials || typeof socials !== 'object') return [];

  return Object.entries(socials as Record<string, string>)
    .filter(([, href]) => href && href.trim() !== '' && href.trim() !== '#')
    .map(([key, href]) => {
      const meta = SOCIAL_META[key] ?? { label: key, icon: 'ph-globe' };
      return { key, label: meta.label, icon: meta.icon, href: href.trim() };
    });
}

/** List of sameAs profile URLs for Schema.org JSON-LD. */
export function getSocialUrls(company: CompanyConfig | Record<string, unknown>): string[] {
  return getEnabledSocialLinks(company).map((s) => s.href);
}
