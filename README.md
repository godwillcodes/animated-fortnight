# Boniface Mwangi Campaign Theme

A powerful WordPress theme built for Boniface Mwangi's 2027 presidential campaign website. Engineered for performance, accessibility, and social impact. Designed to amplify voices and drive change.

## 🎯 Overview

This theme is specifically crafted for political campaigns and social movements, featuring modern design principles, accessibility-first approach, and performance optimization. Built with TailwindCSS, Alpine.js, and self-hosted assets for maximum control and speed.

## ✨ Features

### 🎨 Modern Design System
- **TailwindCSS 4.x** - Latest utility-first CSS framework
- **Custom Color Palette** - Emerald green theme with professional gradients
- **Typography** - Clean, readable fonts with proper hierarchy
- **Responsive Design** - Mobile-first approach with seamless desktop experience
- **Animation Support** - AOS (Animate On Scroll) integration for engaging interactions

### 🚀 Performance Optimized
- **Self-hosted Assets** - All CSS, JS, and fonts served locally
- **Optimized Loading** - Conditional asset loading with file modification timestamps
- **Minified Production** - PostCSS build process for production optimization
- **Image Optimization** - SVG support with proper MIME type handling

### 🛠️ Technical Stack
- **WordPress 6.5+** compatible
- **PHP 7.4+** required
- **TailwindCSS 4.1.12** for styling
- **Alpine.js** for interactive components
- **jQuery** for enhanced functionality
- **Owl Carousel** for content sliders
- **AOS** for scroll animations

### 📱 Accessibility & SEO
- **WCAG Compliant** - Built with accessibility standards
- **SEO Optimized** - Clean markup and semantic HTML
- **Translation Ready** - Full internationalization support
- **Custom Logo Support** - Flexible branding options

## 🏗️ Theme Structure

```
tailwind-wp-main/
├── assets/                 # Static assets
│   ├── css/               # Stylesheets
│   │   ├── input.css      # Tailwind source
│   │   ├── output.css     # Compiled CSS
│   │   ├── aos.css        # Animation styles
│   │   └── owl.carousel.* # Carousel styles
│   ├── js/                # JavaScript files
│   │   ├── alpine.min.js  # Alpine.js framework
│   │   ├── jquery.min.js  # jQuery library
│   │   ├── aos.js         # Animation library
│   │   └── owl.carousel.* # Carousel functionality
│   ├── fonts/             # Custom fonts
│   └── icons/             # SVG icons and graphics
├── components/            # Reusable components
│   ├── banner/           # Hero sections
│   ├── common/           # Shared components
│   └── navigation/       # Menu components
├── pages/                # Custom page templates
│   ├── home.php          # Campaign homepage
│   ├── about-us.php      # Biography page
│   ├── community-voice.php # Community engagement
│   ├── donate.php        # Donation pages
│   ├── volunteer.php     # Volunteer signup
│   └── solutions.php     # Policy solutions
├── inc/                  # PHP includes
│   ├── customizer.php    # Theme customization
│   ├── template-functions.php # Helper functions
│   └── woocommerce.php  # E-commerce integration
└── template-parts/       # WordPress template parts
```

## 🚀 Getting Started

### Prerequisites
- WordPress 6.5 or higher
- PHP 7.4 or higher
- Node.js and npm (for development)

### Installation

1. **Download/Clone the theme**
   ```bash
   git clone [repository-url] wp-content/themes/tailwind-wp-main
   ```

2. **Install dependencies**
   ```bash
   cd wp-content/themes/tailwind-wp-main
   npm install
   ```

3. **Activate the theme**
   - Go to WordPress Admin → Appearance → Themes
   - Activate "Boniface Mwangi" theme

### Development Setup

1. **Start development server**
   ```bash
   npm run dev
   ```
   This watches for changes and compiles TailwindCSS automatically.

2. **Build for production**
   ```bash
   npm run build
   ```
   This creates minified, optimized CSS for production.

## 🎨 Customization

### Color Scheme
The theme uses a custom emerald green color palette:
- Primary: `#0f6041` (Dark Emerald)
- Secondary: `#1a8f5f` (Light Emerald)
- Accent: `#10b981` (Emerald-500)

