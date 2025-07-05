# Angular Internationalization (i18n) Guide

This document provides a comprehensive guide for implementing internationalization (i18n) in the FDMS Angular application.

## Overview

Internationalization (i18n) is the process of designing and preparing your application for use in different locales around the world. This project supports multiple languages with Vietnamese (vi) as the primary additional locale alongside English (en-US).

## Current Setup

### Supported Locales
- **en-US** (English - United States) - Source locale
- **vi** (Vietnamese - Vietnam) - Target locale

### Configuration Files

#### 1. Angular Configuration (`angular.json`)
The project has been configured with i18n support in the Angular workspace:

```json
{
  "i18n": {
    "sourceLocale": "en-US",
    "locales": {
      "vi": {
        "translation": "src/locale/messages.vi.xlf"
      }
    }
  }
}
```

#### 2. Build Configurations
- **Development**: `vi` configuration for development builds
- **Production**: `vi` configuration for production builds
- **Localization**: Enabled with `"localize": true`

#### 3. Dependencies
- `@angular/localize`: Angular's official i18n package
- Added to polyfills for proper initialization

## Implementation Details

### 1. Package Installation
The `@angular/localize` package has been added to the project:

```bash
./dev.sh exec mobileview npx ng add @angular/localize
```

### 2. TypeScript Configuration
Updated `tsconfig.app.json` and `tsconfig.spec.json` to include localize types:

```json
{
  "compilerOptions": {
    "types": ["@angular/localize"]
  }
}
```

### 3. Main Application Entry
Updated `src/main.ts` to include localize reference:

```typescript
/// <reference types="@angular/localize" />
```

### 4. Polyfills Configuration
Added `@angular/localize/init` to the polyfills array in `angular.json`.

## Usage Guide

### Marking Text for Translation

#### 1. Basic Translation
Use the `i18n` attribute to mark text for translation:

```html
<ion-button i18n>Click me!</ion-button>
```

#### 2. Translation with Custom ID
For better management, use custom IDs:

```html
<h1 i18n="@@home.title">Welcome to FDMS</h1>
<p i18n="@@home.description">Manage your documents efficiently</p>
```

#### 3. Translation with Meaning and Description
For complex translations, provide meaning and description:

```html
<button i18n="@@button.submit|Submit the form|Button to submit the form">
  Submit
</button>
```

### Translation File Structure

Translation files are stored in XLIFF format at `src/locale/messages.vi.xlf`:

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<xliff version="1.2" xmlns="urn:oasis:names:tc:xliff:document:1.2">
  <file source-language="en-US" datatype="plaintext" original="ng2.template">
    <body>
      <trans-unit id="2590927982756485133" datatype="html">
        <source>Click me!</source>
        <target>Bấm vào đây!</target>
        <context-group purpose="location">
          <context context-type="sourcefile">src/app/home/home.page.html</context>
          <context context-type="linenumber">19</context>
        </context-group>
      </trans-unit>
    </body>
  </file>
</xliff>
```

## Development Workflow

### 1. Extract Translation Messages
To extract translatable text from your templates:

```bash
./dev.sh exec mobileview npx ng extract-i18n --output-path src/locale
```

### 2. Build for Specific Locale
Build the application for Vietnamese locale:

```bash
# Development build
./dev.sh exec mobileview npx ng build --configuration=vi

# Production build
./dev.sh exec mobileview npx ng build --configuration=production,vi
```

### 3. Serve with Locale Configuration
The development server is configured to serve with Vietnamese locale:

```yaml
# compose.dev.yml
command: ["ionic", "serve", "--external", "--no-open", "--configuration=vi"]
```

## Deployment

### Multi-Locale Deployment
The application can be deployed with multiple locale configurations:

1. **Build each locale separately**:
   ```bash
   ng build --configuration=production,vi
   ng build --configuration=production,en-US
   ```

2. **Deploy to different paths**:
   - English: `/`
   - Vietnamese: `/vi/`

### Docker Configuration
The Docker setup includes locale-specific configurations for development and production environments.

## Best Practices

### 1. Translation Management
- Use descriptive IDs for better organization
- Group related translations together
- Maintain consistent terminology across the application

### 2. Text Extraction
- Run extraction regularly during development
- Review extracted messages for accuracy
- Update translation files promptly

### 3. Testing
- Test with different locales during development
- Verify text length fits UI components
- Check for right-to-left (RTL) language support if needed

### 4. Performance
- Lazy load locale-specific resources
- Use AOT compilation for better performance
- Consider code splitting by locale

## Adding New Locales

### 1. Update Angular Configuration
Add new locale to `angular.json`:

```json
{
  "i18n": {
    "locales": {
      "vi": {
        "translation": "src/locale/messages.vi.xlf"
      },
      "fr": {
        "translation": "src/locale/messages.fr.xlf"
      }
    }
  }
}
```

### 2. Create Translation File
Create `src/locale/messages.fr.xlf` with French translations.

### 3. Add Build Configuration
Add build configuration for the new locale in `angular.json`.

## Troubleshooting

### Common Issues

1. **Missing @angular/localize**: Ensure the package is installed and configured
2. **Translation not appearing**: Check that text is properly marked with `i18n`
3. **Build errors**: Verify locale configuration in `angular.json`
4. **Runtime errors**: Ensure polyfills are properly configured

### Debugging
- Use browser dev tools to inspect translation keys
- Check console for i18n-related errors
- Verify translation file format and syntax

## Resources

- [Angular i18n Official Documentation](https://angular.dev/guide/i18n)
- [XLIFF Format Specification](http://docs.oasis-open.org/xliff/xliff-core/v2.1/os/xliff-core-v2.1-os.html)
- [Angular Localize Package](https://www.npmjs.com/package/@angular/localize)

