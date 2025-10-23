# Integrate MCP with Copilot

<img src="https://octodex.github.com/images/Professortocat_v2.png" align="right" height="200px" />

Hey henriquebonadio-magalu!

Mona here. I'm done preparing your exercise. Hope you enjoy! 💚

Remember, it's self-paced so feel free to take a break! ☕️

[![](https://img.shields.io/badge/Go%20to%20Exercise-%E2%86%92-1f883d?style=for-the-badge&logo=github&labelColor=197935)](https://github.com/henriquebonadio-magalu/skills-integrate-mcp-with-copilot/issues/1)

---

&copy; 2025 GitHub • [Code of Conduct](https://www.contributor-covenant.org/version/2/1/code_of_conduct/code_of_conduct.md) • [MIT License](https://gh.io/mit)

## Security: Hashing admin passwords (migration helper)

This repository includes a helper to migrate plain-text admin passwords (from imported projects like the Sports Club example) into bcrypt hashes.

- Script: `src/tools/migrate_admin_passwords.php` — run with PHP CLI.
- Example login: `src/tools/secure_login_example.php` — demonstrates PDO + password_verify().

Usage (example):

```bash
# export DB connection for the script
export DB_HOST=127.0.0.1 DB_NAME=sports_club DB_USER=root DB_PASS=""
php src/tools/migrate_admin_passwords.php
```

Make a DB backup before running the migration.
