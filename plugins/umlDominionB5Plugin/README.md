# UML Dominion Bootstrap 5 Plugin

This plugin is a Bootstrap 5 version of the University of Miami Libraries (UML) custom theme for AtoM (Access to Memory).

## Features

- Built on Bootstrap 5.3.0
- Inherits functionality from arDominionB5Plugin
- Custom UML branding with orange (#f17430) color scheme
- Custom footer with UML branding
- Custom header styling
- Repository logo customizations
- Request material button styling

## Installation

1. The plugin is already included in the repository under `plugins/umlDominionB5Plugin/`

2. Build the plugin assets:
   ```bash
   npm install
   npm run build
   ```

3. Enable the plugin by adding it to your `config/ProjectConfiguration.class.php` or `apps/qubit/config/settings.yml`:
   
   In `apps/qubit/config/settings.yml`, set:
   ```yaml
   all:
     .settings:
       enabled_modules: [default, ..., umlDominionB5Plugin]
   ```

4. Clear the cache:
   ```bash
   php symfony cc
   ```

5. The plugin will automatically load the Bootstrap 5 theme and apply UML customizations.

## Customizations

The plugin includes the following UML-specific customizations:

- **Colors**: Primary orange (#f17430), dark gray footer (#4f5b65)
- **Footer**: Custom UML footer with social links and branding
- **Header**: Custom logo sizing (248px x 40px)
- **Home tiles**: Orange heading with gray borders
- **Repository logos**: Shadow boxes with sizing constraints
- **Request material button**: Orange branded button with hover effects
- **Breadcrumbs**: Orange arrow separators using FontAwesome

## File Structure

```
umlDominionB5Plugin/
├── config/
│   └── umlDominionB5PluginConfiguration.class.php
├── images/                      # Image assets from original plugin
├── js/
│   └── main.js                  # JavaScript entry point
├── scss/
│   ├── _variables.scss          # UML color variables
│   ├── _uml.scss               # UML-specific styles
│   └── main.scss               # Main SCSS file importing Bootstrap 5
├── templates/                   # PHP templates for layout
└── webpack.entry.js            # Webpack entry point
```

## Development

To make changes to the theme:

1. Edit SCSS files in `scss/`
2. Edit JavaScript files in `js/`
3. Rebuild with `npm run build`
4. Clear AtoM cache with `php symfony cc`

## Migration from umlDominionPlugin

The original `umlDominionPlugin` was based on Bootstrap 2 with LESS stylesheets. This new `umlDominionB5Plugin` has been updated to:

- Use Bootstrap 5 instead of Bootstrap 2
- Convert all LESS files to SCSS
- Use webpack for bundling instead of Gulp
- Follow the same structure as arDominionB5Plugin
- Maintain all UML-specific customizations

Both plugins can coexist in the repository, but only one should be enabled at a time.
