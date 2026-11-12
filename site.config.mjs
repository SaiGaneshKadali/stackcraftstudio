// Single source of truth for site configuration.
// Update SITE_URL when your production domain is finalized.
export const SITE_URL = 'https://stackcraftstudio.com/';
export const ACTIVE_TEMPLATE = 'stackcraftstudio';
export const SITE_LOCALE = 'en';

// BUILD_SCOPE — pages included in production build (dist/).
export const BUILD_SCOPE = {
  pages: ['/', '/cookies', '/privacy-policy'],
  forceRemove: ['dev', 'qa'],
  images: true,
};
