# Feature Manager

Feature flag manager for PHP, configured from a YAML file.

## Installation

```sh
composer require baptchau/feature-manager
```

Requires PHP 8.2 or later.

## Declaring flags

Flags are written by hand in `config/feature_flag.yaml`, at the root of your project:

```yaml
flags:
  christmas_banner:
    author: "baptiste-chaudron"
    description: "Banner displayed during christmas week"
    deleteDate: "2026-01-15"
    constraints:
      date:
        openingDate: "2025-12-20"
        closingDate: "2025-12-27"
      switch: true
  beta_dashboard:
    author: "baptiste-chaudron"
    description: "New dashboard, only for beta testers"
    deleteDate: "2026-06-01"
    constraints:
      list: ["user-1", "user-2"]
```

| Field         | Required | Description                                    |
|---------------|----------|------------------------------------------------|
| `author`      | yes      | Who owns the flag                              |
| `description` | yes      | What the flag does                             |
| `deleteDate`  | yes      | Date after which the flag should be removed    |
| `constraints` | no       | The flag is enabled only if **all** of them pass |

### Constraints

| Constraint | Value                                  | Enabled when                                                   |
|------------|----------------------------------------|----------------------------------------------------------------|
| `date`     | `openingDate` and/or `closingDate`     | `openingDate` <= now < `closingDate`                           |
| `list`     | list of identifiers                    | the subject passed to `isEnabled()` is in the list             |
| `switch`   | `true` / `false`                       | the value is `true`                                            |

A flag without constraints is always enabled.

### Using another file

Set the path in your project's `composer.json`. A relative path is resolved from the project root:

```json
"extra": {
  "feature-manager": {
    "config-file": "path/to/flags.yaml"
  }
}
```

## Usage

```php
use BaptChau\FeatureManager;

$features = FeatureManager::fromYamlFile();

if ($features->isEnabled('christmas_banner')) {
    // ...
}

if ($features->isEnabled('beta_dashboard', $user->getId())) {
    // ...
}
```

- An unknown flag is considered disabled.
- A malformed file throws `BaptChau\Exception\InvalidFlagException` with the flag name and the problem.
- `$features->getExpiredFlags()` returns the flags past their `deleteDate`.

## Validating the file

After editing the flag file:

```sh
vendor/bin/validate-flags
```

It exits with an error if the file is invalid and warns about expired flags.

## Development

```sh
composer install
make test-unit
```

## License

MIT
