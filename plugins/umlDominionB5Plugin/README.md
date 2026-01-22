# umlDominionB5Plugin (generated starter)

This folder was generated from `umlDominionPlugin.zip`.

## What’s in here
- `css/umlDominionB5.less`: a **Bootstrap-2-free** LESS entry point that reuses UML’s legacy component rules (header/search/facets/etc.) as an *overlay*.
- `css/less/*.less`: selected LESS sources copied from the legacy plugin.
- `images/` and `vendor/`: copied from the legacy plugin so asset URLs keep working.
- `config/umlDominionB5PluginConfiguration.class.php`: loads the overlay stylesheet **last**.

## How to use in AtoM
1. Copy this folder into your AtoM install: `plugins/umlDominionB5Plugin/`
2. Enable the Bootstrap 5 base theme plugin (`arDominionB5Plugin`) and enable `umlDominionB5Plugin`.
3. In production mode, build the CSS once:
   - `make` (or run the `lessc` command in the Makefile)
4. Clear cache: `php symfony cc`

## Important
This is a starting point. Some selectors may need tweaks if the Bootstrap 5 theme markup differs from the old Dominion markup.
