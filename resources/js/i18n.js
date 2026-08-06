import { createI18n } from 'vue-i18n';
import en from './locales/en.json';
import kh from './locales/kh.json';

const savedLanguage = localStorage.getItem('app-language') || 'EN';

const i18n = createI18n({
    legacy: false, // Use Composition API
    locale: savedLanguage,
    fallbackLocale: 'EN',
    messages: {
        EN: en,
        KH: kh,
    },
});

export default i18n;
