# Design tokens

Put this site's design tokens here as `tokens.json`: the client's Figma
variables exported as JSON (W3C/DTCG format, as Figma's export and most
token plugins produce), or a Tokens Studio file.

Apply them to *Settings > Theme* (colours, fonts, sizes):

```sh
vendor/bin/sake tasks:import-design-tokens --dry-run   # see what would change
vendor/bin/sake tasks:import-design-tokens
```

Name the Figma variables after the theme settings (`color/primary`,
`font/heading`, `size/container`, `radius/box`, ...); the full list is in
antlion/silverwind-settings' README. Keep `tokens.json` committed: it's the
record of what the site's brand came from.
