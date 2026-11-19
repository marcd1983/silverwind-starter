# Design tokens

Put this site's design tokens here as `tokens.json`: the client's Figma
variables exported as JSON (W3C/DTCG format, as Figma's export and most
token plugins produce), or a Tokens Studio file.

Apply them to *Settings > Theme* (colours, fonts, sizes):

```sh
vendor/bin/sake tasks:import-design-tokens --dry-run   # see what would change
vendor/bin/sake tasks:import-design-tokens
```

Start the client's Figma file from the **Silverwind Kit** (variables, text
styles and components that match the theme), so its variables already
have the names the importer reads (`color/primary`, `font/heading`,
`size/container`, `radius/box`, ...). For other files, the full list of
names is in antlion/silverwind-settings' README. Keep `tokens.json` committed: it's the
record of what the site's brand came from.
