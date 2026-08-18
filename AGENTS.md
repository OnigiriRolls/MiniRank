# Project Conventions

MiniRank is a simulated keyword position tracker (no real search engines involved). It uses plain php, HTML, CSS and JavaScript.

## Security

- Use parameterized queries (no SQL built by string concatenation)
- Use escaped output
- No secrets or passwords hardcoded in the code

## UI
- UI should be responsive: usable at phone width

## Code Conventions
- Reuse logic and functions; don't duplicate code
- Keep responsibilities and concerns separated
- Keep frontend, backend and db logic separated