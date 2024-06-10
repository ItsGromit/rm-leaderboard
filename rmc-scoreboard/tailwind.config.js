// tailwind.config.js
module.exports = {
  content: [
    './index.html',
    './src/**/*.{vue,js,ts,jsx,tsx}',
  ],
  theme: {
    extend: {
      fontFamily: {
        coolvetica: ['Coolvetica', 'sans-serif'],
        coolveticaItalic: ['CoolveticaItalic', 'sans-serif'],
        coolveticaCompressed: ['CoolveticaCompressed', 'sans-serif'],
      },
      colors: {
        red: '#ff4647',
        blue: '#00a2ff',
      }
    },
  },
  plugins: [],
};
