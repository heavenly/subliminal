# Agent Guidelines for Blog Repository

## Build/Lint/Test Commands
- **No build system**: This is a pure PHP blog with no build tools
- **No test framework**: No formal testing setup
- **No linting tools**: No configured linters
- **Single test execution**: N/A (no tests exist)

## Code Style Guidelines

### PHP
- **Naming**: snake_case for functions and variables
- **Indentation**: 4 spaces
- **File structure**: One function per logical operation
- **Security**: Always use `htmlspecialchars()` for output, validate file paths
- **Error handling**: Basic validation with early returns
- **Includes**: Use `require_once` for file includes

### JavaScript
- **Naming**: camelCase for variables and functions
- **Features**: Modern ES6+ (async/await, arrow functions, template literals)
- **DOM**: Use modern APIs, addEventListener for events
- **Error handling**: Try/catch for async operations

### CSS
- **Variables**: Use CSS custom properties (`--variable-name`)
- **Color functions**: Use `color-mix()` for theme variations
- **Themes**: Support both light/dark themes with CSS variables
- **Units**: Use rem/em for responsive design

### HTML
- **Semantics**: Use proper semantic elements
- **Escaping**: Always escape output in PHP templates
- **Accessibility**: Include proper meta tags and structure

### Markdown
- **Front matter**: YAML format with title, date, description, tags
- **Images**: Reference as `[filename.ext]` in markdown content
- **Structure**: Standard markdown with custom image syntax

### General
- **No type checking**: Dynamic typing throughout
- **No comments**: Code is self-documenting, minimal comments needed
- **Security first**: Validate all inputs and file paths
- **Performance**: Efficient DOM manipulation, minimal reflows