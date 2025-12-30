/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.jsx",
  ],
  theme: {
    extend: {
      fontSize: {
        'xs': ['0.875rem', { lineHeight: '1.25rem' }],      // 14px (was 12px)
        'sm': ['1rem', { lineHeight: '1.5rem' }],            // 16px (was 14px)
        'base': ['1.125rem', { lineHeight: '1.75rem' }],    // 18px (was 16px)
        'lg': ['1.25rem', { lineHeight: '1.75rem' }],        // 20px (was 18px)
        'xl': ['1.375rem', { lineHeight: '1.75rem' }],       // 22px (was 20px)
        '2xl': ['1.625rem', { lineHeight: '2rem' }],         // 26px (was 24px)
        '3xl': ['2rem', { lineHeight: '2.25rem' }],         // 32px (was 30px)
        '4xl': ['2.375rem', { lineHeight: '2.5rem' }],       // 38px (was 36px)
        '5xl': ['3.125rem', { lineHeight: '1' }],            // 50px (was 48px)
        '6xl': ['3.875rem', { lineHeight: '1' }],            // 62px (was 60px)
        '7xl': ['4.625rem', { lineHeight: '1' }],            // 74px (was 72px)
        '8xl': ['6.125rem', { lineHeight: '1' }],            // 98px (was 96px)
        '9xl': ['8.125rem', { lineHeight: '1' }],            // 130px (was 128px)
      },
    },
  },
  plugins: [],
}


