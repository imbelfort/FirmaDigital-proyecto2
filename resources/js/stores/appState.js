import { reactive } from 'vue';

export const THEMES = {
    UAGRM: 'uagrm',         // Azul y Rojo vino UAGRM
    ESMERALDA: 'emerald',   // Verde esmeralda académico
    INDIGO: 'indigo',       // Índigo formal moderno
    NINOS: 'ninos',         // Tema Niños (Naranja y tonos lúdicos amigables)
    JOVENES: 'jovenes',     // Tema Jóvenes (Cian / Azul Neón Tecnológico)
    ADULTOS: 'adultos',     // Tema Adultos (Pizarra Formal y Alta Legibilidad)
};

export const FONT_SIZES = {
    SMALL: 'font-sm',
    NORMAL: 'font-normal',
    LARGE: 'font-lg',
};

// Cargar preferencias con persistencia híbrida (localStorage y sesión del usuario)
const savedAutoDark = localStorage.getItem('pref_auto_dark') === 'true';
const savedDarkManual = localStorage.getItem('pref_theme_dark');
const savedTheme = localStorage.getItem('pref_theme_color') || THEMES.UAGRM;
const savedFontSize = localStorage.getItem('pref_theme_font_size') || FONT_SIZES.NORMAL;
const savedContrast = localStorage.getItem('pref_high_contrast') === 'true';
const savedUser = localStorage.getItem('auth_user') ? JSON.parse(localStorage.getItem('auth_user')) : null;

// Determinar modo oscuro según horario (Automático: 19:00 a 06:00 es noche)
function calcularModoOscuroHorario() {
    const hora = new Date().getHours();
    return hora >= 19 || hora < 6;
}

const inicialDark = savedAutoDark 
    ? calcularModoOscuroHorario() 
    : (savedDarkManual !== null ? savedDarkManual === 'true' : false);

export const state = reactive({
    isDark: inicialDark,
    autoDarkSchedule: savedAutoDark,
    theme: savedTheme,
    fontSize: savedFontSize,
    highContrast: savedContrast,
    user: savedUser,
    activeTab: 'dashboard',
    globalSearchOpen: false,
    visitasPagina: 1,
    visitasGlobales: 1,
});

export const actions = {
    toggleDark() {
        // Al tocar manualmente, desactiva el automático y fija la preferencia del usuario
        state.autoDarkSchedule = false;
        savePreference('pref_auto_dark', false);
        state.isDark = !state.isDark;
        savePreference('pref_theme_dark', state.isDark);
        applyThemeClasses();
    },
    toggleAutoDark() {
        state.autoDarkSchedule = !state.autoDarkSchedule;
        savePreference('pref_auto_dark', state.autoDarkSchedule);
        if (state.autoDarkSchedule) {
            state.isDark = calcularModoOscuroHorario();
            savePreference('pref_theme_dark', state.isDark);
        }
        applyThemeClasses();
    },
    checkAutoDarkByTime() {
        if (state.autoDarkSchedule) {
            const deberiaSerDark = calcularModoOscuroHorario();
            if (state.isDark !== deberiaSerDark) {
                state.isDark = deberiaSerDark;
                applyThemeClasses();
            }
        }
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
    setVisitas(pagina, globales) {
        state.visitasPagina = pagina;
        state.visitasGlobales = globales;
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
