import { reactive, watch } from 'vue';

const THEMES = {
    UAGRM: 'uagrm',     // Azul institucional UAGRM & Rojo vino
    ESMERALDA: 'emerald', // Esmeralda académico moderno
    INDIGO: 'indigo',   // Indigo tecnológico formal
};

const FONT_SIZES = {
    SMALL: 'font-sm',
    NORMAL: 'font-normal',
    LARGE: 'font-lg',
};

// Cargar preferencias guardadas
const savedDark = localStorage.getItem('theme_dark') === 'true';
const savedTheme = localStorage.getItem('theme_color') || THEMES.UAGRM;
const savedFontSize = localStorage.getItem('theme_font_size') || FONT_SIZES.NORMAL;
const savedUser = localStorage.getItem('auth_user') ? JSON.parse(localStorage.getItem('auth_user')) : null;

export const state = reactive({
    isDark: savedDark,
    theme: savedTheme,
    fontSize: savedFontSize,
    user: savedUser,
    activeTab: 'dashboard',
    globalSearchOpen: false,
});

export const actions = {
    toggleDark() {
        state.isDark = !state.isDark;
        localStorage.setItem('theme_dark', state.isDark);
        applyThemeClasses();
    },
    setTheme(themeName) {
        state.theme = themeName;
        localStorage.setItem('theme_color', themeName);
        applyThemeClasses();
    },
    setFontSize(size) {
        state.fontSize = size;
        localStorage.setItem('theme_font_size', size);
        applyThemeClasses();
    },
    setUser(user) {
        state.user = user;
        if (user) {
            localStorage.setItem('auth_user', JSON.stringify(user));
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

export function applyThemeClasses() {
    const root = document.documentElement;
    // Dark mode
    if (state.isDark) {
        root.classList.add('dark');
    } else {
        root.classList.remove('dark');
    }

    // Font size
    root.classList.remove('font-sm', 'font-normal', 'font-lg');
    root.classList.add(state.fontSize);

    // Color theme
    root.setAttribute('data-theme', state.theme);
}