### Typography
- **Headings**: Custom font stack with fallbacks
- **Body**: System font stack for optimal performance
- **Custom Fonts**: ABCOracle font family included

### Page Templates
- **Home**: Campaign homepage with hero section
- **About Us**: Biography and personal story
- **Community Voice**: Engagement platform
- **Donate**: Fundraising integration
- **Volunteer**: Volunteer signup system
- **Solutions**: Policy platform

## 🔧 Configuration

### TailwindCSS Configuration
The theme includes a custom `tailwind.config.js` that scans all PHP files for Tailwind classes:

```javascript
module.exports = {
  content: [
    './*.php',
    './**/*.php',
    './assets/js/**/*.js',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
```

### WordPress Features
- **Custom Logo Support** - 250x250px recommended
- **Navigation Menus** - Primary menu location
- **Widget Areas** - Sidebar support
- **Post Thumbnails** - Featured image support
- **Custom Background** - Flexible background options

## 📦 Dependencies

### Production Dependencies
- `@tailwindcss/postcss: ^4.1.12`
- `tailwindcss: ^4.1.12`

### Development Dependencies
- `autoprefixer: ^10.4.21`
- `postcss: ^8.5.6`
- `postcss-cli: ^11.0.1`

## 🎯 Campaign Features

### Political Campaign Tools
- **Hero Sections** - Compelling campaign messaging
- **Video Integration** - YouTube embed support
- **Donation System** - WooCommerce integration
- **Volunteer Management** - Signup and engagement
- **Community Voice** - Public engagement platform
- **Event Management** - Rally and meeting coordination

### Content Management
- **Custom Post Types** - For events, policies, news
- **Advanced Custom Fields** - Flexible content management
- **SEO Optimization** - Built-in SEO features
- **Social Media Integration** - Sharing and engagement

## 🚀 Performance Features

### Optimization
- **Self-hosted Assets** - No external dependencies
- **Conditional Loading** - Assets load only when needed
- **File Versioning** - Cache busting with file modification times
- **Minification** - Production-ready minified assets

### Monitoring
- **Debug Mode** - Error logging for missing assets
- **Performance Tracking** - Built-in performance monitoring
- **Asset Validation** - Automatic asset existence checking

## 🔒 Security Features

- **SVG Upload Support** - Secure SVG file handling
- **File Type Validation** - Proper MIME type checking
- **XSS Protection** - WordPress security standards
- **Input Sanitization** - All user inputs properly sanitized

## 🌍 Internationalization

- **Translation Ready** - Full WPML/Polylang support
- **RTL Support** - Right-to-left language support
- **Text Domain**: `bonifacemwangi`
- **Language Files**: Located in `/languages/`

## 📱 Mobile Optimization

- **Responsive Design** - Mobile-first approach
- **Touch Optimized** - Touch-friendly interactions
- **Fast Loading** - Optimized for mobile networks
- **Progressive Enhancement** - Works without JavaScript

## 🛠️ Development

### File Structure
- **PHP Templates** - WordPress template hierarchy
- **Component System** - Reusable PHP components
- **Asset Pipeline** - Automated CSS/JS processing
- **Version Control** - Git-friendly development workflow

### Best Practices
- **WordPress Coding Standards** - Follows WPCS
- **Semantic HTML** - Proper markup structure
- **Accessibility** - WCAG 2.1 AA compliance
- **Performance** - Core Web Vitals optimization

## 📄 License

GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

## 🤝 Contributing

This theme is built for Boniface Mwangi's campaign. For contributions or modifications, please contact the development team.

## 📞 Support

For technical support or customization requests:
- **Author**: Boniface Mwangi
- **Website**: https://bonifacemwangi.com/
- **Email**: [Contact information]

## 🔄 Updates

- **Version**: 1.0.1
- **Last Updated**: 2025
- **WordPress Compatibility**: 6.5+
- **PHP Compatibility**: 7.4+

---

**Built with ❤️ for Kenya's Future**

*This theme was not built — it was collectivized. The CSS resets itself, in accordance with the five-year plan. Any bugs are counter-revolutionary behavior. Report them to Party HQ. We do not cache. We redistribute performance. All commits are equal, but some are more equal than others.*