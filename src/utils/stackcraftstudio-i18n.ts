import copy from '@data/i18n/stackcraftstudio.json';

export type StudioLocale = 'en';

export function getStudioCopy(_locale: StudioLocale = 'en') {
  return copy.en;
}

export function getStudioSectionCopy(_locale: StudioLocale, dataKey?: string) {
  if (!dataKey) return undefined;
  return copy.en.sections[dataKey as keyof typeof copy.en.sections];
}

function withStudioBase(path: string): string {
  const configuredBase = process.env.PUBLIC_BASE_PATH || import.meta.env.BASE_URL;
  const base = configuredBase === '/'
    ? ''
    : `/${configuredBase.replace(/^\/+|\/+$/g, '')}`;
  return `${base}${path}` || '/';
}

export function getStudioLocalePath(_locale: StudioLocale = 'en'): string {
  return withStudioBase('/');
}

export function getStudioNavigationPath(href: string, _locale: StudioLocale = 'en'): string {
  if (href.startsWith('#') || href.startsWith('http') || href.startsWith('mailto:') || href.startsWith('tel:')) {
    return href;
  }

  const normalized = href.startsWith('/') ? href : `/${href}`;
  return withStudioBase(normalized);
}
