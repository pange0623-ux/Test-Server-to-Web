/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './app/**/*.{js,ts,jsx,tsx,mdx}',
    './components/**/*.{js,ts,jsx,tsx,mdx}',
    './lib/**/*.{js,ts,jsx,tsx,mdx}',
  ],
  theme: {
    extend: {
      colors: {
        background: 'rgb(10, 14, 39)',
        foreground: 'rgb(241, 245, 249)',
        card: 'rgb(15, 22, 41)',
        'card-foreground': 'rgb(241, 245, 249)',
        primary: 'rgb(59, 130, 246)',
        'primary-foreground': 'rgb(255, 255, 255)',
        secondary: 'rgb(30, 58, 138)',
        'secondary-foreground': 'rgb(255, 255, 255)',
        muted: 'rgb(100, 116, 139)',
        'muted-foreground': 'rgb(203, 213, 225)',
        accent: 'rgb(96, 165, 250)',
        'accent-foreground': 'rgb(10, 14, 39)',
        destructive: 'rgb(239, 68, 68)',
        'destructive-foreground': 'rgb(255, 255, 255)',
        border: 'rgb(30, 41, 59)',
        input: 'rgb(26, 31, 58)',
        ring: 'rgb(59, 130, 246)',
      },
      borderRadius: {
        lg: 'var(--radius)',
        md: 'calc(var(--radius) - 2px)',
        sm: 'calc(var(--radius) - 4px)',
      },
    },
  },
  plugins: [],
}
