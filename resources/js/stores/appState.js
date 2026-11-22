import { reactive } from 'vue';

export const THEMES = {
    UAGRM: 'uagrm',         // Azul y Rojo vino UAGRM
    ESMERALDA: 'emerald',   // Verde esmeralda académico
    INDIGO: 'indigo',       // Índigo formal moderno
};

export const FONT_SIZES = {
    SMALL: 'font-sm',
    NORMAL: 'font-normal',
    LARGE: 'font-lg',
};

// Cargar preferencias con persistencia híbrida (localStorage y sesión del usuario)
const savedDark = localStorage.getItem('pref_theme_dark') === 'true';
const savedTheme = localStorage.getItem('pref_theme_color') || THEMES.UAGRM;
const savedFontSize = localStorage.getItem('pref_theme_font_size') || FONT_SIZES.NORMAL;
const savedContrast = localStorage.getItem('pref_high_contrast') === 'true';
const savedUser = localStorage.getItem('auth_user') ? JSON.parse(localStorage.getItem('auth_user')) : null;

export const state = reactive({
    isDark: savedDark,
    theme: savedTheme,
    fontSize: savedFontSize,
    highContrast: savedContrast,
    user: savedUser,
    activeTab: 'dashboard',
    globalSearchOpen: false,
});

export const actions = {
    toggleDark() {
        state.isDark = !state.isDark;
        savePreference('pref_theme_dark', state.isDark);
        applyThemeClasses();
    },
    setTheme(themeName) {
        state.theme = themeName;
        savePreference('pref_theme_color', themeName);
        applyThemeClasses();
    },
    setFontSize(size) {
        state.fontSize = size;
        savePreference('pref_theme_font_size', size);
        applyThemeClasses();
    },
    toggleContrast() {
        state.highContrast = !state.highContrast;
        savePreference('pref_high_contrast', state.highContrast);
        applyThemeClasses();
    },
    setUser(user) {
        state.user = user;
        if (user) {
            localStorage.setItem('auth_user', JSON.stringify(user));
            // Cargar preferencias personalizadas si existen
            if (user.preferencias) {
                if (user.preferencias.dark !== undefined) state.isDark = user.preferencias.dark;
                if (user.preferencias.theme) state.theme = user.preferencias.theme;
                if (user.preferencias.fontSize) state.fontSize = user.preferencias.fontSize;
                if (user.preferencias.contrast !== undefined) state.highContrast = user.preferencias.contrast;
                applyThemeClasses();
            }
        } else {
            localStorage.removeItem('auth_user');
        }
    },
    logout() {
        state.user = null;
        localStorage.removeItem('auth_user');
        state.activeTab = 'dashboard';
    },
    setActiveTab(tab) {
        state.activeTab = tab;
    },
    openGlobalSearch() {
        state.globalSearchOpen = true;
    },
    closeGlobalSearch() {
        state.globalSearchOpen = false;
    }
};

function savePreference(key, value) {
    localStorage.setItem(key, value);
    sessionStorage.setItem(key, value);
}

export function applyThemeClasses() {
    const root = document.documentElement;
    
    // Modo oscuro
    if (state.isDark) {
        root.classList.add('dark');
    } else {
        root.classList.remove('dark');
    }

    // Tamaño de fuente
    root.classList.remove('font-sm', 'font-normal', 'font-lg');
    root.classList.add(state.fontSize);

    // Color del tema Material You
    root.setAttribute('data-theme', state.theme);

    // Alto Contraste (Accesibilidad WCAG AAA)
    if (state.highContrast) {
        root.classList.add('high-contrast');
    } else {
        root.classList.remove('high-contrast');
    }
}
