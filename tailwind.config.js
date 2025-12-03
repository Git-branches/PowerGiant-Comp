module.exports = {
  content: [
    "./pages/*.{php,js}",
    "./index.php",
    "./js/*.js",
    "./components/*.html"
  ],
  theme: {
    extend: {
      colors: {
        // Primary Colors - Navy Blue
        primary: {
          50: "#E6F0FF", // navy-50
          100: "#CCE1FF", // navy-100
          200: "#99C3FF", // navy-200
          300: "#66A5FF", // navy-300
          400: "#3387FF", // navy-400
          500: "#0056B3", // navy-500
          600: "#004A9F", // navy-600
          700: "#003D8A", // navy-700
          800: "#003075", // navy-800
          900: "#002B5B", // navy-900
          DEFAULT: "#002B5B" // navy-900
        },
        
        // Secondary Colors - Energy Orange
        secondary: {
          50: "#FFF4E6", // orange-50
          100: "#FFE9CC", // orange-100
          200: "#FFD399", // orange-200
          300: "#FFBD66", // orange-300
          400: "#FFA733", // orange-400
          500: "#FF6B00", // orange-500
          600: "#E65F00", // orange-600
          700: "#CC5300", // orange-700
          800: "#B34700", // orange-800
          900: "#993B00", // orange-900
          DEFAULT: "#FF6B00" // orange-500
        },
        
        // Accent Colors - Metallic Silver
        accent: {
          50: "#F9F9F9", // gray-50
          100: "#F3F3F3", // gray-100
          200: "#EEEEEE", // gray-200
          300: "#E0E0E0", // gray-300
          400: "#BDBDBD", // gray-400
          500: "#9E9E9E", // gray-500
          DEFAULT: "#E0E0E0" // gray-300
        },
        
        // Background Colors
        background: "#FFFFFF", // white
        surface: "#F8F9FA", // gray-50
        
        // Text Colors
        text: {
          primary: "#111111", // gray-900
          secondary: "#6C757D" // gray-600
        },
        
        // Status Colors
        success: {
          50: "#E8F5E8", // green-50
          100: "#D1F2D1", // green-100
          500: "#28A745", // green-500
          DEFAULT: "#28A745" // green-500
        },
        
        warning: {
          50: "#FFFBF0", // yellow-50
          100: "#FFF3CD", // yellow-100
          500: "#FFC107", // yellow-400
          DEFAULT: "#FFC107" // yellow-400
        },
        
        error: {
          50: "#FDF2F2", // red-50
          100: "#FCE8E6", // red-100
          500: "#DC3545", // red-500
          DEFAULT: "#DC3545" // red-500
        }
      },
      
      fontFamily: {
        sans: ['Roboto', 'sans-serif'],
        poppins: ['Poppins', 'sans-serif'],
        roboto: ['Roboto', 'sans-serif'],
        headline: ['Poppins', 'sans-serif'],
        body: ['Roboto', 'sans-serif']
      },
      
      fontWeight: {
        light: '300',
        normal: '400',
        medium: '500',
        semibold: '600',
        bold: '700',
        extrabold: '800'
      },
      
      boxShadow: {
        'cta': '0 4px 12px rgba(0, 43, 91, 0.15)',
        'card': '0 2px 8px rgba(0, 0, 0, 0.1)',
        'button-hover': '0 8px 24px rgba(255, 107, 0, 0.2)',
        'form-focus': '0 0 0 3px rgba(255, 107, 0, 0.1)'
      },
      
      borderRadius: {
        'xl': '1rem',
        '2xl': '1.5rem',
        '3xl': '2rem'
      },
      
      spacing: {
        '18': '4.5rem',
        '88': '22rem',
        '128': '32rem'
      },
      
      transitionDuration: {
        '250': '250ms',
        '350': '350ms'
      },
      
      transitionTimingFunction: {
        'ease-in-out': 'ease-in-out'
      },
      
      animation: {
        'counter': 'countUp 2s ease-out forwards',
        'fade-in': 'fadeIn 0.6s ease-out forwards',
        'slide-up': 'slideUp 0.8s ease-out forwards',
        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
        'bounce-slow': 'bounce 2s infinite'
      },
      
      keyframes: {
        countUp: {
          '0%': {
            opacity: '0',
            transform: 'translateY(20px)'
          },
          '100%': {
            opacity: '1',
            transform: 'translateY(0)'
          }
        },
        fadeIn: {
          '0%': {
            opacity: '0'
          },
          '100%': {
            opacity: '1'
          }
        },
        slideUp: {
          '0%': {
            opacity: '0',
            transform: 'translateY(30px)'
          },
          '100%': {
            opacity: '1',
            transform: 'translateY(0)'
          }
        }
      },
      
      backgroundImage: {
        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
        'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
        'hero-pattern': 'linear-gradient(135deg, rgba(0, 43, 91, 0.9) 0%, rgba(255, 107, 0, 0.1) 100%)'
      },
      
      backdropBlur: {
        xs: '2px'
      }
    }
  },
  plugins: [
    require('@tailwindcss/forms')({
      strategy: 'class'
    }),
    require('@tailwindcss/typography'),
    require('@tailwindcss/aspect-ratio')
  ]
}